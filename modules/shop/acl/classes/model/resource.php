<?php defined('SYSPATH') or die('No direct script access.');

class Model_Resource extends Model
{    
    /**
     * Save section and link it to selected properties
     *
     * @param boolean $force_create
     * @param boolen  $link_properties
     * @param boolean $update_stats
     */
//    public function save($force_create = FALSE)
//    {
//        if (!$this->role_id) {
//            $this->role_id = Model_User::current()->id;
//        }
//        parent::save($force_create);
//    }
    
    public function get_resource(){
        if ( ! isset($this->_properties['resource']))
        {
            $resource_class = $this->resource_type;
            if (class_exists($resource_class)) {
                $resource = new $resource_class();        
                $resource = $resource->find($this->resource_id);
                $this->_properties['resource'] = $resource;
            }
        }
        return $this->_properties['resource'];
        
    }
    
//    public function get_role(){
//        if ( ! isset($this->_properties['role']))
//        {
//            $role_class = $this->role_type;
//            if (class_exists($role_class)) {
//                $role = new $role_class();        
//                $role = $role->find($this->role_id);
//                $this->_properties['role'] = $role;
//            }
//        }
//        return $this->_properties['role'];
//    }
}