<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * HTML helper class.
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class HTML extends Kohana_HTML {

	/**
	 * Creates a style sheet link.
     *
     * It's also possible to link style sheet under conditional expression for IE.
     * eg. $ie_condition = 'if lte IE 6'
	 *
	 * @param   string  file name
	 * @param   array   default attributes
	 * @param   boolean  include the index page
     * @param   boolean $ie_condition Wrap in conditional comment for IE
	 * @return  string
	 */
	public static function style($file, array $attributes = NULL, $index = FALSE, $ie_condition = FALSE)
	{
        if ($ie_condition === FALSE)
        {
            return parent::style($file, $attributes, $index);
        }
        else
        {
            return
                "<!--[$ie_condition]>"
              .     parent::style($file, $attributes, $index)
              . '<![endif]-->';
        }
	}

	/**
	 * Creates a script link.
	 *
	 * @param   string   file name
     * @param   boolean  Link external script file or use $file as raw script contents
	 * @param   array    default attributes
	 * @param   boolean  include the index page
	 * @return  string
	 */
	public static function script($file, array $attributes = NULL, $index = FALSE, $raw = FALSE)
	{
		// Set the script type
		$attributes['type'] = 'text/javascript';

        if ( ! $raw)
        {
            if (strpos($file, '://') === FALSE)
            {
                // Add the base URL
                $file = URL::base($index).$file;
            }

            // Set the script link
            $attributes['src'] = $file;

            return '    <script'.HTML::attributes($attributes).'></script>';
        }
        else
        {
            return '
    <script'.HTML::attributes($attributes).'>
        /* <![CDATA[ */
            ' . $file . '
        /* ]]> */
    </script>
';
        }
	}

}
