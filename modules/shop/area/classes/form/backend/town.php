<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Town extends Form_Backend
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
                $element = new Form_Element_Input('name', array('label' => 'Название', 'required' => TRUE), array('maxlength' => 63));
                $element
                    ->add_filter(new Form_Filter_TrimCrop(63))
                    ->add_validator(new Form_Validator_NotEmptyString());

                $fieldset->add_component($element, 1);
                
                // ----- Phonecode
                $element = new Form_Element_Input('phonecode', array('label' => 'Телефонный код', 'required' => TRUE), array('maxlength' => 63));
                $element
                    ->add_filter(new Form_Filter_TrimCrop(63))
                    ->add_validator(new Form_Validator_NotEmptyString());
                $fieldset->add_component($element, 1);
                
                // ----- Timezone
                $options = Model_Town::$_timezone_options;

                $element = new Form_Element_Select('timezone', $options, array('label' => 'Временная зона','required' => TRUE),array('class' => 'w300px'));
                $element
                    ->add_validator(new Form_Validator_InArray(array_keys($options)));
                
                $fieldset->add_component($element, 1);
                
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
