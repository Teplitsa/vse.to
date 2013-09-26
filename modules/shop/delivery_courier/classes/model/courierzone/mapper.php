<?php defined('SYSPATH') or die('No direct script access.');

class Model_CourierZone_Mapper extends Model_Mapper
{

    public function init()
    {
        parent::init();

        $this->add_column('id',          array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('position',    array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('delivery_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        $this->add_column('name',  array('Type' => 'varchar(63)'));
        $this->add_column('price', array('Type' => 'float'));
    }

    /**
     * Move zone up
     *
     * @param Model $zone
     * @param Database_Expression_Where $condition
     */
    public function up(Model $zone, Database_Expression_Where $condition = NULL)
    {
        parent::up($zone, DB::where('delivery_id', '=', $zone->delivery_id));
    }

    /**
     * Move zone down
     *
     * @param Model $zone
     * @param Database_Expression_Where $condition
     */
    public function down(Model $zone, Database_Expression_Where $condition = NULL)
    {
        parent::down($zone, DB::where('delivery_id', '=', $zone->delivery_id));
    }
}