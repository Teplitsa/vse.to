<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Flashblock extends Form_Backend {

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set html class
        $this->attribute('class', 'w99per');
        
        $cols = new Form_Fieldset_Columns('cols', array('column_classes' => array(1 => 'w55per')));
        
        // ----- "general" tab
        $this->add_component($cols);

        // ----- Name
        $options = Kohana::config('flashblocks.names');
        if (empty($options))
        {
            $options = array();
        }

        $element = new Form_Element_Select('name', $options, array('label' => 'Положение', 'required' => TRUE), array('maxlength' => 15));
        $element
            ->add_validator(new Form_Validator_InArray(array_keys($options)));
        $cols->add_component($element);

        // ----- Caption
        $element = new Form_Element_Input('caption', array('label' => 'Название'), array('maxlength' => 63));
        $element
            ->add_filter(new Form_Filter_TrimCrop(63));
        $cols->add_component($element);

        // ----- File
        $element = new Form_Element_File(
                'file',
                array('label' => 'SWF Flash File')
            );
        $element->add_validator(new Form_Validator_File());
        $cols->add_component($element);

        // ----- Width
        $element = new Form_Element_Integer('width', array('label' => 'Ширина', 'required' => TRUE));
        $element
            ->add_filter(new Form_Filter_Trim())
            ->add_validator(new Form_Validator_Integer(1, NULL, FALSE));
        $cols->add_component($element);

        // ----- Height
        $element = new Form_Element_Integer('height', array('label' => 'Высота', 'required' => TRUE));
        $element
            ->add_filter(new Form_Filter_Trim())
            ->add_validator(new Form_Validator_Integer(1, NULL, FALSE));
        $cols->add_component($element);

        // ----- Version
        $element = new Form_Element_Input('version',
            array('label' => 'Поддерживаемая версия', 'required' => TRUE),
            array('maxlength' => 16)
        );
        $element
            ->add_validator(new Form_Validator_Regexp('/^\d+\.\d+\.\d+$/',
                array(
                    Form_Validator_Regexp::NOT_MATCH => 'Данной версии Flash плеера не существует'
                ),
                TRUE, TRUE
            ));
        $element->comment = 'формат: "major.minor.release"';            
        $cols->add_component($element);

        
        // ----- Flashvars
        $element = new Form_Element_Options("flashvars", 
            array('label' => 'Flashvars', 'options_count' => 1,'options_count_param' => 'options_count1'),
            array('maxlength' => Model_Flashblock::MAXLENGTH)
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(Model_Flashblock::MAXLENGTH));
        $cols->add_component($element);

        // ----- Params
        $element = new Form_Element_Options("params", 
            array('label' => 'Params', 'options_count' => 1,'options_count_param' => 'options_count2'),
            array('maxlength' => Model_Flashblock::MAXLENGTH)
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(Model_Flashblock::MAXLENGTH));
        $cols->add_component($element);

        // ----- Attributes
        $element = new Form_Element_Options("attributes", 
            array('label' => 'Attributes', 'options_count' => 1,'options_count_param' => 'options_count3'),
            array('maxlength' => Model_Flashblock::MAXLENGTH)
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(Model_Flashblock::MAXLENGTH));
        $cols->add_component($element);
        
        // ----- "visibility" column

        // ----- Default_visibility
        $element = new Form_Element_Checkbox('default_visibility', array('label' => 'Блок отображается на новых страницах'));
        $cols->add_component($element,2);

        // ----- Nodes visibility
        $nodes = Model::fly('Model_Node')->find_all_by_site_id($this->model()->site_id, array('order_by' => 'lft'));

        $options = array();
        foreach ($nodes as $node)
        {
            $options[$node->id] = str_repeat('&nbsp;', ((int)$node->level - 1) * 4) . $node->caption;
        }
        $element = new Form_Element_CheckSelect('nodes_visibility', $options, 
                array('label' => 'Отображать на страницах', 'default_selected' => $this->model()->default_visibility)
        );
        $element->config_entry = 'checkselect_fieldset';
        $cols->add_component($element,2);

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
