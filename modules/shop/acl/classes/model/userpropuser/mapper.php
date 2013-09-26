<?php defined('SYSPATH') or die('No direct script access.');

class Model_UserPropUser_Mapper extends Model_Mapper {

    public function init()
    {
        parent::init();

        $this->add_column('userprop_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('user_id',  array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('active', array('Type' => 'boolean', 'Key' => 'INDEX'));
    }

    /**
     * Link userprop to given users
     * 
     * @param Model_UserProp $userprop
     * @param array $userpropusers
     */
    public function link_userprop_to_users(Model_UserProp $userprop, array $userpropusers)
    {                
        $this->delete_rows(DB::where('userprop_id', '=', (int) $userprop->id));
        foreach ($userpropusers as $userpropuser)
        {
            $userpropuser['userprop_id'] = $userprop->id;
            $this->insert($userpropuser);
        }    }

    /**
     * Link user to given userprops
     *
     * @param Model_User $user
     * @param array $userpropusers
     */
    public function link_user_to_userprops(Model_User $user, array $userpropusers)
    {
        $this->delete_rows(DB::where('user_id', '=', (int) $user->id));
        foreach ($userpropusers as $userpropuser)
        {
            $userpropuser['user_id'] = $user->id;

            $this->insert($userpropuser);
        }
    }

    /**
     * Find all userprop-user infos by given user
     *
     * @param  Model_UserPropUser $userpropus
     * @param  Model_User $user
     * @param  array $params
     * @return Models
     */
    public function find_all_by_user(Model_UserPropUser $userpropus, Model_User $user, array $params = NULL)
    {
        $userpropus_table  = $this->table_name();
        $userprop_table = Model_Mapper::factory('Model_UserProp_Mapper')->table_name();
        $user_table  = Model_Mapper::factory('Model_User_Mapper')->table_name();

        $columns = isset($params['columns'])
                        ? $params['columns']
                        : array(
                            array("$userprop_table.id", 'userprop_id'),
                            "$userprop_table.caption",
                            "$userprop_table.system",

                            "$userpropus_table.active",
                        );

        $query = DB::select_array($columns)
            ->from($userprop_table)
            ->join($userpropus_table, 'LEFT')
                ->on("$userpropus_table.userprop_id", '=', "$userprop_table.id")
                ->on("$userpropus_table.user_id", '=', DB::expr((int) $user->id));

        $data = parent::select(NULL, $params, $query);

        // Add user properties
        foreach ($data as & $userprops)
        {
            $userprops['user_id']      = $user->id;
            $userprops['user_login'] = $user->email;
        }

        return new Models(get_class($userpropus), $data);
    }

    /**
     * Find all userprop-user infos by given userprop
     *
     * @param  Model_UserPropUser $userpropus
     * @param  Model_UserProp $userprop
     * @param  array $params
     * @return Models
     */
    public function find_all_by_userprop(Model_UserPropUser $userpropus, Model_UserProp $userprop, array $params = NULL)
    {
        $userpropus_table  = $this->table_name();
        $userprop_table = Model_Mapper::factory('Model_UserProp_Mapper')->table_name();
        $user_table  = Model_Mapper::factory('Model_User_Mapper')->table_name();

        $columns = isset($params['columns'])
                        ? $params['columns']
                        : array(
                            array("$user_table.id", 'user_id'),
                            array("$user_table.login", 'user_login'),

                            "$userpropus_table.active"
                        );

        $query = DB::select_array($columns)
            ->from($user_table)
            ->join($userpropus_table, 'LEFT')
                ->on("$userpropus_table.user_id", '=', "$user_table.id")
                ->on("$userpropus_table.userprop_id", '=', DB::expr((int) $userprop->id));

        $data = parent::select(array(), $params, $query);

        // Add property properties
        foreach ($data as & $userprops)
        {
            $userprops['userprop_id'] = $userprop->id;
            $userprops['caption']     = $userprop->caption;
            $userprops['system']      = $userprop->system;
        }
        
        return new Models(get_class($userpropus), $data);
    }    
}