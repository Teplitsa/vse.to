<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Menu extends Form_Backend {

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set html class
        $this->attribute('class', 'lb150px');
        $this->layout = 'wide';        

        // 2-column layout
        $cols = new Form_Fieldset_Columns('cols', array('column_classes' => array(1 => 'w55per')));
        $this->add_component($cols);
        
        // ----- Name
        $element = new Form_Element_Input('name', array('label' => 'Имя', 'required' => TRUE), array('maxlength' => 15));
        $element
            ->add_filter(new Form_Filter_TrimCrop(15))
            ->add_validator(new Form_Validator_NotEmptyString())
            ->add_validator(new Form_Validator_Alnum());
        $cols->add_component($element);

        // ----- Caption
        $element = new Form_Element_Input('caption', array('label' => 'Название'), array('maxlength' => 63));
        $element
            ->add_filter(new Form_Filter_TrimCrop(63));
        $cols->add_component($element);

        // ----- Root_node_id
        $node = new Model_Node();
        $nodes = $node->find_all(array('order_by' => 'lft', 'desc' => FALSE, 'columns' => array('id', 'lft', 'rgt', 'level', 'caption')));

        $options = array(0 => '---');
        foreach ($nodes as $node)
        {
            $options[$node['id']] = str_repeat('&nbsp;', ((int)$node->level - 1) * 2) . Text::limit_chars($node->caption, 30);
        }

        $element = new Form_Element_Select('root_node_id', $options,  array('label' => 'Корневая страница', 'required' => TRUE));
        $element
            ->add_validator(new Form_Validator_InArray(array_keys($options)));
        $cols->add_component($element);

        // ----- View
        // Select all *.php files from views/menu_templates directory
        $options = array();

        $templates = glob(Modules::path('menus') . 'views/menu_templates/*.php');
        foreach ($templates as $template)
        {
            $template = basename($template, '.php');
            $options[$template] = $template;
        }

        $element = new Form_Element_Select('view', $options, array('label' => 'Шаблон', 'required' => TRUE));
        $element
            ->add_validator(new Form_Validator_InArray(array_keys($options)));
        $cols->add_component($element);

        // ----- Additional settings
        $fieldset = new Form_Fieldset('settings', array('label' => 'Дополнительные настройки'));
        $cols->add_component($fieldset);

            // ----- Maximum level
            $element = new Form_Element_Input('max_level', array('label' => 'Макс. уровень вложенности'), array('class' => 'integer'));
            $element
                ->add_validator(new Form_Validator_Integer(0, NULL));
            $element->comment = 'Относительно корневого раздела. 0 - не ограничивать';
            $fieldset->add_component($element);

            // ----- root_selected
            $fieldset
                ->add_component(new Form_Element_Checkbox('settings[root_selected]', array('label' => 'Корневая страница из выбранной ветки')));

            // ----- selected_level
            $element = new Form_Element_Input('settings[root_level]', array('label' => 'Уровень корневой страницы в выбранной ветке'), array('class' => 'integer'));
            $element
                ->add_validator(new Form_Validator_Integer());
            $element->comment = '0 - использовать текущую страницу, <br />отрицательное значение - отсчитывать от уровня текущей страницы';
            $fieldset->add_component($element);


        // ----- Nodes visibility
        $options = array();
        $value = array();
        foreach ($nodes as $node)
        {
            $options[$node->id] = str_repeat('&nbsp;', ((int)$node->level - 1) * 4) . $node->caption;
        }
        $element = new Form_Element_CheckSelect('nodes_visibility', $options, array('label' => 'Страницы в меню'));
        $element->config_entry = 'checkselect_fieldset';
        $cols->add_component($element,2 );

        // ----- Default_visibility
        $element = new Form_Element_Checkbox('default_visibility', array('label' => 'Новые страницы видимы по умолчанию'));
        $cols->add_component($element,2);
        
        // ----- Form buttons
        $fieldset = new  Form_Fieldset_Buttons('buttons');
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
