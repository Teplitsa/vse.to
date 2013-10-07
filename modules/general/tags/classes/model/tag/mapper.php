<?php defined('SYSPATH') or die('No direct script access.');

class Model_Tag_Mapper extends Model_Mapper
{    
    public function init()
    {
        $this->add_column('id',         array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('name', array('Type' => 'varchar(63)',  'Key' => 'INDEX'));
        $this->add_column('alias', array('Type' => 'varchar(63)',  'Key' => 'INDEX'));        
        $this->add_column('owner_type', array('Type' => 'varchar(15)',  'Key' => 'INDEX'));
        $this->add_column('owner_id',   array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('weight',   array('Type' => 'int unsigned', 'Key' => 'INDEX'));        
    }
    
    /**
     * Move tag one position up
     *
     * @param Model $model
     * @param Database_Expression_Where $condition
     */
    public function up(Model $model, Database_Expression_Where $condition = NULL)
    {
        parent::up($model, DB::where('owner_type', '=', $model->owner_type)->and_where('owner_id', '=', $model->owner_id));
    }

    /**
     * Move tag one position down
     *
     * @param Model $model
     * @param Database_Expression_Where $condition
     */
    public function down(Model $model, Database_Expression_Where $condition = NULL)
    {
        parent::down($model, DB::where('owner_type', '=', $model->owner_type)->and_where('owner_id', '=', $model->owner_id));
    } 
    
    /**
     * Find all tags by part of the name
     *
     * @param  Model $model
     * @param  string $name
     * @param  array $params
     * @return Models
     */
    public function find_all_like_name(Model $model, $name, array $params = NULL)
    {
        $table = $this->table_name();

        $query = DB::select_array(array('name'))
                ->distinct('whatever')
                ->from($table)
                ->where('name', 'LIKE', "$name%");
        
        return $this->find_all_by($model, NULL, $params, $query);
    }        
}