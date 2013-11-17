<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_Organizer extends Form_Frontend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
            // Set HTML class
             $this->view_script = 'frontend/forms/organizer';

            $creating = ((int)$this->model()->id == 0) ? TRUE : FALSE;
             
            // ----- Name
            $element = new Form_Element_Input('name', array('label' => 'Название', 'required' => TRUE), array('maxlength' => 63, 'placeholder' =>'Название' ));
            $element
                ->add_filter(new Form_Filter_TrimCrop(63))
                ->add_validator(new Form_Validator_NotEmptyString());

            $this->add_component($element);

            // ----- Town
            $towns = Model_Town::towns();

            $element = new Form_Element_Select('town_id', $towns, array('label' => 'Город', 'required' => TRUE));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($towns)));
            $this->add_component($element);                

            // ----- Type
            $options = $this->model()->get_types();

            $element = new Form_Element_Select('type', $options, array('label' => 'Тип'));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $this->add_component($element);

            // ----- Address
            $this->add_component(new Form_Element_TextArea('address', array('label' => 'Адрес'))); 
            
            // ----- External Links
            $options_count = 1;
            if ($this->model()->id) {
                $options_count = count($this->model()->links);
            }            
            $element = new Form_Element_Options("links", 
                array('label' => 'Ссылки', 'options_count' => $options_count,'options_count_param' => 'options_count','option_caption' => 'добавить ссылку'),
                array('maxlength' => Model_Organizer::LINKS_LENGTH,'placeholder' => 'Сайт организации')
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(Model_Organizer::LINKS_LENGTH));
            $this->add_component($element);

            // ----- File
            $element = new Form_Element_File('file', array('label' => 'Загрузить фото'),array('placeholder' => 'Загрузить фото'));
            $element->add_validator(new Form_Validator_File(NULL,TRUE,TRUE));
            $this->add_component($element);
            
            // ----- Description
            $this->add_component(new Form_Element_Textarea('info', array('label' => ' Информация об организации'),array('placeholder' => 'Информация об организации')));
            // ----- Form buttons
            $button = new Form_Element_Button('submit_organizer',
                    array('label' => 'Добавить'),
                    array('class' => 'button button-modal')
            );
            $this->add_component($button);        
    }    
        
}
