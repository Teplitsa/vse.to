<?php defined('SYSPATH') or die('No direct script access.');

class Model_Backend_Menu extends Model
{
    /**
     * Cache of all menus
     * @var array
     */
    protected static $_menus;

    /**
     * Used to generate unique id for menu items (emulate autoincrement)
     * Leave first 100 ids for manually specifing item position
     * @var integer
     */
    protected static $_id = 100;

    /**
     * Configure the specified menu
     * 
     * @param string $menu
     * @param array $properties
     */
    public static function configure($menu, array $properties)
    {
        if ( ! isset(self::$_menus[$menu]))
        {
            self::$_menus[$menu] = array('items' => array());
        }
        self::$_menus[$menu] = array_merge(self::$_menus[$menu], $properties);
    }

    /**
     * Add new menu item
     * 
     * @param array $properties
     * @return integer id of inserted item
     */
    public static function add_item(array $properties)
    {
        if ( ! isset($properties['menu']))
        {
            throw new Kohana_Exception('Menu is not specified for menu item ":caption"', 
                    array(':caption' => isset($properties['caption']) ? $properties['caption'] : ''));
        }
        
        $menu = $properties['menu'];

        // Emulate autoincrement
        if ( ! isset($properties['id']))
        {
            $id = ++self::$_id;
        }
        else
        {
            $id = $properties['id'];
            self::$_id = max(self::$_id, $id) + 1;
        }

        $parent_id = isset($properties['parent_id']) ? $properties['parent_id'] : 0;

        if (isset(self::$_menus[$menu]['items'][$parent_id][$id]))
        {
            throw new Kohana_Exception('You are trying to create item ":new_caption" with it ":id", but there is already an item ":old_caption" with such id!',
                    array(
                        ':id' => $id,
                        ':new_caption' => isset($properties['caption']) ? $properties['caption'] : '',
                        ':old_caption' => isset(self::$_menus[$menu]['items'][$parent_id][$id]['caption']) ? self::$_menus[$menu]['items'][$parent_id][$id]['caption'] : ''
                    ));
        }

        // Automatically generate select condition based on route for new items
        // that don't have "select_conds" explicitly specified
        if ( ! isset($properties['select_conds']))
        {
            $cond = array();

            if (isset($properties['route']))
            {
                $cond['route'] = $properties['route'];
            }
            if (isset($properties['route_params']))
            {
                $cond['route_params'] = $properties['route_params'];
            }
            
            $properties['select_conds'][] = $cond;
        }

        $properties['id'] = $id;
        self::$_menus[$menu]['items'][$parent_id][$id] = $properties;

        return $id;
    }

    /**
     * Get the specified menu
     * 
     * @param string $menu
     * @return array|false
     */
    public static function get_menu($menu)
    {
        if ( ! isset(self::$_menus[$menu]))
        {
            return FALSE;
        }

        // Generate menu path and mark selected items if called for the first time
        if ( ! isset(self::$_menus[$menu]['path']))
        {
            self::_generate_path($menu);
        }

        return self::$_menus[$menu];
    }

    /**
     * Url to the menu item
     *
     * @return string
     */
    public static function url(array $item)
    {
        return URL::to($item['route'], isset($item['route_params']) ? $item['route_params'] : NULL);
    }

    /**
     * Generate path and mark selected items for the specified menu
     *
     * @param string $menu
     */
    protected static function _generate_path($menu)
    {
        $path = array();
        $items = self::$_menus[$menu]['items'];

        // Current route name
        $route_name = Route::name(Request::current()->route);

        // Current controller
        $controller = Request::current()->controller;
        $directory  = Request::current()->directory;

        if ( ! empty($directory))
        {
                // Prepend the directory name to the controller name
                $controller = str_replace(array('\\', '/'), '_', trim($directory, '/')) . '_' . $controller;
        }

        // Current action
        $action = Request::current()->action;

        // Traverse the menu tree and mark current selected items
        // Also, add these selected items to path
        foreach ($items as $parent_id => & $branch)
        {
            // Sort branch by id
            ksort($branch, SORT_ASC);
            
            foreach ($branch as $id => $item)
            {
                // Remove item if there is no current site and item require the site
                if (Model_Site::current()->id === NULL && ! empty($item['site_required']))
                {
                    unset($branch[$id]);
                    continue;
                }
                
                // Check conditions to determine whether this item is selected
                if ( ! empty($item['select_conds']))
                {
                    foreach ($item['select_conds'] as $cond)
                    {
                        $is_selected = TRUE;

                        if (
                            (isset($cond['route'])        && $route_name != $cond['route'])
                         || (isset($cond['route_params']) && ! self::_params_equal($cond['route_params']))
                        )
                        {
                            $is_selected = FALSE;
                        }

                        if ($is_selected)
                        {
                            $items[$parent_id][$id]['selected'] = TRUE;

                            // Add item to path
                            $path[$parent_id] = $item;
                            $path[$parent_id]['id'] = $id;
                            break;
                        }
                    }
                }
            }
        }

        // It's assumed that menu items are NOT MOVED after they are added
        // Than the id of any item is less than that of its parent and we can simply
        // use ksort() to sort path items by parent_id and, thereby, by depth level
        ksort($path, SORT_ASC);
        $path = array_values($path);

        self::$_menus[$menu]['path']  = $path;
        self::$_menus[$menu]['items'] = $items;
    }

    /**
     * Check that specified route params equals to the params from the current uri
     * This is used to determine whether current menu item is selected
     *
     * @param  array $route_params
     * @return boolean
     */
    protected static function _params_equal($route_params)
    {
        foreach ($route_params as $k => $v)
        {
            switch ($k)
            {
                case 'controller':
                    $current_v = Request::current()->controller;
                    break;
                
                case 'action':
                    $current_v = Request::current()->action;
                    break;
                
                default:
                    $current_v = Request::current()->param($k);
            }

            if ($current_v != $v)
            {
                return FALSE;
            }
        }
        return TRUE;
    }
}