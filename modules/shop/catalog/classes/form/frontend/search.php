<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_Search extends Form_Frontend
{
    /**
     * Initialize form fields
     */
    public function init()
    {
        // Render using view
        $this->view_script = 'frontend/forms/search';

        // ----- search_text
        $element = new Form_Element_Input('search_text', array('label' => 'Поиск'), array('maxlength' => 255,'class' => 'go','placeholder' => 'Поиск'));
        $element
            ->add_filter(new Form_Filter_TrimCrop(255));
        $this->add_component($element);

        // ----- Submit button
        /*
        $this
            ->add_component(new Form_Element_Submit('submit',
                array('label' => 'Выбрать')
                array('class' => 'submit')
            ));
         */

    }
}
