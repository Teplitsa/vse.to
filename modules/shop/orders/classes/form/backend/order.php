<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Order extends Form_Backend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
        $this->attribute('class', "w600px");

        if ($this->model()->id !== NULL)
        {
            // datetime_created
            $element = new Form_Element_Input('datetime_created',
                    array('label' => 'Время создания', 'disabled' => TRUE, 'layout' => 'wide'),
                    array('class' => 'w200px')
            );
            $this->add_component($element);
        }

        // ----- Status_id
        $statuses = Model::fly('Model_OrderStatus')->find_all(array('order_by' => 'id', 'desc' => FALSE));

        $options = array();
        foreach ($statuses as $status)
        {
            $options[$status->id] = $status->caption;
        }

        $element = new Form_Element_Select('status_id', $options,
            array('label' => 'Статус', 'required' => TRUE, 'layout' => 'wide')
        );
        $element
            ->add_validator(new Form_Validator_InArray(array_keys($options)));
        $this->add_component($element);

        // ----- name
        $element = new Form_Element_Input('name', array('label' => 'Имя пользователя'), array('maxlength' => 63));
        $element
            ->add_filter(new Form_Filter_TrimCrop(63));
        $this->add_component($element, 1);

        // ----- phone
        $element = new Form_Element_Input('phone', array('label' => 'Телефон'), array('maxlength' => 63));
        $element
            ->add_filter(new Form_Filter_TrimCrop(63));
        $this->add_component($element, 2);

        // ----- email
        $element = new Form_Element_Input('email', array('label' => 'E-Mail'), array('maxlength' => 31));
        $element
            ->add_filter(new Form_Filter_TrimCrop(31));
        $this->add_component($element);

        // ----- address
        $element = new Form_Element_Textarea('address', array('label' => 'Адрес доставки'));
        $element
            ->add_filter(new Form_Filter_TrimCrop(1023));
        $this->add_component($element);

        // ----- comment
        $element = new Form_Element_Textarea('comment', array('label' => 'Комментарий к заказу'));
        $element
            ->add_filter(new Form_Filter_TrimCrop(1023));
        $this->add_component($element);

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
                    array('label' => ($this->model()->id !== NULL ? 'Сохранить' : 'Далее')),
                    array('class' => 'button_accept')
                ));
    }
}
