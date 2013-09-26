<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Menu extends Controller_Backend
{  
    /**
     * Renders backend menu
     *
     * @return string
     */
    public function widget_menu($menu, $view, $level = 0)
    {
        $menu = Model_Backend_Menu::get_menu($menu);
        if ($menu === FALSE)
            return; // There is no menu with such name

        $path = $menu['path'];
        $tree = $menu['items'];
        $caption = isset($menu['caption']) ? $menu['caption'] : NULL;

        if ($level == 0)
        {
            $items = $tree[0];
        }
        elseif (isset($path[$level-1]['id']) && ! empty($tree[$path[$level-1]['id']]))
        {
            $items = $tree[$path[$level-1]['id']];
        }
        else
        {
            $items = array();
        }

        if (empty($items))
        {
            return;
        }

        $view = new View($view);

        $view->caption = $caption;
        $view->items = $items;

        return $view->render();
    }

    /**
     * Render breadcrumbs for the given menu
     * 
     * @param string $menu
     */
    public function widget_breadcrumbs($menu)
    {
        $menu = Model_Backend_Menu::get_menu($menu);
        if ($menu === FALSE)
            return; // There is no menu with such name

        $path = $menu['path'];

        $view = new View('backend/breadcrumbs');
        $view->path = $path;
        return $view->render();
    }

}
