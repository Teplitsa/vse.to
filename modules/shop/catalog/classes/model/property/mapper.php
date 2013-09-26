<?php defined('SYSPATH') or die('No direct script access.');

class Model_Property_Mapper extends Model_Mapper {

    public function init()
    {
        parent::init();

        $this->add_column('id',       array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('site_id',  array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('position', array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        $this->add_column('name',    array('Type' => 'varchar(31)', 'Key' => 'INDEX'));
        $this->add_column('caption', array('Type' => 'varchar(31)'));
        $this->add_column('type',    array('Type' => 'tinyint'));
        $this->add_column('options', array('Type' => 'array'));

        $this->add_column('system', array('Type' => 'boolean'));
    }

    /**
     * Find all active non-system properties with their values for given product
     * 
     * @param  Model_Property $property
     * @param  Model_Product $product
     * @param  boolean $active
     * @param  boolean $system
     * @param  array $params
     * @return Models
     */

    /**
     * Find all properties by given condition
     *
     * @param  Model $model
     * @param  string|array|Database_Expression_Where $condition
     * @param  array $params
     * @param  Database_Query_Builder_Select $query
     * @return Models|array
     */
    public function find_all_by(Model $model, $condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL)
    {
        if (is_array($condition) && ! empty($condition['section_id']))
        {            
            // Find properties that apply to selected section
            $columns = $this->_prepare_columns($params);

            $table = $this->table_name();
            $propsec_table = Model_Mapper::factory('Model_PropertySection_Mapper')->table_name();
            
            $query = DB::select_array($columns)
                ->from($table)
                ->join($propsec_table, 'INNER')
                    ->on("$propsec_table.property_id", '=', "$table.id")
                    ->on("$propsec_table.section_id", '=', DB::expr((int) ($condition['section_id'])));

            if (isset($condition['active']))
            {
                $query->and_where("$propsec_table.active", '=', $condition['active']);
                unset($condition['active']);
            }
            
            unset($condition['product']);
        }

        return parent::find_all_by($model, $condition, $params, $query);
    }

    /**
     * Move property up
     *
     * @param Model $property
     * @param Database_Expression_Where $condition
     */
    public function up(Model $property, Database_Expression_Where $condition = NULL)
    {
        parent::up($property, DB::where('site_id', '=', $property->site_id));
    }

    /**
     * Move property down
     *
     * @param Model $property
     * @param Database_Expression_Where $condition
     */
    public function down(Model $property, Database_Expression_Where $condition = NULL)
    {
        parent::down($property, DB::where('site_id', '=', $property->site_id));
    }
}