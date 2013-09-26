<?php

class View_Helper_Admin
{
    /**
     * Renders table header row with ability to sort by columns
     *
     * @param array $columns            List of columns
     * @param string $order_by_param    Name of parameter in request that holds order column name
     * @param string $desc_param        Name of parameter in request that holds the ordering direction
     * @param Request $request          Request
     * @return html
     */
    public static function table_header(array $columns, $order_by_param = 'order', $desc_param = 'desc', $desc_default = TRUE)
    {
        $request = Request::current();

        $current_order_by = $request->param($order_by_param);
        $current_desc     = $request->param($desc_param);

        $html = '';

        foreach ($columns as $field => $column)
        {
            if (is_array($column))
            {
                $label      = isset($column['label']) ? $column['label'] : $field;
                $sort_field = isset($column['sort_field']) ? $column['sort_field'] : $field;
                $sortable   = isset($column['sortable']) ? (bool) $column['sortable'] : TRUE;
            }
            else
            {
                $label      = $column;
                $sort_field = $field;
                $sortable   = TRUE;
            }
            
            if ($sortable)
            {
                $class = '';
                if ($current_order_by == $sort_field)
                {
                    $class = $current_desc ? 'class="sort_desc"' : 'class="sort_asc"';
                    $desc  = $current_desc ? '0' : '1';
                }
                else
                {
                    $desc = $desc_default ? '1' : '0';
                }

                $url = URL::self(array($order_by_param => $sort_field, $desc_param => $desc));

                $html .= '<th><a href="' . $url . '" ' . $class . '>' . $label .'</a></th>';
            }
            else
            {
                $html .= '<th><span>' . $label . '</span></th>';
            }
        }

        return $html;
    }

    /**
     * Renders tabs
     *
     * @param  array $tabs
     * @param  string $selected
     * @return string
     */
    public static function tabs($tabs, $selected)
    {
        $html = '<div class="tabs">';
        foreach ($tabs as $tab_value => $tab)
        {
            $class = ($tab_value == $selected) ? ' class="selected"' : '';
            $icon  = (isset($tab['icon'])) ? $tab['icon'] : '';

            $html .= 
                '<a href="' . $tab['url'] . '"' . $class . '><div class="corner left"><div class="corner right">'
              . '   <div class="icon' . $icon . '">' . HTML::chars($tab['label']) . '</div>'
              . '</div></div></a>';
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * Renders a control button - a html link with image
     *
     * @param  string $url
     * @param  string $title
     * @param  string $image
     * @param  string $alt
     * @param  string $module
     * @return string
     */
    public static function image_control($url, $title, $image, $alt = NULL, $module = 'frontend')
    {
        if ($alt === NULL)
        {
            $alt = $title;
        }

        return
            '<a href="' . $url . '" title="' . HTML::chars($title) . '">'
          .     self::image($image, $alt, $module)
          . '</a>';
    }

    /**
     * Renders an image
     *
     * @param  string $image
     * @param  string $alt
     * @param  string $module
     * @return string
     */
    public static function image($image, $alt = NULL, $module = 'frontend')
    {
        return HTML::image(Modules::uri($module) . '/public/css/frontend/' . $image, array('alt' => $alt));
    }

    /**
     * Open multi-action form
     *
     * @param  string $uri
     * @return string
     */
    public static function multi_action_form_open($uri, array $attributes = array())
    {
        // Apply "multi_action" class
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' multi_action' : 'multi_action';

        return Form_Helper::open($uri, $attributes);
    }

    /**
     * Render a checkbox to select an item in multi-action form
     *
     * @param  mixed $value
     * @return string
     */
    public static function multi_action_checkbox($value)
    {
        return '<input type="checkbox" name="ids[]" value="' . HTML::chars($value) . '" class="checkbox sel" />';
    }

    /**
     * Render a checkbox to select all items in multi-action form
     *
     * @return string
     */
    public static function multi_action_select_all()
    {
        return '<input type="checkbox" name="toggle_all" value="1" class="checkbox toggle_all" />';
    }

    /**
     * Render submit buttons for multi-action form
     * 
     * @param  array $actions
     * @return string
     */
    public static function multi_actions(array $actions, $text = 'с отмеченными:')
    {
        $html =
            '<div class="table_multi_action">'
          . '   <div class="buttons">' . $text;

        foreach ($actions as $action)
        {
            $act    = $action['action'];
            $class  = isset($action['class']) ? $action['class'] : $act;
            $label  = isset($action['label']) ? $action['label'] : $act;

            $html .=
                ' <span class="button ' . $class . '">'
              . '   <input type="submit" name="action[' . $act . '.]" value="' . $label . '" />'
              . '</span>';
        }

        $html .=
            '    </div>'
          . '</div>';

        return $html;
    }

    /**
     * Close a multi-action form
     * 
     * @return string
     */
    public static function multi_action_form_close()
    {
        return Form_Helper::close();
    }

	protected function __construct()
	{
		// This is a static class
	}
}