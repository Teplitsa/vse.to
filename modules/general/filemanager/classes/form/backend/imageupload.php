<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_ImageUpload extends Form {

    /**
     * Initialize form fields
     */
    public function init()
    {
        // HTML class
        $this->set_attribute('class', 'fileupload_form wide');

        // ----- File upload
        $fieldset = new Form_Fieldset('file_upload', array('label' => 'Загрузка файла'));
        $this->add_fieldset($fieldset);

        // ----- File
            $element = new Form_Element_File(
                    'uploaded_file',
                    NULL,
                    array('label' => 'Загрузить файл')
                );
            $element
                ->add_validator(new Validator_Upload())
                ->set_errors_target($this)
                ->set_template('<dt></dt><dd>$(label) $(input) $(errors)')
                ->add_to_fieldset($fieldset);

            // ----- Submit button
            $element = new Form_Admin_Element_Button(
                        'submit',
                        NULL,
                        array('label' => 'Загрузить', 'type' => 'submit'), array('class' => 'ok')
                    );
            $element
                ->set_template('$(input)</dd>')
                ->add_to_fieldset($fieldset);

        // ----- Image resizing
            $image_resize = Kohana::config('filemanager.image_resize');

            // ----- Resize
            $element = new Form_Element_Checkbox(
                        'resize_image',
                        $image_resize['enable'],
                        array('label' => 'Уменьшить изображение')
                    );
            $element
                ->set_template('<dt></dt><dd>$(input)$(label)$(errors)')
                ->add_to_fieldset($fieldset);

            // ----- Width
            $element = new Form_Element_Input("width", $image_resize['width'], array('label' => 'Ширина'), array('maxlength' => 15, 'class'=>'integer'));
            $element
                ->add_filter(new Filter_Trim())
                ->add_validator(new Validator_Integer(0))
                ->set_template(' до размера: $(input) x ')
                ->add_to_fieldset($fieldset);

            // ----- Height
            $element = new Form_Element_Input("height", $image_resize['height'], array('label' => 'Высота'), array('maxlength' => 15, 'class'=>'integer'));
            $element
                ->add_filter(new Filter_Trim())
                ->add_validator(new Validator_Integer(0))
                ->set_template('$(input)</dd>')
                ->add_to_fieldset($fieldset);

            // ----- Resize
            $element = new Form_Element_Checkbox(
                        'enable_popups',
                        Kohana::config('filemanager.thumbs.popups.enable'),
                        array('label' => 'Создать всплывающее изображение')
                    );
            $element
                ->set_template('<dt></dt><dd>$(input)$(label)$(errors)</dd>')
                ->add_to_fieldset($fieldset);
    }
}
