<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Event extends Form_BackendRes
{
    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
        $this->attribute('class', "lb150px");
        $this->layout = 'wide';
        
        // User is being created or updated?
        $creating = ((int)$this->model()->id == 0) ? TRUE : FALSE;

        // ----- General tab
        $tab = new Form_Fieldset_Tab('general_tab', array('label' => 'Основные свойства'));
        $this->add_component($tab);

            // 2-column layout
            $cols = new Form_Fieldset_Columns('cols', array('column_classes' => array(1 => 'w55per')));
            $tab->add_component($cols);

            $fieldset = new Form_Fieldset('main_props', array('label' => 'Основные свойства'));
            $cols->add_component($fieldset);
            
            if ($this->model()->id === NULL)
            {
                // ----- "Find in catalog" button
                $product_select_url = URL::to('backend/catalog', array('site_id' => Model_Site::current()->id, 'action' => 'product_select'), TRUE);

                $element = new Form_Element_Text('product_select');
                $element->value =
                    '<div class="buttons">'
                  . '   <a href="' . $product_select_url . '" class="button dim700x500">Подобрать анонс</a>'
                  . '</div>'
                ;
                $fieldset->add_component($element);            
            }
            
            // ----- Caption
            $element = new Form_Element_Input('caption',
                array('label' => 'Название', 'disabled' => TRUE, 'required' => TRUE),
                array('maxlength' => 255)
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(255))
                ->add_validator(new Form_Validator_NotEmptyString());
            $fieldset->add_component($element);

            // ----- Section_id_original
            $element = new Form_Element_Hidden('section_id_original');
            $this->add_component($element);

            // [!] Set up new section id to product model
            if ($element->value === FALSE)
            {
                $element->value = $this->model()->section_id;
            }
            else
            {
                $this->model()->section_id = $element->value;
            }
        
            // ----- Section_id
            $sectiongroups = Model::fly('Model_SectionGroup')->find_all_by_site_id_and_type(Model_Site::current()->id,  Model_SectionGroup::TYPE_EVENT, array('columns' => array('id', 'caption')));
            
            $sections = array();
            foreach ($sectiongroups as $sectiongroup)
            {
                $sections[$sectiongroup->id] = Model::fly('Model_Section')->find_all_by_sectiongroup_id($sectiongroup->id, array(
                    'order_by' => 'lft',
                    'desc' => FALSE,
                    'columns' => array('id', 'rgt', 'lft', 'level', 'caption')
                ));
            }

            $sections_options = array();
            foreach ($sections as $sections_in_group)
            {
                foreach ($sections_in_group as $section)
                {
                    $sections_options[$section->id] = str_repeat('&nbsp;', ($section->level - 1) * 3) . Text::limit_chars($section->caption, 30);
                }
            }
            
            //lecturer
            $element = new Form_Element_Hidden('lecturer_id');
            $this->add_component($element);

            if ($element->value !== FALSE)
            {
                $lecturer_id = (int) $element->value;
            }
            else
            {
                $lecturer_id = (int) $this->model()->lecturer_id;
            }
            // ----- Lecturer name
            // Lecturer for this order
            $lecturer = new Model_Lecturer();
            $lecturer->find($lecturer_id);

            $lecturer_name = ($lecturer->id !== NULL) ? $lecturer->name : '--- лектор не указан ---';

            $element = new Form_Element_Input('lecturer_name',
                    array('label' => 'Лектор', 'disabled' => TRUE, 'layout' => 'wide'),
                    array('class' => 'w150px')
            );
            $element->value = $lecturer_name;
            $fieldset->add_component($element);
            
            
            // ----- datetime
            $element = new Form_Element_DateTimeSimple('datetime', array('label' => 'Дата проведения', 'disabled' => TRUE));
            $element->value_format = Model_Product::$date_as_timestamp ? 'timestamp' : 'datetime';
            $element
                ->add_validator(new Form_Validator_DateTimeSimple());
            $fieldset->add_component($element);
            
            
            // ----- access            
            $element = new Form_Element_Select('access',
                Model_Product::$_access_options,
                array('label' => 'Тип события','disabled' => TRUE)
            );
            $fieldset->add_component($element);
            
            // ----- maxAllowedUsers
            $element = new Form_Element_Input('maxAllowedUsers',
                array('label' => 'Колличество участников','disabled' => TRUE),
                array('class' => 'w150px','maxlength' => '4')
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(255))
                ->add_validator(new Form_Validator_NotEmptyString())
                ->add_validator(new Form_Validator_Integer(0, NULL, FALSE));
            $fieldset->add_component($element);
            
            // ----- Active
            $fieldset->add_component(new Form_Element_Checkbox('active', array('label' => 'Активный')));

            // ----- Visible
            $fieldset->add_component(new Form_Element_Checkbox('visible', array('label' => 'Видимый')));
            
            // ----- Role properties
            $fieldset = new Form_Fieldset('role', array('label' => 'Пользователь'));
            $cols->add_component($fieldset);

            if (!$creating) {            
                // ----- External Links
                $options_count = 1;
                if ($this->model()->id) {
                    $options_count = count($this->model()->links);
                }            
                $element = new Form_Element_Options("links", 
                    array('label' => 'Внешние ссылки', 'options_count' => $options_count,'options_count_param' => 'options_count','option_caption' => 'добавить ссылку'),
                    array('maxlength' => Model_Product::LINKS_LENGTH)
                );
                $element
                    ->add_filter(new Form_Filter_TrimCrop(Model_Product::LINKS_LENGTH));
                $cols->add_component($element);

                // ----- Additional properties
                if ($this->model()->id === NULL) {
                    $main_section_id = key($sections_options);
                    $this->model()->section_id = $main_section_id;
                }
                $properties = $this->model()->properties;
                if ($properties->valid()) {
                    $fieldset = new Form_Fieldset('props', array('label' => 'Характеристики'));
                    $cols->add_component($fieldset);
                    foreach ($properties as $property)
                    {
                        switch ($property->type)
                        {
                            case Model_Property::TYPE_TEXT:
                                $element = new Form_Element_Input(
                                    $property->name,
                                    array('label' => $property->caption),
                                    array('maxlength' => Model_Property::MAX_TEXT)
                                );
                                $element
                                    ->add_filter(new Form_Filter_TrimCrop(Model_Property::MAX_TEXT));
                                $fieldset->add_component($element);
                                break;

                            case Model_Property::TYPE_SELECT:
                                $options = array('' => '---');
                                foreach ($property->options as $option)
                                {
                                    $options[$option] = $option;
                                }

                                $element = new Form_Element_Select(
                                    $property->name,
                                    $options,
                                    array('label' => $property->caption)
                                );
                                $element
                                    ->add_validator(new Form_Validator_InArray(array_keys($options)));
                                $fieldset->add_component($element);
                                break;

                            case Model_Property::TYPE_TEXTAREA:
                                $element = new Form_Element_Textarea(
                                    $property->name,
                                    array('label' => $property->caption),
                                    array('maxlength' => Model_Property::MAX_TEXTAREA)
                                );
                                $element
                                    ->add_filter(new Form_Filter_TrimCrop(Model_Property::MAX_TEXTAREA));
                                $fieldset->add_component($element);
                                break;

                        }
                    }
                }
            }
            
        $fieldset = new Form_Fieldset('product_sections', array('label' => 'Основная категория события'));
        $cols->add_component($fieldset, 2); 
        // main category    
        $element = new Form_Element_Select('section_id', $sections_options, array('label' => 'Основная категория', 'required' => TRUE));
        $element
            ->add_validator(new Form_Validator_InArray(array_keys($sections_options), array(
                Form_Validator_InArray::NOT_FOUND => 'Вы не указали основную категорию'
            )));
        $fieldset->add_component($element);
        if (!$creating) {                        
        // ----- Additional sections
        if (Request::current()->param('cat_section_ids') != '')
        {
            // Are section ids explicitly specified in the uri?
            $section_ids = explode('_', Request::current()->param('cat_section_ids'));
        }
        
        foreach ($sectiongroups as $sectiongroup)
        {
            if (count($sections[$sectiongroup->id]) <= 20)
            {
                // Use a simple list of checkboxes to select additional sections
                // when the total number of sections is not very big
                $options = array();
                foreach ($sections[$sectiongroup->id] as $section)
                {
                    $label = str_repeat('&nbsp;', ($section->level - 1) * 3) . Text::limit_chars($section->caption, 30);

                    if ($this->model()->id !== NULL && $section->id === $this->model()->section_id)
                    {
                        // Render checkbox for the main section as "disabled", to prevent
                        // adding it as additional section when user changes the main section
                        $options[$section->id] = array('label' => $label, 'disabled' => TRUE);
                    }
                    else
                    {
                        $options[$section->id] = $label;
                    }

                }
            
                $element = new Form_Element_CheckSelect('sections[' . $sectiongroup->id . ']', $options, array('label' => 'Дополнительные категории: '.$sectiongroup->caption));
                $element->config_entry = 'checkselect_fieldset';
                $cols->add_component($element, 2);
            }
            else
            {
                $fieldset = new Form_Fieldset('sections[' . $sectiongroup->id . ']', array('label' => 'Дополнительные категории: '.$sectiongroup->caption));
                $cols->add_component($fieldset, 2);

                // Button to select additional sections for product
                $history = URL::uri_to('backend/catalog/products', array('action' => 'sections_select'), TRUE);

                $sections_select_url = URL::to('backend/catalog/sections', array(
                                                    'action' => 'select',
                                                    'cat_sectiongroup_id' => $sectiongroup->id,
                                                    'history' => $history
                                                ), TRUE);

                $button = new Form_Element_LinkButton('select_sections_button[' . $sectiongroup->id . ']',
                        array('label' => 'Выбрать'),
                        array('class' => 'button_select_sections open_window')
                );
                $button->layout = 'standalone';
                $button->url   = $sections_select_url;
                $fieldset->add_component($button);

                $sections_fieldset = new Form_Fieldset('additional_sections[' . $sectiongroup->id . ']');
                $sections_fieldset->config_entry = 'fieldset_inline';
                $sections_fieldset->layout = 'standart';
                $fieldset->add_component($sections_fieldset);

                // Obtain a list of selected sections in the following precedence
                // 1. From $_POST
                // 2. From cat_section_ids request param
                // 3. From model
                if ($this->is_submitted())
                {
                    $supplied_sections = $this->get_post_data('sections[' . $sectiongroup->id . ']');
                    if ( ! is_array($supplied_sections))
                    {
                        $supplied_sections = array();
                    }

                    // Filter out invalid sections
                    foreach ($supplied_sections as $section_id => $selected)
                    {
                        if ( ! isset($sections[$sectiongroup->id][$section_id]))
                        {
                            unset($supplied_sections[$section_id]);
                        }
                    }

                    // Add main section
                    if ($this->model()->id !== NULL)
                    {
                        $supplied_sections[$this->model()->section_id] = 1;
                    }
                }
                elseif (isset($section_ids))
                {
                    // Section ids are explicitly specified in the uri
                    $supplied_sections = array();
                    foreach ($section_ids as $section_id)
                    {
                        if (isset($sections[$sectiongroup->id][$section_id]))
                        {
                            $supplied_sections[$section_id] = 1;
                        }
                    }
                    
                    // Add main section
                    if ($this->model()->id !== NULL)
                    {
                        $supplied_sections[$this->model()->section_id] = 1;
                    }
                }
                else
                {
                    $supplied_sections = $this->model()->sections;
                    $supplied_sections = (isset($supplied_sections[$sectiongroup->id])) ? $supplied_sections[$sectiongroup->id] : array();                    
                }

                if ( ! empty($supplied_sections))
                {
                    foreach ($supplied_sections as $section_id => $selected)
                    {
                        if (isset($sections[$sectiongroup->id][$section_id]))
                        {
                            $section = $sections[$sectiongroup->id][$section_id];

                            $element = new Form_Element_Checkbox('sections[' . $sectiongroup->id . '][' . $section_id . ']', array('label' => $section->full_caption));
                            $element->default_value = $selected;
                            $element->layout = 'default';
                            if ($this->model()->id !== NULL && $section_id == $this->model()->section_id)
                            {
                                $element->disabled = TRUE;
                            }
                            $sections_fieldset->add_component($element);
                        }
                    }
                }
                else
                {                   
                    // No additional sectons were selected - add hidden element to emulate an empty array
                    // Without this the previous value of main section_id is stored in additinal sections :-(
                    $element = new Form_Element_Hidden('sections[' . $sectiongroup->id . '][0]');
                    $element->value = '0';
                    $this->add_component($element);
                }
            }

        } // foreach ($sectiongroups as $sectiongroup)




            // ----- Product images
            if ($this->model()->id !== NULL)
            {
                $element = new Form_Element_Custom('images', array('label' => '', 'layout' => 'standart'));
                $element->value = Request::current()->get_controller('images')->widget_images('product', $this->model()->id, 'product');
                $cols->add_component($element, 2);
            }


        // ----- Description tab
        $tab = new Form_Fieldset_Tab('description_tab', array('label' => 'Описание'));
        $this->add_component($tab);


            // ----- Description
            $tab->add_component(new Form_Element_Wysiwyg('description', array('label' => 'Описание события')));

        // ----- Address tab
        $tab = new Form_Fieldset_Tab('address_tab',array('label' => 'Адрес проведения'));
        
        $this->add_component($tab);
            
            // ----- Town                               
            $towns = Model_Town::towns();
            $element = new Form_Element_Select('town_show',
                $towns,
                array('label' => 'Город')
            );
            $tab->add_component($element); 
            // ----- Address
            $element = new Form_Element_Textarea('address_show', 
                    array('label' => 'Адрес проведения'),
                    array('class' => 'w300px'));
            $element
                ->add_filter(new Form_Filter_TrimCrop(1023));
            $tab->add_component($element);
        }
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
    
    /**
     * Add javascripts
     */
    public function render_js()
    {
        parent::render_js();
        // ----- Install javascripts
        
        // Url for ajax requests to redraw product properties when main section is changed
        $properties_url = URL::to('backend/catalog/products', array(
            'action' => 'ajax_properties',
            'type_id' => Model_SectionGroup::TYPE_EVENT,            
            'id' => $this->model()->id
        ));
        // Url for ajax requests to redraw selected additional sections for product
        $on_sections_select_url = URL::to('backend/catalog/products', array(
            'action' => 'ajax_sections_select',
            'type_id' => Model_SectionGroup::TYPE_EVENT,            
            'id' => $this->model()->id,
            'cat_section_ids' => '{{section_ids}}',
            'cat_sectiongroup_id' => '{{sectiongroup_id}}'
        ));
        
        Layout::instance()->add_script(
            "var product_form_name='" . $this->name . "';\n\n"
                
          . "var properties_url = '" . $properties_url . "';\n"
          . "var on_sections_select_url = '" . $on_sections_select_url . "';\n"
        , TRUE);

        // "props" fieldset id
        $component = $this->find_component('props');
        if ($component !== FALSE)
        {
            Layout::instance()->add_script(
                "var properties_fieldset_id='" . $component->id . "';"
            , TRUE);
        }

        // additional sections fieldset id's to redraw via ajax requests
        $script = "var sections_fieldset_ids = {};\n";
        
        $sectiongroups = Model::fly('Model_SectionGroup')->find_all_by_site_id_and_type(Model_Site::current()->id,  Model_SectionGroup::TYPE_EVENT, array('columns' => array('id', 'caption')));
        
        foreach ($sectiongroups as $sectiongroup)
        {
            $component = $this->find_component('additional_sections[' . $sectiongroup->id . ']');
            if ($component !== FALSE)
            {
                $script .= "sections_fieldset_ids[" . $sectiongroup->id . "]='" . $component->id . "';\n";
            }
        }
        Layout::instance()->add_script($script, TRUE);

        // Link product form scripts
        jQuery::add_scripts();
        Layout::instance()->add_script(Modules::uri('catalog') . '/public/js/backend/product_form.js');
        Layout::instance()->add_script(Modules::uri('catalog') . '/public/js/backend/produser.js');        
        
    }
}
