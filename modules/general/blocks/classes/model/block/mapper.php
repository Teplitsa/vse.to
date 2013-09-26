<?php defined('SYSPATH') or die('No direct script access.');

class Model_Block_Mapper extends Model_Mapper
{
    public function init()
    {
        $this->add_column('id',       array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('site_id',  array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('position', array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        // Имя блока (для вставки в шаблон)
        $this->add_column('name', array('Type' => 'varchar(15)', 'Key' => 'INDEX'));
        // Название блока
        $this->add_column('caption',  array('Type' => 'varchar(63)'));
        // Текст блока
        $this->add_column('text', array('Type' => 'text'));
        // Видимость на страницах по умолчанию
        $this->add_column('default_visibility', array('Type' => 'boolean'));
    }

    /**
     * Find all visible blocks for given node
     *
     * @param  Model_Block $block
     * @param  string $name
     * @param  integer $node_id
     * @param  array $params
     * @return Models|array
     */
    public function find_all_visible_by_name_and_node_id(Model_Block $block, $name, $node_id, array $params = NULL)
    {
        $blocknode_table = Model_Mapper::factory('BlockNode_mapper')->table_name();
        $block_table = $this->table_name();
        
        $query = DB::select("$block_table.*")
            ->from($block_table)
            ->join($blocknode_table, 'LEFT')
                ->on("$blocknode_table.node_id", '=', DB::expr((int) $node_id))
                ->on("$blocknode_table.block_id", '=', "$block_table.id");
        
        
        $condition = DB::where('name', '=', $name)
            ->and_where_open()
                ->and_where("$blocknode_table.visible", '=', 1)
                ->or_where_open()
                    ->and_where("ISNULL(\"$blocknode_table.visible\")", NULL, NULL)
                    ->and_where("$block_table.default_visibility", '=', 1)
                ->or_where_close()
            ->and_where_close();        

        return $this->find_all_by($block, $condition, $params, $query);
    }

    /**
     * Move block one position up
     *
     * @param Model $model
     * @param Database_Expression_Where $condition
     */
    public function up(Model $model, Database_Expression_Where $condition = NULL)
    {
        parent::up($model, DB::where('site_id', '=', $model->site_id));
    }

    /**
     * Move block one position down
     *
     * @param Model $model
     * @param Database_Expression_Where $condition
     */
    public function down(Model $model, Database_Expression_Where $condition = NULL)
    {
        parent::down($model, DB::where('site_id', '=', $model->site_id));
    }
}