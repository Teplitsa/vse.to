<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_CreatePayment extends Form_Backend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
        $this->attribute('class', "w500px wide lb120px");

        // ----- Module
        $modules = Model_Payment::modules();

        $options = array();
        foreach ($modules as $module_info)
        {
            $caption = isset($module_info['caption'])? $module_info['caption']: $module_info['module'];
            $options[$module_info['module']] = $caption;
        }

        $element = new Form_Element_Select('module', $options, array('label' => 'Модуль', 'required' => TRUE));
        $element
            ->add_validator(new Form_Validator_InArray(array_keys($options)));
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
                    array('label' => 'Далее'),
                    array('class' => 'button_accept')
                ));
    }
}
