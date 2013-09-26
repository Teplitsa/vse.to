<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_CatImport_Structure extends Form_Backend
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

        // ----- file
        $element = new Form_Element_File('file', array('label' => 'XLS файл прайслиста'));
        $element->add_validator(new Form_Validator_File());
        $this->add_component($element);

        // ----- task status
        $element = new Form_Element_Text('task_status');
        $element->value = Widget::render_widget('tasks', 'status', 'import_structure');
        $this->add_component($element);
        
        // ----- sections
        $fieldset = new Form_Fieldset('sections', array('label' => 'Разделы'));
        $this->add_component($fieldset);

            $fieldset->add_component(new Form_Element_Checkbox('create_sections', array('label' => 'Создавать новые разделы')));
            $fieldset->add_component(new Form_Element_Checkbox('update_section_parents', array('label' => 'Перемешать разделы')));
            $fieldset->add_component(new Form_Element_Checkbox('update_section_captions', array('label' => 'Обновлять названия')));           
            $fieldset->add_component(new Form_Element_Checkbox('update_section_descriptions', array('label' => 'Обновлять описания')));
            $fieldset->add_component(new Form_Element_Checkbox('update_section_images', array('label' => 'Обновлять логотипы')));

        // ----- products
        $fieldset = new Form_Fieldset('products', array('label' => 'Товары'));
        $this->add_component($fieldset);

            $fieldset->add_component(new Form_Element_Checkbox('create_products', array('label' => 'Создавать новые товары')));
            $fieldset->add_component(new Form_Element_Checkbox('update_product_markings', array('label' => 'Обновлять артикулы')));
            $fieldset->add_component(new Form_Element_Checkbox('update_product_captions', array('label' => 'Обновлять названия')));
            $fieldset->add_component(new Form_Element_Checkbox('update_product_descriptions', array('label' => 'Обновлять описания')));
            $fieldset->add_component(new Form_Element_Checkbox('update_product_properties', array('label' => 'Обновлять свойства товаров')));
            $fieldset->add_component(new Form_Element_Checkbox('update_product_images', array('label' => 'Обновлять изображения')));


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
