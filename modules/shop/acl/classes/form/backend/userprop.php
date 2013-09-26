<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_UserProp extends Form_Backend {

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
        $this->layout = 'wide';

        // Render using view
        //$this->view_script = 'backend/forms/property';

        $cols = new Form_Fieldset_Columns('userprop');
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

            // ----- name
            $element = new Form_Element_Input('name',
                array('label' => 'Имя', 'required' => TRUE),
                array('maxlength' => 31)
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(31))
                ->add_validator(new Form_Validator_NotEmptyString())
                ->add_validator(new Form_Validator_Alnum());
            $element->comment = 'Имя для характеристики, состоящее из цифр и букв латинсокого алфавита для использования в шаблонах. (например: page_numbers, news_creation, ...)';
            $cols->add_component($element);

            // ----- Type
            $options = $this->model()->get_types();

            $element = new Form_Element_RadioSelect('type', $options, array('label' => 'Тип'));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $cols->add_component($element);

            // ----- Options
            $element = new Form_Element_Options("options", 
                array('label' => 'Возможные значения', 'options_count' => 5),
                array('maxlength' => Model_UserProp::MAXLENGTH)
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(Model_UserProp::MAXLENGTH));
            $cols->add_component($element);

            // ----- PropSections grid
            $x_options = array('active' => 'Акт.');

            $y_options = array();
            $users = Model::fly('Model_User')->find_all();
            foreach ($users as $user)
            {
                $y_options[$user->id] = $user->email;
            }
            $element = new Form_Element_CheckGrid('userprops', $x_options, $y_options, 
                    array('label' => 'Настройки для пользователей')
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