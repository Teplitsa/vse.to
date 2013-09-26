    <?php defined('SYSPATH') or die('No direct script access.');

class Model_Acl extends Model
{
    /**
     * Current node
     * @var Model_Node
     */
    protected static $_current_acl;

    /**
     * Registered node types
     * @var array
     */
    protected static $_acl_types = array();

    /**
     * Add new acl type
     *
     * @param string $type
     * @param array  $type_info
     */
    public static function add_acl_type($type, array $type_info)
    {
        self::$_acl_types[$type] = $type_info;
    }

    /**
     * Returns list of all registered acl types
     *
     * @return array
     */
    public static function acl_types()
    {
        return self::$_acl_types;
    }

    /**
     * Get acl type information
     *
     * @param string $type Type name
     * @return array
     */
    public static function acl_type($type)
    {
        if (isset(self::$_acl_types[$type]))
        {
            return self::$_acl_types[$type];
        }
        else
        {
            return NULL;
        }
    }

    
    /**
     * Acl is active by default
     * 
     * @return boolean
     */
    public function default_active()
    {
        return TRUE;
    }

    /**
     * Default site id for acl
     *
     * @return id
     */
    public function default_site_id()
    {
        return Model_Site::current()->id;
    }

    /**
     * Save node
     *
     * @param boolean $force_create
     */
    public function save($force_create = FALSE)
    {
        $new_type = $this->new_type;
        $old_type = $this->type;

        if ($new_type !== $old_type && $old_type !== NULL)
        {
            // Deal with old type
            $old_type_info = self::node_type($old_type);

            if (isset($old_type_info['model']))
            {
                // Delete old node model
                $class = "Model_" . ucfirst($old_type_info['model']);
                Model::fly($class)->delete_all_by_node_id($this->id);
            }
        }

        // Save node
        $this->type = $this->new_type;
        parent::save($force_create);

        if ($new_type !== $old_type)
        {
            // Deal with new type
            $new_type_info = self::node_type($new_type);

            if (isset($new_type_info['model']))
            {
                // Create new node model
                $class = "Model_" . ucfirst($new_type_info['model']);
                $model = new $class;

                $model->node_id = $this->id;
                $model->save();
            }
        }

        // Recalculate activity
        $this->mapper()->update_activity();

        return $this;
    }

    /**
     * Delete node with all subnodes and corresponding models
     */
    public function delete()
    {
        // Selecting subtree
        $subnodes = $this->find_all_subtree(array('as_array' => TRUE, 'columns' => array('id','type')));

        // Adding node itself
        $subnodes[]= $this->properties();

        foreach ($subnodes as $node)
        {
            // Deleting all node models
            $type_info = self::node_type($node['type']);

            if (isset($type_info['model']))
            {
                // Delete node model
                $class = "Model_" . ucfirst($type_info['model']);
                Model::fly($class)->delete_all_by_node_id($node['id']);
            }
        }

        // Deleting node from db with all subnodes
        $this->mapper()->delete_with_subtree($this);
    }
    
    /**
     * Validate create and update actions
     *
     * @param  array $newvalues
     * @return boolean
     */
    public function validate(array $newvalues)
    {
        return $this->validate_alias($newvalues);
    }


    /**
     * Validate node alias (must be unique within the current site)
     *
     * @param  array $newvalues
     * @return boolean
     */
    public function validate_alias(array $newvalues)
    {
        if ( ! isset($newvalues['alias']) || $newvalues['alias'] == '')
        {
            // It's allowed no to specify alias for node at all
            return TRUE;
        }

        if ($this->exists_another_by_alias_and_site_id($newvalues['alias'], $this->site_id))
        {
            $this->error('Страница с таким именем в URL уже существует!', 'alias');
            return FALSE;
        }

        return TRUE;
    }
}