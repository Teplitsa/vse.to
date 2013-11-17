<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_Register extends Form_Frontend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Render using view
        $this->view_script = 'frontend/forms/register';

        $element =  new Form_Element_Hidden('group_id', array('id' => 'group_id'));
        $element->value = Model_Group::USER_GROUP_ID;
        $this->add_component($element);
        
        // ----- email
        $email = new Form_Element_Input('email', 
                array('label' => 'E-Mail', 'required' => TRUE), 
                array('maxlength' => 31, 'placeholder' => 'E-Mail'));
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

        // ----- Password confirmation
        $element = new Form_Element_Password('password2',
            array('label' => 'Подтверждение', 'required' => TRUE),
            array('maxlength' => 255, 'placeholder' => 'Повтор пароля')
        );
        $element
            ->add_validator(new Form_Validator_NotEmptyString(array(
                    Form_Validator_NotEmptyString::EMPTY_STRING => 'Вы не указали подтверждение пароля!',
            )))
            ->add_validator(new Form_Validator_EqualTo(
                'password',
                array(
                    Form_Validator_EqualTo::NOT_EQUAL => 'Пароль и подтверждение не совпадают!',
                )
            ));
        $this->add_component($element);
        
        // organizer
        $element = new Form_Element_Hidden('organizer_id',array('id' => 'organizer_id'));

        $this->add_component($element);

        $element->value = Model_Organizer::DEFAULT_ORGANIZER_ID;

        $element = new Form_Element_Hidden('organizer_name',array('id' => 'organizer_name'));

        $this->add_component($element);

        $element->value = Model_Organizer::DEFAULT_ORGANIZER_NAME;

        $element = new Form_Element_Hidden('town_id',array('id' => 'town_id'));

        $this->add_component($element);

        $element->value = Model_Town::DEFAULT_TOWN_ID;
        
        // ----- Form buttons
        $button = new Form_Element_Button('submit_register',
                array('label' => 'Регистрация'),
                array('class' => 'button_login button-modal')
        );
        $this->add_component($button);   

        parent::init();
    }
    
    
    /**
     * Add javascripts
     */
    public function render_js()
    {
        parent::render_js();
        
        // ----- Install javascripts
                
        //Layout::instance()->add_script(Modules::uri('jquery') . '/public/js/jquery.saveform.js');
        Layout::instance()->add_script(Modules::uri('acl') . '/public/js/frontend/register.js');
    }        
}