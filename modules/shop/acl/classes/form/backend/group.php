<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Group extends Form_Backend {

    /**
     * Initialize form fields
     */
    public function init()
    {
        // HTML class
        $this->attribute('class', 'w500px lb150px');
        $this->layout = 'wide';

        // ----- Group name
        $element = new Form_Element_Input('name', 
            array('label' => 'Имя группы', 'required' => TRUE),
            array('maxlength' => 31)
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(31))
            ->add_validator(new Form_Validator_NotEmptyString());
        $this->add_component($element);

        // ----- Privileges
        /*$privileges = Auth::instance()->privileges();
        $element = new Form_Element_CheckSelect('privileges', $privileges, array('label' => 'Привилегии'));
        if ($this->model()->system)
        {
            // It's impossible to change privileges for a system group
            $element->disabled = TRUE;
        }
        $this->add_component($element);
        */
        // ----- Privileges
        $fieldset = new Form_Fieldset('privs', array('label' => 'Привилегии'));
        $this->add_component($fieldset);

        /*$privileges = Model::fly('Model_Privilege')->find_all_by_site_id(Model_Site::current()->id, array(
            'order_by' => 'position',
            'desc' => FALSE
        ));*/
        foreach ($this->model()->privileges as $privilege)
        {
            $element = new Form_Element_Checkbox(
                $privilege->name, 
                array('label' => $privilege->caption)
            );

            $fieldset->add_component($element);             
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
    }
}
