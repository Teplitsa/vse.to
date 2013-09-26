<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_Relogin extends Form_Frontend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Render using view
        $this->view_script = 'frontend/forms/relogin';

        // ----- E-mail
        $element = new Form_Element_Input('email',
            array('label' => 'E-mail', 'required' => TRUE),
            array('maxlength' => 63,'placeholder' => ' Электронная почта')
        );

        $element
            ->add_filter(new Form_Filter_TrimCrop(63))
            ->add_validator(new Form_Validator_NotEmptyString())
            ->add_validator(new Form_Validator_Email());
        $this->add_component($element);

        // ----- Password
        $element = new Form_Element_Password('password',
            array('label' => 'Пароль', 'required' => TRUE),
            array('maxlength' => 255,'placeholder' => 'Пароль')
        );
        $element
            ->add_validator(new Form_Validator_NotEmptyString())
            ->add_validator(new Form_Validator_StringLength(0,255));
        $this->add_component($element);

        // ----- Remember
        $this->add_component(new Form_Element_Checkbox('remember', array('label' => 'Запомнить')));

        // ----- Form buttons
        $button = new Form_Element_Button('submit_login',
                array('label' => 'Вход'),
                array('class' => 'button_login button-modal')
        );
        $this->add_component($button);  

        parent::init();
    }
}