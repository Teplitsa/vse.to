<?php defined('SYSPATH') or die('No direct script access.');

class Model_Image_Mapper extends Model_Mapper
{    
    public function init()
    {
        $this->add_column('id',         array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('owner_type', array('Type' => 'varchar(15)',  'Key' => 'INDEX'));
        $this->add_column('owner_id',   array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('position',   array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        for ($i = 1; $i <= Model_Image::MAX_SIZE_VARIANTS; $i++)
        {
            $this->add_column("image$i",  array('Type' => 'varchar(63)'));
            $this->add_column("width$i",  array('Type' => 'smallint unsigned'));
            $this->add_column("height$i", array('Type' => 'smallint unsigned'));
        }        
    }

    /**
     * Move image one position up
     *
     * @param Model $model
     * @param Database_Expression_Where $condition
     */
    public function up(Model $model, Database_Expression_Where $condition = NULL)
    {
        parent::up($model, DB::where('owner_type', '=', $model->owner_type)->and_where('owner_id', '=', $model->owner_id));
    }

    /**
     * Move image one position down
     *
     * @param Model $model
     * @param Database_Expression_Where $condition
     */
    public function down(Model $model, Database_Expression_Where $condition = NULL)
    {
        parent::down($model, DB::where('owner_type', '=', $model->owner_type)->and_where('owner_id', '=', $model->owner_id));
    }
}