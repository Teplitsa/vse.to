<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_File extends Form {

    protected $_is_dir;

    protected $_creating;

    public function  __construct($is_dir = FALSE, $creating = FALSE)
    {
        $this->_is_dir   = $is_dir;
        $this->_creating = $creating;

        parent::__construct();
    }

    /**
     * Initialize form fields
     */
    public function init()
    {
        // HTML class
        $this->attribute('class', 'wide w500px lb120px');

        // ----- Base name
        $element = new Form_Element_Input('base_name',
                array('label' => 'Имя ' . ($this->_is_dir ? 'директории' : 'файла'), 'required' => TRUE),
                array('class' => 'input_file_name', 'maxlength' => 63)
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(63))
            ->add_validator(new Form_Validator_NotEmptyString())
            ->add_validator(new Form_Validator_Filename());
        $this->add_component($element);

        // ----- Folder name
        if ( ! $this->_is_dir || ! $this->_creating)
        {
            // Only if not creating a directory
            $element = new Form_Element_Input('relative_dir_name',
                    array('label' => 'Директория'),
                    array('class' => 'input_file_name', 'maxlength' => 255)
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(255));
            $this->add_component($element);
        }

        // ----- Form buttons
        $fieldset = new Form_Fieldset_Buttons('buttons');
        $this->add_component($fieldset);

            // ----- Cancel button
            $fieldset
                ->add_component(new Form_Element_LinkButton('cancel',
                    array('url' => URL::back(), 'label' => 'Отменить'),
                    array('class' => 'button_cancel')
                ));

            // ----- Submit button
            $fieldset
                ->add_component(new Form_Backend_Element_Submit('submit',
                    array('label' => 'Сохранить'),
                    array('class' => 'button_accept')
                ));

        parent::init();
    }
}
