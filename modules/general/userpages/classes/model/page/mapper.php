<?php defined('SYSPATH') or die('No direct script access.');

class Model_Page_Mapper extends Model_Mapper
{
    public function init()
    {
        parent::init();

        $this->add_column('id',      array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('node_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        $this->add_column('content', array('Type' => 'text'));
    }

    /**
     * Find page by criteria joining some node information
     *
     *
     * @param  Model $model
     * @param  string|array|Database_Expression_Where $condition
     * @param  array $params
     * @param  Database_Query_Builder_Select $query
     * @return Model
     */
    public function find_by(Model $model, $condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL)
    {
        $page_table = $this->table_name();
        $node_table = Model_Mapper::factory('Model_Node_Mapper')->table_name();
        
        $columns = isset($params['columnd']) ? $params['columns'] : array("$page_table.*");

        // Select node caption as page caption
        $columns[] = "$node_table.caption";
        $columns[] = "$node_table.type";
        $columns[] = "$node_table.active";

        $query = DB::select_array($columns)
            ->from($page_table)
            ->join($node_table, 'INNER')
                ->on("$node_table.id", '=', "$page_table.node_id");

        return parent::find_by($model, $condition, $params, $query);
    }
}