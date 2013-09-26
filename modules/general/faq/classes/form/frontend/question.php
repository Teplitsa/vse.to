<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_Question extends Form_Frontend
{
    /**
     * Initialize form fields
     */
    public function init()
    {
        // ----- user_name
        $element = new Form_Element_Input('user_name',
            array('label' => 'Ваше имя', 'required' => TRUE),
            array('maxlength' => 255)
        );
        $element->add_filter(new Form_Filter_TrimCrop(255));
        $element->add_validator(new Form_Validator_NotEmptyString());
        $this->add_component($element);

        // ----- email
        $element = new Form_Element_Input('email', array('label' => 'E-Mail'), array('maxlength' => 63));
        $element->add_filter(new Form_Filter_TrimCrop(63));
        $this->add_component($element);

        // ----- phone
        $element = new Form_Element_Input('phone', array('label' => 'Телефон'), array('maxlength' => 63));
        $element->add_filter(new Form_Filter_TrimCrop(63));
        $this->add_component($element);

        // ----- question
        $element = new Form_Element_Textarea('question', array('label' => 'Вопрос', 'required' => TRUE));
        $element->add_filter(new Form_Filter_TrimCrop(512));
        $element->add_validator(new Form_Validator_NotEmptyString());
        $this->add_component($element);

        // ----- Form buttons
        $fieldset = new Form_Fieldset_Buttons('buttons');
        $this->add_component($fieldset);

            // ----- Submit button
            $fieldset
                ->add_component(new Form_Element_Submit('submit',
                    array('label' => 'Отправить вопрос')
                ));
    }
}
