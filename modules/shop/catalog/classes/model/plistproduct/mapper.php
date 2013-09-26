<?php defined('SYSPATH') or die('No direct script access.');

class Model_PListProduct_Mapper extends Model_Mapper
{

    public function init()
    {
        parent::init();

        $this->add_column('id',         array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('position',   array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        
        $this->add_column('plist_id',   array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('product_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
    }

    /**
     * Find all products in list joining the product information
     * 
     * @param  Model $model
     * @param  string|array|Database_Expression_Where $condition
     * @param  array $params
     * @param  Database_Query_Builder_Select $query
     * @return Models|array
     */
    public function find_all_by(Model $model, $condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL)
    {
        $product_table = Model_Mapper::factory('Model_Product_Mapper')->table_name();
        $plistproduct_table = $this->table_name();
        
        $columns = isset($params['columns'])
                        ? $params['columns']
                        : array(
                            "$plistproduct_table.*",
                            "$product_table.caption",
                            "$product_table.active"
                        );

        $query = DB::select_array($columns)
            ->from($plistproduct_table)
            ->join($product_table, 'INNER')
                ->on("$product_table.id", '=', "$plistproduct_table.product_id");

        return parent::find_all_by($model, $condition, $params, $query);
    }
}