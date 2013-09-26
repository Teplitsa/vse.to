<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_Pasrecovery extends Form_Frontend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Render using view
        $this->view_script = 'frontend/forms/pasrecovery';

        // ----- email
        $email = new Form_Element_Input('email', 
                array('label' => 'E-Mail', 'required' => TRUE), 
                array('maxlength' => 31, 'placeholder' => 'Электронная почта'));
        $email->add_filter(new Form_Filter_TrimCrop(31))
                ->add_validator(new Form_Validator_NotEmptyString())
                ->add_validator(new Form_Validator_Email());
        $this->add_component($email);

        // ----- Form buttons
        $button = new Form_Element_Button('submit_pasrecovery',
                array('label' => 'Отправить'),
                array('class' => 'button_pasrecovery button-modal')
        );
        $this->add_component($button);  

        parent::init();
    }
}