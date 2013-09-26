<?php defined('SYSPATH') or die('No direct script access.');

class Model_Question_Mapper extends Model_Mapper
{
    public function init()
    {
        $this->add_column('id',       array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('site_id',  array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('position', array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        $this->add_column('created_at', array('Type' => 'datetime'));
        $this->add_column('user_name',  array('Type' => 'varchar(255)'));
        $this->add_column('email',      array('Type' => 'varchar(63)'));
        $this->add_column('phone',      array('Type' => 'varchar(63)'));

        $this->add_column('question',   array('Type' => 'text'));
        $this->add_column('answer',     array('Type' => 'text'));
        $this->add_column('answered',   array('Type' => 'boolean'));
        $this->add_column('active',     array('Type' => 'boolean'));
    }

    /**
     * Move question one position up
     *
     * @param Model $model
     * @param Database_Expression_Where $condition
     */
    public function up(Model $model, Database_Expression_Where $condition = NULL)
    {
        parent::up($model, DB::where('site_id', '=', $model->site_id));
    }

    /**
     * Move question one position down
     *
     * @param Model $model
     * @param Database_Expression_Where $condition
     */
    public function down(Model $model, Database_Expression_Where $condition = NULL)
    {
        parent::down($model, DB::where('site_id', '=', $model->site_id));
    }
}