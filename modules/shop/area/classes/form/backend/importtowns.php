<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_ImportTowns extends Form_Backend {

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
        $this->attribute('class', "w500px wide lb120px");

        // ----- File
        $element = new Form_Element_File('file', array('label' => 'Файл .csv'));
        $element->add_validator(new Form_Validator_File());
        $this->add_component($element);

        // ----- Form buttons
        $fieldset = new Form_Fieldset_Buttons('buttons');
        $this->add_component($fieldset);

            // ----- Submit button
            $fieldset
                ->add_component(new Form_Backend_Element_Submit('submit',
                    array('label' => 'Импорт'),
                    array('class' => 'button_accept')
                ));
    }
}
