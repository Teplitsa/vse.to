<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_Place extends Form_Frontend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
            // Set HTML class
             $this->view_script = 'frontend/forms/place';
             
            // ----- Name
            $element = new Form_Element_Input('name', array('label' => 'Название', 'required' => TRUE), array('maxlength' => 63, 'placeholder' =>'Название' ));
            $element
                ->add_filter(new Form_Filter_TrimCrop(63))
                ->add_validator(new Form_Validator_NotEmptyString());
            $this->add_component($element);

            $element = new Form_Element_Hidden('town_id');
            $this->add_component($element);
            
            $element->value = Model_User::current()->town->id;
            
            // ----- Address
            $element = new Form_Element_TextArea('address', array('label' => 'Адрес'));            
            $element
                ->add_validator(new Form_Validator_NotEmptyString());

            $this->add_component($element);
            
            // ----- Description
            $element = new Form_Element_TextArea('description', array('label' => 'Описание'));            
            $element
                ->add_validator(new Form_Validator_NotEmptyString());

            $this->add_component($element);

            // ----- ISpeed
            $options = Model_Place::$_ispeed_options;

            $element = new Form_Element_Select('ispeed', $options, array('label' => 'Качество сети'));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $this->add_component($element);

            
            // ----- External Links
            $options_count = 1;
            if ($this->model()->id) {
                $options_count = count($this->model()->links);
            }            
            $element = new Form_Element_Options("links", 
                array('label' => 'Ссылки', 'options_count' => $options_count,'options_count_param' => 'options_count','option_caption' => 'добавить ссылку'),
                array('maxlength' => Model_Lecturer::LINKS_LENGTH,'placeholder' => 'Сайт площадки')
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(Model_Place::LINKS_LENGTH));
            $this->add_component($element);
            
            // ----- File
            $element = new Form_Element_File('file', array('label' => 'Загрузить фото'),array('placeholder' => 'Загрузить фото'));
            $element->add_validator(new Form_Validator_File());
            $this->add_component($element);
            
            // ----- Form buttons
            $button = new Form_Element_Button('submit_place',
                    array('label' => 'Добавить'),
                    array('class' => 'button button-modal')
            );
            $this->add_component($button);                
    }    
        
}
