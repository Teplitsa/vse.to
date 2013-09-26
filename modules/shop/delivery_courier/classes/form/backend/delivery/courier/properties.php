<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Delivery_Courier_Properties extends Form_Backend_Delivery_Properties
{
    /**
     * Initialize form fields
     */
    public function init()
    {
        // ----- City
        $element = new Form_Element_Input('city', array('label' => 'Город', 'disabled' => TRUE));
        $element->value = 'Москва';
        $this->add_component($element);

        // ----- zone_id
        // Obtain a list of zones
        $zones = Model::fly('Model_CourierZone')->find_all_by_delivery_id($this->delivery()->id, array('order_by' => 'position', 'desc' => FALSE));

        $options = array();
        foreach ($zones as $zone)
        {
            $options[$zone->id] = $zone->name;
        }

        $element =  new Form_Element_Select('zone_id', $options,  array('label' => 'Зона доставки'));
        $element
            ->add_validator(new Form_Validator_InArray(array_keys($options)));
        $this->add_component($element);

        // ----- Address
        $element = new Form_Element_Textarea('address', array('label' => 'Адрес'), array('rows' => 3));
        $element
            ->add_filter(new Form_Filter_TrimCrop(511));
        $this->add_component($element);
    }
}
