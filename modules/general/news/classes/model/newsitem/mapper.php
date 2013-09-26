<?php defined('SYSPATH') or die('No direct script access.');

class Model_Newsitem_Mapper extends Model_Mapper
{
    public function init()
    {
        $this->add_column('id',      array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('site_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        $this->add_column('date',       array('Type' => 'date', 'Key' => 'INDEX'));
        $this->add_column('caption',    array('Type' => 'varchar(255)'));
        $this->add_column('short_text', array('Type' => 'text'));
        $this->add_column('text',       array('Type' => 'text'));
    }

    /**
     * Find all news in specified year and month
     *
     * @param  Model_Newsitem $newsitem
     * @param  integer $year
     * @param  integer $month
     * @param  integer $site_id
     * @param  array   $params
     * @return array
     */
    public function find_all_by_year_and_month_and_site_id(Model_Newsitem $newsitem, $year, $month, $site_id, array $params = NULL)
    {
        $from = "$year-$month-01";

        if ($month < 12)
        {
            $to = "$year-" . ($month+1) . '-01';
        }
        else
        {
            $to = ($year+1) . "-01-01";
        }

        $condition = DB::where('site_id', '=', (int) $site_id)
            ->and_where('date', '>=', $from)
            ->and_where('date', '<', $to);

        return $this->find_all_by($newsitem, $condition, $params);
    }

    /**
     * Find all years for which there are news
     * 
     * @param  Model_Newsitem $newsitem
     * @param  integer $site_id
     * @return array
     */
    public function find_all_years_by_site_id(Model_Newsitem $newsitem, $site_id)
    {
        $query = DB::select(array(DB::expr("YEAR(date)"), 'year'))
            ->distinct('year')
            ->from($this->table_name());

        $condition = DB::where('site_id', '=', (int) $site_id);

        $result = $this->select($condition, array('order_by' => 'date', 'desc' => TRUE), $query);
        
        $years = array();
        foreach ($result as $values)
        {
            $years[] = (int) $values['year'];
        }
        return $years;
    }

    /**
     * Find all month for which there are news for given year
     *
     * @param  Model_Newsitem $newsitem
     * @param  string $year
     * @param  integer $site_id
     * @return array
     */
    public function find_all_months_by_year_and_site_id(Model_Newsitem $newsitem, $year, $site_id)
    {
        $year = (int) $year;
        
        $query = DB::select(array(DB::expr("MONTH(date)"), 'month'))
            ->distinct('month')
            ->from($this->table_name());

        $condition = DB::where('site_id', '=', (int) $site_id)
            ->and_where('date', '>=', "$year-01-01")
            ->and_where('date', '<', ($year+1) . "-01-01");

        $result = $this->select($condition, array('order_by' => 'date', 'desc' => FALSE), $query);

        $months = array();
        foreach ($result as $values)
        {
            $months[] = (int) $values['month'];
        }
        return $months;
    }
}