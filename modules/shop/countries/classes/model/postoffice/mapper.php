<?php defined('SYSPATH') or die('No direct script access.');

class Model_PostOffice_Mapper extends Model_Mapper
{

    public function init()
    {
        parent::init();

        $this->add_column('id', array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));

        $this->add_column('country_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('region_id',  array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        $this->add_column('name',     array('Type' => 'varchar(255)'));
        $this->add_column('city',     array('Type' => 'varchar(255)'));
        $this->add_column('postcode', array('Type' => 'varchar(31)', 'KEY' => 'INDEX'));
    }

    /**
     * Find all postoffices by part of the postcode
     *
     * @param  Model $model
     * @param  string $postcode
     * @param  array $params
     * @return Models
     */
    public function find_all_like_postcode(Model $model, $postcode, array $params = NULL)
    {
        return $this->find_all_by($model, DB::where('postcode', 'LIKE', "$postcode%"), $params);
    }
    
    /**
     * Find all postoffices by part of the city name
     *
     * @param  Model $model
     * @param  string $city
     * @param  array $params
     * @return Models
     */
    public function find_all_like_city(Model $model, $city, array $params = NULL)
    {
        return $this->find_all_by($model, DB::where('city', 'LIKE', "%$city%"), $params);
    }

    /**
     * Find all postoffices by condition
     * 
     * @param  Model $model
     * @param  Database_Expression_Where $condition
     * @param  array $params
     * @param  Database_Query_Builder_Select $query
     * @return Models|array
     */
    public function find_all_by(
        Model                         $model,
        Database_Expression_Where     $condition = NULL,
        array                         $params = NULL,
        Database_Query_Builder_Select $query = NULL
    )
    {
        $postoffice_table = $this->table_name();
        $region_table     = $region_mapper = Model_Mapper::factory('Model_Region_Mapper')->table_name();

        $query = DB::select('*', array("$region_table.name", 'region_name'))
            ->from($this->table_name())
            ->join($region_table, 'LEFT')
                ->on("$region_table.id", '=', "$postoffice_table.region_id");

        return parent::find_all_by($model, $condition, $params, $query);
    }
}