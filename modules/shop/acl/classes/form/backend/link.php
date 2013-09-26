<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Link extends Form_Backend {

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
        $this->layout = 'wide';

        // Render using view
        //$this->view_script = 'backend/forms/property';

        $cols = new Form_Fieldset_Columns('link');
        $this->add_component($cols);

            // ----- caption
            $element = new Form_Element_Input('caption',
                array('label' => 'Название', 'required' => TRUE),
                array('maxlength' => 31)
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(31))
                ->add_validator(new Form_Validator_NotEmptyString());
            $cols->add_component($element);

            // ----- name
            $element = new Form_Element_Input('name',
                array('label' => 'Имя', 'required' => TRUE),
                array('maxlength' => 31)
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(31))
                ->add_validator(new Form_Validator_NotEmptyString())
                ->add_validator(new Form_Validator_Alnum());
            $element->comment = 'Имя для внешней ссылки, состоящее из цифр и букв латинсокого алфавита для использования в шаблонах. (например: page_numbers, news_creation, ...)';
            $cols->add_component($element);
        
        // ----- Form buttons
        $fieldset = new Form_Fieldset_Buttons('buttons');
        $cols->add_component($fieldset);

            // ----- Cancel button
            $fieldset
                ->add_component(new Form_Element_LinkButton('cancel',
                    array('url' => URL::back(), 'label' => 'Назад'),
                    array('class' => 'button_cancel')
                ));

            // ----- Submit button
            $fieldset
                ->add_component(new Form_Backend_Element_Submit('submit',
                    array('label' => 'Сохранить'),
                    array('class' => 'button_accept')
                ));
    }
}