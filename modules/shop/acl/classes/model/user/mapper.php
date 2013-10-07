<?php defined('SYSPATH') or die('No direct script access.');

class Model_User_Mapper extends Model_Mapper {

    public function init()
    {
        $this->add_column('id', array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('system', array('Type' => 'boolean'));

        $this->add_column('group_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('email',   array('Type' => 'varchar(63)'));

        //$this->add_column('login', array('Type' => 'varchar(31)', 'Key' => 'UNIQUE'));
        $this->add_column('hash', array('Type' => 'char(45)'));

        $this->add_column('first_name',  array('Type' => 'varchar(63)'));
        $this->add_column('last_name',   array('Type' => 'varchar(63)'));
        $this->add_column('middle_name', array('Type' => 'varchar(63)'));

        $this->add_column('organizer_id', array('Type' => 'int unsigned'));
        $this->add_column('organizer_name', array('Type' => 'varchar(127)'));
        $this->add_column('position', array('Type' => 'varchar(63)'));
        $this->add_column('webpages', array('Type' => 'array'));

        $this->add_column('town_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));        
        $this->add_column('phone',   array('Type' => 'varchar(63)'));
        
        $this->add_column('info', array('Type' => 'text'));
        $this->add_column('recovery_link', array('Type' => 'text'));        

        //$this->add_column('country_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        //$this->add_column('region_id',  array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        //$this->add_column('postcode',   array('Type' => 'varchar(63)'));
        //$this->add_column('city',       array('Type' => 'varchar(63)'));
        //$this->add_column('address',    array('Type' => 'text'));
    }
    
    /**
     * Find user by condition
     * Load additional userprops
     *
     * @param Model $model
     * @param string|array|Database_Condition_Where $condition
     * @param array $params
     * @param Database_Query_Builder_Select $query
     */
    public function find_by(Model $model, $condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL)
    {
        $table = $this->table_name();
        if ($query === NULL)
        {
            $query = DB::select_array($this->_prepare_columns($params))
                ->from($table);
        }
        
        if ( ! isset($params['with_userprops']) || ! empty($params['with_userprops']))
        {
            // Select groups with additional (non-system) privilege values
            $userprops = Model::fly('Model_UserProp')->find_all_by_site_id_and_system(Model_Site::current()->id, 0, array(
                'columns'  => array('id', 'name'),
                'order_by' => 'position',
                'desc'     => FALSE,
                'as_array' => TRUE
            ));

            $table = $this->table_name();
            $userpropvalue_table = Model_Mapper::factory('Model_UserPropValue_Mapper')->table_name();

            foreach ($userprops as $userprop)
            {
                $id = (int) $userprop['id'];

                // Add column to query
                $query->select(array(DB::expr("userpropval$id.value"), $userprop['name']));

                $query->join(array($userpropvalue_table, "userpropval$id"), 'LEFT')
                    ->on(DB::expr("userpropval$id.userprop_id"), '=', DB::expr($id))
                    ->on(DB::expr("userpropval$id.user_id"), '=', "$table.id");
            }
        }
        
        if ( ! isset($params['with_links']) || ! empty($params['with_links']))
        {
            // Select groups with additional (non-system) privilege values
            $links = Model::fly('Model_Link')->find_all_by_site_id(Model_Site::current()->id, array(
                'columns'  => array('id', 'name'),
                'order_by' => 'position',
                'desc'     => FALSE,
                'as_array' => TRUE
            ));

            $table = $this->table_name();
            $linkvalue_table = Model_Mapper::factory('Model_LinkValue_Mapper')->table_name();

            foreach ($links as $link)
            {
                $id = (int) $link['id'];

                // Add column to query
                $query->select(array(DB::expr("linkval$id.value"), $link['name']));

                $query->join(array($linkvalue_table, "linkval$id"), 'LEFT')
                    ->on(DB::expr("linkval$id.link_id"), '=', DB::expr($id))
                    ->on(DB::expr("linkval$id.user_id"), '=', "$table.id");
            }
        }
        
        if ( ! isset($params['with_town']) || ! empty($params['with_town']))
        {
            $table = $this->table_name();
            $town_table = Model_Mapper::factory('Model_Town_Mapper')->table_name();
            
            // Add column to query
            $query->select(array(DB::expr("town.name"), 'town_name'));

            $query->join(array($town_table, "town"), 'LEFT')
                ->on(DB::expr("town.id"), '=', "$table.town_id");

        }        
        return parent::find_by($model, $condition, $params, $query);
    }    

    /**
     * Find all models by criteria and return them in {@link Models} container
     *
     * @param  Model $model
     * @param  string|array|Database_Expression_Where $condition
     * @param  array $params
     * @param  Database_Query_Builder_Select $query
     * @return Models|array
     */
    public function find_all_by(
        Model                         $model,
                                      $condition = NULL,
        array                         $params = NULL,
        Database_Query_Builder_Select $query = NULL
    )
    {
        $table = $this->table_name();

        if ($query === NULL)
        {
            $query = DB::select_array($this->_prepare_columns($params))
                ->distinct('whatever')
                ->from($table);
        }

        // ----- process contition
        if (is_array($condition) &&  ! empty($condition['ids']))
        {
            // find product by several ids
            $query->where("$table.id", 'IN', DB::expr('(' . implode(',', $condition['ids']) . ')'));

            unset($condition['ids']);
        }

        if ( ! empty($params['with_userprops']))
        {
            $userprops = Model::fly('Model_UserProp')->find_all_by_site_id_and_system(Model_Site::current()->id, 0, array(
                'columns'  => array('id', 'name'),
                'order_by' => 'position',
                'desc'     => FALSE,
                'as_array' => TRUE
            ));

            $table = $this->table_name();
            $userpropvalue_table = Model_Mapper::factory('Model_UserPropValue_Mapper')->table_name();

            foreach ($userprops as $userprop)
            {
                $id = (int) $userprop['id'];

                // Add column to query
                $query->select(array(DB::expr("userpropval$id.value"), $userprop['name']));
                
                $query->join(array($userpropvalue_table, "userpropval$id"), 'LEFT')
                    ->on(DB::expr("userpropval$id.userprop_id"), '=', DB::expr($id))
                    ->on(DB::expr("userpropval$id.user_id"), '=', "$table.id");
            }
        }

        if ( ! isset($params['with_links']) || ! empty($params['with_links']))
        {
            // Select groups with additional (non-system) privilege values
            $links = Model::fly('Model_Link')->find_all_by_site_id(Model_Site::current()->id, array(
                'columns'  => array('id', 'name'),
                'order_by' => 'position',
                'desc'     => FALSE,
                'as_array' => TRUE
            ));

            $table = $this->table_name();
            $linkvalue_table = Model_Mapper::factory('Model_LinkValue_Mapper')->table_name();

            foreach ($links as $link)
            {
                $id = (int) $link['id'];

                // Add column to query
                $query->select(array(DB::expr("linkval$id.value"), $link['name']));

                $query->join(array($linkvalue_table, "linkval$id"), 'LEFT')
                    ->on(DB::expr("linkval$id.link_id"), '=', DB::expr($id))
                    ->on(DB::expr("linkval$id.user_id"), '=', "$table.id");
            }
        }
        if ( ! isset($params['with_town']) || ! empty($params['with_town']))
        {
            $table = $this->table_name();
            $town_table = Model_Mapper::factory('Model_Town_Mapper')->table_name();
            
            // Add column to query
            $query->select(array(DB::expr("town.name"), 'town_name'));

            $query->join(array($town_table, "town"), 'LEFT')
                ->on(DB::expr("town.id"), '=', "$table.town_id");
        }        
        
        return parent::find_all_by($model, $condition, $params, $query);
    }   
    
}