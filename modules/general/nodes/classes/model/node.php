<?php defined('SYSPATH') or die('No direct script access.');

class Model_Node extends Model
{
    /**
     * Current node
     * @var Model_Node
     */
    protected static $_current_node;

    /**
     * Registered node types
     * @var array
     */
    protected static $_node_types = array();

    /**
     * Add new node type
     *
     * @param string $type
     * @param array  $type_info
     */
    public static function add_node_type($type, array $type_info)
    {
        self::$_node_types[$type] = $type_info;
    }

    /**
     * Returns list of all registered node types
     *
     * @return array
     */
    public static function node_types()
    {
        return self::$_node_types;
    }

    /**
     * Get node type information
     *
     * @param string $type Type name
     * @return array
     */
    public static function node_type($type)
    {
        if (isset(self::$_node_types[$type]))
        {
            return self::$_node_types[$type];
        }
        else
        {
            return NULL;
        }
    }
    
    /**
     * Load current node using 'node_id' request parameter / sets current node
     *
     * @param  Model_Node
     * @return Model_Node
     */
    public static function current(Model_Node $node = NULL)
    {
        $site_id = (int) Model_Site::current()->id;
        
        if ($node !== NULL)
        {
            // Explicitly set current node
            self::$_current_node = $node;
        }
        elseif (self::$_current_node === NULL)
        {
            $request = Request::current();
            $node_id = $request->param('node_id');

            $node = new Model_Node();
            if ($node_id != '' && ctype_digit($node_id))
            {
                // Find node by id
                $node->find_by_id_and_site_id((int) $node_id, $site_id);
            }
            elseif ($node_id != '')
            {
                // Find node by alias
                $node->find_by_alias_and_site_id($node_id, $site_id);
            }
            else
            {
                // Determine node type by current route
                $route_name = Route::name(Request::current()->route);

                $route_key = strtolower(APP) . '_route';

                $type = NULL;
                foreach (self::node_types() as $type_name => $type_info)
                {
                    if ($type_info[$route_key] === $route_name)
                    {
                        $type = $type_name;
                        break;
                    }
                }

                if ($type !== NULL)
                {
                    // Try to find node by node type
                    $node->find_by_type_and_site_id($type, $site_id);
                }
            }

            self::$_current_node = $node;
        }
        return self::$_current_node;
    }

    
    /**
     * Node is active by default
     * 
     * @return boolean
     */
    public function default_node_active()
    {
        return TRUE;
    }

    /**
     * Default site id for node
     *
     * @return id
     */
    public function default_site_id()
    {
        return Model_Site::current()->id;
    }

    /**
     * Default node layout (basename of file, without ".php")
     * 
     * @return string
     */
    public function default_layout()
    {
        return 'default';
    }

    /**
     * Finds a parent node id for this node
     *
     * @return integer
     */
    public function get_parent_id()
    {
        if ( ! isset($this->_properties['parent_id']))
        {
            $parent = $this->mapper()->find_parent($this, array('columns' => array('id')));
            $this->_properties['parent_id'] = $parent->id;
        }
        return $this->_properties['parent_id'];
    }

    /**
     * Get path to this node
     * 
     * @return array(id => array(id, caption))
     */
    public function get_path()
    {
        if ( ! isset($this->_properties['path']))
        {
            $path = $this->find_all_parents(array('columns' => array('id', 'lft', 'rgt', 'level', 'caption', 'type'), 'key' => 'id'));
            $path[$this->id] = $this;

            $this->_properties['path'] = $path;
        }
        return $this->_properties['path'];
    }

    /**
     * Get nodes of the same level
     * 
     * @return array(id => array(id, caption))
     */
    public function get_siblings()
    {
        if ( ! isset($this->_properties['siblings']))
        {
            $siblings = $this->find_all_siblings(array('columns' => array('id', 'lft', 'rgt', 'level', 'caption', 'type'), 'key' => 'id'));
            $path[$this->id] = $this;

            $this->_properties['siblings'] = $siblings;
        }
        return $this->_properties['siblings'];
    }
    
    /**
     * Get frontend URI to this node
     *
     * @return string
     */
    public function get_frontend_uri()
    {
        $type_info = self::node_type($this->type);

        if ($type_info === NULL)
        {
            throw new Kohana_Exception('Type info for node type ":type" was not found! (May be you have fogotten to register this node type?)',
                    array(':type' => $this->type));
        }

        $url_params = array();

        if (isset($type_info['model']))
        {
            if ($this->alias != '')
            {
                $url_params['node_id'] = $this->alias;
            }
            else
            {
                $url_params['node_id'] = $this->id;
            }
        }

        if (isset($type_info['frontend_route_params']))
        {
            $url_params += $type_info['frontend_route_params'];
        }        

        return URL::uri_to($type_info['frontend_route'], $url_params);
    }

    /**
     * Backend uri to this node contents
     *
     * @param  boolean $save_history
     * @return string
     */
    public function get_backend_uri($save_history = FALSE)
    {
        $type_info = Model_Node::node_type($this->type);

        // Generate url params and url to node
        $url_params = isset($type_info['backend_params']) ? $type_info['backend_params'] : array();

        if (isset($type_info['model']))
        {
            $url_params['node_id'] = $this->id;
        }

        return URL::uri_to($type_info['backend_route'], $url_params, $save_history);
    }

    /**
     * Full backend url to this node contents
     * 
     * @param  boolean $save_history
     * @return string
     */
    public function get_backend_url($save_history = FALSE)
    {
        return URL::site($this->get_backend_uri($save_history));
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