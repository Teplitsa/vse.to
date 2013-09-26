<?php defined('SYSPATH') or die('No direct script access.');

class Model_Dialog_Mapper extends Model_Mapper
{
    public function init()
    {
        $this->add_column('id',       array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('site_id',  array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('sender_id',  array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('receiver_id',  array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('sender_active',     array('Type' => 'boolean'));
        $this->add_column('receiver_active',     array('Type' => 'boolean'));        
    }
    
    public function find_all_by(Model $model, $condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL) {
        $table = $this->table_name();

        if ($query === NULL)
        {
            $query = DB::select_array($this->_prepare_columns($params))
                ->distinct('whatever')
                ->from($table);
        }        
        // ----- process contition
        if (is_array($condition) &&  ! empty($condition['sender_id']))
        {
            unset($condition['sender_id']);
        }
        if (is_array($condition) &&  ! empty($condition['receiver_id']))
        {
            unset($condition['receiver_id']);
        }

        // find only user's dialogs
        $query->where("$table.sender_id", '=', Auth::instance()->get_user()->id)
              ->or_where("$table.receiver_id", '=', Auth::instance()->get_user()->id);
        
        return parent::find_all_by($model, $condition, $params, $query);
    }
    
    public function count_by(Model $model, $condition = NULL) {
        $table = $this->table_name();
        
        $query = DB::select(array('COUNT(DISTINCT "' . $table . '.id")', 'total_count'))
            ->from($table);

        // ----- process contition
        if (is_array($condition) &&  ! empty($condition['sender_id']))
        {
            unset($condition['sender_id']);
        }
        if (is_array($condition) &&  ! empty($condition['receiver_id']))
        {
            unset($condition['receiver_id']);
        }

        // find only user's dialogs
        $query->where("$table.sender_id", '=', Auth::instance()->get_user()->id)
              ->or_where("$table.receiver_id", '=', Auth::instance()->get_user()->id);
        
        if ($condition !== NULL)
        {
            $condition = $this->_prepare_condition($condition);
            $query->where($condition, NULL, NULL);
        }

        $count = $query->execute($this->get_db())
            ->get('total_count');

        return (int) $count;        
    }
    
}