<?php defined('SYSPATH') or die('No direct script access.');

class Model_Section_Mapper extends Model_Mapper_NestedSet
{
    public function init()
    {
        parent::init();

        $this->add_column('id',       array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('alias',    array('Type' => 'varchar(63)', 'Key' => 'INDEX'));
        
        $this->add_column('sectiongroup_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        $this->add_column('web_import_id', array('Type' => 'varchar(31)', 'Key' => 'INDEX'));
        $this->add_column('import_id',     array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        
        $this->add_column('caption',        array('Type' => 'varchar(255)'));
        $this->add_column('description',    array('Type' => 'text'));

        $this->add_column('meta_title',          array('Type' => 'text'));
        $this->add_column('meta_description',    array('Type' => 'text'));
        $this->add_column('meta_keywords',       array('Type' => 'text'));

        $this->add_column('section_active', array('Type' => 'boolean', 'Key' => 'INDEX'));
        $this->add_column('active',         array('Type' => 'boolean', 'Key' => 'INDEX'));

        $this->add_column('products_count', array('Type' => 'int unsigned'));

        
        $this->add_virtual_column('sectiongroup_name', array('Type' => 'varchar(31)'));
    }

    /**
     * Find section by condition
     *
     * @param  Model $model
     * @param  string|array|Database_Expression_Where $condition
     * @param  array $params
     * @param  Database_Query_Builder_Select $query
     * @return Model
     */
    public function find_by(Model $model, $condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL)
    {
        $table = $this->table_name();
        
        if ($query === NULL)
        {
            $query = DB::select_array($this->_prepare_columns($params))
                ->from($table);
        }
        
        if (is_array($condition) && ! empty($condition['site_id']))
        {
            // Left join section group to get site_id column
            $sectiongroup_table = Model_Mapper::factory('Model_SectionGroup_Mapper')->table_name();

            $query->join($sectiongroup_table, 'INNER')
                ->on("$sectiongroup_table.id", '=', "$table.sectiongroup_id");
        }

        if (is_array($condition) && ! empty($condition['parent']))
        {
            // Find only descendants of given parent section
            if ($query === NULL)
            {
                $query = DB::select_array($this->_prepare_columns($params))
                    ->from($table);
            }

            $query
                ->where("$table.lft", '>', $condition['parent']->lft)
                ->and_where("$table.rgt", '<', $condition['parent']->rgt)
                ->and_where("$table.level", '=', $condition['parent']->level + 1);

            unset($condition['parent']);
        }
        
        if ( ! empty($params['with_images']))
        {
            // Select logo for section
            $image_table = Model_Mapper::factory('Model_Image_Mapper')->table_name();

            // Select smallest image - the last one
            $image_sizes = Kohana::config('images.section');
            $i = count($image_sizes);

            // Additinal columns
            $query->select(
                array("$image_table.image$i",  'image'),
                array("$image_table.width$i",  'image_width'),
                array("$image_table.height$i", 'image_height')
            );

            $query
                ->join($image_table, 'LEFT')
                    ->on("$image_table.owner_id", '=', "$table.id")
                    ->on("$image_table.owner_type", '=', DB::expr("'section'"))

               ->join(array($image_table, 'img'), 'LEFT')
                    ->on('"img".owner_id', '=', "$image_table.owner_id")
                    ->on('"img".owner_type', '=', DB::expr("'section'"))
                    ->on('"img".position', '<', "$image_table.position")
               ->where(DB::expr('ISNULL(img.id)'), NULL, NULL);
        }

        return parent::find_by($model, $condition, $params, $query);
    }

    /**
     * Find sections by condition
     *
     * @param  Model $model
     * @param  string|array|Database_Expression_Where $condition
     * @param  array $params
     * @param  Database_Query_Builder_Select $query
     * @return Model
     */
    public function find_all_by(Model $model, $condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL)
    {
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

        
        $table = $this->table_name();

        if ($query === NULL)
        {
            $query = DB::select_array($this->_prepare_columns($params))
                ->from($table);
        }

        if (
                (is_array($condition) && ( ! empty($condition['site_id'])))
             || ( ! empty($params['with_sectiongroup_name']))
        )
        {
            // Left join section group to get site_id column
            $sectiongroup_table = Model_Mapper::factory('Model_SectionGroup_Mapper')->table_name();

            $query->join($sectiongroup_table, 'INNER')
                ->on("$sectiongroup_table.id", '=', "$table.sectiongroup_id");

            if ( ! empty($params['with_sectiongroup_name']))
            {
                $query->select(array("$sectiongroup_table.name", 'sectiongroup_name'));
            }
        }

        if ( ! empty($params['with_images']))
        {
            // Select logo for section
            $image_table = Model_Mapper::factory('Model_Image_Mapper')->table_name();

            // Select smallest image - the last one
            $image_sizes = Kohana::config('images.section');
            $i = count($image_sizes);

            // Additinal columns
            $query->select(
                array("$image_table.image$i",  'image'),
                array("$image_table.width$i",  'image_width'),
                array("$image_table.height$i", 'image_height')
            );

            $query
                ->join($image_table, 'LEFT')
                    ->on("$image_table.owner_id", '=', "$table.id")
                    ->on("$image_table.owner_type", '=', DB::expr("'section'"))

               ->join(array($image_table, 'img'), 'LEFT')
                    ->on('"img".owner_id', '=', "$image_table.owner_id")
                    ->on('"img".owner_type', '=', DB::expr("'section'"))
                    ->on('"img".position', '<', "$image_table.position")
               ->where(DB::expr('ISNULL(img.id)'), NULL, NULL);
        }

        /*
        if ( ! empty($params['with_active_products_count']))
        {
            // slow, not used ... :-(
            $prodsec_table = Model_Mapper::factory('ProductSection_Mapper')->table_name();
            $product_table = Model_Mapper::factory('Model_Product_Mapper')->table_name();

            $query->select(array("COUNT(DISTINCT \"$product_table.id\")", 'products_count'));

            // Select number of active products in section and it's active subsections
            $query
                ->join(array($table, 'subsection'), 'INNER')
                    ->on('"subsection".lft', '>=', "$table.lft")
                    ->on('"subsection".rgt', '<=', "$table.rgt")
                    ->on('"subsection".active', '=', DB::expr('1'))

                ->join($prodsec_table, 'LEFT')
                    ->on("$prodsec_table.section_id", '=', '"subsection".id')

                ->join($product_table, 'LEFT')
                    ->on("$product_table.id", '=', "$prodsec_table.product_id")
                    ->on("$product_table.active", '=', DB::expr('1'));

            $query->group_by("$table.id");
        }
         */

        return parent::find_all_by($model, $condition, $params, $query);
    }

    /**
     * Return sections to which the given product belongs
     *
     * @param  Model $model
     * @param  Model_Product $product
     * @param  array $params
     * @return Models
     */
    public function find_all_by_product(Model $model, Model_Product $product, array $params = NULL)
    {
        $table         = $this->table_name();
        $prodsec_table = DbTable::instance('ProductSection')->table_name();

        $query = DB::select_array($this->_prepare_columns($params))
            ->from($table)
            ->join($prodsec_table, 'INNER')
                ->on("$table.id", '=', "$prodsec_table.section_id")
            ->where("$prodsec_table.product_id", '=', (int) $product->id)
            ->and_where("$prodsec_table.distance", '=', 0);

        $params['as_list'] = TRUE;

        return $this->find_all_by($model, NULL, $params, $query);
    }

    /**
     * Find all sections, except the subsection of given one for the specified section group
     *
     * @param  Model $model
     * @param  integer $sectiongroup_id
     * @param  array $params
     * @return ModelsTree
     */
    public function find_all_but_subtree_by_sectiongroup_id(Model $model, $sectiongroup_id, array $params = NULL)
    {
        return parent::find_all_but_subtree_by($model, DB::where('sectiongroup_id', '=', $sectiongroup_id), $params);
    }

    /**
     * Find all visible (which have their parents unfolded or are top-level) sections for the given site
     * @FIXME:  This also loads unfolded branches that a children of folded ones - i.e. still invisible
     *          Maybe switch to recursive version?
     *
     * @param  Model $model
     * @param  integer $sectiongroup_id
     * @param  Model $parant
     * @param  array $unfolded
     * @param  array $params
     * @return ModelsTree|Models|array
     */
    public function find_all_unfolded(Model $model, $sectiongroup_id, Model $parent = NULL, array $unfolded, array $params = NULL)
    {
        $table = $this->table_name();
        $columns = $this->_prepare_columns($params);

        // "has_children" column
        $sub_q = DB::select('id')
            ->from(array($table, 'child'))
            ->where('"child".lft', '>', DB::expr($this->get_db()->quote_identifier("$table.lft")))
            ->and_where('"child".rgt', '<', DB::expr($this->get_db()->quote_identifier("$table.rgt")));

        $columns[] = array(DB::expr("EXISTS($sub_q)"), 'has_children');

        $query = DB::select_array($columns)
            ->from($table)
            ->join(array($table, 'parent'), 'LEFT')
                ->on('"parent".lft', '<', "$table.lft")
                ->on('"parent".rgt', '>', "$table.rgt")
                ->on('"parent".level', '=', DB::expr($this->get_db()->quote_identifier("$table.level") . " - 1"));

        // ----- condition
        $condition = DB::where();

        if ($sectiongroup_id !== NULL)
        {
            $condition->and_where("$table.sectiongroup_id", '=', $sectiongroup_id);
        }

        if ($parent !== NULL)
        {
            $condition
                ->and_where("$table.lft", '>', $parent->lft)
                ->and_where("$table.rgt", '<', $parent->rgt);
        }

        if ( ! empty($unfolded))
        {
            $condition
                ->and_where_open()
                    ->or_where('"parent".id', 'IN', DB::expr('(' . implode(',', $unfolded) . ')'));
            if ($parent === NULL)
            {
                $condition
                    ->or_where('ISNULL("parent".id)');
            }
            $condition
                ->and_where_close();
        }
        elseif ($parent === NULL)
        {
            $condition->and_where('ISNULL("parent".id)');
        }

        $tree = $this->find_all_by($model, $condition, $params, $query);

        if ($parent !== NULL && $tree instanceof ModelsTree)
        {
            // Set $parent as a root of the tree
            $tree->root($parent);
        }
        return $tree;
    }


    /**
     * Check if there is another section with given condition
     *
     * @param  Model $model
     * @param  string|array|Database_Expression_Where $condition
     * @return boolean
     */
    public function exists_another_by(Model $model, $condition)
    {
        if (is_array($condition))
        {
            if (isset($condition['parent']) && ($condition['parent']->id !== NULL))
            {
                $parent = $condition['parent'];
                unset($condition['parent']);
                $condition = $this->_prepare_condition($condition);
                
                $table = $this->table_name();
                $condition
                    ->and_where("$table.lft", '>', $parent->lft)
                    ->and_where("$table.rgt", '<', $parent->rgt)
                    ->and_where("$table.level", '=', $parent->level + 1);
            }
            else
            {
                unset($condition['parent']);
            }
        }
        return parent::exists_another_by($model, $condition);
    }

    /**
     * Move section up
     *
     * @param Model $section
     * @param Database_Expression_Where $condition
     */
    public function up(Model $section, Database_Expression_Where $condition = NULL)
    {
        parent::up($section, DB::where('sectiongroup_id', '=', $section->sectiongroup_id));
    }

    /**
     * Move section down
     *
     * @param Model $section
     * @param Database_Expression_Where $condition
     */
    public function down(Model $section, Database_Expression_Where $condition = NULL)
    {
        parent::down($section, DB::where('sectiongroup_id', '=', $section->sectiongroup_id));
    }

    /**
     * Update activity for sections
     *
     * @param array $ids
     */
    public function update_activity(array $ids = NULL)
    {
        $table = $this->table_name();

        $query = DB::select("$table.id")->from($table);

        // ----- Does inactive parent section exist for the section?
        $sub_q = DB::select('id')
            ->from(array($table, 'parent'))
            ->where('"parent".lft', '<=', DB::expr($this->get_db()->quote_identifier("$table.lft")))
            ->and_where('"parent".rgt', '>=', DB::expr($this->get_db()->quote_identifier("$table.rgt")))
            ->and_where('"parent".section_active', '=', 0);

        $exists = DB::expr("NOT EXISTS($sub_q)");

        $query->select(array($exists, 'active'));

        if ( ! empty($ids))
        {
            // Update stats only for specified sections
            // Select their children - they will be affected too
            $more_ids = DB::select(
                    "$table.id",
                    array('"child".id', 'child_id')
            )->from($table)
                ->join(array($table, 'child'), 'LEFT')
                    ->on('"child".lft', '>', "$table.lft")
                    ->on('"child".rgt', '<', "$table.rgt")
             ->where("$table.id", 'IN', DB::expr('(' . implode(',', $ids) . ')'))
             ->execute($this->get_db());

            foreach ($more_ids as $id)
            {
                if ( ! empty($id['id']))       $ids[] = $id['id'];
                if ( ! empty($id['child_id'])) $ids[] = $id['child_id'];
            }

            $ids = array_unique($ids);

            $query->where("$table.id", 'IN', DB::expr('(' . implode(',', $ids) . ')'));
        }
        $count = $this->count_rows();

        $limit = 100;
        $offset = 0;
        $loop_prevention = 0;

        do {
            $sections = $query
                ->offset($offset)->limit($limit)
                ->execute($this->get_db());

            foreach ($sections as $section)
            {
                $this->update(array(
                    'active' => $section['active'],
                ), DB::where('id', '=', $section['id']));
            }

            $offset += count($sections);
            $loop_prevention++;
        }
        while ($offset < $count && count($sections) && $loop_prevention < 1000);

        if ($loop_prevention >= 1000)
        {
            throw new Kohana_Exception('Possible infinite loop in ' . __METHOD__);
        }
    }

    /**
     * Recalculate active products count for sections
     */
    public function update_products_count(array $ids = NULL)
    {
        $table = $this->table_name();
        $prodsec_table = DbTable::instance('ProductSection')->table_name();
        $product_table = Model_Mapper::factory('Model_Product_Mapper')->table_name();

        // Count active products
        $subq = DB::select(array("COUNT(DISTINCT \"$product_table.id\")", 'products_count'))
            ->from($product_table)
            ->join($prodsec_table, 'INNER')
                ->on("$prodsec_table.product_id", '=', "$product_table.id")
             ->where("$prodsec_table.section_id", '=', DB::expr($this->get_db()->quote_identifier("$table.id")))
             ->and_where("$product_table.active", '=', 1);

        // Update product count for section
        $query = DB::update($table)
            ->set(array('products_count' => $subq));

        if (is_array($ids))
        {
            $ids = array_filter($ids);
        }
        if ( ! empty($ids))
        {
            // Update stats only for specified sections
            // Select their parents - they will be affected too
            $more_ids = DB::select(
                    "$table.id",
                    array('"parent".id', 'parent_id')
            )->from($table)
                ->join(array($table, 'parent'), 'LEFT')
                    ->on('"parent".lft', '<', "$table.lft")
                    ->on('"parent".rgt', '>', "$table.rgt")
             ->where("$table.id", 'IN', DB::expr('(' . implode(',', $ids) . ')'))
             ->execute($this->get_db());

            foreach ($more_ids as $id)
            {
                if ( ! empty($id['id']))        $ids[] = $id['id'];
                if ( ! empty($id['parent_id'])) $ids[] = $id['parent_id'];
            }

            $ids = array_unique($ids);

            $query->where("$table.id", 'IN', DB::expr('(' . implode(',', $ids) . ')'));
        }

        // do
        $query->execute($this->get_db());
    }

}