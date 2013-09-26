<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_CatImport_Pricelist extends Form_Backend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
        $this->attribute('class', "lb150px w500px");
        $this->layout = 'wide';

        // ----- supplier
        $element = new Form_Element_Select('supplier', Model_Product::suppliers(), array('label' => 'Поставщик'));
        $element->add_validator(new Form_Validator_InArray(array_keys(Model_Product::suppliers())));
        $this->add_component($element);

        // ----- price_factor
        $element = new Form_Element_Float('price_factor', array('label' => 'Коэффициент для цен'));
        $element->add_validator(new Form_Validator_Float(0, NULL));
        $element->default_value = 1;
        $this->add_component($element);

        // ----- file
        $element = new Form_Element_File('file', array('label' => 'XLS файл прайслиста'));
        $element->add_validator(new Form_Validator_File());
        $this->add_component($element);

        // ----- task status
        $element = new Form_Element_Text('task_status');
        $element->value = Widget::render_widget('tasks', 'status', 'import_pricelist');
        $this->add_component($element);

        // ----- Form buttons
        $fieldset = new Form_Fieldset_Buttons('buttons');
        $this->add_component($fieldset);

            // ----- Submit button
            $fieldset
                ->add_component(new Form_Backend_Element_Submit('start',
                    array('label' => 'Начать'),
                    array('class' => 'button_accept')
                ));
    }
}
