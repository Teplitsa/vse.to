<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_User extends Form_Frontend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
        $this->view_script = 'frontend/forms/user';
        
        // User is being created or updated?
        $creating = ((int)$this->model()->id == 0) ? TRUE : FALSE;

        $element =  new Form_Element_Hidden('group_id');
        $this->add_component($element);

        if ($creating) $element->value = Model_Group::EDITOR_GROUP_ID;
        else $element->value = $this->model()->group_id;
        
        // ----- E-mail
        $element = new Form_Element_Input('email',
            array('label' => 'E-mail'),
            array('maxlength' => 63)
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(63))
            ->add_validator(new Form_Validator_NotEmptyString())
            ->add_validator(new Form_Validator_Email());

        $ajax_uri = URL::uri_self(array('action' => 'validate', 'v_action' => Request::current()->action));
        $element
            ->add_validator(new Form_Validator_Ajax($ajax_uri));

        $this->add_component($element);
            if ($creating) {
                // ----- Password
                $element = new Form_Element_Password('password',
                    array('label' => 'Пароль', 'visible' => ! $creating)
                );
                $element
                    ->add_validator(new Form_Validator_NotEmptyString())
                    ->add_validator(new Form_Validator_StringLength(0, 255));
                $this->add_component($element); 

                // ----- Password confirmation
                $element = new Form_Element_Password('password2',
                    array('label' => 'Подтверждение', 'visible' => ! $creating),
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
                $this->add_component($element);
            } else {
                // ----- Password
                $element = new Form_Element_Password('password',
                    array('label' => 'Пароль', 'visible' => ! $creating)
                );
                $element
                    ->add_validator(new Form_Validator_NotEmptyString())
                    ->add_validator(new Form_Validator_StringLength(0, 255));
                $this->add_component($element);                 
            }
        // ----- Last_name
        $element = new Form_Element_Input('last_name', array('label' => 'Фамилия'), array('maxlength' => 63));
        $element
            ->add_filter(new Form_Filter_TrimCrop(63))
            ->add_validator(new Form_Validator_NotEmptyString());

        $this->add_component($element);

        // ----- First_name
        $element = new Form_Element_Input('first_name', array('label' => 'Имя'), array('maxlength' => 63));
        $element
            ->add_filter(new Form_Filter_TrimCrop(63))
            ->add_validator(new Form_Validator_NotEmptyString());

        $this->add_component($element);
                
        // -------------------------------------------------------------------------------------
        // organizer
        // -------------------------------------------------------------------------------------        
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
 
        // ----- tags

        $element = new Form_Element_Input('tags', array('label' => 'Мои интересы','id' => 'tag'), array('maxlength' => 255));
        $element->autocomplete_url = URL::to('frontend/tags', array('action' => 'ac_tag'));
        $element->autocomplete_chunk = Model_Tag::TAGS_DELIMITER; 
        $element
            ->add_filter(new Form_Filter_TrimCrop(255));
        $this->add_component($element);

        $element = new Form_Element_Checkbox_Enable('notify', array('label' => 'Информировать о новых интересных событиях'));                
        $this->add_component($element);
        
        // ----- town_id                
        $towns = Model_Town::towns();
        $element = new Form_Element_Select('town_id',
            $towns,
            array('label' => 'Город'),
            array('class' => 'w300px')    

        );
        $element
            ->add_validator(new Form_Validator_InArray(array_keys($towns)));

        $this->add_component($element);          
        
        // ----- User Photo
        $element = new Form_Element_File('file', array('label' => 'Загрузить фото'),array('placeholder' => 'Загрузить фото'));
        $this->add_component($element);

        if ($this->model()->id !== NULL)
        {
            $element = new Form_Element_Custom('images', array('label' => '', 'layout' => 'standart'));
            $element->value = Request::current()->get_controller('images')->widget_image('user', $this->model()->id, 'user');
            $this->add_component($element);
        }
        
        foreach ($this->model()->links as $link)
        {
            $element = new Form_Element_Input(
                $link->name,
                array('label' => $link->caption),
                array('label_class' => $link->name,'maxlength' => Model_Link::MAXLENGTH)
            );
            
            $this->add_component($element);
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
        $this->add_component($element);        
        
        // ----- Description
        $element = new Form_Element_Textarea('info', array('label' => 'О себе'));
        $element
            ->add_validator(new Form_Validator_NotEmptyString());
        $this->add_component($element);
        
        
        // ----- Form buttons
       if ($this->model()->id !== NULL)
        {                       
           $label = 'Изменить';
        } else {
            $label = 'Зарегистрироваться';
        }
        $button = new Form_Element_Button('submit_user',
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
                
        Layout::instance()->add_script(Modules::uri('acl') . '/public/js/backend/organizer_name.js');
        
        // Url for ajax requests to redraw product properties when main section is changed
        Layout::instance()->add_script(Modules::uri('tags') . '/public/js/backend/tag.js');
        
    }     
}

