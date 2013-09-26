<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_Cart extends Form
{

    /**
     * Initialize form fields based on products from cart
     * 
     * @param array $quantities
     */
    public function init_fields(array $quantities)
    {
        foreach ($quantities as $product_id => $quantity)
        {
            $element = new Form_Element_Input('quantities[' . $product_id . ']');
            $element->default_value = $quantity;
            $element->add_validator(new Form_Validator_Integer(0, Model_Cart::MAX_QUANTITY));
            $this->add_component($element);
        }

        $element = new Form_Element_Submit('recalculate');
        $element->value = 'Пересчитать';
        $this->add_component($element);
    }
}
