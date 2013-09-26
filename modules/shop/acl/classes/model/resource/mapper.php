<?php defined('SYSPATH') or die('No direct script access.');

class Model_Resource_Mapper extends Model_Mapper
{    
    public function init()
    {
        $this->add_column('id',         array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('resource_type', array('Type' => 'varchar(15)',  'Key' => 'INDEX'));
        $this->add_column('resource_id',   array('Type' => 'int unsigned', 'Key' => 'INDEX'));  
        $this->add_column('user_id',   array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('organizer_id',   array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('town_id',   array('Type' => 'int unsigned', 'Key' => 'INDEX'));        
        $this->add_column('mode',   array('Type' => 'int unsigned', 'Key' => 'INDEX'));        
    }
   

    /**
     *
     * @param  Model $model
     * @param  Model $resource
     * @param  array $params
     * @return Model
     */
//    public function find_all_by_role(Model $model, Model $role, array $params = NULL)
//    {
//        $res_pk = $role->get_pk();
//        $condition['role_type'] = get_class($role);
//        $condition['role_id'] = $role->$res_pk;
//        
//        return parent::find_by($model, $condition, $params);
//    }    
}