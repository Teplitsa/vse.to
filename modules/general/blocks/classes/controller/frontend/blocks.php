<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Blocks extends Controller_Frontend
{
    /**
     * Render all blocks with given name
     * 
     * @param  string $name
     * @return string
     */
    public function widget_block($name)
    {
        // Find all visible blocks with given name for current node
        $node_id = Model_Node::current()->id;
        
        $block = new Model_Block();
        $blocks = $block->find_all_visible_by_name_and_node_id($name, $node_id, array('order_by' => 'position', 'desc' => FALSE));

        if ( ! count($blocks))
        {
            return '';
        }

        $html = '';
        foreach ($blocks as $block)
        {
            $html .= $block->text;
        }

        return $html;
    }
}
