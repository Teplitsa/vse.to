<?php defined('SYSPATH') or die('No direct script access.');

class Model_Coupon_Mapper extends Model_Mapper
{
    public function init()
    {
        parent::init();

        $this->add_column('id',  array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));

        $this->add_column('code', array('Type' => 'char(8)', 'Key' => 'INDEX'));
        
        $this->add_column('user_id', array('Type' => 'int unsigned'));

        $this->add_column('discount_percent', array('Type' => 'float'));
        $this->add_column('discount_sum',     array('Type' => 'money'));

        $date_type = Model_Coupon::$date_as_timestamp ? 'unix_timestamp' : 'date';
        $this->add_column('valid_after',  array('Type' => $date_type));
        $this->add_column('valid_before', array('Type' => $date_type));
        
        $this->add_column('multiple', array('Type' => 'boolean'));

        $this->add_column('description',  array('Type' => 'text'));
        $this->add_column('settings',     array('Type' => 'array'));
    }


    /**
     * Find all coupons with info about user and sites it's valid in
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
        
        $coupon_table = $this->table_name();

        $columns = isset($params['columns']) ? $params['columns'] : array("$coupon_table.*");

        // user_name
        $user_table = Model_Mapper::factory('Model_User_Mapper')->table_name();
        $user_name_q = DB::select(DB::expr(
            "CONCAT("
          .     $this->get_db()->quote_identifier("$user_table.last_name")
          . ", ' ', "
          .     $this->get_db()->quote_identifier("$user_table.first_name")
          . ")"
        ))
            ->from($user_table)
            ->where("$user_table.id", '=', DB::expr($this->get_db()->quote_identifier("$coupon_table.user_id")));

        $columns[] = array($user_name_q, 'user_name');

        // site_captions
        $site_table = Model_Mapper::factory('Model_Site_Mapper')->table_name();
        $couponsite_table = Model_Mapper::factory('CouponSite_Mapper')->table_name();
        $site_captions_q = DB::select(DB::expr("GROUP_CONCAT(" . $this->get_db()->quote_identifier("$site_table.caption") . ")"))
            ->from($site_table)
            ->join($couponsite_table)
                ->on("$couponsite_table.site_id", '=', "$site_table.id")
            ->where("$couponsite_table.coupon_id", '=', DB::expr($this->get_db()->quote_identifier("$coupon_table.id")))
            ->group_by(DB::expr("'ALL'"));

        $columns[] = array($site_captions_q, 'site_captions');

        
        $params['columns'] = $columns;

        return parent::find_all_by($model, $condition, $params, $query);
    }
}