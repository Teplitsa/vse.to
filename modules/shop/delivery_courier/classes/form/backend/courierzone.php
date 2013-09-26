<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_CourierZone extends Form_Backend {

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
        $this->attribute('class', "wide w300px lb120px");

        // ----- Name
        $element = new Form_Element_Input('name',
            array('label' => 'Название', 'required' => TRUE),
            array('maxlength' => 63)
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(63))
            ->add_validator(new Form_Validator_NotEmptyString());
        $this->add_component($element);

        // ----- Price
        $element = new Form_Element_Input('price',
            array('label' => 'Стоимость доставки', 'required' => TRUE),
            array('class' => 'integer')
        );
        $element
            ->add_filter(new Form_Filter_Trim())
            ->add_validator(new Form_Validator_Float(0, NULL, FALSE, array(
                Form_Validator_Float::EMPTY_STRING => 'Вы не указали цену!',
                Form_Validator_Float::TOO_SMALL    => 'Вы не указали цену!'
            )));
        $element->append = ' руб.';
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
                    array('label' => 'Сохранить'),
                    array('class' => 'button_accept')
                ));
    }
}