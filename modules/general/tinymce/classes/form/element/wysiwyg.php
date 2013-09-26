<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Textarea converted to WYSIWYG editor
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_Wysiwyg extends Form_Element_Textarea
{

    /**
     * Use the same config entry as the simple textarea
     * 
     * @return string
     */
    public function default_config_entry()
    {
        return 'textarea';
    }

    /**
     * Set form element value, replace urls macroses with actual urls
     *
     * @param <type> $value
     * @return Form_Element_Wysiwyg
     */
    public function set_value($value)
    {
        parent::set_value($value);

        $this->_value = str_replace(
            array('{{base_url}}', '{{base_uri}}'),
            array(URL::base(TRUE, TRUE), URL::base()),
            $this->_value
        );

        return $this;
    }

    /**
     * Replace full urls with macroses
     *
     * @return string
     */
    public function get_value()
    {
        $value = parent::get_value();

        $value = str_replace(URL::base(TRUE, TRUE), '{{base_url}}', $value);

        return $value;
    }

    /**
     * Default number of rows
     * 
     * @return integer
     */
    public function defaultattr_rows()
    {
        return 30;
    }

    /**
     * Renders textarea
     *
     * @return string
     */
    public function render_input()
    {
        // Add TinyMCE scripts (scripts are added only once, which is controlled by add_scripts function)
        if (Modules::registered('tinymce'))
        {
            TinyMCE::add_scripts();
        }

        $this->attribute('class', 'wysiwyg_content');

        return parent::render_input();
    }
}
