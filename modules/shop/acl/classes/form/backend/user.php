<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_User extends Form_Backend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class

        // User is being created or updated?
        $creating = ((int)$this->model()->id == 0) ? TRUE : FALSE;
        
        $this->attribute('class', "lb130px");
        $this->layout = 'wide';            
        
        // ----- General tab
        $tab = new Form_Fieldset_Tab('general_tab', array('label' => 'Основные свойства'));
        $this->add_component($tab);

        // 2-column layout
        $cols = new Form_Fieldset_Columns('cols', array('column_classes' => array(1 => 'w55per')));
        $tab->add_component($cols);
        
        // ----- Group
        // Obtain a list of groups
        $groups = Model::fly('Model_Group')->find_all();

        $options = array();
        foreach ($groups as $group)
        {
            $options[$group->id] = $group->name;
        }

        $element =  new Form_Element_Select('group_id', $options,  array('label' => 'Группа', 'required' => TRUE));
        $element
            ->add_validator(new Form_Validator_InArray(
                array_keys($options),
                array(Form_Validator_InArray::NOT_FOUND => 'Указана несуществующая группа!')
            ));
        $cols->add_component($element);
        // ----- Active
        $cols->add_component(new Form_Element_Checkbox('active', array('label' => 'Активность')));

        // ----- Login and password
        $fieldset = new Form_Fieldset('email_and_password', array('label' => 'E-mail и пароль'));
        $cols->add_component($fieldset);

            // ----- Login
            $element = new Form_Element_Input('email',
                array('label' => 'E-mail', 'required' => TRUE),
                array('maxlength' => 63)
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(63))
                ->add_validator(new Form_Validator_NotEmptyString());

            $ajax_uri = URL::uri_self(array('action' => 'validate', 'v_action' => Request::current()->action));
            $element
                ->add_validator(new Form_Validator_Ajax($ajax_uri));

            $fieldset->add_component($element);

            // ----- Password
            $element = new Form_Element_Password('password',
                array('label' => 'Пароль', 'required' => $creating, 'ignored' => ! $creating),
                array('maxlength' => 255)
            );
            $element
                ->add_validator(new Form_Validator_NotEmptyString())
                ->add_validator(new Form_Validator_StringLength(0, 255));
            $fieldset->add_component($element);

            // ----- Password confirmation
            $element = new Form_Element_Password('password2',
                array('label' => 'Подтверждение', 'required' => $creating, 'ignored' => ! $creating),
                array('maxlength' => 255)
            );
            $element
                ->add_validator(new Form_Validator_NotEmptyString(array(
                        Form_Validator_NotEmptyString::EMPTY_STRING => 'Вы не указали подтверждение пароля!',
                )))
                ->add_validator(new Form_Validator_EqualTo(
                    'password',
                    array(
                        Form_Validator_EqualTo::NOT_EQUAL => 'Пароль и подтверждение не совпадают!',
                    )
                ));
            $fieldset->add_component($element);

            if ( ! $creating)
            {
                $element = new Form_Element_Checkbox_Enable('change_credentials', array('label' => 'Сменить пароль'));                
                $element->dep_elements = array('password', 'password2');
                $fieldset->add_component($element);
            }

            // ----- Personal data
            $fieldset = new Form_Fieldset('personal_data', array('label' => 'Личные данные'));
            $cols->add_component($fieldset);

                $cols1 = new Form_Fieldset_Columns('name');
                $fieldset->add_component($cols1);

                    // ----- Last_name
                    $element = new Form_Element_Input('last_name', array('label' => 'Фамилия', 'required' => TRUE), array('maxlength' => 63));
                    $element
                        ->add_filter(new Form_Filter_TrimCrop(63))
                        ->add_validator(new Form_Validator_NotEmptyString());
                    
                    $cols1->add_component($element, 1);

                    // ----- First_name
                    $element = new Form_Element_Input('first_name', array('label' => 'Имя', 'required' => TRUE), array('maxlength' => 63));
                    $element
                        ->add_filter(new Form_Filter_TrimCrop(63))
                        ->add_validator(new Form_Validator_NotEmptyString());
                    $cols1->add_component($element, 2);

                // ----- Middle_name
                $element = new Form_Element_Input('middle_name', array('label' => 'Отчество'), array('maxlength' => 63));
                $element
                    ->add_filter(new Form_Filter_TrimCrop(63));
                $fieldset->add_component($element);

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
                        array('label' => 'Организация', 'layout' => 'wide','id' => 'organizer_name','required' => true));
                $element->autocomplete_url = URL::to('backend/acl/organizers', array('action' => 'ac_organizer_name'));
                $element
                    ->add_validator(new Form_Validator_NotEmptyString());
                
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

                // ----- Position
                $element = new Form_Element_Input('position', array('label' => 'Должность'), array('maxlength' => 63));
                $element
                    ->add_filter(new Form_Filter_TrimCrop(63));
                $fieldset->add_component($element);
                
                // ----- phone
                $element = new Form_Element_Input('phone', array('label' => 'Телефон'), array('maxlength' => 63));
                $element
                    ->add_filter(new Form_Filter_TrimCrop(63));
                $fieldset->add_component($element);
                
                // ----- town_id                
                $towns = Model_Town::towns();
                $element = new Form_Element_Select('town_id',
                    $towns,
                    array('label' => 'Город','required' => true),
                    array('class' => 'w300px')    
                        
                );
                $element
                    ->add_validator(new Form_Validator_InArray(array_keys($towns)));
                
                $fieldset->add_component($element);                

                // ----- tags
                
                $element = new Form_Element_Input('tags', array('label' => 'Мои интересы','id' => 'tag'), array('maxlength' => 255));
                $element->autocomplete_url = URL::to('backend/tags', array('action' => 'ac_tag'));
                $element->autocomplete_chunk = Model_Tag::TAGS_DELIMITER; 
                $element
                    ->add_filter(new Form_Filter_TrimCrop(255));
                $fieldset->add_component($element);

                $element = new Form_Element_Checkbox_Enable('notify', array('label' => 'Информировать о новых событиях'));                
                $fieldset->add_component($element);
                
                
        // ----- Userprops                
        if (!$creating && count($this->model()->userprops)) {
            $fieldset = new Form_Fieldset('userprops', array('label' => 'Параметры учетной записи'));
            $cols->add_component($fieldset);
            
            foreach ($this->model()->userprops as $userprop)
            {
                switch ($userprop->type)
                {
                    case Model_UserProp::TYPE_TEXT:
                        $element = new Form_Element_Input(
                            $userprop->name,
                            array('label' => $userprop->caption),
                            array('maxlength' => Model_UserProp::MAXLENGTH)
                        );
                        $element
                            ->add_filter(new Form_Filter_TrimCrop(Model_UserProp::MAXLENGTH));
                        $fieldset->add_component($element);
                        break;

                    case Model_UserProp::TYPE_SELECT:
                        $options = array('' => '---');
                        foreach ($userprop->options as $option)
                        {
                            $options[$option] = $option;
                        }

                        $element = new Form_Element_Select(
                            $userprop->name,
                            $options,
                            array('label' => $userprop->caption)
                        );
                        $element
                            ->add_validator(new Form_Validator_InArray(array_keys($options)));
                        $fieldset->add_component($element);
                        break;
                }
            }
        }
        // ----- User Photo
        if ($this->model()->id !== NULL)
        {
            $element = new Form_Element_Custom('images', array('label' => '', 'layout' => 'standart'));
            $element->value = Request::current()->get_controller('images')->widget_images('user', $this->model()->id, 'user');
            $cols->add_component($element, 2);
        }
        
        // ----- links
        $fieldset = new Form_Fieldset('links', array('label' => 'Внешние ссылки','class' => 'links'));
        $cols->add_component($fieldset,2);

        foreach ($this->model()->links as $link)
        {
            $element = new Form_Element_Input(
                $link->name,
                array('label' => $link->caption),
                array('label_class' => $link->name,'maxlength' => Model_Link::MAXLENGTH)
            );
            
            $fieldset->add_component($element);
        }
        
        // ----- Personal Webpages
        if ($this->model()->id) {
            $options_count = count($this->model()->webpages);
        }
        if (!isset($options_count) || $options_count==0) $options_count = 1;
        
        $element = new Form_Element_Options("webpages", 
            array('label' => 'Сайты', 'options_count' => $options_count,'options_count_param' => 'options_count','option_caption' => 'добавить ссылку'),
            array('maxlength' => Model_User::LINKS_LENGTH)
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(Model_User::LINKS_LENGTH));
        $cols->add_component($element,2);
        
        // ----- Description tab
        $tab = new Form_Fieldset_Tab('info_tab', array('label' => 'О себе'));
        $this->add_component($tab);


            // ----- Description
            $tab->add_component(new Form_Element_Wysiwyg('info', array('label' => 'О себе')));
        
        
        // ----- Address_data
                /*
        $fieldset = new Form_Fieldset('address_data', array('label' => 'Адрес'));
        $this->add_component($fieldset);

            $cols = new Form_Fieldset_Columns('city_and_postcode', array('column_classes' => array(2 => 'w40per')));
            $fieldset->add_component($cols);

                // ----- City
                $element = new Form_Element_Input('city', array('label' => 'Город'), array('maxlength' => 63));
                $element
                    ->add_filter(new Form_Filter_TrimCrop(63));
                $element->autocomplete_url = URL::to('backend/countries', array('action' => 'ac_city'));
                $cols->add_component($element, 1);

                // ----- Postcode
                $element = new Form_Element_Input('postcode', array('label' => 'Индекс'), array('maxlength' => 63));
                $element
                    ->add_filter(new Form_Filter_TrimCrop(63));
                $element->autocomplete_url = URL::to('backend/countries', array('action' => 'ac_postcode'));
                $cols->add_component($element, 2);

            // ----- Region_id
            // Obtain a list of regions
            $regions = Model::fly('Model_Region')->find_all(array('order_by' => 'name', 'desc' => FALSE));

            $options = array();
            foreach ($regions as $region)
            {
                $options[$region->id] = UTF8::ucfirst($region->name);
            }

            $element =  new Form_Element_Select('region_id', $options,  array('label' => 'Регион'));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $fieldset->add_component($element);

            // ----- zone_id
            // Obtain a list of ALL zones
            $zones = Model::fly('Model_CourierZone')->find_all(array('order_by' => 'position', 'desc' => FALSE));

            $options = array(0 => '---');
            foreach ($zones as $zone)
            {
                $options[$zone->id] = $zone->name;
            }

            $element =  new Form_Element_Select('zone_id', $options,  array('label' => 'Зона доставки (Москва)'));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $fieldset->add_component($element);

            // ----- Address
            $element = new Form_Element_Textarea('address', array('label' => 'Адрес'), array('rows' => 3));
            $element
                ->add_filter(new Form_Filter_TrimCrop(511));
            $fieldset->add_component($element);
                 * 
                 */
        
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
    
    /**
     * Add javascripts
     */
    public function render_js()
    {
        parent::render_js();
        // ----- Install javascripts
        
        // Url for ajax requests to redraw product properties when main section is changed
        Layout::instance()->add_script(Modules::uri('acl') . '/public/js/backend/organizer_name.js');

        // Url for ajax requests to redraw product properties when main section is changed
        Layout::instance()->add_script(Modules::uri('tags') . '/public/js/backend/tag.js');
        
    }    
}
