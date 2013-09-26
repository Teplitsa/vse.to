<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Question extends Form_Backend {

    /**
     * Initialize form fields
     */
    public function init()
    {
        $this->layout = 'wide';
        $this->attribute('class', 'lb120px');
        
        // ----- user_name
        $element = new Form_Element_Input('user_name', array('label' => 'Имя пользователя'), array('maxlength' => 255));
        $element->add_filter(new Form_Filter_TrimCrop(255));
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

        // ----- answer
        $element = new Form_Element_Textarea('answer', array('label' => 'Ответ'));
        $element->add_filter(new Form_Filter_TrimCrop(2048));
        $this->add_component($element);

        // ----- created_at
        $element = new Form_Element_DateTimeSimple('created_at', array('label' => 'Создан'));
        $element->add_validator(new Form_Validator_DateTimeSimple());
        $this->add_component($element);

        // ----- active
        $this->add_component(new Form_Element_Checkbox('active', array('label' => 'Активный')));

        // ----- notify
        $this->add_component(new Form_Element_Checkbox('notify', array(
            'label' => 'Оповестить клиента об ответе по e-mail',
            'default_value' => ! $this->model()->answered
        )));

        // ----- Form buttons
        $fieldset = new Form_Fieldset_Buttons('buttons');
        $this->add_component($fieldset);

            // ----- Cancel button
            $fieldset
                ->add_component(new Form_Element_LinkButton('cancel',
                    array('url' => URL::back(), 'label' => 'Отменить'),
                    array('class' => 'button_cancel')
                ));

            // ----- Submit button
            $fieldset
                ->add_component(new Form_Backend_Element_Submit('submit',
                    array('label' => 'Сохранить'),
                    array('class' => 'button_accept')
                ));

        parent::init();
    }
}
