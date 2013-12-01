<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Product extends Form_BackendRes
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
            
            // ----- Caption
            $element = new Form_Element_Input('caption',
                array('label' => 'Название', 'required' => TRUE),
                array('maxlength' => 255)
            );
            
            $element
                ->add_filter(new Form_Filter_TrimCrop(255))
                ->add_validator(new Form_Validator_NotEmptyString());
            $fieldset->add_component($element);

            // ----- Section_id_original
            $element = new Form_Element_Hidden('section_id_original');
            $cols->add_component($element, 2);
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
            $sectiongroups = Model::fly('Model_SectionGroup')->find_all_by_site_id(Model_Site::current()->id, array('columns' => array('id', 'caption')));
            
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
            
            // ------ lecturer_id
            // control hidden field
            $control_element = new Form_Element_Hidden('lecturer_id',array('id' => 'lecturer_id'));

            $this->add_component($control_element);
            
            /*$req_lecturer_id = Request::instance()->param('lecturer_id', FALSE);
            
            if ($req_lecturer_id) {
                $control_element->set_value($req_lecturer_id);
                $lecturer_id = $req_lecturer_id;
            }*/
            
            if ($control_element->value !== FALSE)
            {
                $lecturer_id = (int) $control_element->value;
            }
            else
            {
                $lecturer_id = (int) $this->model()->lecturer_id;
            }

            // ----- lecturer_name
            // input field with autocomplete ajax
            $element = new Form_Element_Input('lecturer_name',
                    array('label' => 'Лектор', 'layout' => 'wide','id' => 'lecturer_name','required' => TRUE));
            $element->autocomplete_url = URL::to('backend/acl/lecturers', array('action' => 'ac_lecturer_name'));            
            Layout::instance()->add_style(Modules::uri('acl') . '/public/css/frontend/acl.css');
            
            $fieldset->add_component($element);
            
            $lecturer = new Model_Lecturer();
            $lecturer->find($lecturer_id);
            
            $lecturer_name = ($lecturer->id !== NULL) ? $lecturer->name : '--- лектор не указан ---';

            /*if ($req_lecturer_id) {
                $element->set_value($lecturer_name);
            }*/
            
            if ($element->value === FALSE) {   
                $element->value = $lecturer_name;                
            } else {
                if ($element->value !== $lecturer_name) $control_element->set_value(NULL); 
            }
            // ----- select_lecturer_button
            // Button to select lecturer
            /*$button = new Form_Element_LinkButton('select_lecturer_button',
                    array('label' => 'Выбрать', 'render' => FALSE),
                    array('class' => 'button_select_lecturer open_window')
            );
            $button->url   = URL::to('backend/acl', array('action' => 'lecturer_select'), TRUE);            
            $fieldset->add_component($button);
            
            if ($creating) $element->append = '&nbsp;&nbsp;' . $button->render();
            */
            // organizer
            // control hidden field
            $control_element = new Form_Element_Hidden('organizer_id',array('id' => 'organizer_id'));
            
            $this->add_component($control_element);
            
            if ($control_element->value !== FALSE)
            {
                $organizer_id = (int) $control_element->value;
            }
            else
            {
                $organizer_id = (int) $this->model()->organizer_id;
            }

            // ----- organizer_name
            // input field with autocomplete ajax
            $element = new Form_Element_Input('organizer_name',
                    array('label' => 'Организация', 'layout' => 'wide','id' => 'organizer_name','required' => TRUE));
            $element->autocomplete_url = URL::to('backend/acl/organizers', array('action' => 'ac_organizer_name'));            
            Layout::instance()->add_style(Modules::uri('acl') . '/public/css/backend/acl.css');
            
            $fieldset->add_component($element);
            
            $organizer = new Model_Organizer();
            $organizer->find($organizer_id);
            
            $organizer_name = ($organizer->id !== NULL) ? $organizer->name : '';
            
            if ($element->value === FALSE) {   
                $element->value = $organizer_name;                
            } else {
                if ($element->value !== $organizer_name) $control_element->set_value(NULL); 
            }            

            // ----- datetime
            $element = new Form_Element_DateTimeSimple('datetime', array('label' => 'Дата проведения', 'required' => TRUE),array('class' => 'w300px'));
            $element->value_format = Model_Product::$date_as_timestamp ? 'timestamp' : 'datetime';
            $element
                ->add_validator(new Form_Validator_DateTimeSimple());
            $fieldset->add_component($element);

            // ----- Duration
            $options = Model_Product::$_duration_options;

            $element = new Form_Element_Select('duration', $options, array('label' => 'Длительность'),array('class' => 'w300px'));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $fieldset->add_component($element);
            
            // ----- place_id
            // Store place_id in hidden field
            $element = new Form_Element_Hidden('place_id');
            $this->add_component($element);

            if ($element->value !== FALSE)
            {
                $place_id = (int) $element->value;
            }
            else
            {
                $place_id = (int) $this->model()->place_id;
            }

            // ----- Place name
            $place = new Model_Place();
            $place->find($place_id);

            $place_name = ($place->id !== NULL) ? $place->name : '--- площадка не указана ---';

            $element = new Form_Element_Input('place_name',
                    array('label' => 'Площадка', 'disabled' => TRUE, 'layout' => 'wide','required' => TRUE),
                    array('class' => 'w190px')
            );
            $element->value = $place_name;
            $fieldset->add_component($element);

            // Button to select place
            $button = new Form_Element_LinkButton('select_place_button',
                    array('label' => 'Выбрать', 'render' => FALSE),
                    array('class' => 'button_select_place open_window dim500x500')
            );
            $button->url   = URL::to('backend/area', array('action' => 'place_select'), TRUE);
            $this->add_component($button);

            $element->append = '&nbsp;&nbsp;' . $button->render();
            
            /*$towns = Model_Town::towns();
            
            $element = new Form_Element_Select('town',
                $towns,
                array('label' => 'Город проведения','required' => TRUE),
                array('class' => 'w300px')    
            );
            $fieldset->add_component($element);
            */
            // ----- theme
            $options = Model_Product::$_theme_options;

            $element = new Form_Element_Select('theme', $options, array('label' => 'Тема'),array('class' => 'w300px'));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $fieldset->add_component($element);
            
            
            // ----- format
            $options = Model_Product::$_format_options;

            $element = new Form_Element_Select('format', $options, array('label' => 'Формат'),array('class' => 'w300px'));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $fieldset->add_component($element);

            $element = new Form_Element_Input('tags', array('label' => 'Теги','id' => 'tag'), array('maxlength' => 255));
            $element->autocomplete_url = URL::to('backend/tags', array('action' => 'ac_tag'));
            $element->autocomplete_chunk = Model_Tag::TAGS_DELIMITER; 
            $element
                ->add_filter(new Form_Filter_TrimCrop(255));
            $fieldset->add_component($element);
            
            // ----- Description
            $fieldset->add_component(new Form_Element_Textarea('description', array('label' => 'О событии')));
            
            // ----- Product images
            if ($this->model()->id !== NULL)
            {
                $element = new Form_Element_Custom('images', array('label' => '', 'layout' => 'standart'));
                $element->value = Request::current()->get_controller('images')->widget_images('product', $this->model()->id, 'product');
                $cols->add_component($element, 2);
    
                $element = new Form_Element_Custom('telemosts', array('label' => '', 'layout' => 'standart'));
                $element->value = Request::current()->get_controller('telemosts')->widget_telemosts($this->model());
                $cols->add_component($element, 2);                  
            }            

            // ----- interact
            $options = Model_Product::$_interact_options;

            $element = new Form_Element_RadioSelect('interact', $options, array('label' => 'Интерактивность','required' => TRUE),array('class' => 'w300px'));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $fieldset->add_component($element);

            // ----- Price
            $element = new Form_Element_Money('price', array('label' => 'Стоимость лицензии'));
            $element
                ->add_filter(new Form_Filter_Trim())
                ->add_validator(new Form_Validator_Float(0, NULL));
            $fieldset->add_component($element);
            
            // ----- numviews
            $options = Model_Product::$_numviews_options;

            $element = new Form_Element_Select('numviews', $options, array('label' => 'Количество телемостов'),array('class' => 'w300px'));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $fieldset->add_component($element);
            
                /*$cols1 = new Form_Fieldset_Columns('price',array('column_classes' => array(2 => 'w60per')));
                $fieldset->add_component($cols1);

                $element = new Form_Element_Checkbox_Enable('change_payable', array('label' => 'Платное событие'));                
                $element->dep_elements = array('price');
                $cols1->add_component($element,1);
                
                // ----- Price
                $element = new Form_Element_Money('price', array('label' => 'Стоимость лицензии',));
                $element
                    ->add_filter(new Form_Filter_Trim())
                    ->add_validator(new Form_Validator_Float(0, NULL));
                $cols1->add_component($element,2);
                */
            // ----- choalg
            $options = Model_Product::$_choalg_options;

            $element = new Form_Element_Select('choalg', $options, array('label' => 'Кто выбирает'),array('class' => 'w300px'));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $fieldset->add_component($element);            
            
            // ----- Require
            $fieldset->add_component(new Form_Element_Textarea('require', array('label' => 'Требования к площадке, аудитории и др.')));            
            
            // ----- access
            $fieldset = new Form_Fieldset('access', array('label' => 'Кто может подать заявку'));
            $cols->add_component($fieldset); 
                                 
            // ----- Role properties
            $fieldset = new Form_Fieldset('user', array('label' => 'Управление'));
            $cols->add_component($fieldset);

            // ----- Active
            $fieldset->add_component(new Form_Element_Checkbox('active', array('label' => 'Активность')));

            // ----- Visible
            $fieldset->add_component(new Form_Element_Checkbox('visible', array('label' => 'Видимость')));
            
            if (!$creating) {
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
            'id' => $this->model()->id
        ));
        // Url for ajax requests to redraw selected additional sections for product
        $on_sections_select_url = URL::to('backend/catalog/products', array(
            'action' => 'ajax_sections_select',
            'id' => $this->model()->id,
            'cat_section_ids' => '{{section_ids}}',
            'cat_sectiongroup_id' => '{{sectiongroup_id}}'
        ));

        // Url for ajax requests to redraw selected additional sections for product
        $on_towns_select_url = URL::to('backend/catalog/products', array(
            'action' => 'ajax_towns_select',
            'id' => $this->model()->id,            
            'access_town_ids' => '{{town_ids}}',
        ));
        
        // Url for ajax requests to redraw selected additional sections for product
        $on_organizers_select_url = URL::to('backend/catalog/products', array(
            'action' => 'ajax_organizers_select',
            'id' => $this->model()->id,            
            'access_organizer_ids' => '{{organizer_ids}}',
        ));        

        // Url for ajax requests to redraw selected additional sections for product
        $on_users_select_url = URL::to('backend/catalog/products', array(
            'action' => 'ajax_users_select',
            'id' => $this->model()->id,            
            'access_user_ids' => '{{user_ids}}',
        )); 
        
        Layout::instance()->add_script(
            "var product_form_name='" . $this->name . "';\n\n"
                
          . "var properties_url = '" . $properties_url . "';\n"
          . "var on_sections_select_url = '" . $on_sections_select_url . "';\n"
          . "var on_towns_select_url = '" . $on_towns_select_url . "';\n"
          . "var on_organizers_select_url = '" . $on_organizers_select_url . "';\n"
          . "var on_users_select_url = '" . $on_users_select_url . "';\n"
                
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
        
        $sectiongroups = Model::fly('Model_SectionGroup')->find_all_by_site_id(Model_Site::current()->id, array('columns' => array('id', 'caption')));

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
        Layout::instance()->add_script(Modules::uri('catalog') . '/public/js/backend/prodlecturer.js');        
        Layout::instance()->add_script(Modules::uri('acl') . '/public/js/backend/lecturer_name.js');       
        Layout::instance()->add_script(Modules::uri('acl') . '/public/js/backend/organizer_name.js');
        Layout::instance()->add_script(Modules::uri('catalog') . '/public/js/backend/prodplace.js');        
        Layout::instance()->add_script(Modules::uri('tags') . '/public/js/backend/tag.js');
        
    }
}
