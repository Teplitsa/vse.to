<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Section extends Form_Backend {

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
        $this->attribute('class', "wide lb100px");

        // "general" tab
        $tab = new Form_Fieldset_Tab('general_tab', array('label' => 'Основные свойства'));
        $this->add_component($tab);

            // 2-column layout
            $cols = new Form_Fieldset_Columns('cols', array('column_classes' => array(1 => 'w55per')));
            $tab->add_component($cols);

            // ----- Caption
            $element = new Form_Element_Input('caption',
                array('label' => 'Заголовок', 'required' => TRUE),
                array('maxlength' => 255)
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(255))
                ->add_validator(new Form_Validator_NotEmptyString());
            $cols->add_component($element);

            // ----- web_import_id
            $element = new Form_Element_Input('web_import_id', array('label' => 'ID импорта из web'), array('maxlength' => 31));
            $element
                ->add_filter(new Form_Filter_TrimCrop(31));
            $cols->add_component($element);

            // ----- Parent_id
            $params = array('order_by' => 'lft', 'desc' => FALSE);
            {
                $sections = $this->_model->find_all_but_subtree_by_sectiongroup_id($this->model()->sectiongroup_id, $params);
            }

            $options = array(0 => '---');
            foreach ($sections as $section)
            {
                $options[$section->id] = str_repeat('&nbsp;', ((int)$section->level - 1) * 2) . Text::limit_chars($section->caption, 30);
            }

            $element = new Form_Element_Select('parent_id', $options, array('label' => 'Родитель', 'required' => TRUE));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $cols->add_component($element);

            // ----- Active
            $cols->add_component(new Form_Element_Checkbox('section_active', array('label' => 'Активный')));

            // ----- Properties grid
            $x_options = array('active' => 'Акт.', 'filter' => 'Фильтр', 'sort' => 'Сорт.');

            $y_options = array();
            $properties = Model::fly('Model_Property')->find_all_by_site_id(Model_Site::current()->id, array(
                'order_by' => 'position',
                'desc' => FALSE
            ));
            foreach ($properties as $property)
            {
                $y_options[$property->id] = $property->caption;
            }
            $element = new Form_Element_CheckGrid('propsections', $x_options, $y_options, array('label' => 'Характеристики событий в разделе'));
            $element->config_entry = 'checkgrid_capt';
            $cols->add_component($element);
   
            // ----- Section logo
            if ($this->model()->id !== NULL)
            {
                $element = new Form_Element_Custom('images', array('label' => '', 'layout' => 'standart'));
                $element->value =
                        '<div class="content_caption">Логотип раздела</div>'
                      . Request::current()->get_controller('images')->widget_image('section', $this->model()->id, 'section');
                $cols->add_component($element, 2);
            }

        // ----- "description" tab
        $tab = new Form_Fieldset_Tab('description_tab', array('label' => 'Описание'));
        $this->add_component($tab);

            // ----- Description
            $tab->add_component(new Form_Element_Wysiwyg('description', array('label' => 'Описание раздела')));

        // ----- "SEO" tab
        $tab = new Form_Fieldset_Tab('seo_tab', array('label' => 'SEO'));
        $this->add_component($tab);

            // ----- URL alias
            $element = new Form_Element_Input('alias', array('label' => 'Имя в URL'), array('maxlength' => 31));
            $element
                ->add_filter(new Form_Filter_TrimCrop(31))
                ->add_validator(new Form_Validator_Regexp('/^\s*[\w-]+\s*$/',
                    array(
                        Form_Validator_Alnum::NOT_MATCH => 'Имя раздела в URL содержит недопустимые символы! Разрешается использовать только английские буквы, цифры и символы "_" и "-".'
                    ),
                    TRUE, TRUE
                ))
                ->add_validator(new Form_Validator_Regexp('/^(?!\d+$)/',
                    array(
                        Form_Validator_Regexp::NOT_MATCH => 'Имя раздела в URL не может быть числом'
                    ),
                    TRUE, TRUE
                ));
            $element->comment = 'Имя раздела в URL';
            $element->disabled = TRUE;
            $tab->add_component($element);

            // ----- Title
            $element = new Form_Element_Textarea('meta_title', array('label' => 'Метатег title'), array('rows' => 3));
            $element
                ->add_filter(new Form_Filter_TrimCrop(511));
            $tab->add_component($element);

            // ----- Description
            $element = new Form_Element_Textarea('meta_description', array('label' => 'Метатег description'), array('rows' => 3));
            $element
                ->add_filter(new Form_Filter_TrimCrop(511));
            $tab->add_component($element);

            // ----- Keywords
            $element = new Form_Element_Textarea('meta_keywords', array('label' => 'Метатег keywords'), array('rows' => 3));
            $element
                ->add_filter(new Form_Filter_TrimCrop(511));
            $tab->add_component($element);

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