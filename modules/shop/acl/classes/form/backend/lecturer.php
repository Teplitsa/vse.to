<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Lecturer extends Form_Backend
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
        $fieldset = new Form_Fieldset('personal_data', array('label' => 'Личные данные'));
        $cols->add_component($fieldset);

            $cols1 = new Form_Fieldset_Columns('name');
            $fieldset->add_component($cols1);

                // ----- Last_name
                $element = new Form_Element_Input('last_name', array('label' => 'Фамилия', 'required' => TRUE), array('maxlength' => 63));
                $element
                    ->add_filter(new Form_Filter_TrimCrop(63))
                    ->add_validator(new Form_Validator_NotEmptyString());

                $cols1->add_component($element, 1);

                // ----- First_name
                $element = new Form_Element_Input('first_name', array('label' => 'Имя', 'required' => TRUE), array('maxlength' => 63));
                $element
                    ->add_filter(new Form_Filter_TrimCrop(63))
                    ->add_validator(new Form_Validator_NotEmptyString());
                $cols1->add_component($element, 2);

            // ----- Middle_name
            $element = new Form_Element_Input('middle_name', array('label' => 'Отчество'), array('maxlength' => 63));
            $element
                ->add_filter(new Form_Filter_TrimCrop(63));
            $fieldset->add_component($element);

            // ----- External Links
            $options_count = 1;
            if ($this->model()->id) {
                $options_count = count($this->model()->links);
            }            
            $element = new Form_Element_Options("links", 
                array('label' => 'Внешние ссылки', 'options_count' => $options_count,'options_count_param' => 'options_count','option_caption' => 'добавить ссылку'),
                array('maxlength' => Model_Lecturer::LINKS_LENGTH)
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(Model_Lecturer::LINKS_LENGTH));
            $cols->add_component($element);
            
        // ----- Userprops
        if (!$creating) {        
            // ----- User Photo
            $element = new Form_Element_Custom('images', array('label' => '', 'layout' => 'standart'));
            $element->value = Request::current()->get_controller('images')->widget_images('lecturer', $this->model()->id, 'user');
            $cols->add_component($element, 2);
        }
        
        // ----- Description tab
        $tab = new Form_Fieldset_Tab('info_tab', array('label' => 'О себе'));
        $this->add_component($tab);


        // ----- Description
        $tab->add_component(new Form_Element_Wysiwyg('info', array('label' => 'О себе')));
        
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
