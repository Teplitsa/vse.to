<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_Checkout extends Form_Frontend
{
    public function init()
    {
        // ----- name
        $element = new Form_Element_Input('name',
            array(
                'label' => 'Ваше имя',
                'required' => TRUE,
                'comment' => 'Как нам к вам обращаться?'
            ),
            array(
                'size' => '80',
                'maxlength' => '63',
            )
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(63))
            ->add_validator(new Form_Validator_NotEmptyString());
        $this->add_component($element);

        // ----- phone
        $element = new Form_Element_Input('phone',
            array(
                'label' => 'Телефоны для связи',
                'required' => TRUE,
                'comment' => 'Мы обязательно захотим вам позвонить, но вы не всегда бываете дома или на работе. Поэтому, огромная просьба, - указывайте номер мобильного телефона, который всегда при себе.'
            ),
            array(
                'size' => '80',
                'maxlength' => '63',
            )
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(63))
            ->add_validator(new Form_Validator_NotEmptyString());
        $this->add_component($element);

        // ----- email
        $element = new Form_Element_Input('email',
            array(
                'label' => 'E-mail',
                'required' => TRUE,
                'comment' => 'На введенный адрес придет уведомление с номером заказа, который вы в дальнейшем сможете использовать при общении с менеджерами нашего магазина.'
            ),
            array(
                'size' => '80',
                'maxlength' => '31',
            )
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(31))
            ->add_validator(new Form_Validator_NotEmptyString());
        $this->add_component($element);

        // ----- address
        $element = new Form_Element_Textarea('address',
            array(
                'label' => 'Адрес доставки',
                'required' => TRUE,
                'comment' => 'Пожалуйста, указывайте адрес максимально точно. Если найти адрес на месте тяжело, подскажите, пожалуйста, как вас лучше искать.'
            ),
            array(
                'cols' => '80',
                'rows' => '5'
            )
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(1023))
            ->add_validator(new Form_Validator_NotEmptyString());
        $this->add_component($element);

        // ----- comment
        $element = new Form_Element_Textarea('comment',
            array(
                'label' => 'Дополнительная информация по заказу',
                'comment' => 'Любые пожелания и предложения'
            ),
            array(
                'cols' => '80',
                'rows' => '5'
            )
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(1023));
        $this->add_component($element);
        
        // ----- Form buttons
        $fieldset = new Form_Fieldset_Buttons('buttons');
        $this->add_component($fieldset);

            // ----- Submit button
            $fieldset
                ->add_component(new Form_Element_Submit('submit',
                    array('label' => 'Заказать'),
                    array('class' => 'submit')
                ));
    }
}
