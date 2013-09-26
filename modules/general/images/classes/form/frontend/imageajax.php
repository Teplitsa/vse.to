<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_ImageAJAX extends Form_Frontend {

    /**
     * Initialize form fields
     */
    public function init()
    {
        
        $this->view_script = 'frontend/forms/image';
        // Set HTML class
        $this->attribute('class', "w500px wide lb120px");

        // ----- File
        $element = new Form_Element_File('file', array('label' => 'Файл с изображением'),array('placeholder' => 'Выберите файл'));
        $element->add_validator(new Form_Validator_File());
        $this->add_component($element);
                
        // ----- Form buttons
        $button = new Form_Element_Button('submit_image',
                array('label' => 'Загрузить'),
                array('class' => 'button button-modal')
        );
        $this->add_component($button);
    }
}
