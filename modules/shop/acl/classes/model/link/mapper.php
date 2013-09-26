<?php defined('SYSPATH') or die('No direct script access.');

class Model_Link_Mapper extends Model_Mapper {

    public function init()
    {
        parent::init();

        $this->add_column('id',       array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('site_id',  array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('position', array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        $this->add_column('caption', array('Type' => 'varchar(31)'));
        $this->add_column('name',    array('Type' => 'varchar(31)', 'Key' => 'INDEX'));
    }

    /**
     * Move privilege up
     *
     * @param Model $link
     * @param Database_Expression_Where $condition
     */
    public function up(Model $link, Database_Expression_Where $condition = NULL)
    {
        parent::up($link, DB::where('site_id', '=', $link->site_id));
    }

    /**
     * Move property down
     *
     * @param Model $link
     * @param Database_Expression_Where $condition
     */
    public function down(Model $link, Database_Expression_Where $condition = NULL)
    {
        parent::down($link, DB::where('site_id', '=', $link->site_id));
    }
}