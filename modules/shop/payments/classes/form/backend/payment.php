<?php defined('SYSPATH') or die('No direct script access.');

abstract class Form_Backend_Payment extends Form_Backend
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

        $element = new Form_Element_Select('module', $options, array('label' => 'Модуль', 'required' => TRUE, 'disabled' => TRUE));
        $element
            ->add_validator(new Form_Validator_InArray(array_keys($options)));
        $this->add_component($element);

        // ----- Caption
        $element = new Form_Element_Input('caption',
            array('label' => 'Название', 'required' => TRUE),
            array('maxlength' => 63)
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(63))
            ->add_validator(new Form_Validator_NotEmptyString());
        $this->add_component($element);

        // ----- Description
        $this->add_component(new Form_Element_Textarea('description', array('label' => 'Описание')));

        // ----- Supported delivery types
        $deliveries = Model::fly('Model_Delivery')->find_all(array('order_by' => 'id', 'desc' => FALSE));
        $options = array();
        foreach ($deliveries as $delivery)
        {
            $options[$delivery->id] = $delivery->caption;
        }
        $element = new Form_Element_CheckSelect('delivery_ids', $options, array('label' => 'Возможные способы доставки'));
        $element->config_entry = 'checkselect_fieldset';
        $this->add_component($element);
    }
}
