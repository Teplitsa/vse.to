<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Layout
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Layout extends View {

    /**
     * Layout instance
     * @var Layout
     */
    protected static $_instance;

	/**
	 * Returns a new Layout object.
	 *
	 * @param   string  view filename
	 * @param   array   array of values
	 * @return  View
	 */
	public static function factory($file = NULL, array $data = NULL)
	{
		return new Layout($file, $data);
	}

    /**
     * Get layout instance
     *
     * @return Layout
     */
    public static function instance()
    {
        if (self::$_instance === NULL)
        {
            self::$_instance = new Layout();
        }

        return self::$_instance;
    }

    /**
     * @var array
     */
    protected $_title = array();

    /**
     * @var array
     */
    protected $_description = array();

    /**
     * @var array
     */
    protected $_keywords = array();

    /**
     * Stylesheets for this layout
     *
     * @var array
     */
    protected $_styles = array();

    /**
     * Script files for layout
     *
     * @var array
     */
    protected $_scripts = array();

    /**
     * Add string to site title
     *
     * @param boolean $reverse
     * @param string $title
     */
    public function add_title($title, $reverse = FALSE)
    {
        $title = trim($title);
        if ($title !== '')
        {
            if ($reverse)
            {
                array_unshift($this->_title, $title);
            }
            else
            {
                $this->_title[] = $title;
            }
        }
    }

    /**
     * Add string to site description
     *
     * @param boolean $reverse
     * @param string $description
     */
    public function add_description($description, $reverse = FALSE)
    {
        $description = trim($description);
        if ($description !== '')
        {
            if ($reverse)
            {
                array_unshift($this->_description, $description);
            }
            else
            {
                $this->_description[] = $description;
            }
        }
    }

    /**
     * Add string to site keywords
     *
     * @param boolean $reverse
     * @param string $keywords
     */
    public function add_keywords($keywords, $reverse = FALSE)
    {
        $keywords = trim($keywords);
        if ($keywords !== '')
        {
            if ($reverse)
            {
                array_unshift($this->_keywords, $keywords);
            }
            else
            {
                $this->_keywords[] = $keywords;
            }
        }
    }

    /**
     * Add stylesheet to layout
     *
     * @param  string $file Url to stylesheet
     * @return Layout
     */
    public function add_style($file, $ie_condition = FALSE)
    {
        $this->_styles[$file] = array(
            'file'          => $file,
            'ie_condition'  => $ie_condition
        );
        return $this;
    }

    /**
     * Add script to layout
     *
     * $raw determines whether to link $script points to script file or is a raw script text
     *
     * @param  string $script Url to script file | raw script text
     * @param  boolean $raw If true - $file is link scripts file, if f
     * @return Layout
     */
    public function add_script($script, $raw = FALSE)
    {
        $this->_scripts[$script] = array(
            'script'    => $script,
            'raw'       => $raw
        );
        return $this;
    }

    /**
     * Replace title, description, keywords, styles and scripts
     *
     * @param  string $name
     * @return string
     */
    public function render_placeholder($name)
    {
        switch ($name)
        {
            case 'title':
                $this->add_title(Model_Site::current()->settings['meta_title']);
                return implode(' - ', $this->_title);

            case 'description':
                $this->add_description(Model_Site::current()->settings['meta_description']);
                return implode(' ', $this->_description);

            case 'keywords':
                $this->add_keywords(Model_Site::current()->settings['meta_keywords']);
                return implode(' ', $this->_keywords);

            case 'styles':
                $styles_html = '';

                foreach ($this->_styles as $style)
                {
                    $styles_html .= HTML::style($style['file'], NULL, FALSE, $style['ie_condition']) . "\n";
                }

                return $styles_html;

            case 'scripts':
                $scripts_html = '';

                foreach ($this->_scripts as $script)
                {
                    $scripts_html .= HTML::script($script['script'], NULL, FALSE, $script['raw']) . "\n";
                }

                return $scripts_html;

            default:
                return parent::render_placeholder($name);
        }
    }
}