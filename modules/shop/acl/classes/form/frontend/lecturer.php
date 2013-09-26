<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_Lecturer extends Form_Frontend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
         $this->view_script = 'frontend/forms/lecturer';
         
        $creating = ((int)$this->model()->id == 0) ? TRUE : FALSE;

         
        // ----- Last_name
        $element = new Form_Element_Input('last_name', array('label' => 'Фамилия', 'required' => TRUE), array('maxlength' => 63,'placeholder' => 'Фамилия'));
        $element
            ->add_filter(new Form_Filter_TrimCrop(63))
            ->add_validator(new Form_Validator_NotEmptyString());

        $this->add_component($element);

        // ----- First_name
        $element = new Form_Element_Input('first_name', array('label' => 'Имя', 'required' => TRUE), array('maxlength' => 63,'placeholder' => 'Имя'));
        $element
            ->add_filter(new Form_Filter_TrimCrop(63))
            ->add_validator(new Form_Validator_NotEmptyString());
        $this->add_component($element);

        // ----- Middle_name
        $element = new Form_Element_Input('middle_name', array('label' => 'Отчество'), array('maxlength' => 63,'placeholder' => 'Отчество'));
        $element
            ->add_filter(new Form_Filter_TrimCrop(63));
        $this->add_component($element);

        // ----- External Links
        $options_count = 1;
        if ($this->model()->id) {
            $options_count = count($this->model()->links);
        }            
        $element = new Form_Element_Options("links", 
            array('label' => 'Внешние ссылки', 'options_count' => $options_count,'options_count_param' => 'options_count','option_caption' => 'добавить ссылку'),
            array('maxlength' => Model_Lecturer::LINKS_LENGTH,'placeholder' => 'Сайт лектора')
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(Model_Lecturer::LINKS_LENGTH));
        $this->add_component($element);
        
        // ----- File
        $element = new Form_Element_File('file', array('label' => 'Загрузить фото'),array('placeholder' => 'Загрузить фото'));
        $element->add_validator(new Form_Validator_File());
        $this->add_component($element);

        // ----- Description
        $this->add_component(new Form_Element_Textarea('info', array('label' => ' Информация о лекторе'),array('placeholder' => 'Информация о лекторе')));
        // ----- Form buttons
        $button = new Form_Element_Button('submit_lecturer',
                array('label' => 'Добавить'),
                array('class' => 'button button-modal')
        );
        $this->add_component($button);        
    }    
}
