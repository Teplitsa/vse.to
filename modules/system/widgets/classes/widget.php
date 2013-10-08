<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Ajax widget
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Widget extends View
{
    /**
     * Add necessary javascripts for ajax requests
     */
    public static function add_scripts()
    {
        // Depends on jQuery
        jQuery::add_scripts();

        Layout::instance()->add_script("var base_url='" . URL::base() . "';", TRUE);
        Layout::instance()->add_script(Modules::uri('widgets') . '/public/js/widgets.js');
    }
    
    /**
     * The same as @see Widget::widget_with_args, but widget parameters are
     * function arguments instead of array
     *
     * @param  string $controller
     * @param  string $widget
     * @return Widget|View|string
     */
    public static function render_widget($controller, $widget)
    {
        $args = func_get_args();
        array_shift($args); // controller
        array_shift($args); // widget

        return Widget::render_widget_with_args($controller, $widget, $args);
    }

    /**
     * Calls widget_$widget function of the given $controller for the current request
     * and returns the result
     * @TODO: [!!] Access control! Access control!!
     *
     * @param  string $controller
     * @param  string $widget
     * @return Widget|View|string
     */
    public static function render_widget_with_args($controller, $widget, array $args = NULL)
    {
        //$token = Profiler::start('render_widget', $widget);

        try {
            $controller = Request::current()->get_controller($controller);

            $method = 'widget_' . $widget;

            if (empty($args))
            {
                $output = call_user_func(array($controller, $method));
            }
            else
            {
                $output = call_user_func_array(array($controller, $method), $args);
            }
        }
        catch (Exception $e)
        {
            if (Kohana::$environment === Kohana::DEVELOPMENT)
            {
                throw $e;
            }

            $output =  'Произошла ошибка при отрисовке виджета';
        }

        //Profiler::stop($token);
        return $output;
    }

    /**
     * Switch context (substitute the request's uri with uri from $_GET['context'] parameter)
     * and return the new request
     *
     * @return Request
     */
    public static function switch_context()
    {
        if ( ! empty($_GET['context']))
        {
            // context can be uri or url - strip base path & index
            $uri = $_GET['context'];
            $uri = parse_url($uri, PHP_URL_PATH);

            $base_url = parse_url(Kohana::$base_url, PHP_URL_PATH);
            if (strpos($uri, $base_url) === 0)
            {
                $uri = substr($uri, strlen($base_url));
            }
            if (Kohana::$index_file AND strpos($uri, Kohana::$index_file) === 0)
            {
                $uri = substr($uri, strlen(Kohana::$index_file));
            }
            
            // Switch context to given uri
            $request = new Request($uri);
            Request::$current = $request;
            return $request;
        }
        else
        {
            return Request::current();
        }
    }

    
    /**
     * Widget id
     * @var string
     */
    public $id;

    /**
     * Widget wrapper class
     * @var string
     */
    public $class;

    /**
     * Uri to use to redraw the widget using ajax requests
     * @var string
     */
    public $ajax_uri;

    /**
     * Context uri for this widget (defaults to the uri of current request)
     * FALSE - do not use context uri
     * @var string
     */
    public $context_uri;

    /**
     * Widget wrapper
     * @var string
     */
    public $wrapper = '<div id="{{id}}" class="widget {{class}}">{{output}}</div>';
    
	/**
     * Renders widget content, wrapping it and adding special info if necessary
     * 
	 * @param    string $file view filename
	 * @return   string
	 */
    public function render($file = NULL)
    {        
        // constuct widget properties
        $props = '';
        
        if ($this->ajax_uri !== NULL)
        {
            // Url that should be used to update the widget
            $props .=
                '<div id="' . $this->id . '_url" style="display:none;">'
              .     HTML::chars(URL::site($this->ajax_uri))
              . '</div>';
        }

        // Context uri
        $context_uri = $this->context_uri;
        if ($context_uri === NULL)
        {
            $context_uri = Request::current()->uri;
        }
        if ($context_uri !== FALSE)
        {
            $props .=
                '<div id="' . $this->id . '_context_uri" style="display:none;">'
              .     HTML::chars($context_uri)
              . '</div>';
        }

        $this->props = $props;

        $output = parent::render($file);

        if ($this->wrapper === FALSE)
            return $output; // Wrapping is supposed to be done manually (inluding properties)

        
        $output = $props . $output;

        if ($this->id !== NULL && ! Request::$is_ajax)
        {
            $output = str_replace(
                array('{{id}}', '{{class}}', '{{output}}'),
                array($this->id, $this->class, $output),
                $this->wrapper
            );
        }

        return $output;
    }

    /**
     * Add the output of this widget to the response for specified request
     * 
     * @param Request $request
     */
    public function to_response(Request $request)
    {
        $request->response['widgets'][$this->id] = $this->render();
    }
}