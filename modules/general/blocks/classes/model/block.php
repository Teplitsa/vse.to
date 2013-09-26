<?php defined('SYSPATH') or die('No direct script access.');

class Model_Block extends Model
{
    /**
     * Is block visible by default for new nodes?
     *
     * @return boolean
     */
    public function default_default_visibility()
    {
        return TRUE;
    }

    /**
     * Default site id for block
     * 
     * @return id
     */
    public function default_site_id()
    {
        return Model_Site::current()->id;
    }

    /**
     * Get nodes visibility info for this block
     * 
     * @return array(node_id => visible)
     */
    public function get_nodes_visibility()
    {
        if ( ! isset($this->_properties['nodes_visibility']))
        {
            $result = Model_Mapper::factory('BlockNode_Mapper')
                ->find_all_by_block_id($this, (int) $this->id, array('as_array' => TRUE));

            $nodes_visibility = array();
            foreach ($result as $visibility)
            {
                $nodes_visibility[$visibility['node_id']] = $visibility['visible'];
            }
            $this->_properties['nodes_visibility'] = $nodes_visibility;
        }
        return $this->_properties['nodes_visibility'];
    }

    /**
     * Save block properties and block nodes visibility information
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
            Model_Mapper::factory('BlockNode_Mapper')->update_nodes_visibility($this, $this->nodes_visibility);
        }

        return $this;
    }

}
