<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_Notify extends Form_Frontend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Render using view
        $this->view_script = 'frontend/forms/notify';

        $element = new Form_Element_Text('text', array('class' => 'confirm_message'));
        $element->value = "Зарегистрируйтесь или войдите в свою учетную запись";
        $this->add_component($element);

        // ----- Form buttons
        $button = new Form_Element_Button('submit_notify',
                array('label' => 'Вернуться'),
                array('class' => 'button_notify button-modal')
        );
        $this->add_component($button);        


        parent::init();
    }
}