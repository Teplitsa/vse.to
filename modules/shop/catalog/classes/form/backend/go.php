<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Go extends Form_BackendRes
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
        $this->attribute('class', "w600px");
        
        // ----- User properties
        Request::current()->set_value('spec_group', Model_Group::EDITOR_GROUP_ID);        
        $fieldset = new Form_Fieldset('user', array('label' => 'Пользователь'));
        $this->add_component($fieldset);
        
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
    }
}
