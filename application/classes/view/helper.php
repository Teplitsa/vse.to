<?php

/**
 * View helper
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class View_Helper
{
    /**
     * Renders table header row with ability to sort by columns
     *
     * @param array $columns            List of columns
     * @param array $params
     * @param array $ignored_columns
     * @return string
     */
    //$order_by_param = 'order', $desc_param = 'desc', $desc_default = TRUE, $ignored_columns = array()
    public static function table_header(array $columns, array $params = NULL, array $ignored_columns = array())
    {
        $request = Request::current();

        $order_by_param = isset($params['order_by_param']) ? $params['order_by_param'] : 'order';
        $desc_param     = isset($params['desc_param']) ? $params['desc_param'] : 'desc';
        $desc_default   = isset($params['desc_default']) ? $params['desc_default'] : TRUE;

        $current_order_by = $request->param($order_by_param);
        $current_desc     = $request->param($desc_param);

        $template      = isset($params['template'])
                            ? $params['template']
                            : '<th><a href="$(url)">$(label)</a></th>';

        $template_asc  = isset($params['template_asc'])
                            ? $params['template_asc']
                            : '<th><a href="$(url)" class="asc">$(label)</a></th>';

        $template_desc = isset($params['template_desc'])
                            ? $params['template_desc']
                            : '<th><a href="$(url)" class="desc">$(label)</a></th>';

        $template_ignore = isset($params['template_ignore'])
                            ? $params['template_ignore']
                            : '<th><span>$(label)</span></th>';

        $html = '';

        foreach ($columns as $field => $label)
        {
            if ( ! in_array($field, $ignored_columns))
            {
                $class = '';
                if ($current_order_by == $field)
                {
                    $desc = $current_desc ? '0' : '1';
                    $tmpl = $desc ? $template_asc : $template_desc;
                }
                else
                {
                    $desc = $desc_default ? '1' : '0';
                    $tmpl =  $template;
                }

                $url = URL::self(array($order_by_param => $field, $desc_param => $desc));

                Template::replace($tmpl, array('label' => HTML::chars($label), 'url' => $url));
            }
            else
            {
                Template::replace($tmpl, array('label' => HTML::chars($label)));
            }

            $html .= $tmpl;
        }

        return $html;
    }

    protected static $_words_regexp;

    /**
     * Highlight specific words inside tags with class = $class
     *
     * @param  array $words
     * @param  string $content
     * @param  string $class
     * @return string
     */
    public static function highlight_search($words, $content, $class = "searchable")
    {
        self::$_words_regexp = '#(' . implode('|', $words) . ')#iu';

        $content = preg_replace_callback('#(<[^>]*class="searchable"[^>]*>)([^<]*)#ui', array(__CLASS__, '_highlight_search_cb'), $content);

        return $content;
    }

    protected static function _highlight_search_cb($matches)
    {
        $tag  = $matches[1];
        $text = $matches[2];

        $highlighted = preg_replace(self::$_words_regexp, '<span class="highlight">$0</span>', $text);
        return $tag . $highlighted;
    }

    /**
     * Displays a flash-style message
     *
     * @param string $message   Message text
     * @param string $class     Message class
     * @return string
     */
    public static function flash_msg($message, $type = FlashMessages::MESSAGE)
    {
        switch ($type)
        {
            case FlashMessages::MESSAGE:
                $class = 'ok';
                break;

            default:
                $class = 'error';
        }
        return '<div class="flash_msg_' . $class .'">' . HTML::chars($message) . '</div>';
    }

    /**
     * Highlight a cache block and display some info
     *
     * @param  string $html
     * @param  string $cache_id
     * @param  array $cache_tags
     * @return string
     */
    public static function highlight_cache_block($html, $cache_id, array $cache_tags)
    {
        return
            '<div class="cache_block">'
          . '   <div class="cache_block_info">'
          . '       id: "' . $cache_id . '", tags: "' . implode(',', $cache_tags) . '"'
          . '   </div>'
          .     $html
          . '</div>';
    }

	protected function __construct()
	{
		// This is a static class
	}
}