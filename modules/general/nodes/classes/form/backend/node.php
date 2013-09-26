<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Node extends Form_Backend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
        $this->attribute('class', "wide w500px lb100px");

        // ----- "general" tab
        $tab = new Form_Fieldset_Tab('general', array('label' => 'Свойства'));
        $this->add_component($tab);

            // ----- Caption
            $element = new Form_Element_Input('caption',
                array('label' => 'Заголовок', 'required' => TRUE),
                array('maxlength' => 63)
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(63))
                ->add_validator(new Form_Validator_NotEmptyString());
            $tab->add_component($element);

            // ----- Menu_caption
            $element = new Form_Element_Input('menu_caption',
                array('label' => 'Заголовок в меню'),
                array('maxlength' => 63)
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(63));
            $tab->add_component($element);

            // ----- Parent_id
            $params = array('order_by' => 'lft', 'desc' => FALSE);
            {
                $nodes = $this->model()->find_all_but_subtree_by_site_id(Model_Site::current()->id, $params);
            }

            $options = array(0 => '---');
            foreach ($nodes as $node)
            {
                $options[$node->id] = str_repeat('&nbsp;', ((int)$node->level - 1) * 2) . Text::limit_chars($node->caption, 30);
            }

            $element = new Form_Element_Select('parent_id', $options, 
                array('label' => 'Родитель', 'required' => TRUE, 'layout' => 'wide')
            );
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $tab->add_component($element);

            // ----- Node type
            $node_types = Model_Node::node_types();

            $options = array();
            foreach ($node_types as $type => $info)
            {
                $options[$type] = $info['name'];
            }
            $element =  new Form_Element_Select('new_type', $options, 
                array('label' => 'Модуль', 'required' => TRUE, 'layout' => 'wide')
            );
            $element->default_value = $this->model()->type;
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $tab->add_component($element);

            // ----- Layout
            // Select all *.php files from APPPATH/views/layouts directory
            $options = array();

            $templates = glob(APPPATH . '/views/layouts/*.php');
            foreach ($templates as $template)
            {
                $template = basename($template, '.php');
                $options[$template] = $template;
            }

            $element = new Form_Element_Select('layout', $options, 
                array('label' => 'Шаблон', 'required' => TRUE, 'layout' => 'wide')
            );
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $tab->add_component($element);

            // ----- URL alias
            $element = new Form_Element_Input('alias', array('label' => 'Имя в URL'), array('maxlength' => 31));
            $element
                ->add_filter(new Form_Filter_TrimCrop(31));
//                ->add_validator(new Form_Validator_Regexp('/^\s*[\w-]+\s*$/',
//                    array(
//                        Form_Validator_Alnum::NOT_MATCH => 'Имя раздела в URL содержит недопустимые символы! Разрешается использовать только английские буквы, цифры и символы "_" и "-".'
//                    ),
//                    TRUE, TRUE
//                ))
//                ->add_validator(new Form_Validator_Regexp('/^(?!\d+$)/',
//                    array(
//                        Form_Validator_Regexp::NOT_MATCH => 'Имя раздела в URL не может быть числом'
//                    ),
//                    TRUE, TRUE
//                ));
            $element->comment = 'Имя страницы в URL';
            $tab->add_component($element);

            // ----- Active
            $tab->add_component(new Form_Element_Checkbox('node_active', array('label' => 'Активная')));

            /*
            // ----- Info about page url (only for existing pages)
            if ($this->_model->id !== NULL)
            {
                $url = URL::site($this->_model->uri(), TRUE);
                $this->add_element(new Form_Element_Text('page_address', '
                    Адрес страницы: <a href="' . $url . '">' . HTML::chars($url) . '</a>
                '));
            }
             */

        // ----- "meta" tab
        $tab = new Form_Fieldset_Tab('meta', array('label' => 'Мета-теги'));
        $this->add_component($tab);
        
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