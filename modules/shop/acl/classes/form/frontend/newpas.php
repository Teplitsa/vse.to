<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_Newpas extends Form_Frontend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Render using view
        $this->view_script = 'frontend/forms/newpas';

        
        // ----- Password
        $element = new Form_Element_Password('password',
            array('label' => 'Пароль', 'required' => TRUE),
            array('maxlength' => 255, 'placeholder' => 'Пароль')
        );
        $element
            ->add_validator(new Form_Validator_NotEmptyString())
            ->add_validator(new Form_Validator_StringLength(0, 255));
        $this->add_component($element);
        
        // ----- Form buttons
        $button = new Form_Element_Button('submit_newpas',
                array('label' => 'Заменить'),
                array('class' => 'button_newpas button-modal')
        );
        $this->add_component($button);  

        parent::init();
    }
}