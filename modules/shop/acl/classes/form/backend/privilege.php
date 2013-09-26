<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Privilege extends Form_Backend {

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
        $this->layout = 'wide';

        // Render using view
        //$this->view_script = 'backend/forms/property';

        $cols = new Form_Fieldset_Columns('privilege');
        $this->add_component($cols);

            // ----- caption
            $element = new Form_Element_Input('caption',
                array('label' => 'Название', 'required' => TRUE),
                array('maxlength' => 31)
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(31))
                ->add_validator(new Form_Validator_NotEmptyString());
            $cols->add_component($element);

            // ----- Privilege type
            $privilege_types = Model_Privilege::privilege_types();

            $options = array();
            foreach ($privilege_types as $type => $info)
            {
                $options[$type] = $info['name'];
            }
            $element =  new Form_Element_Select('name', $options, 
                array('label' => 'Привилегия', 'required' => TRUE, 'layout' => 'wide')
            );
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $cols->add_component($element);

            // ----- Params
            $element = new Form_Element_Options("options", 
                array('label' => 'Params', 'options_count' => 1,'options_count_param' => 'options_count'),
                array('maxlength' => Model_Flashblock::MAXLENGTH)
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(Model_Flashblock::MAXLENGTH));
            $cols->add_component($element);
            
            // ----- Type
            /*$options = $this->model()->get_types();

            $element = new Form_Element_RadioSelect('type', $options, array('label' => 'Тип'));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $cols->add_component($element);

            // ----- Options
            $element = new Form_Element_Options("options", 
                array('label' => 'Возможные значения', 'options_count' => 5),
                array('maxlength' => Model_Property::MAXLENGTH)
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(Model_Property::MAXLENGTH));
            $cols->add_component($element);
            */
            // ----- PropSections grid
            $x_options = array('active' => 'Акт.');

            $y_options = array();
            $groups = Model::fly('Model_Group')->find_all();
            foreach ($groups as $group)
            {
                $y_options[$group->id] = $group->name;
            }
            $element = new Form_Element_CheckGrid('privgroups', $x_options, $y_options, 
                    array('label' => 'Настройки для групп')
            );
            $element->config_entry = 'checkgrid_capt';
            $cols->add_component($element, 2);
                    
        // ----- Form buttons
        $fieldset = new Form_Fieldset_Buttons('buttons');
        $cols->add_component($fieldset);

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
    }
}