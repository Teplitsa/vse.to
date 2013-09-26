<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_FilterPrice extends Form_Frontend
{
    /**
     * Initialize form fields
     */
    public function init()
    {
        // Render using view
        $this->view_script = 'frontend/forms/filter_price';

        // ----- price_from
        $element = new Form_Element_Float('price_from', array('label' => 'от'));
        $element
            ->add_filter(new Form_Filter_Trim())
            ->add_validator(new Form_Validator_Float(0, NULL, TRUE, NULL, TRUE, TRUE));
        $this->add_component($element);

        // ----- price_to
        $element = new Form_Element_Float('price_to', array('label' => 'до'));
        $element
            ->add_filter(new Form_Filter_Trim())
            ->add_validator(new Form_Validator_Float(0, NULL, TRUE, NULL, TRUE, TRUE));
        $this->add_component($element);

        // ----- Submit button
        /*
        $this
            ->add_component(new Form_Element_Submit('submit',
                array('label' => 'Выбрать')
                array('class' => 'submit')
            ));
         */

    }
}
