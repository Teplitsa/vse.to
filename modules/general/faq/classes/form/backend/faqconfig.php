<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_FaqConfig extends Form_Backend {

    /**
     * Initialize form fields
     */
    public function init()
    {
        $this->layout = 'wide';
        $this->attribute('class', 'lb120px');

        // ----- email_client
        $fieldset = new Form_Fieldset('email_client', array('label' => 'Оповещение клиента'));
        $this->add_component($fieldset);

            // ----- email[client][subject]
            $element = new Form_Element_Input('email[client][subject]',
                array('label' => 'Заголовок письма', 'required' => TRUE),
                array('maxlength' => 255)
            );
            $element->add_filter(new Form_Filter_TrimCrop(255));
            $element->add_validator(new Form_Validator_NotEmptyString());
            $fieldset->add_component($element);

            // ----- email[client][body]
            $element = new Form_Element_Textarea('email[client][body]',
                array('label' => 'Шаблон письма'),
                array('rows' => 7)
            );
            $fieldset->add_component($element);

        // ----- email_admin
        $fieldset = new Form_Fieldset('email_admin', array('label' => 'Оповещение администратора'));
        $this->add_component($fieldset);

            // ----- email[admin][subject]
            $element = new Form_Element_Input('email[admin][subject]',
                array('label' => 'Заголовок письма', 'required' => TRUE),
                array('maxlength' => 255)
            );
            $element->add_filter(new Form_Filter_TrimCrop(255));
            $element->add_validator(new Form_Validator_NotEmptyString());
            $fieldset->add_component($element);

            // ----- email[admin][body]
            $element = new Form_Element_Textarea('email[admin][body]',
                array('label' => 'Шаблон письма'),
                array('rows' => 7)
            );
            $fieldset->add_component($element);

        // ----- Form buttons
        $fieldset = new Form_Fieldset_Buttons('buttons');
        $this->add_component($fieldset);

            // ----- Submit button
            $fieldset
                ->add_component(new Form_Backend_Element_Submit('submit',
                    array('label' => 'Сохранить'),
                    array('class' => 'button_accept')
                ));

        parent::init();
    }
}
