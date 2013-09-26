<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Block extends Form_Backend {

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set html class
        $this->attribute('class', 'w99per');

        // ----- "general" tab
        $tab = new Form_Fieldset_Tab('general', array('label' => 'Основные свойства'));
        $this->add_component($tab);

            // ----- Name
            $options = Kohana::config('blocks.names');
            if (empty($options))
            {
                $options = array();
            }
            
            $element = new Form_Element_Select('name', $options, array('label' => 'Положение', 'required' => TRUE), array('maxlength' => 15));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $tab->add_component($element);

            // ----- Caption
            $element = new Form_Element_Input('caption', array('label' => 'Название'), array('maxlength' => 63));
            $element
                ->add_filter(new Form_Filter_TrimCrop(63));
            $tab->add_component($element);

            // ----- Text
            $element = new Form_Element_Wysiwyg('text', array('label' => 'Содержимое'));
            $tab->add_component($element);

        // ----- "visibility" tab
        $tab = new Form_Fieldset_Tab('visibility', array('label' => 'Видимость на страницах'));
        $this->add_component($tab);

            // ----- Default_visibility
            $tab
                ->add_component(new Form_Element_Checkbox('default_visibility', array('label' => 'Блок отображается на новых страницах')));

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
