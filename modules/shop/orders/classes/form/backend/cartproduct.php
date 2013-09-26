<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_CartProduct extends Form_Backend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
        $this->attribute('class', "w500px wide lb120px");

        if ($this->model()->id === NULL)
        {
            // ----- "Find in catalog" button
            $product_select_url = URL::to('backend/catalog', array('site_id' => $this->model()->order->site_id, 'action' => 'product_select'), TRUE);

            $element = new Form_Element_Text('product_select');
            $element->value =
                '<div class="buttons">'
              . '   <a href="' . $product_select_url . '" class="button open_window dim700x500">Найти в каталоге</a>'
              . '</div>'
            ;
            $this->add_component($element);
        }

        // ----- Marking
        $element = new Form_Element_Input('marking',
            array('label' => 'Артикул', 'required' => TRUE),
            array('maxlength' => 63)
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(63))
            ->add_validator(new Form_Validator_NotEmptyString());
        $this->add_component($element);
        
        // ----- Caption
        $element = new Form_Element_Input('caption',
            array('label' => 'Название', 'required' => TRUE),
            array('maxlength' => 255)
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(255))
            ->add_validator(new Form_Validator_NotEmptyString());
        $this->add_component($element);

        // ----- Price
        $element = new Form_Element_Money('price',
            array('label' => 'Цена', 'required' => TRUE)
        );
        $element
            ->add_filter(new Form_Filter_Trim())
            ->add_validator(new Form_Validator_Float(0, NULL, FALSE));
        $this->add_component($element);
        
        // ----- Quantity
        $element = new Form_Element_Integer('quantity', array('label' => 'Количество', 'required' => TRUE));
        $element
            ->add_filter(new Form_Filter_Trim())
            ->add_validator(new Form_Validator_Integer(0, NULL, FALSE));
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
