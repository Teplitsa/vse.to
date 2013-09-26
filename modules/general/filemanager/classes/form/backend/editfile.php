<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_EditFile extends Form {

    /**
     * Initialize form fields
     */
    public function init()
    {
        //$this->attribute('class', 'w700px');
        
        // ----- Content
        $element = new Form_Element_Textarea('content', array('label' => 'Содержимое файла'), array('rows' => 40));
        $this->add_component($element);

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
