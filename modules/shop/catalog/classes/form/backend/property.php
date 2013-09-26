<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Property extends Form_Backend {

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
        $this->layout = 'wide';

        $cols = new Form_Fieldset_Columns('property');
        $this->add_component($cols);

            // ----- caption
            $element = new Form_Element_Input('caption',
                array('label' => 'Название', 'required' => TRUE),
                array('maxlength' => 31)
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(31))
                ->add_validator(new Form_Validator_NotEmptyString());
            $cols->add_component($element);

            // ----- name
            $element = new Form_Element_Input('name',
                array('label' => 'Имя', 'required' => TRUE),
                array('maxlength' => 31)
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(31))
                ->add_validator(new Form_Validator_NotEmptyString())
                ->add_validator(new Form_Validator_Alnum());
            $element->comment = 'Имя для свойства, состоящее из цифр и букв латинсокого алфавита для использования в шаблонах. (например: price, weight, ...)';
            $cols->add_component($element);
            
            // ----- Type
            $options = $this->model()->get_types();

            $element = new Form_Element_RadioSelect('type', $options, array('label' => 'Тип'));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $cols->add_component($element);

            // ----- Options
            $options_count = 5;
            if ($this->model()->options !== NULL) {
                $options_count = count($this->model()->options);
            } 
            $element = new Form_Element_Options("options", 
                array('label' => 'Возможные значения', 'options_count' => $options_count),
                array('maxlength' => Model_Property::MAX_PROPERTY)
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(Model_Property::MAX_PROPERTY));
            $cols->add_component($element);

            // ----- PropSections grid
            // ----- Role properties            
            // ----- Role properties
            
            // Button to select active sectiongroups for property
            $history = URL::uri_to('backend/catalog/properties', array('action' => 'sectiongroups_select'), TRUE);

            $sections_select_url = URL::to('backend/catalog/sectiongroups', array(
                                                'action' => 'select',
                                                'history' => $history
                                            ), TRUE);

            $button = new Form_Element_LinkButton('select_sectiongroups_button',
                    array('label' => 'Выбрать'),
                    array('class' => 'button_select_sectiongroups open_window')
            );
            $button->layout = 'standalone';
            $button->url   = $sections_select_url;
            $cols->add_component($button,2);

            $fieldset = new Form_Fieldset('secgr', array('label' => 'Разделы'));            
            
            $cols->add_component($fieldset,2);
            
            // ----- Additional sectiongroups
            $sectiongroups = Model::fly('Model_SectionGroup')->find_all_by_site_id(Model_Site::current()->id, array('columns' => array('id', 'caption'))); 
            if (Request::current()->param('cat_sectiongroup_ids') != '')
            {
                // Are section ids explicitly specified in the uri?
                $sectiongroup_ids = explode('_', Request::current()->param('cat_sectiongroup_ids'));
            }            
            // Obtain a list of selected sectiongroups in the following precedence
            // 1. From $_POST
            // 2. From cat_sectiongroup_ids request param
            // 3. From model
            if ($this->is_submitted())
            {
                $supplied_sectiongroups = $this->get_post_data('sectiongroups');
                if ( ! is_array($supplied_sectiongroups))
                {
                    $supplied_sectiongroups = array();
                }
                // Filter out invalid sectiongroups
                foreach ($supplied_sectiongroups as $sectiongroup_id => $selected)
                {
                    if ( ! isset($sectiongroups[$sectiongroup_id]))
                    {
                        unset($supplied_sectiongroups[$sectiongroup_id]);
                    }
                }
                
            } elseif (isset($sectiongroup_ids))
            {
                // Section ids are explicitly specified in the uri
                $supplied_sectiongroups = array();
                foreach ($sectiongroup_ids as $sectiongroup_id)
                {
                    if (isset($sectiongroups[$sectiongroup_id]))
                    {
                        $supplied_sectiongroups[$sectiongroup_id] = 1;
                    }
                }
            } else
            {
                $supplied_sectiongroups = $sectiongroups;
            }            

            if ( ! empty($supplied_sectiongroups))
            {
                foreach ($supplied_sectiongroups as $sectiongroup_id => $selected)
                {
                    if (isset($sectiongroups[$sectiongroup_id]))
                    {
                        $sectiongroup = $sectiongroups[$sectiongroup_id];

                        $element = new Form_Element_Hidden('sectiongroups[' . $sectiongroup->id . ']', array('label' => $sectiongroup->caption));
                        $this->add_component($element);
                    }
                }
            }
            $x_options = array('active' => 'Акт.', 'filter' => 'Фильтр', 'sort' => 'Сорт.');

            $y_options = array();
            $label = '';
            
            foreach ($supplied_sectiongroups as $supplied_sectiongroup_id => $supplied_sectiongroup_selected) {
                $supplied_sectiongroup = $sectiongroups[$supplied_sectiongroup_id]; 
                $label = $label.' '.$supplied_sectiongroup->caption;
                $sections = Model::fly('Model_Section')->find_all_by_sectiongroup_id($supplied_sectiongroup->id, array(
                    'order_by' => 'lft',
                    'desc' => FALSE,
                ));
                
                foreach ($sections as $section) {
                    $y_options[$section->id] = str_repeat('&nbsp;', max($section->level - 1, 0)*4) . $section->caption;                
                }
                
            }
            
            if (!empty($y_options)) {
                $element = new Form_Element_CheckGrid('propsections', $x_options, $y_options, 
                        array('label' => 'Настройки для разделов: '.$label)
                );
                $element->config_entry = 'checkgrid_capt';
                $fieldset->add_component($element);                                    
            }
            
            
        
        // ----- Form buttons
        $fieldset = new Form_Fieldset_Buttons('buttons');
        $cols->add_component($fieldset);

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
        
        // Url for ajax requests to redraw additional sections for selected sectiongroups
        $on_sectiongroups_select_url = URL::to('backend/catalog/properties', array(
            'action' => 'ajax_sectiongroups_select',
            'id' => $this->model()->id,
            'cat_sectiongroup_ids' => '{{sectiongroup_ids}}',
        ));

        Layout::instance()->add_script(
            "var property_form_name='" . $this->name . "';\n\n"
          . "var on_sectiongroups_select_url = '" . $on_sectiongroups_select_url . "';\n"
        , TRUE);

        $script ='';
        
        $component = $this->find_component('secgr');
        if ($component !== FALSE)
        {
            $script .= "var propsections_fieldset ='" . $component->id . "';\n";
        }
        Layout::instance()->add_script($script, TRUE);
                
        // Link product form scripts
        jQuery::add_scripts();
        Layout::instance()->add_script(Modules::uri('catalog') . '/public/js/backend/property_form.js');
        
    }
    
}