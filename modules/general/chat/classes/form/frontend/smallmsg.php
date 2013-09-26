<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_SmallMsg extends Form_Frontend {

    /**
     * Initialize form fields
     */
    public function init()
    {
        $this->view_script = 'frontend/forms/smallmsg';

        // ----- message
        $element = new Form_Element_Textarea('message', array('label' => 'Сообщение', 'required' => TRUE),array('rows' => '2'));
        $element->add_filter(new Form_Filter_TrimCrop(512));
        $element->add_validator(new Form_Validator_NotEmptyString());
        $this->add_component($element);

        // ----- notify
        $this->add_component(new Form_Element_Checkbox('notify', array(
            'label' => 'Оповестить клиента о сообщении по e-mail',
        )));

        $element = new Form_Element_Hidden('dialog_id');
        $this->add_component($element);
        
        parent::init();
    }
}
