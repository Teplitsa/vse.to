<?php defined('SYSPATH') or die('No direct script access.');

class Model_Acl_Mapper extends Model_Mapper
{
    // Turn on find_all_by_...() results caching
    public $cache_find_all = FALSE;

    public function init()
    {
        parent::init();

        $this->add_column('id',      array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('site_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        $this->add_column('type',           array('Type' => 'varchar(31)', 'Key' => 'INDEX'));

        $this->add_column('caption',        array('Type' => 'varchar(63)'));
        $this->add_column('menu_caption',   array('Type' => 'varchar(63)'));

        $this->add_column('alias',            array('Type' => 'varchar(31)', 'Key' => 'INDEX'));
        $this->add_column('active',         array('Type' => 'boolean', 'Key' => 'INDEX'));
    }


    /**
     * Move node up
     *
     * @param Model $node
     * @param Database_Expression_Where $condition
     */
    public function up(Model $node, Database_Expression_Where $condition = NULL)
    {
        parent::up($node, DB::where('site_id', '=', $node->site_id));
    }

    /**
     * Move node down
     *
     * @param Model $node
     * @param Database_Expression_Where $condition
     */
    public function down(Model $node, Database_Expression_Where $condition = NULL)
    {
        parent::down($node, DB::where('site_id', '=', $node->site_id));
    }
}