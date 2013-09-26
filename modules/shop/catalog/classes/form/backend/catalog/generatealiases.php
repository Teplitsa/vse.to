<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Catalog_GenerateAliases extends Form_Backend {

    /**
     * Initialize form fields
     */
    public function init()
    {
        $this->attribute('class', 'w300px');
        // Set HTML class
        //$this->layout = 'wide';

        $element = new Form_Element_Text('text');
        $element->value = 
            'Пересоздать алиасы (имена в URL) для товаров и разделов'
          . Widget::render_widget('tasks', 'status', 'generatealiases');
        $this->add_component($element);

        // ----- Form buttons
        $fieldset = new Form_Fieldset_Buttons('buttons');
        $this->add_component($fieldset);

            // ----- Submit button
            $fieldset
                ->add_component(new Form_Backend_Element_Submit('submit',
                    array('label' => 'Пересоздать'),
                    array('class' => 'button_accept')
                ));
    }
}