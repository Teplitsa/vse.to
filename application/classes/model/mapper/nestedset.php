<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Represents a hierarchical structure in database table using nested sets
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
abstract class Model_Mapper_NestedSet extends Model_Mapper {

    public function init()
    {
        parent::init();

        $this->add_column('lft',   array('Type' => 'int unsigned', 'Key' => 'INDEX'))
             ->add_column('rgt',   array('Type' => 'int unsigned', 'Key' => 'INDEX'))
             ->add_column('level', array('Type' => 'smallint unsigned', 'Key' => 'INDEX'));
    }

    /**
     * Prepare tree item for saving
     * calculate necessary values, update nested set structure
     *
     * @param  Model $model
     * @param  boolean $force_create
     * @param  array $values that will be prepared for saving in the database
     * @return array
     */
    public function before_save(Model $model, $force_create = FALSE, array $values = array())
    {
        $values = parent::before_save($model, $force_create, $values);
        
        if ( ! isset($model->id) || $force_create)
        {
            // Inserting new row to db
            $values = $this->before_create($model, $values);
        }
        else
        {
            // Updating row in db
            $values = $this->before_update($model, $values);
        }

        return $values;
    }
    
    /**
     * Prepare values & table structure for a new model
     *
     * @param  Model $model
     * @param  array  $values
     * @return array
     */
    public function before_create(Model $model, array $values)
    {
        $this->lock();

        if ((int) $model->parent_id > 0)
        {
            // Insert node as child of parent_id
            DB::select(DB::expr('@parentRight:=`rgt`, @parentLevel:=`level`'))
                ->from($this->table_name())
                ->where('id', '=', (int) $model->parent_id)
                ->execute($this->get_db());

            DB::update($this->table_name())
                ->set(array('rgt' => DB::expr('rgt + 2')))
                ->where('rgt', '>=', DB::expr('@parentRight'))
                ->execute($this->get_db());

            DB::update($this->table_name())
                ->set(array('lft' => DB::expr('lft + 2')))
                ->where('lft', '>', DB::expr('@parentRight'))
                ->execute($this->get_db());

        }
        else
        {
            // defaults for empty table
            DB::select(DB::expr('@parentRight:=1, @parentLevel:=0'))
                ->execute($this->get_db());

            // Insert new as child of root
            DB::select(DB::expr('@parentRight:=IFNULL((`rgt`+1),1), @parentLevel:=0'))
                ->from($this->table_name())
                ->order_by('rgt', 'DESC')
                ->limit(1)
                ->execute($this->get_db());

        }

        $values['lft'] = DB::expr('@parentRight');
        $values['rgt'] = DB::expr('@parentRight + 1');
        $values['level'] = DB::expr('@parentLevel + 1');

        $this->unlock();

        return $values;
    }

    /**
     * Updates existing row
     *
     * @param  Model $model
     * @param  array $values
     * @return array
     */
    public function before_update(Model $model, array $values)
    {
        $pk = $this->get_pk();

        $this->lock();

        // ----- Node is moved from old_parent to new_parent

        $parent_id = (int) $model->parent_id;
        if ($parent_id > 0)
        {
            // get old_parent (current parent for node)
            $old_parent = array();
            $old_parent = $this->select_row(
                DB::where('lft', '<', (int) $model->lft)->and_where('rgt', '>', (int) $model->rgt),
                array(
                    'order_by' => 'level',
                    'desc' => TRUE,
                    'limit' => 1,
                    'columns' => array($pk, 'lft', 'rgt', 'level')
                )
            );

            // get new_parent (where the node will be moved to)
            $new_parent = array();
            if ($parent_id > 0)
            {
                $new_parent = $this->select_row(
                    DB::where($pk, '=', $parent_id),
                    array('columns' => array($pk, 'lft', 'rgt', 'level'))
                );
            }

            $old_parent_id = ! empty($old_parent) ? $old_parent[$pk] : NULL;
            $new_parent_id = ! empty($new_parent) ? $new_parent[$pk] : NULL;
        }
        else
        {
            // Don't move
            $new_parent_id = $old_parent_id = NULL;
        }

        // Move node only when new parent differs from old parent
        if ($new_parent_id !== $old_parent_id)
        {
            $lft = (int) $model->lft;
            $rgt = (int) $model->rgt;
            $width = $rgt - $lft + 1;
            $level = (int) $model->level;

            if ( ! empty($new_parent))
            {
                // ----- Move node to existing parent node
                $level_diff = $new_parent['level'] - $level + 1;

                if ($new_parent['rgt'] < $rgt)
                {
                    // Moving to the "left"
                    $diff = $new_parent['rgt'] + $width - 1 - $rgt;

                    DB::update($this->table_name())
                        ->set(array(
                            'level' => DB::expr("IF(`lft` < $lft, `level`, `level` + ($level_diff))"),
                            'lft'   => DB::expr("IF(`lft` < $lft, `lft` + $width, `lft` + ($diff))")
                        ))
                        ->where('lft', '>', $new_parent['rgt'])
                        ->and_where('lft', '<', $rgt)
                        ->execute($this->get_db());

                    DB::update($this->table_name())
                        ->set(array('rgt' => DB::expr("IF(`rgt` < $lft, `rgt` + $width, `rgt` + ($diff))")))
                        ->where('rgt', '>=', $new_parent['rgt'])
                        ->and_where('rgt', '<=', $rgt)
                        ->execute($this->get_db());
                }
                else
                {
                    // Moving to the "right"
                    $diff = $new_parent['rgt'] - 1 - $rgt;

                    DB::update($this->table_name())
                        ->set(array(
                            'level' => DB::expr("IF(`lft` > $rgt, `level`, `level` + ($level_diff))"),
                            'lft'   => DB::expr("IF(`lft` > $rgt, `lft` - $width, `lft` + ($diff))")
                        ))
                        ->where('lft', '<', $new_parent['rgt'])
                        ->and_where('lft', '>=', $lft)
                        ->execute($this->get_db());

                    DB::update($this->table_name())
                        ->set(array('rgt' => DB::expr("IF(`rgt` > $rgt, `rgt` - $width, `rgt` + ($diff))")))
                        ->where('rgt', '<', $new_parent['rgt'])
                        ->and_where('rgt', '>', $lft)
                        ->execute($this->get_db());
                }

            }
            else // if (empty($new_parent))
            {
                // ----- Move node to root (moving to "right")
                DB::select(DB::expr("@level_diff:=-" . ($level - 1) . ", @diff:=`rgt`-$rgt"))
                    ->from($this->table_name())
                    ->order_by('rgt', 'DESC')
                    ->limit(1)
                    ->execute($this->get_db());

                DB::update($this->table_name())
                    ->set(array(
                        'level' => DB::expr("IF(`lft` > $rgt, `level`, `level` + @level_diff)"),
                        'lft'   => DB::expr("IF(`lft` > $rgt, `lft` - $width, `lft` + @diff)")
                    ))
                    ->where('lft', '>=', $lft)
                    ->execute($this->get_db());

                DB::update($this->table_name())
                    ->set(array('rgt' => DB::expr("IF(`rgt` > $rgt, `rgt` - $width, `rgt` + @diff)")))
                    ->where('rgt', '>', $lft)
                    ->execute($this->get_db());
            }
        }

        unset($values['lft']);   unset($model->lft);
        unset($values['rgt']);   unset($model->rgt);
        unset($values['level']); unset($model->level);

        $this->unlock();

        // print structure errors
        /*
        $errors = $this->check_structure();
        if (!empty($errors))
        {
            foreach ($errors as $error)
            {
                echo '<strong>' . $error[0] . '</strong><br />';

                if (isset($error[1]) && is_array($error[1]))
                {
                    foreach ($error[1] as $row)
                    {
                        echo print_r($row, TRUE) . '<br />';
                    }
                }

                echo '<br />';
            }
        }
         *
         */

        return $values;
    }

    /***************************************************************************
     * Find
     **************************************************************************/
    /**
     * Find all models by given condition
     *
     * @param  Model $model
     * @param  string|array|Database_Expression_Where $condition
     * @param  array $params
     * @param  Database_Query_Builder_Select $query
     * @return ModelsTree_NestedSet|Models|array
     */
    public function find_all_by(
        Model                         $model,
                                      $condition = NULL,
        array                         $params = NULL,
        Database_Query_Builder_Select $query = NULL
    )
    {
        // ----- params
        // This flag indicates whether the result must be reordered when returned as list
        $reorder_list = TRUE;

        // Apply correct sorting direction, when sorting by lft or rgt (this equals to "position" sotring)
        if (isset($params['order_by']) && ($params['order_by'] == 'lft' || $params['order_by'] == 'rgt'))
        {
            if (empty($params['desc'])) {
                $params['order_by'] = 'lft';
            } else {
                $params['order_by'] = 'rgt';
            }

            $reorder_list = FALSE; // We do not need to reoder the result it its order is "ORDER BY lft/rgt"
        }

        if ( ! isset($params['key']))
        {
            $params['key'] = $this->get_pk();
        }
        
        if ( ! empty($params['batch']))
        {
            
            $params['offset'] = $this->_batch_offset;
            $params['limit']  = $params['batch'];
        }

        // ----- cache
        if ($this->cache_find_all)
        {
            $condition = $this->_prepare_condition($condition);
            $hash = $this->params_hash($params, $condition);

            if (isset($this->_cache[$hash]))
            {
                // Cache hit!
                return $this->_cache[$hash];
            }
        }

        $result = $this->select($condition, $params, $query);
        
        if ( ! empty($params['batch']))
        {
            // Batch find
            if ( ! empty($result))
            {
                $this->_batch_offset += count($result);
            }
            else
            {
                // Reset
                $this->_batch_offset = 0;
            }
        }

        // Return the result of the desired type
        if ( ! empty($params['as_array']))
        {
            // Intentionally blank - result is already an array
        }
        elseif ( ! empty($params['as_list']))
        {
            $result = new Models(get_class($model), $result, $params['key']);
        }
        elseif (empty($params['as_tree']))
        {            
            // Return result as models LIST, but in the natural tree order
            if ($reorder_list)
            {
                // List needs to be reordered
                $result = new ModelsTree_NestedSet(get_class($model), $result, $params['key']);                
                $result = $result->as_list();
            }
            else
            {
                $result = new Models(get_class($model), $result, $params['key']);
            }
        }
        else
        {
            // Return as models tree
            $result = new ModelsTree_NestedSet(get_class($model), $result, $params['key']);
        }

        // ----- cache
        if ($this->cache_find_all)
        {
            if (count($this->_cache) < $this->cache_limit)
            {
                $this->_cache[$hash] = $result;
            }
        }
        
        return $result;
    }

    /**
     * Select all subnodes of given node
     *
     * @param  Model $model
     * @param  array $params
     * @return ModelsTree_NestedSet|Models|array
     */
    public function find_all_subtree($model, array $params = NULL)
    {
        $condition = DB::where('lft', '>', (int)$model->lft)
            ->and_where('rgt', '<', (int)$model->rgt);

        return $this->find_all_by($model, $condition, $params);
    }

    /**
     * Select all subnodes of given node with this node
     *
     * @param  Model $model
     * @param  array $params
     * @return ModelsTree_NestedSet|Models|array
     */
    public function find_all_with_subtree($model, array $params = NULL)
    {
        $condition = DB::where('lft', '>=', (int)$model->lft)
            ->and_where('rgt', '<=', (int)$model->rgt);

        return $this->find_all_by($model, $condition, $params);
    }    
    /**
     * Select all nodes from tree except those, that are subnodes of the given node
     * by criteria
     *
     * @param  Model $model
     * @param  Database_Expression_Where $condition
     * @param  array $params
     * @return ModelsTree_NestedSet|Models|array
     */
    public function find_all_but_subtree_by(Model $model, Database_Expression_Where $condition = NULL, array $params = NULL)
    {
        if ($condition !== NULL)
        {
            $condition
                ->and_where_open()
                    ->and_where('lft', '<', (int) $model->lft)
                    ->or_where('rgt', '>', (int) $model->rgt)
                ->and_where_close();
        }
        else
        {
            $condition =
                DB::where('lft', '<', (int) $model->lft)
                    ->or_where('rgt', '>', (int) $model->rgt);
        }

        return $this->find_all_by($model, $condition, $params);
    }

    /**
     * Select direct children of given node
     *
     * @param  Model $model
     * @param  array $params
     * @return Models|array
     */
    public function find_all_children($model, array $params = NULL)
    {
        $lft   = (int)$model->lft;
        $rgt   = (int)$model->rgt;
        $level = (int) $model->level;

        $condition = DB::where('level', '=', $level + 1);
        if ($lft != 0 && $rgt != 0)
        {
            $condition
                ->and_where('lft', '>', $lft)
                ->and_where('rgt', '<', $rgt);
        }

        return parent::find_all_by($model, $condition, $params);
    }

    /**
     * Find all parents for given node sorted by level
     * Sorting direction and limit can be specified in $params
     *
     * @param  Model $model
     * @param  array $params
     * @param  array $columns
     * @return Models|array
     */
    public function find_all_parents(Model $model, array $params = NULL)
    {
        $condition = DB::where('lft', '<', (int)$model->lft)
            ->and_where('rgt', '>', (int)$model->rgt);

        $params['order_by'] = 'level';

        return parent::find_all_by($model, $condition, $params);
    }

    /**
     * Find all siblings for given node sorted by left
     * Sorting direction and limit can be specified in $params
     *
     * @param  Model $model
     * @param  array $params
     * @param  array $columns
     * @return Models|array
     */
    public function find_all_siblings(Model $model, array $params = NULL)
    {
        $condition = DB::where('level', '=', (int)$model->level);

        $params['order_by'] = 'lft';

        return parent::find_all_by($model, $condition, $params);
    }
    
    /**
     * Find parent for given node
     *
     * @param  Model $model
     * @param  array $params
     * @return Model
     */
    public function find_parent(Model $model, array $params = NULL)
    {
        $where = DB::where('lft', '<', (int)$model->lft)
            ->and_where('rgt', '>', (int)$model->rgt);

        $params['order_by'] = 'level';
        $params['desc'] = TRUE;

        $result = $this->select_row($where, $params);

        $class = get_class($model);
        $parent = new $class;
        $parent->properties($result);
        return $parent;
    }

    /**
     * Find root parent for given node
     *
     * @param  Model $model
     * @param  array $params
     * @return Model
     */
    public function find_root_parent(Model $model, array $params = NULL)
    {
        if ($model->level == 1)
        {
            // Already root node
            return $model->get_properties();
        }

        $where = DB::where('lft', '<', $model->lft)
            ->and_where('rgt', '>', $model->rgt)
            ->and_where('level', '=', 1);

        $result = $this->select_row($where, $params);

        $class = get_class($model);
        $parent = new $class;
        $parent->properties($result);
        return $parent;
    }

    /***************************************************************************
     * Delete
     **************************************************************************/
    /**
     * Deletes node with all subnodes
     *
     * @param Model $model
     */
    public function delete_with_subtree(Model $model)
    {
        $this->lock();
        
        $pk = $this->get_pk();
        $values = $this->select_row(DB::where($pk, '=', $model->$pk), array('columns' => array('lft', 'rgt')));
        if ( ! empty($values))
        {
            $this->delete_rows(DB::where('lft', '>=', $values['lft'])->and_where('rgt', '<=', $values['rgt']));

            $width = $values['rgt'] - $values['lft'] + 1;

            DB::update($this->table_name())
                ->set(array('lft' => DB::expr("`lft` - $width")))
                ->where('lft', '>', $values['rgt'])
                ->execute($this->get_db());


            DB::update($this->table_name())
                ->set(array('rgt' => DB::expr("`rgt` - $width")))
                ->where('rgt', '>', $values['rgt'])
                ->execute($this->get_db());
        }
        
        $this->unlock();
    }

    /***************************************************************************
     * Position
     **************************************************************************/
    /**
     * Move model one position up
     *
     * @param Model $model
     * @param Database_Expression_Where $condition
     */
    public function up(Model $model, Database_Expression_Where $condition = NULL)
    {
        $pk = $this->get_pk();

        $db = $this->get_db();

        // Escape everything
        $id = $db->quote($model->$pk);
        $pk = $db->quote_identifier($pk);
        $table = $db->quote_table($this->table_name());

        if ($condition !== NULL) {
            $condition = " AND " . (string) $condition;
        } else {
            $condition = '';
        }

        $this->lock();
        $db->query(NULL, "SELECT @lft:=NULL, @rgt:=NULL, @next_lft:=null, @next_rgt:=null, @next_id:=null", FALSE);
        $db->query(NULL, "SELECT @lft:=`lft`, @rgt:=`rgt`, @width:=`rgt`-`lft`+1 FROM $table WHERE ($pk=$id)", FALSE);
        $db->query(NULL, "SELECT @next_id:=$pk, @next_lft:=`lft`, @next_rgt:=`rgt`, @next_width:=`rgt`-`lft`+1 FROM $table WHERE (`lft`=@rgt+1) $condition LIMIT 1", FALSE);
        $db->query(NULL,
            "UPDATE $table "
          . "   SET `lft`=IF(`lft`<@rgt,`lft`+@next_width,`lft`-@width), "
          . "       `rgt`=IF(`rgt`<=@rgt,`rgt`+@next_width,`rgt`-@width) "
          . "   WHERE (@next_id IS NOT NULL) AND (`lft`>=@lft AND `rgt`<=@next_rgt)", FALSE);
        $this->unlock();
    }

    /**
     * Move model one position down
     *
     * @param Model $model
     */
    public function down(Model $model, Database_Expression_Where $condition = NULL)
    {
        $pk = $this->get_pk();

        $db = $this->get_db();

        // Escape everything
        $id = $db->quote($model->$pk);
        $pk = $db->quote_identifier($pk);
        $table = $db->quote_table($this->table_name());

        if ($condition !== NULL) {
            $condition = " AND " . (string) $condition;
        } else {
            $condition = '';
        }

        $this->lock();
        $db->query(NULL, "SELECT @lft:=NULL, @rgt:=NULL, @next_lft:=null, @next_rgt:=null, @next_id:=null", FALSE);
        $db->query(NULL, "SELECT @lft:=`lft`, @rgt:=`rgt`, @width:=`rgt`-`lft`+1 FROM $table WHERE ($pk=$id)", FALSE);
        $db->query(NULL, "SELECT @next_id:=$pk, @next_lft:=`lft`, @next_rgt:=`rgt`, @next_width:=`rgt`-`lft`+1 FROM $table WHERE (`rgt`=@lft-1) $condition LIMIT 1", FALSE);
        $db->query(NULL,
            "UPDATE $table "
          . "   SET `lft`=IF(`lft`>=@lft,`lft`-@next_width,`lft`+@width), "
          . "       `rgt`=IF(`rgt`>@lft,`rgt`-@next_width,`rgt`+@width) "
          . "   WHERE (@next_id IS NOT NULL) AND (`lft`>=@next_lft AND `rgt`<=@rgt)", FALSE);
        $this->unlock();
    }

    /***************************************************************************
     * Misc
     **************************************************************************/
    /**
     * Returns true if $child is a child of $parent
     * ($parent can be either a model or an id)
     *
     * @param  Model $child
     * @param  Model|integer $parent
     * @return boolean
     */
    public function is_child_of(Model $child, $parent)
    {
        if ($parent instanceof Model)
        {
            return (isset($parent->id) && $parent->lft < $child->lft && $parent->rgt > $child->rgt);
        }
        else
        {
            return $this->exists(
                DB::where('id', '=', (int) $parent)
                    ->and_where('lft', '<', $child->lft)
                    ->and_where('rgt', '>', $child->rgt)
            );
        }
    }

    /**
     * Returns true if $parent is parent of $child
     * ($child can be either a model or an id)
     *
     * @param  Model $parent
     * @param  Model|integer $child
     * @return boolean
     */
    public function is_parent_of(Model $parent, $child)
    {
        if ($child instanceof Model)
        {
            return (isset($child->id) && $child->lft > $parent->lft && $child->rgt < $parent->rgt);
        }
        else
        {
            return $this->exists(
                DB::where('id', '=', (int) $child)
                    ->and_where('lft', '>', $parent->lft)
                    ->and_where('rgt', '<', $parent->rgt)
            );
        }
    }

    /**
     * Checks the structure of tree
     */
    public function check_structure()
    {
        $errors = array();

        // 1. Check that lft is less than rgt for every row
        $invalid_rows = $this->select(DB::where('lft', '>=', DB::expr("`rgt`")), array('id', 'lft', 'rgt', 'level'));
        if ( ! empty($invalid_rows))
        {
            $errors[] = array("There are nodes with 'lft' greater or equeal to 'rgt'!", $invalid_rows);
        }

        // 2. Check that all levels are correctly computed
        $level_query = DB::select(DB::expr("(COUNT(`id`)+1)"))
            ->from(array($this->table_name(), 't2'))
            ->where('t2.lft', '<', DB::expr('t1.lft'))
            ->where('t2.rgt', '>', DB::expr('t1.rgt'));

        $query = DB::select('id', 'lft', 'rgt', 'level')
            ->from(array($this->table_name(), 't1'))
            ->where('level', '<>', $level_query);

        $invalid_rows = $query->execute($this->get_db())->as_array();

        if (count($invalid_rows))
        {
            // Convert to correct types
            foreach ($invalid_rows as & $values)
            {
                $values = $this->_unsqlize($values);
            }

            $errors[] = array("There are nodes with incorrectly calculated levels!", $invalid_rows);
        }

        // 3. Check that all lft and rgt values are unique
        $query = DB::select(
                array('t1.id', 'id1'),
                array('t1.lft', 'lft1'),
                array('t1.rgt', 'rgt1'),

                array('t2.id', 'id2'),
                array('t2.lft', 'lft2'),
                array('t2.rgt', 'rgt2')
            )
            ->from(array($this->table_name(), 't1'), array($this->table_name(), 't2'))
            ->and_where_open()
                ->where('t2.lft', '=', DB::expr('t1.lft'))
                ->or_where('t2.lft', '=', DB::expr('t1.rgt'))
                ->or_where('t2.rgt', '=', DB::expr('t1.rgt'))
            ->and_where_close()
            ->and_where('t2.id', '<>', DB::expr('t1.id'));

        $invalid_rows = $query->execute($this->get_db())->as_array();

        if (count($invalid_rows))
        {
            // Convert to correct types
            foreach ($invalid_rows as & $values)
            {
                $values = $this->_unsqlize($values);
            }

            $errors[] = array("There are nodes with duplicate lft or rgt values!", $invalid_rows);
        }

        // 4. Check that there are no intersections between (lft, rgt) intervals, only nesting
        $query = DB::select(
                array('t1.id', 'id1'),
                array('t1.lft', 'lft1'),
                array('t1.rgt', 'rgt1'),

                array('t2.id', 'id2'),
                array('t2.lft', 'lft2'),
                array('t2.rgt', 'rgt2')
            )
            ->from(array($this->table_name(), 't1'), array($this->table_name(), 't2'))
            ->or_where_open()
                ->where('t2.lft', '>', DB::expr('t1.lft'))
                ->and_where('t2.lft', '<', DB::expr('t1.rgt'))
                ->and_where('t2.rgt', '>', DB::expr('t1.rgt'))
            ->or_where_close()
            ->or_where_open()
                ->where('t2.rgt', '>', DB::expr('t1.lft'))
                ->and_where('t2.rgt', '<', DB::expr('t1.rgt'))
                ->and_where('t2.lft', '<', DB::expr('t1.lft'))
            ->or_where_close();

        $invalid_rows = $query->execute($this->get_db())->as_array();

        if (count($invalid_rows))
        {
            // Convert to correct types
            foreach ($invalid_rows as & $values)
            {
                $values = $this->_unsqlize($values);
            }

            $errors[] = array("There are nodes who have intersecting lft and rgt values!", $invalid_rows);
        }

        // 5. Difference between max rgt and min lft + 1 must be 2*number of elements
        $tmp = $this->select_row(array(
            'columns' => array(
                array('MIN("lft")', 'min_lft'),
                array('MAX("rgt")', 'max_rgt'),
                array('COUNT("*")', 'count')
            )
        ));

        if ($tmp['min_lft'] != 1)
        {
            $errors[] = array("Minimal lft value must be 1, not " . $tmp['min_lft'] . " !");
        }

        if ($tmp['max_rgt'] - $tmp['min_lft'] + 1 != 2 * $tmp['count'])
        {
            $errors[] = array(
                "Maximum rgt minus minimum lft plus 1 must be equal to 2 x number of elements. "
              . "But: " . $tmp['max_rgt'] . " - " . $tmp['min_lft'] . " + 1 != 2 * " . $tmp['count'] . "!"
            );
        }

        return $errors;
    }
}