<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Delivery_Courier extends Form_Backend_Delivery
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        parent::init();

        // Set HTML class
        $this->attribute('class', "wide w400px lb120px");
        
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
