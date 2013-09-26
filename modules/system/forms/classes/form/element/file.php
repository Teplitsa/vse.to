<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form file upload element
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_File extends Form_Element_Input
{
    /**
     * Add the 'multipart/form-data' attribute for form this element belongs to
     * 
     * @param Form $form 
     */
    public function form(Form $form = NULL)
    {
        if ($form !== NULL)
        {
            $form->attribute('enctype', 'multipart/form-data');
        }
        
        return parent::form($form);
    }

    /**
     * @return string
     */
    public function get_type()
    {
        return 'file';
    }

    /**
     * Full name of file element - to use in form HTML.
     *
     * Unlike ordinary element ($form_name[$element_name]) it's impossible to
     * pass file name in array, so we have to construct it in another way.
     * 
     * @return string
     */
    public function get_full_name()
    {
        return $this->form()->name . '_' . $this->name;
    }

    /**
     * Get element value - an array with information about uploaded file (if any)
     * from $_FILES
     * 
     * @return array
     */
    public function get_value()
    {
        if (isset($_FILES[$this->full_name]))
        {
            return $_FILES[$this->full_name];
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Renders file input
     *
     * @return string
     */
    public function render_input()
    {
        return Form_Helper::input($this->full_name, NULL, $this->attributes());
    }

}
