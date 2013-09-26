<?php defined('SYSPATH') or die('No direct script access.');

class Model_Newsitem extends Model
{
    public static $date_as_timestamp = FALSE;       
       
    /**
     * Default site id for news
     * 
     * @return integer
     */
    public function default_site_id()
    {
        return Model_Site::current()->id;
    }

    /**
     * Default date
     * 
     * @return string
     */
    public function default_date()
    {
        return date(Kohana::config('datetime.date_format'));
    }
}
