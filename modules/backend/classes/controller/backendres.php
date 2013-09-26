<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_BackendRES extends Controller_BackendCRUD
{
    protected $_resource_class = 'Model_Resource';
    protected $_role_class = 'Model_User';
    
    protected $_resource_id = 'resource_id';
    protected $_resource_type = 'resource_type';
    
    protected $_user_id = 'user_id';
    protected $_organizer_id = 'organizer_id';
    protected $_town_id = 'town_id';
    protected $_mode = 'mode';
    
    protected $_access_users = 'access_users';
    protected $_access_organizers = 'access_organizers';
    protected $_access_towns = 'access_towns';
    
        
    protected function _prepare_form($action, Model $model, array $params = NULL) {

        $form = parent::_prepare_form($action, $model, $params);

        if (in_array($action, array('create','update'))) {
            
            // ----- user_id
            // Store user_id in hidden field
            $user_id = NULL;

            $element = new Form_Element_Hidden('user_id');
            $form->add_component($element);

            if ($element->value !== FALSE)
            {
                $user_id = (int) $element->value;
            }
            
            // user_id can be changed via url parameter
            $req_user_id = Request::instance()->param('user_id', NULL);

            if ($req_user_id !== NULL)
            {
                $req_user_id = (int) $req_user_id;
                if (
                    ($req_user_id == 0)
                 || ($req_user_id > 0 && Model::fly('Model_User')->exists_by_id($req_user_id))
                )
                {
                    $user_id = $req_user_id;
                }
            }
            
            $user_name = ($user_id !== NULL) ? Model::fly('Model_User')->find($user_id)->name : '';

            $element = new Form_Element_Input('user_name',
                    array('label' => 'Пользователь', 'disabled' => TRUE, 'layout' => 'wide','required' => TRUE),
                    array('class' => 'w150px')
            );
            $element->value = $user_name;

            // Button to select user
            $button = new Form_Element_LinkButton('select_user_button',
                    array('label' => 'Выбрать', 'render' => FALSE),
                    array('class' => 'button_select_user open_window dim600x500')
            );
            $button->url   = URL::to('backend/acl', array('action' => 'user_select','group_id' => Model_Group::EDITOR_GROUP_ID), TRUE);
            $form->add_component($button);

            $element->append = '&nbsp;&nbsp;' . $button->render();

            $fieldset = $form->find_component('user');
            if ($fieldset) {
                $fieldset->add_component($element);
            } else {
                $form->add_component($element);            
            } 

            //////////////////////////////////////////////////////////////////
            //
            // Button to select access towns for resource
            $fieldset = $form->find_component('access');
            if (!$fieldset) {
                return $form;            
            } 
            $element = new Form_Element_Checkbox_Enable('all', array('label' => 'Все редакторы'));                
            $fieldset->add_component($element);
            
            $history = URL::uri_to('backend/area/towns', array('action' => 'towns_select'), TRUE);
            
            $towns_select_url = URL::to('backend/area/towns', array(
                                                'action' => 'select',
                                                'history' => $history
                                            ), TRUE);

            $button = new Form_Element_LinkButton('select_towns_button',
                    array('label' => 'Выбрать','render' => FALSE),
                    array('class' => 'button_select_towns open_window')
            );
            $button->url   = $towns_select_url;
            $fieldset->add_component($button);

            $checkbox = new Form_Element_Checkbox_Enable('from_town', 
                    array('label' => 'Из городов','layout' => 'wide'),array('visible' => FALSE));                
            $checkbox->dep_elements = array('select_towns_button');
            $fieldset->add_component($checkbox);
            
            $checkbox->append = '&nbsp;&nbsp;' . $button->render();
            
            $access_towns_fieldset = new Form_Fieldset('access_towns');
            $access_towns_fieldset->config_entry = 'fieldset_inline';
            $access_towns_fieldset->layout = 'standart';
            $fieldset->add_component($access_towns_fieldset);
            
            $towns = Model_Town::towns();
            // ----- Access Towns
            if (Request::current()->param('access_town_ids') != '')
            {
                // Are town ids explicitly specified in the uri?
                $town_ids = explode('_', Request::current()->param('access_town_ids'));
            }
            
            if ($form->is_submitted())
            {
                $access_towns = $form->get_post_data('access_towns');
                if ( ! is_array($access_towns))
                {
                    $access_towns = array();
                }
            }
            elseif (isset($town_ids))
            {                
                // Section ids are explicitly specified in the uri
                $access_towns = array();
                foreach ($town_ids as $town_id)
                {
                    if (isset($towns[$town_id]))
                    {
                        $access_towns[$town_id] = 1;
                    }
                }
            } else {
                $access_towns = $model->access_towns;
            }
            
            if ( ! empty($access_towns))
            {
                $checkbox->set_value(1);
                
                foreach ($access_towns as $town_id => $selected)
                {
                    if (isset($towns[$town_id]))
                    {
                        $town = $towns[$town_id];

                        $element = new Form_Element_Checkbox('access_towns['.$town_id.']', array('label' => $town));
                        $element->default_value = $selected;
                        $element->layout = 'default';
                        $access_towns_fieldset->add_component($element);
                    }
                }
            }
                  

         // Button to select access organizers for product
            $history = URL::uri_to('backend/acl/organizers', array('action' => 'organizers_select'), TRUE);

            $organizers_select_url = URL::to('backend/acl/organizers', array(
                                                'action' => 'select',
                                                'history' => $history
                                            ), TRUE);

            $button = new Form_Element_LinkButton('select_organizers_button',
                    array('label' => 'Выбрать','render' => FALSE),
                    array('class' => 'button_select_organizers open_window')
            );
            $button->url   = $organizers_select_url;
            $fieldset->add_component($button);

            $checkbox = new Form_Element_Checkbox_Enable('from_organizer', 
                    array('label' => 'Из организаций','layout' => 'wide'),array('visible' => FALSE));                
            $checkbox->dep_elements = array('select_organizers_button');
            $fieldset->add_component($checkbox);
            
            $checkbox->append = '&nbsp;&nbsp;' . $button->render();
            
            $access_organizers_fieldset = new Form_Fieldset('access_organizers');
            $access_organizers_fieldset->config_entry = 'fieldset_inline';
            $access_organizers_fieldset->layout = 'standart';
            $fieldset->add_component($access_organizers_fieldset);

            // Obtain a list of selected sections in the following precedence
            // 1. From $_POST
            // 2. From model
            
            $organizers = Model_Organizer::organizers();
            // ----- Access Organizers

            if (Request::current()->param('access_organizer_ids') != '')
            {
                // Are organizerr ids explicitly specified in the uri?
                $organizer_ids = explode('_', Request::current()->param('access_organizer_ids'));
            }           
            
            if ($form->is_submitted())
            {
                $access_organizers = $form->get_post_data('access_organizers');
                if ( ! is_array($access_organizers))
                {
                    $access_organizers = array();
                }
            }
            elseif (isset($organizer_ids))
            {
                // Section ids are explicitly specified in the uri
                $access_organizers = array();
                foreach ($organizer_ids as $organizer_id)
                {
                    if (isset($organizers[$organizer_id]))
                    {
                        $access_organizers[$organizer_id] = 1;
                    }
                }
            } else {
                $access_organizers = $model->access_organizers;
            }
            
            if ( ! empty($access_organizers))
            {
                $checkbox->set_value(1);
                
                foreach ($access_organizers as $organizer_id => $selected)
                {
                    if (isset($organizers[$organizer_id]))
                    {
                        $organizer = $organizers[$organizer_id];

                        $element = new Form_Element_Checkbox('access_organizers['.$organizer_id.']', array('label' => $organizer));
                        $element->default_value = $selected;
                        $element->layout = 'default';
                        $access_organizers_fieldset->add_component($element);
                    }
                }
            }
            
         // Button to select access users for product
            $history = URL::uri_to('backend/acl/users', array('action' => 'users_select'), TRUE);

            $users_select_url = URL::to('backend/acl/users', array(
                                                'action' => 'select',
                                                'history' => $history
                                            ), TRUE);

            $button = new Form_Element_LinkButton('select_users_button',
                    array('label' => 'Выбрать','render' => FALSE),
                    array('class' => 'button_select_users open_window')
            );
            $button->url   = $users_select_url;
            $fieldset->add_component($button);

            $checkbox = new Form_Element_Checkbox_Enable('from_user', 
                    array('label' => 'Из редакторов','layout' => 'wide'),array('visible' => FALSE));                
            $checkbox->dep_elements = array('select_users_button');
            $fieldset->add_component($checkbox);
            
            $checkbox->append = '&nbsp;&nbsp;' . $button->render();
            
            $access_users_fieldset = new Form_Fieldset('access_users');
            $access_users_fieldset->config_entry = 'fieldset_inline';
            $access_users_fieldset->layout = 'standart';
            $fieldset->add_component($access_users_fieldset);

            // Obtain a list of selected sections in the following precedence
            // 1. From $_POST
            // 2. From model
            
            // ----- Access Users
            $users = Model_User::users(Model_Group::EDITOR_GROUP_ID);

            if (Request::current()->param('access_user_ids') != '')
            {
                // Are user ids explicitly specified in the uri?
                $user_ids = explode('_', Request::current()->param('access_user_ids'));
            }           
            
            if ($form->is_submitted())
            {
                $access_users = $form->get_post_data('access_users');
                if ( ! is_array($access_users))
                {
                    $access_users = array();
                }
            }
            elseif (isset($user_ids))
            {
                // Section ids are explicitly specified in the uri
                $access_users = array();
                foreach ($user_ids as $user_id)
                {
                    if (isset($users[$user_id]))
                    {
                        $access_users[$user_id] = 1;
                    }
                }
            } else {
                $access_users = $model->access_users;
            }                
            
            
            if ( ! empty($access_users))
            {
                $checkbox->set_value(1);
                
                foreach ($access_users as $user_id => $selected)
                {
                    if (isset($users[$user_id]))
                    {
                        $user = $users[$user_id];

                        $element = new Form_Element_Checkbox('access_users['.$user_id.']', array('label' => $user));
                        $element->default_value = $selected;
                        $element->layout = 'default';
                        $access_users_fieldset->add_component($element);
                    }
                }
            }
            
        }
        
        return $form;
    }    
}
