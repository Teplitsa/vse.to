<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Telemost extends Form_BackendRes
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
        $this->attribute('class', "w600px");

        // ----- place_id
        $fieldset = new Form_Fieldset('place', array('label' => 'Площадка'));
        $this->add_component($fieldset);
 
        // Store place_id in hidden field
        $element = new Form_Element_Hidden('place_id');
        $this->add_component($element);

        if ($element->value !== FALSE)
        {
            $place_id = (int) $element->value;
        }
        else
        {
            $place_id = (int) $this->model()->place_id;
        }

        // ----- Place name
        $place = new Model_Place();
        $place->find($place_id);

        $place_name = ($place->id !== NULL) ? $place->name : '';

        $element = new Form_Element_Input('place_name',
                array('label' => 'Площадка', 'disabled' => TRUE, 'layout' => 'wide','required' => TRUE),
                array('class' => 'w190px')
        );
        
        $element->value = $place_name;
        $fieldset->add_component($element);

        // Button to select place
        $button = new Form_Element_LinkButton('select_place_button',
                array('label' => 'Выбрать', 'render' => FALSE),
                array('class' => 'button_select_place open_window dim500x500')
        );
        $button->url   = URL::to('backend/area', array('action' => 'place_select'), TRUE);
        $this->add_component($button);

        $element->append = '&nbsp;&nbsp;' . $button->render();
        
        // ----- Description
        $fieldset->add_component(new Form_Element_Textarea('info', array('label' => 'Дополнительная информация')));        
        
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
    /**
     * Add javascripts
     */
    public function render_js()
    {
        parent::render_js();
        // ----- Install javascripts
        

        Layout::instance()->add_script(Modules::uri('catalog') . '/public/js/backend/telemostplace.js');        
        
    }    
}
