<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Message extends Form_Backend {

    /**
     * Initialize form fields
     */
    public function init()
    {        
        $this->layout = 'wide';
        //$this->attribute('class', 'lb120px');
        $this->attribute('class', "w500px lb120px");

        // ----- receiver_id
        // Store receiver_id in hidden field
        $element = new Form_Element_Hidden('receiver_id');
        $this->add_component($element);

        if ($element->value !== FALSE)
        {
            $user_id = (int) $element->value;
        }
        else
        {
            $user_id = (int) $this->model()->receiver_id;
        }

        // ----- Receiver name
        // Receiver of this message
        $user = new Model_User();
        $user->find($user_id);

        $user_name = ($user->id !== NULL) ? $user->name : '--- получатель не указан ---';

        $element = new Form_Element_Input('user_name',
                array('label' => 'Получатель', 'disabled' => TRUE, 'layout' => 'wide'),
                array('class' => 'w150px')
        );
        $element->value = $user_name;
        $this->add_component($element);

        // Button to select user
        $button = new Form_Element_LinkButton('select_user_button',
                array('label' => 'Выбрать', 'render' => FALSE),
                array('class' => 'button_select_user open_window dim500x500')
        );
        $button->url   = URL::to('backend/acl', array('action' => 'user_select','group_id' => Model_Group::EDITOR_GROUP_ID), TRUE);
        $this->add_component($button);

        $element->append = '&nbsp;&nbsp;' . $button->render();

        // ----- message
        $element = new Form_Element_Textarea('message', array('label' => 'Сообщение', 'required' => TRUE));
        $element->add_filter(new Form_Filter_TrimCrop(512));
        $element->add_validator(new Form_Validator_NotEmptyString());
        $this->add_component($element);

        // ----- notify
        $this->add_component(new Form_Element_Checkbox('notify', array(
            'label' => 'Оповестить клиента о сообщении по e-mail',
        )));

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
                    array('label' => 'Отправить'),
                    array('class' => 'button_accept')
                ));

        parent::init();
    }
    
    /**
     * Add javascripts
     */
    public function render_js()
    {
        parent::render_js();
        // ----- Install javascripts
        
        Layout::instance()->add_script(Modules::uri('chat') . '/public/js/backend/mesuser.js');        
        
    }    
}
