<?php defined('SYSPATH') or die('No direct script access.');

/**
 * View
 * Extends Kohana_View with filters & placeholders
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class View extends Kohana_View
{
    /**
     * View filters
     * @var array
     */
    protected $_filters = array();

    /**
     * @var array
     */
    protected $_placeholders = array();

    /**
     * Register view filter
     *
     * @param View_Filter|string $filter Filter class name or filter instance
     */
    public function add_filter($filter)
    {
        $this->_filters[] = $filter;
    }

    /**
     * Register several view filters at once
     *
     * @param array $filters
     */
    public function add_filters(array $filters)
    {
        $this->_filters += $filters;
    }

    /**
     * Render placeholder (actually renders special string, that will be replaced with placeholder content after view rendering)
     *
     * @param string $name
     * @return string
     */
    public function placeholder($name)
    {
        if ( ! isset($this->_placeholders[$name]))
        {
            $this->_placeholders[$name] = '';
        }

        return '{{placeholder-' . $name . '}}';
    }

    /**
     * Replace placeholders in view output
     *
     * @param string $output
     */
    public function replace_placeholders(& $output)
    {
        foreach (array_keys($this->_placeholders) as $name)
        {
            $output = str_replace('{{placeholder-'.$name.'}}', $this->render_placeholder($name), $output);
        }
    }

    /**
     * Render placeholder
     *
     * @param  string $name
     * @return string
     */
    public function render_placeholder($name)
    {
        $content = $this->get_placeholder($name);

        if ($content === NULL)
        {
            $content = '';
        }

        return $content;
    }

    /**
     * Set placeholder content
     *
     * @param string $name
     * @param string $content
     */
    public function set_placeholder($name, $content)
    {
        $this->_placeholders[$name] = $content;
    }

    /**
     * Get placeholder content
     *
     * @param  string $name
     * @return string
     */
    public function get_placeholder($name)
    {
        if (isset($this->_placeholders[$name]))
        {
            return $this->_placeholders[$name];
        }
        else
        {
            return NULL;
        }
    }

	/**
	 * Renders the view object to a string. Global and local data are merged
	 * and extracted to create local variables within the view file.
	 *
	 * Note: Global variables with the same key name as local variables will be
	 * overwritten by the local variable.
	 *
	 * @throws   View_Exception
	 * @param    view filename
	 * @return   string
	 */
	public function render($file = NULL)
	{
		// Run before_render() for registered filters in reverse order
        if ( ! empty($this->_filters))
        {
            end($this->_filters);
            do {
                $filter = current($this->_filters);

                if (is_string($filter))
                {
                    // filter is a name of the class - create filter instance
                    $filter = new $filter;

                    $k = key($this->_filters);
                    $this->_filters[$k] = $filter;
                }

                $filter->before_render();

            } while (prev($this->_filters));
        }

        // Render view
        $this->set('view', $this);
        $output = parent::render($file);

        // Replace placeholders
        $this->replace_placeholders($output);

        // Render widgets
        $output = $this->_render_widgets($output);

        // Replace basic macroses
        $output = str_replace(
            array('{{base_url}}', '{{base_uri}}'),
            array(URL::base(TRUE, TRUE), URL::base()),
            $output
        );

		// Run after_render() for registered filters
        foreach ($this->_filters as $filter)
        {
            $filter->after_render($output);
        }

        return $output;
	}

    /**
     * Render widgets in content.
     * Macros format for widget is:
     *  $(widget:controller:action:param1:param2:param2)
     *
     * @param  string $output
     * @return string
     */
    protected function _render_widgets($output)
    {
        if (strpos($output, '$(widget:') !== FALSE)
        {
            // Search for widget macroses
            $output = preg_replace_callback('/\$\(widget:(\w+):(\w+)(:([^\)]+))?\)/i', array($this, '_render_widget'), $output);
        }

        return $output;
    }

    /**
     * Callback for widget rendering
     *
     * @param  array $matches
     * @return string
     */
    protected function _render_widget($matches)
    {
        $controller = $matches[1];
        $widget = $matches[2];

        if ( ! empty($matches[4]))
        {
            $args = explode(':', $matches[4]);
        }
        else
        {
            $args = NULL;
        }
        return Widget::render_widget_with_args($controller, $widget, $args);
    }
}