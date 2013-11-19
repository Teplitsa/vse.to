<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Organizer extends Form_Backend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class

        // User is being created or updated?
        $creating = ((int)$this->model()->id == 0) ? TRUE : FALSE;
        
        if ($creating) {
            $this->attribute('class', 'w400px');        
        } else {
            $this->attribute('class', "lb150px");
            $this->layout = 'wide';            
        }
        // ----- General tab
        $tab = new Form_Fieldset_Tab('general_tab', array('label' => 'Основные свойства'));
        $this->add_component($tab);

        // 2-column layout
        $cols = new Form_Fieldset_Columns('cols', array('column_classes' => array(1 => 'w55per')));
        $tab->add_component($cols);

        // ----- Personal data
        $fieldset = new Form_Fieldset('personal_data', array('label' => 'Общие данные'));
        $cols->add_component($fieldset);
        
            // ----- Name
            $element = new Form_Element_Input('name', array('label' => 'Название', 'required' => TRUE), array('maxlength' => 63));
            $element
                ->add_filter(new Form_Filter_TrimCrop(63))
                ->add_validator(new Form_Validator_NotEmptyString());

            $fieldset->add_component($element, 1);
                
                
            // ----- Email
            $element = new Form_Element_Input('email',
                array('label' => 'E-mail', 'required' => FALSE),
                array('maxlength' => 63)
            );
            $element
                    ->add_filter(new Form_Filter_TrimCrop(63))
                    ->add_validator(new Form_Validator_Email(NULL,TRUE,TRUE));
                    ;
            $fieldset->add_component($element);                
                
            // ----- Phone
            $element = new Form_Element_Input('phone',
                array('label' => 'Телефон', 'required' => FALSE),
                array('maxlength' => 63)
            );
            $element->add_filter(new Form_Filter_TrimCrop(63));
            $fieldset->add_component($element);                     
                
                
                
            // ----- Town
            $towns = Model_Town::towns();

            $element = new Form_Element_Select('town_id', $towns, array('label' => 'Город', 'required' => TRUE));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($towns)));
            $fieldset->add_component($element);                

            // ----- Type
            $options = $this->model()->get_types();

            $element = new Form_Element_Select('type', $options, array('label' => 'Тип'));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $fieldset->add_component($element);

            // ----- Address
            $fieldset->add_component(new Form_Element_TextArea('address', array('label' => 'Адрес'))); 
            
            // ----- External Links
            $options_count = 1;
            if ($this->model()->id) {
                $options_count = count($this->model()->links);
            }            
            $element = new Form_Element_Options("links", 
                array('label' => 'Ссылки', 'options_count' => $options_count,'options_count_param' => 'options_count','option_caption' => 'добавить ссылку'),
                array('maxlength' => Model_Organizer::LINKS_LENGTH)
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(Model_Organizer::LINKS_LENGTH));
            $cols->add_component($element);
            
        // ----- Organizer
        if (!$creating) {        
            // ----- Organizer Photo
            $element = new Form_Element_Custom('images', array('label' => '', 'layout' => 'standart'));
            $element->value = Request::current()->get_controller('images')->widget_images('organizer', $this->model()->id, 'user');
            $cols->add_component($element, 2);
        }
        
        // ----- Description tab
        $tab = new Form_Fieldset_Tab('info_tab', array('label' => 'Об организации'));
        $this->add_component($tab);


        // ----- Description
        $tab->add_component(new Form_Element_Wysiwyg('info', array('label' => 'Об организации')));
        
        // ----- Form buttons
        $fieldset = new Form_Fieldset_Buttons('buttons');
        $this->add_component($fieldset);

            // ----- Cancel button
            $fieldset
                ->add_component(new Form_Element_LinkButton('cancel',
                    array('url' => URL::back(), 'label' => 'Назад'),
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
