<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_OrderComment extends Form_Backend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
        $this->attribute('class', "w500px");

        // ----- notify_client
        $this->add_component(new Form_Element_Checkbox('notify_client', array('label' => 'Оповеcтить клиента')));

        // ----- text
        $element = new Form_Element_Textarea('text', array('label' => 'Текст комментария'), array('rows' => 5));
        $element
            ->add_filter(new Form_Filter_TrimCrop(511));
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
                    array('label' => 'Сохранить'),
                    array('class' => 'button_accept')
                ));
    }
}
