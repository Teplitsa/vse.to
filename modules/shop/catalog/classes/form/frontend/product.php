<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_Product extends Form_Frontend
{
    /**
     * Initialize form fields
     */
    public function init()
    {
        
        // Set HTML class
        $this->view_script = 'frontend/forms/product';
        
        // ----- Caption
        $element = new Form_Element_Input('caption',
            array('label' => 'Название'),
            array('maxlength' => 255)
        );

        $element
            ->add_filter(new Form_Filter_TrimCrop(255))
            ->add_validator(new Form_Validator_NotEmptyString());
        $element->config_entry = 'input_front';
        $this->add_component($element);
        
        // -------------------------------------------------------------------------------------
        // lecturer
        // -------------------------------------------------------------------------------------        
        // control hidden field
        $control_element = new Form_Element_Hidden('lecturer_id',array('id' => 'lecturer_id'));

        $this->add_component($control_element);
        
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
                array('label' => 'Лектор', 'layout' => 'wide','id' => 'lecturer_name'));
        $element->autocomplete_url = URL::to('frontend/acl/lecturers', array('action' => 'ac_lecturer_name'));            
        Layout::instance()->add_style(Modules::uri('acl') . '/public/css/frontend/acl.css');
        $this->add_component($element);

        $lecturer = new Model_Lecturer();
        $lecturer->find($lecturer_id);

        $lecturer_name = ($lecturer->id !== NULL) ? $lecturer->name : '--- лектор не указан ---';

        if ($element->value === FALSE) {   
            $element->value = $lecturer_name;                
        } else {
            if ($element->value !== $lecturer_name) $control_element->set_value(NULL); 
        }
                
        // -------------------------------------------------------------------------------------
        // organizer
        // -------------------------------------------------------------------------------------        
        $control_element = new Form_Element_Hidden('organizer_id',array('id' => 'organizer_id'));

        $this->add_component($control_element);

        if ($control_element->value !== FALSE)
        {
            $organizer_id = (int) $control_element->value;
        }
        elseif ($this->model()->organizer_id)
        {
            $organizer_id = (int) $this->model()->organizer_id;
        }
        elseif (Model_User::current()->organizer_id)
        {
            $organizer_id = (int) Model_User::current()->organizer_id;
        }
        
        // ----- organizer_name
        // input field with autocomplete ajax
        $element = new Form_Element_Input('organizer_name',
                array('label' => 'Организация', 'layout' => 'wide','id' => 'organizer_name'));
        $element->autocomplete_url = URL::to('frontend/acl/organizers', array('action' => 'ac_organizer_name'));
        Layout::instance()->add_style(Modules::uri('acl') . '/public/css/frontend/acl.css');

        $this->add_component($element);

        $organizer = new Model_Organizer();
        $organizer->find($organizer_id);

        $organizer_name = ($organizer->id !== NULL) ? $organizer->name : '';

        if ($element->value === FALSE) {   
            $element->value = $organizer_name;                
        } else {
            if ($element->value !== $organizer_name) $control_element->set_value(NULL); 
        }            
        
        // ----- Form buttons
        $button = new Form_Element_Button('add_organizer',
                array('label' => 'Добавить'),
                array('class' => 'button button-modal')
        );
        $this->add_component($button);          

        // ----- datetime
        $element = new Form_Element_DateTimeSimple('datetime', array('label' => 'Дата проведения', 'required' => TRUE),array('class' => 'w300px','placeholder' => 'dd-mm-yyyy hh:mm'));
        $element->value_format = Model_Product::$date_as_timestamp ? 'timestamp' : 'datetime';
        $element
            ->add_validator(new Form_Validator_DateTimeSimple());
        $this->add_component($element);

        // ----- Duration
        $options = Model_Product::$_duration_options;

        $element = new Form_Element_Select('duration', $options, array('label' => 'Длительность'),array('class' => 'w300px'));
        $element
            ->add_validator(new Form_Validator_InArray(array_keys($options)));
        $this->add_component($element);

        // ----- Place
        $places = Model::fly('Model_Place')->find_all_by_town_id(Model_User::current()->town->id);
        $places_arr = array();
        foreach ($places as $place) {
            $places_arr[$place->id] = $place->name;
        }
        
        $element = new Form_Element_Select('place_id',
            $places_arr,
            array('label' => 'Площадка','required' => TRUE),
            array('placeholder' => 'Выберите площадку')
        );
        $this->add_component($element);
        
        // ----- theme
        $options = Model_Product::$_theme_options;

        $element = new Form_Element_Select('theme', $options, array('label' => 'Тема'),array('class' => 'w300px'));
        $element
            ->add_validator(new Form_Validator_InArray(array_keys($options)));
        $this->add_component($element);


        // ----- format
        $options = Model_Product::$_format_options;

        $element = new Form_Element_Select('format', $options, array('label' => 'Формат'),array('class' => 'w300px'));
        $element
            ->add_validator(new Form_Validator_InArray(array_keys($options)));
        $this->add_component($element);

        // ----- tags

        $element = new Form_Element_Input('tags', array('label' => 'Теги','id' => 'tag'), array('maxlength' => 255));
        $element->autocomplete_url = URL::to('frontend/tags', array('action' => 'ac_tag'));
        $element->autocomplete_chunk = Model_Tag::TAGS_DELIMITER; 
        $element
            ->add_filter(new Form_Filter_TrimCrop(255));
        $this->add_component($element);

        // ----- Description
        
        $element = new Form_Element_Textarea('description', array('label' => 'О событии'));
        $element
            ->add_validator(new Form_Validator_NotEmptyString());
        $this->add_component($element);

        // ----- Product images
//        if ($this->model()->id !== NULL)
//        {
//            $element = new Form_Element_Custom('images', array('label' => '', 'layout' => 'standart'));
//            $element->value = Request::current()->get_controller('images')->widget_images('product', $this->model()->id, 'product');
//            $this->add_component($element, 2);
//        }
        // ----- File
        $element = new Form_Element_File('file', array('label' => 'Загрузить фото'),array('placeholder' => 'Загрузить фото'));
        //$element->add_validator(new Form_Validator_File());
        $this->add_component($element);
        
        if ($this->model()->id !== NULL)
        {
            $element = new Form_Element_Custom('images', array('label' => '', 'layout' => 'standart'));
            $element->value = Request::current()->get_controller('images')->widget_image('product', $this->model()->id, 'product');
            $this->add_component($element);
        }              
        
        // ----- interact
        $options = Model_Product::$_interact_options;

        $element = new Form_Element_RadioSelect('interact', $options, array('label' => 'Интерактивность','required' => TRUE),array('class' => 'w300px'));
        $element
            ->add_validator(new Form_Validator_InArray(array_keys($options)));
        $element->set_disabled(true);
        $element->config_entry = 'checkselect_spec';

        $this->add_component($element);

        // ----- numviews
        $options = Model_Product::$_numviews_options;

        $element = new Form_Element_Select('numviews', $options, array('label' => 'Количество телемостов'),array('class' => 'w300px'));
        $element
            ->add_validator(new Form_Validator_InArray(array_keys($options)));
        $this->add_component($element);

        // ----- choalg
        $options = Model_Product::$_choalg_options;

        $element = new Form_Element_RadioSelect('choalg', $options, array('label' => 'Кто выбирает'),array('class' => 'w300px'));
        $element
            ->add_validator(new Form_Validator_InArray(array_keys($options)));
        $this->add_component($element);            

        // ----- Require
        $this->add_component(new Form_Element_Textarea('require', array('label' => 'Требования к площадке, аудитории и др.')));            

//        // ----- access
//        $fieldset = new Form_Fieldset('access', array('label' => 'Кто может подать заявку'));
//        $cols->add_component($fieldset); 
//        
//        $element->add_validator(new Form_Validator_InArray(array_keys($places_arr)));
//        $this->add_component($element);        
                    
            // ----- Form buttons
        
        
        // ----- Form buttons
       if ($this->model()->id !== NULL)
        { 
            // Button to select place
            $button = new Form_Element_LinkButton('cancel_product',
                    array('label' => 'Отменить'),
                    array('class' => 'button')
            );
            $button->url   = URL::to('frontend/catalog/products/control', array('action' => 'cancel','id' => $this->model()->id), TRUE);
            $this->add_component($button);
                      
           $label = 'Сохранить';
        } else {
            $label = 'Добавить';
        }
        $button = new Form_Element_Button('submit_product',
                array('label' => $label),
                array('class' => 'button button-red')
        );
        $this->add_component($button);

    }
    
    /**
     * Add javascripts
     */
    public function render_js()
    {
        parent::render_js();
        
        // ----- Install javascripts
                
        Layout::instance()->add_script(Modules::uri('acl') . '/public/js/backend/lecturer_name.js');       
        Layout::instance()->add_script(Modules::uri('acl') . '/public/js/backend/organizer_name.js'); 
        Layout::instance()->add_script(Modules::uri('tags') . '/public/js/backend/tag.js');        
    }    
}