<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 * @package    Eresus
 * @author     Ivanov Sergey (ivanovser@list.ru)
 */
abstract class Model_Res extends Model {
    
    const ROLE_CLASS = 'Model_User';
    
    const RESOURCE_ID = 'resource_id';
    const RESOURCE_TYPE = 'resource_type';
    
    const USER_ID = 'user_id';
    const ORGANIZER_ID = 'organizer_id';
    const TOWN_ID = 'town_id';
    
    const MODE = 'mode';
    
    const MODE_AUTHOR = 1;
    const MODE_READER = 2;
    
    public static $_mode_options = array(
        self::MODE_AUTHOR          => 'Автор',                
        self::MODE_READER          => 'Читатель'
    );     

    public function get_access_users()
    {
        if (isset($this->_properties['access_users'])) {
            if (is_string($this->_properties['access_users']))
            {
                // section ids were concatenated with GROUP_CONCAT
                $access_users = array();

                $ids = explode(',', $this->_properties['access_users']);
                foreach ($ids as $id)
                {
                    $access_users[$id] = TRUE;
                }

                $this->_properties['access_users'] = $access_users;
            }

            return $this->_properties['access_users'];
        }
        return NULL;
    }    
    
    public function get_access_organizers()
    {
        if (isset($this->_properties['access_organizers'])) {        
            if (is_string($this->_properties['access_organizers']))
            {
                // section ids were concatenated with GROUP_CONCAT
                $access_organizers = array();

                $ids = explode(',', $this->_properties['access_organizers']);
                foreach ($ids as $id)
                {
                    $access_organizers[$id] = TRUE;
                }

                $this->_properties['access_organizers'] = $access_organizers;
            }
            return $this->_properties['access_organizers'];
        }
        return NULL;
    }    
    
    public function get_access_towns()
    {   
        if (isset($this->_properties['access_towns'])) {
            if (is_string($this->_properties['access_towns']))
            {
                // section ids were concatenated with GROUP_CONCAT
                $access_towns = array();

                $ids = explode(',', $this->_properties['access_towns']);
                foreach ($ids as $id)
                {
                    $access_towns[$id] = TRUE;
                }

                $this->_properties['access_towns'] = $access_towns;
            }
            return $this->_properties['access_towns'];            
        }
        return NULL;
    }       
    public function get_user() {
        if (! isset($this->_properties['user'])) {
            $user = new Model_User();
            if (isset($this->user_id)) {
                $user->find($this->user_id);
            }
            $this->_properties['user'] = $user;            
        }
        return $this->_properties['user'];
    }
    
    public function validate(array $newvalues) {
        if (!isset($newvalues[self::USER_ID])) {
            if ( ! isset($this->{self::USER_ID}))
            {
                $this->error('Не указан пользователь!');
                return FALSE;
            }
            else
            {
                return TRUE;
            }            
        } else {
            if ($newvalues[self::USER_ID]=='')
            {
                $this->error('Не указан пользователь!');
                return FALSE;
            }            
        }
        return parent::validate($newvalues);
    }
    
    public function save($force_create = FALSE) {
        parent::save($force_create);
        
        $pk = $this->get_pk();

        $resources = Model::fly('Model_Resource')->delete_all_by(array(
            'resource_id' => $this->$pk,
            'resource_type'=> get_class($this)));            

        // author 
        $params = array();
        $params[self::RESOURCE_ID] = $this->$pk;
        $params[self::RESOURCE_TYPE] = get_class($this);
        $params[self::USER_ID] = $this->user_id;
        $params[self::MODE] = Model_Res::MODE_AUTHOR;

        $resource = new Model_Resource(); 
        $resource->values($params);
        $resource->save();

        // reader

        // access_users
        if (isset($this->access_users)) {
            $params = array();
            $params[self::RESOURCE_ID] = $this->$pk;
            $params[self::RESOURCE_TYPE] = get_class($this);
            $params[self::MODE] = Model_Res::MODE_READER;
            foreach ($this->access_users as $access_user => $allow) {
                if (!(int)$allow) continue;
                $params[self::USER_ID] = $access_user;
                $resource = new Model_Resource(); 
                $resource->values($params);
                $resource->save();
            }
        }

        // access_organizers
        if (isset($this->access_organizers)) {
            $params = array();
            $params[self::RESOURCE_ID] = $this->$pk;
            $params[self::RESOURCE_TYPE] = get_class($this);
            $params[self::MODE] = Model_Res::MODE_READER;
            foreach ($this->access_organizers as $access_organizer => $allow) {
                if (!(int)$allow) continue;
                $params[self::ORGANIZER_ID] = $access_organizer;
                $resource = new Model_Resource(); 
                $resource->values($params);
                $resource->save();
            }
        }
        // access_towns
        if (isset($this->access_towns)) { 
            $params = array();
            $params[self::RESOURCE_ID] = $this->$pk;
            $params[self::RESOURCE_TYPE] = get_class($this);
            $params[self::MODE] = Model_Res::MODE_READER;
            foreach ($this->access_towns as $access_town => $allow) {
                if (!(int)$allow) continue;
                $params[self::TOWN_ID] = $access_town;
                $resource = new Model_Resource(); 
                $resource->values($params);
                $resource->save();
            }            
        }            
    }
    
    public function delete(){
        $pk = $this->get_pk();
        
        $this->backup();
        
        parent::delete();        
 
        $resources = Model::fly('Model_Resource')->delete_all_by(array(
            'resource_id' => $this->previous()->$pk,
            'resource_type'=> get_class($this)));            
    }    
}