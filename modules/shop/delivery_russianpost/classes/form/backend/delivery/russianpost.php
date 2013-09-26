<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Delivery_RussianPost extends Form_Backend_Delivery
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        parent::init();

        // Set HTML class
        $this->attribute('class', "wide w400px lb120px");

        // ----- Settings
        $fieldset = new Form_Fieldset('settings', array('label' => 'Настройки'));
        $this->add_component($fieldset);

            // ----- viewPost (Вид отправления)
            $options = Model_Delivery_RussianPost::post_types();
            $element = new Form_Element_Select('settings[viewPost]', $options, array('label' => 'Вид отправления'));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $fieldset->add_component($element);
        
            // ----- typePost (Способ пересылки)
            $options = Model_Delivery_RussianPost::post_transfers();
            $element = new Form_Element_Select('settings[typePost]', $options, array('label' => 'Способ пересылки'));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $fieldset->add_component($element);

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
