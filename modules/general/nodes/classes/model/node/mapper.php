<?php defined('SYSPATH') or die('No direct script access.');

class Model_Node_Mapper extends Model_Mapper_NestedSet
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
        $this->add_column('meta_title',       array('Type' => 'text'));
        $this->add_column('meta_description', array('Type' => 'text'));
        $this->add_column('meta_keywords',    array('Type' => 'text'));

        $this->add_column('layout',         array('Type' => 'varchar(63)'));

        $this->add_column('node_active',    array('Type' => 'boolean', 'Key' => 'INDEX'));
        $this->add_column('active',         array('Type' => 'boolean', 'Key' => 'INDEX'));
    }

    /**
     * Recalculate overall activity for nodes
     */
    public function update_activity()
    {
        $node_table = $this->table_name();

        // Subquery to check that there is no inactive parents of current node
        $sub_q = DB::select('id')
            ->from(array($node_table, 's'))
            ->where(DB::expr('s.lft'), '<=', DB::expr($this->get_db()->quote_identifier("$node_table.lft")))
            ->and_where(DB::expr('s.rgt'), '>=', DB::expr($this->get_db()->quote_identifier("$node_table.rgt")))
            ->and_where(DB::expr('s.node_active'), '=', 0);
        
        $exists = DB::expr("NOT EXISTS($sub_q)");


        $count = $this->count_rows();

        $limit = 100;
        $offset = 0;
        $loop_prevention = 0;

        do {
            $nodes = DB::select('id', array($exists, 'active'))
                ->from($node_table)
                ->offset($offset)->limit($limit)
                ->execute($this->get_db());

            foreach ($nodes as $node)
            {
                $this->update(array('active' => $node['active']), DB::where('id', '=', $node['id']));
            }

            $offset += count($nodes);
            $loop_prevention++;
        }
        while ($offset < $count && count($nodes) && $loop_prevention < 1000);

        if ($loop_prevention >= 1000)
        {
            throw new Kohana_Exception('Possible infinite loop in ' . __METHOD__);
        }
    }

    /**
     * Find all nodes, except the subsection of given one for the specified site
     *
     * @param  Model $model
     * @param  integer $site_id
     * @param  array $params
     * @return ModelsTree_NestedSet|Models|array
     */
    public function  find_all_but_subtree_by_site_id(Model $model, $site_id, array $params = NULL)
    {
        return parent::find_all_but_subtree_by($model, DB::where('site_id', '=', $site_id), $params);
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