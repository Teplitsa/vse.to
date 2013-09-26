<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_FileUpload extends Form {

    /**
     * Initialize form fields
     */
    public function init()
    {
        // HTML class
        $this->attribute('class', 'w500px wide');

        // ----- File upload
        $fieldset = new Form_Fieldset('file_upload', array('label' => 'Загрузка файла'));
        $this->add_component($fieldset);

        // ----- File
            $element = new Form_Element_File('uploaded_file', array('label' => 'Загрузить файл'));
            $element
                ->add_validator(new Form_Validator_File());
            $element->errors_target($this);
            $element->set_template('<dt></dt><dd>{{label}}&nbsp;&nbsp;{{input}}&nbsp;&nbsp;');
            $fieldset->add_component($element);

            // ----- Submit button
            $element = new Form_Backend_Element_Submit('submit',
                        array('label' => 'Загрузить'),
                        array('class' => 'button_accept')
                    );
            $element
                ->set_template('{{input}}</dd>');
            $fieldset->add_component($element);
    }
}
