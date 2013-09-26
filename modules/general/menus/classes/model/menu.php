<?php defined('SYSPATH') or die('No direct script access.');

class Model_Menu extends Model
{
    /**
     * Default value for default visibility for nodes in this menu
     *
     * @return boolean
     */
    public function default_default_visibility()
    {
        return TRUE;
    }

    /**
     * Default site id for menu
     * 
     * @return id
     */
    public function default_site_id()
    {
        return Model_Site::current()->id;
    }

    /**
     * Default value for maximum menu level
     * 
     * @return integer
     */
    public function default_max_level()
    {
        return 0;
    }

    /**
     * Default values for additional settings
     * 
     * @return array
     */
    public function default_settings()
    {
        return array(
            'root_level' => 0
        );
    }

    /**
     * Load root node by root node id for this menu
     * 
     * @return Model_Node
     */
    public function get_root_node()
    {
        if ( ! isset($this->_properties['root_node']))
        {
            $root_node = new Model_Node();

            if (empty($this->settings['root_selected']))
            {
                // Root node is defined explicitly
                if ((int)$this->root_node_id > 0)
                {
                    $root_node->find((int) $this->root_node_id);
                }
            }
            else
            {
                // Root node is choosen from currently selected branch
                $root_level = (int) $this->settings['root_level'];
                $path = array_values(Model_Node::current()->path->as_array());

                if (count($path))
                {
                    if ($root_level <= 0)
                    {
                        // Count backwards from the end of the path
                        $root_level = count($path) + $root_level;
                        
                        if ($root_level < 1)
                        {
                            $root_level = 1;
                        }
                    }

                    if (isset($path[$root_level - 1]))
                    {
                        $root_id = (int) $path[$root_level - 1]['id'];
                        $root_node->find($root_id);
                    }
                }
            }

            $this->_properties['root_node'] = $root_node;
        }
        return $this->_properties['root_node'];
    }

    /**
     * Select all nodes with visibility information
     *
     * @return ModelsTree_NestedSet
     */
    public function get_nodes()
    {
        if ( ! isset($this->_properties['nodes']))
        {
            $this->_properties['nodes'] = Model_Mapper::factory('MenuNode_Mapper')->find_all_menu_nodes($this, new Model_Node());
        }
        return $this->_properties['nodes'];
    }

    /**
     * Get nodes visibility info
     * 
     * @return array(node_id => visible)
     */
    public function get_nodes_visibility()
    {
        
        if ( ! isset($this->_properties['nodes_visibility']))
        {
            $visibility = array();
            foreach ($this->nodes as $node)
            {
                $visibility[$node->id] = $node->visible;
            }
            $this->_properties['nodes_visibility'] = $visibility;
        }
        return $this->_properties['nodes_visibility'];
    }

    /**
     * Select nodes for given menu with respect to root id and visibility information
     *
     * @return ModelsTree_NestedSet
     */
    public function get_visible_nodes()
    {
        if ( ! isset($this->_properties['visible_nodes']))
        {
            $this->_properties['visible_nodes'] = Model_Mapper::factory('MenuNode_Mapper')->find_all_visible_menu_nodes($this, $this->root_node);
        }
        return $this->_properties['visible_nodes'];
    }

    /**
     * Save menu properties and menu nodes visibility information
     *
     * @param  boolean $force_create
     * @return Model_Menu
     */
    public function save($force_create = FALSE)
    {
        parent::save($force_create);

        // Update nodes visibility info for this menu
        if ($this->id !== NULL && is_array($this->nodes_visibility))
        {       
            Model_Mapper::factory('MenuNode_Mapper')->update_menu_nodes($this, $this->nodes_visibility);
        }

        return $this;
    }

    
    /**
     * Validate create and update actions
     *
     * @param  array $newvalues
     * @return boolean
     */
    public function validate(array $newvalues)
    {
        return $this->validate_name($newvalues);
    }


    /**
     * Validate menu name
     *
     * @param  array $newvalues
     * @return boolean
     */
    public function validate_name(array $newvalues)
    {
        if ( ! isset($newvalues['name']))
        {
            $this->error('Вы не указали имя меню!', 'name');
            return FALSE;
        }

        if ($this->exists_another_by_name_and_site_id($newvalues['name'], $this->site_id))
        {
            $this->error('Меню с таким именем уже существует!', 'name');
            return FALSE;
        }

        return TRUE;
    }
}
