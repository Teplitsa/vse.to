<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_ProfileEdit extends Form_Frontend
{
    /**
     * Initialize form fields
     */ 
	 
    public function init()
    {
        // Render using view
        $this->view_script = 'frontend/users/profedit';
        
        // ----- email
        $email = new Form_Element_Input('email', 
                array('label' => 'E-Mail', 'required' => TRUE), 
                array('maxlength' => 31, 'placeholder' => 'Электронная почта'));
        $email->add_filter(new Form_Filter_TrimCrop(31))
              ->add_validator(new Form_Validator_NotEmptyString())
			  ->add_validator(new Form_Validator_Email());
        $this->add_component($email);
        
        // ----- Password
        $element = new Form_Element_Password('password',
            array('label' => 'Пароль', 'required' => TRUE),
            array('maxlength' => 255, 'placeholder' => 'Пароль')
        );
        $element
            ->add_validator(new Form_Validator_NotEmptyString())
            ->add_validator(new Form_Validator_StringLength(0, 255));
        $this->add_component($element);
      
        // organizer

        $element = new Form_Element_Input('organizer_name', array('label' => 'Организация', 'id' => 'organizer_name'));
        $this->add_component($element);
		
		$element = new Form_Element_Input('first_name', array('label' => 'Имя', 'id' => 'first_name'));
        $this->add_component($element);
		
		$element = new Form_Element_Input('last_name', array('label' => 'Фамилия', 'id' => 'last_name'));
        $this->add_component($element);
		
        $element = new Form_Element_Input('town_id',array('label' => 'Город', 'id' => 'town_id'));
        $this->add_component($element);
		
		$element = new Form_Element_TextArea('info',array('label' => 'О себе', 'id' => 'info'));
        $this->add_component($element);
        
        // ----- Form buttons
        $button = new Form_Element_Button('submit_register',
                array('label' => 'Сохранить')
        );
        $this->add_component($button);   

        parent::init();
    }
}