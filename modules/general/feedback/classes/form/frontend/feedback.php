<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_Feedback extends Form_Frontend
{
    public function init()
    {
        $this->attribute('class', 'ajax');
        
        // ----- phone
        $element = new Form_Element_Input('phone',
            array(
                'label' => 'Телефоны для связи',
                'required' => TRUE,
                'comment' => ''
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
        
        // ----- name
        $element = new Form_Element_Input('name',
            array(
                'label' => 'Ваше имя',
                'required' => TRUE,
                'comment' => ''
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

       // ----- comment
        $element = new Form_Element_Textarea('comment',
            array(
                'label' => 'Дополнительная информация',
                'comment' => ''
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
                    array('label' => 'Отправить'),
                    array('class' => 'submit')
                ));
    }
}
