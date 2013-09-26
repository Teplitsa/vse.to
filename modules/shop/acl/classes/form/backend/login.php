<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Login extends Form_Backend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        $this->attribute('class', 'wide w300px lb60px');

        // ----- Login
        $element = new Form_Element_Input('email',
            array('label' => 'E-mail', 'required' => TRUE),
            array('maxlength' => 63)
        );

        $element
            ->add_filter(new Form_Filter_TrimCrop(63))
            ->add_validator(new Form_Validator_NotEmptyString());
        $this->add_component($element);

        // ----- Password
        $element = new Form_Element_Password('password',
            array('label' => 'Пароль', 'required' => TRUE),
            array('maxlength' => 255)
        );
        $element
            ->add_validator(new Form_Validator_NotEmptyString())
            ->add_validator(new Form_Validator_StringLength(0,255));
        $this->add_component($element);

        // ----- Remember
        $this->add_component(new Form_Element_Checkbox('remember', array('label' => 'Запомнить')));

        // ----- Form buttons
        $fieldset = new Form_Fieldset_Buttons('buttons');
        $this->add_component($fieldset);

            // ----- Submit button
            $fieldset
                ->add_component(new Form_Backend_Element_Submit('submit',
                    array('label' => 'Войти'),
                    array('class' => 'button_accept')
                ));

        parent::init();
    }
}