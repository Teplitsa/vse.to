<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_PList extends Form_Backend {

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
        $this->attribute('class', "lb150px w500px");
        $this->layout = 'wide';

        // ----- Name
        $element = new Form_Element_Input('name',
            array('label' => 'Имя', 'required' => TRUE),
            array('maxlength' => 15)
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(15))
            ->add_validator(new Form_Validator_NotEmptyString())
            ->add_validator(new Form_Validator_Alnum());
        $this->add_component($element);

        // ----- Caption
        $element = new Form_Element_Input('caption',
            array('label' => 'Название', 'required' => TRUE),
            array('maxlength' => 255)
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(255))
            ->add_validator(new Form_Validator_NotEmptyString());
        $this->add_component($element);

        // ----- Form buttons
        $fieldset = new Form_Fieldset_Buttons('buttons');
        $this->add_component($fieldset);

            // ----- Cancel button
            $fieldset
                ->add_component(new Form_Element_LinkButton('cancel',
                    array('url' => URL::back(), 'label' => 'Назад'),
                    array('class' => 'button_cancel')
                ));

            // ----- Submit button
            $fieldset
                ->add_component(new Form_Backend_Element_Submit('submit',
                    array('label' => ($this->model()->id === NULL) ? 'Далее' : 'Сохранить'),
                    array('class' => 'button_accept')
                ));
    }
}
