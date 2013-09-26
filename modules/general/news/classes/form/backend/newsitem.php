<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Newsitem extends Form_Backend {

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set html class
        $this->attribute('class', 'w99per');

        // ----- date
        $element = new Form_Element_SimpleDate('date', array('label' => 'Дата', 'required' => TRUE));
        $element->value_format = Model_Newsitem::$date_as_timestamp ? 'timestamp' : 'date';
        $element
            ->add_validator(new Form_Validator_Date());
        $this->add_component($element);

        // ----- Caption
        $element = new Form_Element_Input('caption',
            array('label' => 'Заголовок', 'required' => TRUE),
            array('maxlength' => 255)
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(255))
            ->add_validator(new Form_Validator_NotEmptyString());
        $this->add_component($element);

        // ----- text
        $tab = new Form_Fieldset_Tab('text_tab', array('label' => 'Полный текст'));
        $this->add_component($tab);

            $element = new Form_Element_Wysiwyg('text', array('label' => 'Полный текст'), array('rows' => 15));
            $tab->add_component($element);

        // ----- short_text
        $tab = new Form_Fieldset_Tab('short_text_tab', array('label' => 'Краткий текст'));
        $this->add_component($tab);
        
            $element = new Form_Element_Textarea('short_text', array('label' => 'Краткий текст'));
            $tab->add_component($element);

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

        parent::init();
    }
}
