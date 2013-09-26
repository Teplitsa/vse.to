<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Place extends Form_Backend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class

        // User is being created or updated?
        $creating = ((int)$this->model()->id == 0) ? TRUE : FALSE;
        
        $this->attribute('class', "lb150px");
        $this->layout = 'wide';            

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
                $element = new Form_Element_Input('name', array('label' => 'Название', 'required' => TRUE), array('maxlength' => 63,'class' => 'w300px'));
                $element
                    ->add_filter(new Form_Filter_TrimCrop(63))
                    ->add_validator(new Form_Validator_NotEmptyString());

                $fieldset->add_component($element, 1);
                
            // ----- Town
            $towns = Model_Town::towns();
            $current_town = Model_Town::current();
            $options[$current_town->id] = $current_town->name;
            foreach ($towns as $town_id => $town_name) {
                if ($town_id == $current_town->id) continue;
                $options[$town_id] = $town_name;
            }
            $element = new Form_Element_Select('town_id', $options, array('label' => 'Город', 'required' => TRUE), array('class' => 'w300px'));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $fieldset->add_component($element);                

            // ----- Address
            $fieldset->add_component(new Form_Element_TextArea('address', array('label' => 'Адрес')));            
            // ----- Description
            $fieldset->add_component(new Form_Element_TextArea('description', array('label' => 'Описание')));
        // ----- Technical data
        $fieldset = new Form_Fieldset('technical_data', array('label' => 'Технические характеристики'));
        $cols->add_component($fieldset);
            
            // ----- ISpeed
            $options = Model_Place::$_ispeed_options;

            $element = new Form_Element_Select('ispeed', $options, array('label' => 'Качество сети'), array('class' => 'w300px'));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $fieldset->add_component($element);

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
            
        // ----- Place
        if (!$creating) {        
            // ----- Place Photo
            $element = new Form_Element_Custom('images', array('label' => '', 'layout' => 'standart'));
            $element->value = Request::current()->get_controller('images')->widget_images('place', $this->model()->id, 'user');
            $cols->add_component($element, 2);
        }
        
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
