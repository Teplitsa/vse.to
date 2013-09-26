<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form helper class. Unless otherwise noted, all generated HTML will be made
 * safe using the [HTML::chars] method. This prevents against simple XSS
 * attacks that could otherwise be trigged by inserting HTML characters into
 * form fields.
 *
 * @package    Eresus
 * @category   Helpers
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Form_Helper {

	/**
	 * Generates an opening HTML form tag.
	 *
	 *     // Form will submit back to the current page using POST
	 *     echo Form_Helper::open();
	 *
	 *     // Form will submit to 'search' using GET
	 *     echo Form_Helper::open('search', array('method' => 'get'));
	 *
	 *     // When "file" inputs are present, you must include the "enctype"
	 *     echo Form_Helper::open(NULL, array('enctype' => 'multipart/form-data'));
	 *
	 * @param   string  form action, defaults to the current request URI
	 * @param   array   html attributes
	 * @return  string
	 * @uses    Request::instance
	 * @uses    URL::site
	 * @uses    HTML::attributes
	 */
	public static function open($action = NULL, array $attributes = NULL)
	{
		if ($action === NULL)
		{
			// Use the current URI
			$action = Request::current()->uri;
		}

		if ($action === '')
		{
			// Use only the base URI
			$action = Kohana::$base_url;
		}
		elseif (strpos($action, '://') === FALSE)
		{
			// Make the URI absolute
			$action = URL::site($action);
		}

		// Add the form action to the attributes
		$attributes['action'] = $action;

		// Only accept the default character set
		$attributes['accept-charset'] = Kohana::$charset;

		if ( ! isset($attributes['method']))
		{
			// Use POST method
			$attributes['method'] = 'post';
		}

		return '<form'.HTML::attributes($attributes).'>';
	}

	/**
	 * Creates the closing form tag.
	 *
	 *     echo Form_Helper::close();
	 *
	 * @return  string
	 */
	public static function close()
	{
		return '</form>';
	}

	/**
	 * Creates a form input. If no type is specified, a "text" type input will
	 * be returned.
	 *
	 *     echo Form_Helper::input('username', $username);
	 *
	 * @param   string  input name
	 * @param   string  input value
	 * @param   array   html attributes
	 * @return  string
	 * @uses    HTML::attributes
	 */
	public static function input($name, $value = NULL, array $attributes = NULL)
	{
		// Set the input name
		$attributes['name'] = $name;

		// Set the input value
		$attributes['value'] = $value;

		if ( ! isset($attributes['type']))
		{
			// Default type is text
			$attributes['type'] = 'text';
		}
                
                return '<input'.HTML::attributes($attributes).' />';

	}
        
	public static function autoload($id)
	{
            return '<div class="autocomplete_wrapper"><div class="autocomplete_popup" id="'.$id.'-autocomplete"></div></div>';
	}        

	/**
	 * Creates a hidden form input.
	 *
	 *     echo Form_Helper::hidden('csrf', $token);
	 *
	 * @param   string  input name
	 * @param   string  input value
	 * @param   array   html attributes
	 * @return  string
	 * @uses    Form_Helper::input
	 */
	public static function hidden($name, $value = NULL, array $attributes = NULL)
	{
		$attributes['type'] = 'hidden';

		return Form_Helper::input($name, $value, $attributes);
	}

	/**
	 * Creates a password form input.
	 *
	 *     echo Form_Helper::password('password');
	 *
	 * @param   string  input name
	 * @param   string  input value
	 * @param   array   html attributes
	 * @return  string
	 * @uses    Form_Helper::input
	 */
	public static function password($name, $value = NULL, array $attributes = NULL)
	{
		$attributes['type'] = 'password';

		return Form_Helper::input($name, $value, $attributes);
	}

	/**
	 * Creates a file upload form input. No input value can be specified.
	 *
	 *     echo Form_Helper::file('image');
	 *
	 * @param   string  input name
	 * @param   array   html attributes
	 * @return  string
	 * @uses    Form_Helper::input
	 */
	public static function file($name, array $attributes = NULL)
	{
		$attributes['type'] = 'file';

		return Form_Helper::input($name, NULL, $attributes);
	}

	/**
	 * Creates a checkbox form input.
	 *
	 *     echo Form_Helper::checkbox('remember_me', 1, (bool) $remember);
	 *
	 * @param   string   input name
	 * @param   string   input value
	 * @param   boolean  checked status
	 * @param   array    html attributes
	 * @return  string
	 * @uses    Form_Helper::input
	 */
	public static function checkbox($name, $value = NULL, $checked = FALSE, array $attributes = NULL)
	{
		$attributes['type'] = 'checkbox';

		if ($checked === TRUE)
		{
			// Make the checkbox active
			$attributes['checked'] = 'checked';
		}

		return Form_Helper::input($name, $value, $attributes);
	}

	/**
	 * Creates a radio form input.
	 *
	 *     echo Form_Helper::radio('like_cats', 1, $cats);
	 *     echo Form_Helper::radio('like_cats', 0, ! $cats);
	 *
	 * @param   string   input name
	 * @param   string   input value
	 * @param   boolean  checked status
	 * @param   array    html attributes
	 * @return  string
	 * @uses    Form_Helper::input
	 */
	public static function radio($name, $value = NULL, $checked = FALSE, array $attributes = NULL)
	{
		$attributes['type'] = 'radio';

		if ($checked === TRUE)
		{
			// Make the radio active
			$attributes['checked'] = 'checked';
		}

		return Form_Helper::input($name, $value, $attributes);
	}

	/**
	 * Creates a textarea form input.
	 *
	 *     echo Form_Helper::textarea('about', $about);
	 *
	 * @param   string   textarea name
	 * @param   string   textarea body
	 * @param   array    html attributes
	 * @param   boolean  encode existing HTML characters
	 * @return  string
	 * @uses    HTML::attributes
	 * @uses    HTML::chars
	 */
	public static function textarea($name, $body = '', array $attributes = NULL, $double_encode = TRUE)
	{
		// Set the input name
		$attributes['name'] = $name;

		// Add default rows and cols attributes (required)
		$attributes += array('rows' => 10, 'cols' => 50);

		return '<textarea'.HTML::attributes($attributes).'>'.HTML::chars($body, $double_encode).'</textarea>';
	}

	/**
	 * Creates a select form input.
	 *
	 *     echo Form_Helper::select('country', $countries, $country);
	 *
	 * @param   string   input name
	 * @param   array    available options
	 * @param   string   selected option
	 * @param   array    html attributes
	 * @return  string
	 * @uses    HTML::attributes
	 */
	public static function select($name, array $options = NULL, $selected = NULL, array $attributes = NULL)
	{
		// Set the input name
		$attributes['name'] = $name;

		if (empty($options))
		{
			// There are no options
			$options = '';
		}
		else
		{
			if ($selected !== NULL)
			{
				// Cast to string only if something needs to be selected
				$selected = (string) $selected;
			}

			foreach ($options as $value => $name)
			{
				if (is_array($name))
				{
					// Create a new optgroup
					$group = array('label' => $value);

					// Create a new list of options
					$_options = array();

					foreach ($name as $_value => $_name)
					{
						// Force value to be string
						$_value = (string) $_value;

						// Create a new attribute set for this option
						$option = array('value' => $_value);

						if ($_value === $selected)
						{
							// This option is selected
							$option['selected'] = 'selected';
						}

						// Change the option to the HTML string
						$_options[] = '<option'.HTML::attributes($option).'>'.HTML::chars($_name, FALSE).'</option>';
					}

					// Compile the options into a string
					$_options = "\n".implode("\n", $_options)."\n";

					$options[$value] = '<optgroup'.HTML::attributes($group).'>'.$_options.'</optgroup>';
				}
				else
				{
					// Force value to be string
					$value = (string) $value;

					// Create a new attribute set for this option
					$option = array('value' => $value);

					if ($value === $selected)
					{
						// This option is selected
						$option['selected'] = 'selected';
					}

					// Change the option to the HTML string
					$options[$value] = '<option'.HTML::attributes($option).'>'.HTML::chars($name, FALSE).'</option>';
				}
			}

			// Compile the options into a single string
			$options = "\n".implode("\n", $options)."\n";
		}
		return '<select'.HTML::attributes($attributes).'>'.$options.'</select>';
	}

	/**
	 * Creates a submit form input.
	 *
	 *     echo Form_Helper::submit(NULL, 'Login');
	 *
	 * @param   string  input name
	 * @param   string  input value
	 * @param   array   html attributes
	 * @return  string
	 * @uses    Form_Helper::input
	 */
	public static function submit($name, $value, array $attributes = NULL)
	{
		$attributes['type'] = 'submit';

		return Form_Helper::input($name, $value, $attributes);
	}

	/**
	 * Creates a image form input.
	 *
	 *     echo Form_Helper::image(NULL, HTML::image('media/img/login.png'));
	 *
	 * @param   string  input name
	 * @param   string  input value
	 * @param   array   html attributes
	 * @return  string
	 * @uses    Form_Helper::input
	 */
	public static function image($name, $value, array $attributes = NULL)
	{
		$attributes['type'] = 'image';

		return Form_Helper::input($name, $value, $attributes);
	}

	/**
	 * Creates a button form input. Note that the body of a button is NOT escaped,
	 * to allow images and other HTML to be used.
	 *
	 *     echo Form_Helper::button('save', 'Save Profile', array('type' => 'submit'));
	 *
	 * @param   string  input name
	 * @param   string  input value
	 * @param   array   html attributes
	 * @return  string
	 * @uses    HTML::attributes
	 */
	public static function button($name, $body, array $attributes = NULL)
	{
		// Set the input name
		$attributes['name'] = $name;

		return '<button'.HTML::attributes($attributes).'>'.$body.'</button>';
	}

	/**
	 * Creates a form label. Label text is not automatically translated.
	 *
	 *     echo Form_Helper::label('username', 'Username');
	 *
	 * @param   string  target input
	 * @param   string  label text
	 * @param   array   html attributes
	 * @return  string
	 * @uses    HTML::attributes
	 */
	public static function label($input, $text = NULL, array $attributes = NULL)
	{
        $required = ! empty($attributes['required']);
        unset($attributes['required']);

		if ($text === NULL)
		{
			// Use the input name as the text
			$text = ucwords(preg_replace('/\W+/', ' ', $input));
		}

		// Set the label target
        if ($input !== NULL)
        {
            $attributes['for'] = $input;
        }

		$html = '<label'.HTML::attributes($attributes).'>'.$text.'</label>';

        if ($required)
        {
            $html .= '<span class="required">*</span>';
        }

        return $html;
	}

    /**
     * Renders form element errors
     *
     * @param array $errors
     * @return string
     */
    public static function errors(array $errors = NULL)
    {
        if (empty($errors))
        {
            // No errors
            return '';
        }

        $html = '';

        foreach ($errors as $error)
        {
            $html .= '<div>' . HTML::chars($error['text']) . '</div>';
        }

        return $html;
    }

	final private function __construct()
	{
		// This is a static class
	}

}