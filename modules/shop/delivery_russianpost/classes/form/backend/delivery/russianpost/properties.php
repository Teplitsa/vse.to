<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Delivery_RussianPost_Properties extends Form_Backend_Delivery_Properties
{
    /**
     * Initialize form fields
     */
    public function init()
    {
        $cols = new Form_Fieldset_Columns('city_and_postcode', array('column_classes' => array(NULL, 'w40per')));
        $this->add_component($cols);

            // ----- City
            $element = new Form_Element_Input('city', array('label' => 'Город', 'column' => 0), array('maxlength' => 63));
            $element
                ->add_filter(new Form_Filter_TrimCrop(63));
            $element->autocomplete_url = URL::to('backend/countries', array('action' => 'ac_city'));
            $cols->add_component($element);

            // ----- Postcode
            $element = new Form_Element_Input('postcode', array('label' => 'Индекс', 'column' => 1), array('maxlength' => 63));
            $element
                ->add_filter(new Form_Filter_TrimCrop(63));
            $element->autocomplete_url = URL::to('backend/countries', array('action' => 'ac_postcode'));
            $cols->add_component($element);

        // ----- Region_id
        // Obtain a list of regions
        $regions = Model::fly('Model_Region')->find_all(array('order_by' => 'name', 'desc' => FALSE));

        $options = array();
        foreach ($regions as $region)
        {
            $options[$region->id] = UTF8::ucfirst($region->name);
        }

        $element =  new Form_Element_Select('region_id', $options,  array('label' => 'Регион'));
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
