<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_OrderStatus extends Form_Backend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
        $this->attribute('class', "w500px wide lb120px");

        // ----- Caption
        $element = new Form_Element_Input('caption',
            array('label' => 'Статус', 'required' => TRUE),
            array('maxlength' => 63)
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(63))
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
                    array('label' => 'Сохранить'),
                    array('class' => 'button_accept')
                ));
    }
}
