<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Represents a hierarchical structure in database table using nested sets
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
abstract class Model_Mapper_Resource extends Model_Mapper {
    /**
     * Find model by condition
     *
     * @param  Model $model
     * @param  string|array|Database_Expression_Where $condition
     * @param  array $params
     * @param  Database_Query_Builder_Select $query
     * @return Model|array
     */
    public function find_by(Model $model, $condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL)
    {
        $table = $this->table_name();
       
        if ($query === NULL)
        {
            $query = DB::select_array($this->_prepare_columns($params))
                ->from($table);
        }
        
        $resource_table = Model_Mapper::factory('Model_Resource_Mapper')->table_name();
        $pk = $this->get_pk();

        $query->select(
                array(DB::expr("resourceauthor.user_id"),"user_id"),
                array(DB::expr('CONVERT(GROUP_CONCAT(DISTINCT resourcereader_user_id.user_id),CHAR(8))'), 'access_users'),
                array(DB::expr('CONVERT(GROUP_CONCAT(DISTINCT resourcereader_organizer_id.organizer_id),CHAR(8))'), 'access_organizers'),
                array(DB::expr('CONVERT(GROUP_CONCAT(DISTINCT resourcereader_town_id.town_id),CHAR(8))'), 'access_towns')
        );

        $model_class = get_class($model);
        
        $query->join(array($resource_table,"resourceauthor"), 'LEFT')
            ->on(DB::expr("resourceauthor.resource_id"), '=', "$table.id")
            ->on(DB::expr("resourceauthor.resource_type"), '=', DB::expr($this->get_db()->quote($model_class)))
            ->on(DB::expr("resourceauthor.mode"), '=', DB::expr((int)Model_Res::MODE_AUTHOR));
        
        $query->join(array($resource_table,"resourcereader_user_id"), 'LEFT')
            ->on(DB::expr("resourcereader_user_id.resource_id"), '=', "$table.id")
            ->on(DB::expr("resourcereader_user_id.resource_type"), '=', DB::expr($this->get_db()->quote($model_class)))
            ->on(DB::expr("resourcereader_user_id.mode"), '=', DB::expr((int)Model_Res::MODE_READER));
        
        $query->join(array($resource_table,"resourcereader_organizer_id"), 'LEFT')
            ->on(DB::expr("resourcereader_organizer_id.resource_id"), '=', "$table.id")
            ->on(DB::expr("resourcereader_organizer_id.resource_type"), '=', DB::expr($this->get_db()->quote($model_class)))
            ->on(DB::expr("resourcereader_organizer_id.mode"), '=', DB::expr((int)Model_Res::MODE_READER));        

        $query->join(array($resource_table,"resourcereader_town_id"), 'LEFT')
            ->on(DB::expr("resourcereader_town_id.resource_id"), '=', "$table.id")
            ->on(DB::expr("resourcereader_town_id.resource_type"), '=', DB::expr($this->get_db()->quote($model_class)))
            ->on(DB::expr("resourcereader_town_id.mode"), '=', DB::expr((int)Model_Res::MODE_READER));        
   
        if ( ! empty($params['owner']))
        {      
            $user = $params['owner'];
                     
            $query->where(DB::expr("resourceauthor.user_id"), '=', $user->id);
        }
        
        if ( ! empty($params['for']))
        {            
            $user = $params['for'];
                     
            $query->where(DB::expr("resourceauthor.user_id"), '=', $user->id)
                  ->or_where(DB::expr("resourceauthor.organizer_id"), '=', $user->organizer_id)
                          ->or_where(DB::expr("resourceauthor.town_id"), '=', $user->town_id);
                       
        }
        
        $result = $this->select_row($condition, $params, $query);
        if (! empty($params['owner']) && $result['user_id'] === NULL) {
            // FIXME (I don't know)
            $result = array();
        }
        // Return the result of the desired type
        if (empty($params['as_array']))
        {
            if ( ! empty($result))
            {
                $model->properties($result);
            }
            else
            {
                $model->init();
            }
            return $model;
        }

        return $result;
    }
    
    /**
     * Find model by primary key
     *
     * @param  Model $model
     * @param  mixed $pk_value
     * @param  array $params
     * @return Model
     */
    public function find(Model $model, $pk_value, array $params = NULL)
    {
        return $this->find_by($model, array($this->get_pk() => $pk_value), $params);
    }
    
    public function find_another_by(Model $model, $condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL)
    {    
        $pk = $this->get_pk();
        if ($model->$pk !== NULL)
        {
            $condition = $this->_prepare_condition($condition);
            $condition->and_where($pk, '!=', $model->$pk);
        }
        return $this->find_by($model, $condition, $params, $query);
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
    public function find_all_by(Model $model, $condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL)
    {
        if ($params === NULL)
        {
            $params = $model->params_for_find_all();
        }

        if ( ! empty($params['batch']))
        {
            // Batch find
            $params['offset'] = $this->_batch_offset;
            $params['limit']  = $params['batch'];
        }
        
        if ( ! isset($params['key']))
        {
            $params['key'] = $this->get_pk();
        }

        // ----- cache
        if ($this->cache_find_all)
        {
            $condition = $this->_prepare_condition($condition);
            $hash = $this->params_hash($params, $condition);

            if (isset($this->_cache[$hash]))
            {
                // Cache hit!
                return $this->_cache[$hash];
            }
        }

        // ----- role_id, role_type
        $table = $this->table_name();
       
        if ($query === NULL)
        {
            $query = DB::select_array($this->_prepare_columns($params))
                ->from($table);
        }
  
        $resource_table = Model_Mapper::factory('Model_Resource_Mapper')->table_name();
        $pk = $this->get_pk();

        $query->select(
                array(DB::expr("resourceauthor.user_id"),"user_id"),
                array(DB::expr('CONVERT(GROUP_CONCAT(DISTINCT resourcereader_user_id.user_id),CHAR(8))'), 'access_users'),
                array(DB::expr('CONVERT(GROUP_CONCAT(DISTINCT resourcereader_organizer_id.organizer_id),CHAR(8))'), 'access_organizers'),
                array(DB::expr('CONVERT(GROUP_CONCAT(DISTINCT resourcereader_town_id.town_id),CHAR(8))'), 'access_towns')
        );

        $model_class = get_class($model);
        
        $query->join(array($resource_table,"resourceauthor"), 'LEFT')
            ->on(DB::expr("resourceauthor.resource_id"), '=', "$table.id")
            ->on(DB::expr("resourceauthor.resource_type"), '=', DB::expr($this->get_db()->quote($model_class)))
            ->on(DB::expr("resourceauthor.mode"), '=', DB::expr((int)Model_Res::MODE_AUTHOR));
        
        $query->join(array($resource_table,"resourcereader_user_id"), 'LEFT')
            ->on(DB::expr("resourcereader_user_id.resource_id"), '=', "$table.id")
            ->on(DB::expr("resourcereader_user_id.resource_type"), '=', DB::expr($this->get_db()->quote($model_class)))
            ->on(DB::expr("resourcereader_user_id.mode"), '=', DB::expr((int)Model_Res::MODE_READER));
        
        $query->join(array($resource_table,"resourcereader_organizer_id"), 'LEFT')
            ->on(DB::expr("resourcereader_organizer_id.resource_id"), '=', "$table.id")
            ->on(DB::expr("resourcereader_organizer_id.resource_type"), '=', DB::expr($this->get_db()->quote($model_class)))
            ->on(DB::expr("resourcereader_organizer_id.mode"), '=', DB::expr((int)Model_Res::MODE_READER));        

        $query->join(array($resource_table,"resourcereader_town_id"), 'LEFT')
            ->on(DB::expr("resourcereader_town_id.resource_id"), '=', "$table.id")
            ->on(DB::expr("resourcereader_town_id.resource_type"), '=', DB::expr($this->get_db()->quote($model_class)))
            ->on(DB::expr("resourcereader_town_id.mode"), '=', DB::expr((int)Model_Res::MODE_READER));        
        
        if ( ! empty($params['owner']))
        {      
            $user = $params['owner'];
                     
            $query->where(DB::expr("resourceauthor.user_id"), '=', $user->id);  
        }
        
        if ( ! empty($params['for']))
        {      
            $user = $params['for'];
                     
            $query->where(DB::expr("resourceauthor.user_id"), '=', $user->id)
                  ->or_where(DB::expr("resourceauthor.organizer_id"), '=', $user->organizer_id)
                          ->or_where(DB::expr("resourceauthor.town_id"), '=', $user->town_id);
                       
        }
        
        $query->group_by("$table.$pk");
        $result = $this->select($condition, $params, $query);

        if ( ! empty($params['batch']))
        {
            // Batch find
            if ( ! empty($result))
            {
                $this->_batch_offset += count($result);
            }
            else
            {
                // Reset
                $this->_batch_offset = 0;
            }
        }
        
        // Return the result of the desired type
        if (empty($params['as_array']))
        {
            $key = isset($params['key']) ? $params['key'] : FALSE;

            $result = new Models(get_class($model), $result, $key);
        }

        // ----- cache
        if ($this->cache_find_all)
        {
            if (count($this->_cache) < $this->cache_limit)
            {
                $this->_cache[$hash] = $result;
            }
        }
        return $result;
    }

    /**
     * Find all models
     * 
     * @param  Model $model
     * @param  array $params
     * @return Models
     */
    public function find_all(Model $model, array $params = NULL)
    {
        return $this->find_all_by($model, NULL, $params);
    }
    
    public function find_another_all_by(Model $model, $condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL)
    {    
        $pk = $this->get_pk();
        if ($model->$pk !== NULL)
        {
            $condition = $this->_prepare_condition($condition);
            $condition->and_where($pk, '!=', $model->$pk);
        }
        
        return $this->find_all_by($model, $condition, $params, $query);
    }
    
    public function count_by(Model $model, $condition = NULL) {
        if (isset($condition['owner'])) {
            $pk = $this->get_pk();
            $cond['user_id'] = $condition['owner'];
            $cond[Model_Res::RESOURCE_TYPE] = get_class($model);
            $cond['mode'] = Model_Res::MODE_AUTHOR;
            
            if ($model->$pk !==NULL) {
                $cond[Model_Res::RESOURCE_ID] = $model->$pk;            
            }
            unset($condition['owner']);
            
            $resource_mapper = Model_Mapper::factory('Model_Resource_Mapper');
            return $resource_mapper->count_by($model,$cond);
        }   
        return parent::count_by($model, $condition);
    }
    
    /**
     * Returns hash for select parameters (useful when caching results of a select)
     *
     * @param array $params
     * @param Database_Expression_Where $condition
     * @return string
     */
    public function params_hash(array $params = NULL, Database_Expression_Where $condition = NULL)
    {
        if (empty($params['desc']))
        {
            $params['desc'] = FALSE;
        }

        if (empty($params['offset']))
        {
            $params['offset'] = 0;
        }

        if (empty($params['limit']))
        {
            $params['limit'] = 0;
        }

        if ($condition !== NULL)
        {
            $params['condition'] = (string) $condition;
        }

        $str = '';
        foreach ($params as $k => $v)
        {
            if ($k=='owner') $v = $v->id;
            if (is_array($v)) {
                foreach ($v as $vs) {
                    $str .= $k . $vs;    
                }
            } else {
                $str .= $k . $v;
            }
        }

        return substr(md5($str), 0, 16);
    }
    
    
}