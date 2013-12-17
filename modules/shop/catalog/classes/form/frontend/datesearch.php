<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_Datesearch extends Form_Frontend
{
    /**
     * Initialize form fields
     */
    public function init()
    {
        
        // Set HTML class
        $this->view_script = 'frontend/forms/datesearch';
        
        // ----- datetime
        $element = new Form_Element_DateTimeSimple('datetime', array('label' => 'Дата проведения', 'required' => TRUE),array('class' => 'w300px','placeholder' => 'dd-mm-yyyy hh:mm'));
        $element->value_format = Model_Product::$date_as_timestamp ? 'timestamp' : 'datetime';
        $element
            ->add_validator(new Form_Validator_DateTimeSimple());
        $this->add_component($element);
        
        $button = new Form_Element_Button('submit_datesearch',
                array('label' => 'Найти'),
                array('class' => 'button')
        );
        $this->add_component($button);

    }
}