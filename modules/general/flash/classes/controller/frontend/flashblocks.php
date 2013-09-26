<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Flashblocks extends Controller_Frontend
{
    /**
     * Render all blocks with given name
     * 
     * @param  string $name
     * @return string
     */
    public function widget_flashblocks()
    {
        // Find all visible blocks with given name for current node
        $node_id = Model_Node::current()->id;
        
        $flashblock = new Model_Flashblock();
        $flashblocks = $flashblock->find_all_visible_by_node_id($node_id, array('order_by' => 'position', 'desc' => FALSE));

        if ( ! count($flashblocks))
        {
            return '';
        }

        $script = '';
        foreach ($flashblocks as $flashblock)
        {
            $script .= $flashblock->register();
        }
        Layout::instance()->add_script($script,TRUE);
    }
}
