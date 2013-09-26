<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Menus extends Controller_Frontend
{
    /**
     * Render menu with given name
     * 
     * @param  string $name
     * @return string
     */
    public function widget_menu($name)
    {
        // Find menu by name
        $menu = new Model_Menu();
        $menu->find_by_name($name);
        if ( ! isset($menu->id))
        {
            throw new Kohana_Exception('Меню с именем ":name" не найдено!', array(':name' => $name));
        }

        // Load all visible nodes for the menu
        $nodes = $menu->visible_nodes;

        // Path to the currently selected node
        $path = Model_Node::current()->path;

        $view = new View('menu_templates/' . $menu->view);
        
        $view->menu  = $menu;
        $view->root_node = $menu->root_node;
        $view->current_node = Model_Node::current();
        $view->nodes = $nodes;
        $view->path  = $path;

        return $view;
    }
}
