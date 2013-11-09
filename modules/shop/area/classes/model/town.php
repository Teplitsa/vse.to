<?php defined('SYSPATH') or die('No direct script access.');

class Model_Town extends Model
{
    const DEFAULT_TOWN_ID = 1;
    
    const TOWN_TOKEN    = 'town_token'; // A token used to town via cookie

    const TOWN_LIFETIME    = 604800; // Town token is valid for 7 days
   
    const ALL_TOWN = 'all';
    
    static $towns = NULL;

    public static $_timezone_options = array(
        'Pacific/Midway'       => "(GMT-11:00) Midway Island",
        'US/Samoa'             => "(GMT-11:00) Samoa",
        'US/Hawaii'            => "(GMT-10:00) Hawaii",
        'US/Alaska'            => "(GMT-09:00) Alaska",
        'US/Pacific'           => "(GMT-08:00) Pacific Time (US &amp; Canada)",
        'America/Tijuana'      => "(GMT-08:00) Tijuana",
        'US/Arizona'           => "(GMT-07:00) Arizona",
        'US/Mountain'          => "(GMT-07:00) Mountain Time (US &amp; Canada)",
        'America/Chihuahua'    => "(GMT-07:00) Chihuahua",
        'America/Mazatlan'     => "(GMT-07:00) Mazatlan",
        'America/Mexico_City'  => "(GMT-06:00) Mexico City",
        'America/Monterrey'    => "(GMT-06:00) Monterrey",
        'Canada/Saskatchewan'  => "(GMT-06:00) Saskatchewan",
        'US/Central'           => "(GMT-06:00) Central Time (US &amp; Canada)",
        'US/Eastern'           => "(GMT-05:00) Eastern Time (US &amp; Canada)",
        'US/East-Indiana'      => "(GMT-05:00) Indiana (East)",
        'America/Bogota'       => "(GMT-05:00) Bogota",
        'America/Lima'         => "(GMT-05:00) Lima",
        'America/Caracas'      => "(GMT-04:30) Caracas",
        'Canada/Atlantic'      => "(GMT-04:00) Atlantic Time (Canada)",
        'America/La_Paz'       => "(GMT-04:00) La Paz",
        'America/Santiago'     => "(GMT-04:00) Santiago",
        'Canada/Newfoundland'  => "(GMT-03:30) Newfoundland",
        'America/Buenos_Aires' => "(GMT-03:00) Buenos Aires",
        'Greenland'            => "(GMT-03:00) Greenland",
        'Atlantic/Stanley'     => "(GMT-02:00) Stanley",
        'Atlantic/Azores'      => "(GMT-01:00) Azores",
        'Atlantic/Cape_Verde'  => "(GMT-01:00) Cape Verde Is.",
        'Africa/Casablanca'    => "(GMT) Casablanca",
        'Europe/Dublin'        => "(GMT) Dublin",
        'Europe/Lisbon'        => "(GMT) Lisbon",
        'Europe/London'        => "(GMT) London",
        'Africa/Monrovia'      => "(GMT) Monrovia",
        'Europe/Amsterdam'     => "(GMT+01:00) Amsterdam",
        'Europe/Belgrade'      => "(GMT+01:00) Belgrade",
        'Europe/Berlin'        => "(GMT+01:00) Berlin",
        'Europe/Bratislava'    => "(GMT+01:00) Bratislava",
        'Europe/Brussels'      => "(GMT+01:00) Brussels",
        'Europe/Budapest'      => "(GMT+01:00) Budapest",
        'Europe/Copenhagen'    => "(GMT+01:00) Copenhagen",
        'Europe/Ljubljana'     => "(GMT+01:00) Ljubljana",
        'Europe/Madrid'        => "(GMT+01:00) Madrid",
        'Europe/Paris'         => "(GMT+01:00) Paris",
        'Europe/Prague'        => "(GMT+01:00) Prague",
        'Europe/Rome'          => "(GMT+01:00) Rome",
        'Europe/Sarajevo'      => "(GMT+01:00) Sarajevo",
        'Europe/Skopje'        => "(GMT+01:00) Skopje",
        'Europe/Stockholm'     => "(GMT+01:00) Stockholm",
        'Europe/Vienna'        => "(GMT+01:00) Vienna",
        'Europe/Warsaw'        => "(GMT+01:00) Warsaw",
        'Europe/Zagreb'        => "(GMT+01:00) Zagreb",
        'Europe/Athens'        => "(GMT+02:00) Athens",
        'Europe/Bucharest'     => "(GMT+02:00) Bucharest",
        'Africa/Cairo'         => "(GMT+02:00) Cairo",
        'Africa/Harare'        => "(GMT+02:00) Harare",
        'Europe/Helsinki'      => "(GMT+02:00) Helsinki",
        'Europe/Istanbul'      => "(GMT+02:00) Istanbul",
        'Asia/Jerusalem'       => "(GMT+02:00) Jerusalem",
        'Europe/Kiev'          => "(GMT+02:00) Kyiv",
        'Europe/Minsk'         => "(GMT+02:00) Minsk",
        'Europe/Riga'          => "(GMT+02:00) Riga",
        'Europe/Sofia'         => "(GMT+02:00) Sofia",
        'Europe/Tallinn'       => "(GMT+02:00) Tallinn",
        'Europe/Vilnius'       => "(GMT+02:00) Vilnius",
        'Asia/Baghdad'         => "(GMT+03:00) Baghdad",
        'Asia/Kuwait'          => "(GMT+03:00) Kuwait",
        'Africa/Nairobi'       => "(GMT+03:00) Nairobi",
        'Asia/Riyadh'          => "(GMT+03:00) Riyadh",
        'Asia/Tehran'          => "(GMT+03:30) Tehran",
        'Europe/Moscow'        => "(GMT+04:00) Moscow",
        'Asia/Baku'            => "(GMT+04:00) Baku",
        'Europe/Volgograd'     => "(GMT+04:00) Volgograd",
        'Asia/Muscat'          => "(GMT+04:00) Muscat",
        'Asia/Tbilisi'         => "(GMT+04:00) Tbilisi",
        'Asia/Yerevan'         => "(GMT+04:00) Yerevan",
        'Asia/Kabul'           => "(GMT+04:30) Kabul",
        'Asia/Karachi'         => "(GMT+05:00) Karachi",
        'Asia/Tashkent'        => "(GMT+05:00) Tashkent",
        'Asia/Kolkata'         => "(GMT+05:30) Kolkata",
        'Asia/Kathmandu'       => "(GMT+05:45) Kathmandu",
        'Asia/Yekaterinburg'   => "(GMT+06:00) Ekaterinburg",
        'Asia/Almaty'          => "(GMT+06:00) Almaty",
        'Asia/Dhaka'           => "(GMT+06:00) Dhaka",
        'Asia/Novosibirsk'     => "(GMT+07:00) Novosibirsk",
        'Asia/Bangkok'         => "(GMT+07:00) Bangkok",
        'Asia/Jakarta'         => "(GMT+07:00) Jakarta",
        'Asia/Krasnoyarsk'     => "(GMT+08:00) Krasnoyarsk",
        'Asia/Chongqing'       => "(GMT+08:00) Chongqing",
        'Asia/Hong_Kong'       => "(GMT+08:00) Hong Kong",
        'Asia/Kuala_Lumpur'    => "(GMT+08:00) Kuala Lumpur",
        'Australia/Perth'      => "(GMT+08:00) Perth",
        'Asia/Singapore'       => "(GMT+08:00) Singapore",
        'Asia/Taipei'          => "(GMT+08:00) Taipei",
        'Asia/Ulaanbaatar'     => "(GMT+08:00) Ulaan Bataar",
        'Asia/Urumqi'          => "(GMT+08:00) Urumqi",
        'Asia/Irkutsk'         => "(GMT+09:00) Irkutsk",
        'Asia/Seoul'           => "(GMT+09:00) Seoul",
        'Asia/Tokyo'           => "(GMT+09:00) Tokyo",
        'Australia/Adelaide'   => "(GMT+09:30) Adelaide",
        'Australia/Darwin'     => "(GMT+09:30) Darwin",
        'Asia/Yakutsk'         => "(GMT+10:00) Yakutsk",
        'Australia/Brisbane'   => "(GMT+10:00) Brisbane",
        'Australia/Canberra'   => "(GMT+10:00) Canberra",
        'Pacific/Guam'         => "(GMT+10:00) Guam",
        'Australia/Hobart'     => "(GMT+10:00) Hobart",
        'Australia/Melbourne'  => "(GMT+10:00) Melbourne",
        'Pacific/Port_Moresby' => "(GMT+10:00) Port Moresby",
        'Australia/Sydney'     => "(GMT+10:00) Sydney",
        'Asia/Vladivostok'     => "(GMT+11:00) Vladivostok",
        'Asia/Magadan'         => "(GMT+12:00) Magadan",
        'Pacific/Auckland'     => "(GMT+12:00) Auckland",
        'Pacific/Fiji'         => "(GMT+12:00) Fiji",
    );
    
    public static function towns()
    {
        
        if (self::$towns === NULL) {
            self::$towns =array();
            $results = Model::fly('Model_Town')->find_all(array(
                'order_by'=> 'name',
                'columns'=>array('id','name'),
                'as_array' => TRUE));
            foreach ($results as $result) {
                self::$towns[$result['id']] = $result['name'];
            }
        }
        return self::$towns;
    }
    
    /**
     * Get currently selected town for the specified request using the value of
     * corresponding parameter in the uri
     *
     * @param  Request $request (if NULL, current request will be used)
     * @return Model_Town
     */
    public static function current(Request $request = NULL)
    {        
        if ($request === NULL)
        {
            $request = Request::current();
        }

        // cache?
        $town_model = $request->get_value('town_model');
        if ($town_model !== NULL) {
            return $town_model;
        }
        
        $alias = $request->param('are_town_alias',NULL);
        if (!$alias) {
            $alias = Cookie::get(Model_Town::TOWN_TOKEN);
            
            if ($alias == null) {
                $alias = Model_Town::ALL_TOWN;
            }
        }
        
        $town_model = new Model_Town();
        
        if ($alias !== NULL) {
            if ($alias == Model_Town::ALL_TOWN) {
                $town_model = new Model_Town(array('id'=>0, 'alias'=>'all', 'name'=>'Все города'));
            }
            else {
                $town_model = Model::fly('Model_Town')->find_by_alias($alias);
            }
        }
        if ($town_model->id === NULL) {
            $town_model = Model::fly('Model_Town')->find_by(array(),array('order_by' => 'id'));
        }     
        
        $request->set_value('town_model', $town_model);
        return $town_model;
    }
    
    /**
     * Make alias for town from it's 'town' field
     *
     * @return string
     */
    public function make_alias()
    {
        $caption_alias = str_replace(' ', '_', strtolower(l10n::transliterate($this->name)));
        $caption_alias = preg_replace('/[^a-z0-9_-]/', '', $caption_alias);

        $i = 0;
        $loop_prevention = 1000;
        do {

            if ($i > 0)
            {
                $alias = substr($caption_alias, 0, 30);
                $alias .= $i;
            }
            else
            {
                $alias = substr($caption_alias, 0, 31);
            }

            $i++;

            $exists = $this->exists_another_by_alias($alias);
        }
        while ($exists && ($loop_prevention-- > 0));

        if ($loop_prevention <= 0)
            throw new Kohana_Exception ('Possible infinite loop in :method', array(':method' => __METHOD__));

        return $alias;
    }
    
    /**
     * Import list of towns from an uploaded file
     * 
     * @param array $uploaded_file
     */
    public function import(array $uploaded_file)
    {
        // Increase php script time limit.
        set_time_limit(480);
        
        try {

            // Move uploaded file to temp location
            $tmp_file = File::upload($uploaded_file);

            if ($tmp_file === FALSE)
            {
                $this->error('Failed to upload a file');
                return;
            }

            $town = Model::fly('Model_Town');

            // hash region ids to save one db query
            $region_hash = array();

            // Read file (assuming a UTF-8 encoded csv format)
            $delimiter = ';';
            $enclosure = '"';
            
            $h = fopen($tmp_file, 'r');

            $first_line = true;
            while ($fields = fgetcsv($h, NULL, $delimiter, $enclosure))
            {
                if ($first_line)
                {
                    // Skip first header line
                    $first_line = false;
                    continue;
                }
                if (count($fields) < 3)
                {
                    $this->error('Invalid file format. Expected 3 columns in a row, but ' . count($fields) . ' found');
                    return;
                }

                // Regions: insert new or find existing
                $name = UTF8::strtolower(trim($fields[0]));
                $name = UTF8::ucfirst($name);
                $phonecode = UTF8::strtolower(trim($fields[1]));
                $timezone = UTF8::strtolower(trim($fields[2]));
                
                $town->find_by_phonecode($phonecode);

                $town->name = $name;
                $town->phonecode = $phonecode;
                $town->timezone = $timezone;
                $town->save();
            }
            fclose($h);

            @unlink($tmp_file);
        }
        catch (Exception $e)
        {
            // Shit happened
            if (Kohana::$environment === Kohana::DEVELOPMENT)
            {
                throw $e;
            }
            else
            {
                $this->error($e->getMessage());
            }
        }
    }

    public function validate_update(array $newvalues = NULL)
    {
        return $this->validate_create($newvalues);
    }    

    public function validate_create(array $newvalues = NULL)
    {
        if (Modules::registered('gmaps3')) {
            $geoinfo = Gmaps3::instance()->get_from_address($newvalues['name']);

            $err = 0;
            if (!$geoinfo) {
                FlashMessages::add('Город не найден и не будет отображен на карте!',FlashMessages::ERROR);
                $err = 1;
            }
            
            if (!$err) {
                if (!isset($geoinfo->geometry->location->lat)) {          
                    FlashMessages::add('Город не найден и не будет отображен на карте!',FlashMessages::ERROR); 
                    $err = 1;
                }
            }
            
            if (!$err) {
                if (!isset($geoinfo->geometry->location->lng)) {
                    FlashMessages::add('Город не найден и не будет отображен на карте!',FlashMessages::ERROR); 
                    $err = 1;
                }
            }
            
            if (!$err) {
                $this->lat = $geoinfo->geometry->location->lat;
                $this->lon = $geoinfo->geometry->location->lng;                    
            }           
        }
        return TRUE;
    }    
        
    public function save($force_create = NULL) {
        // Create alias from name
        if (!$this->id) $this->alias = $this->make_alias();
        return parent::save($force_create);
    }

    /**
     * Is group valid to be deleted?
     *
     * @param  array $newvalues
     * @return boolean
     */
    public function validate_delete(array $newvalues = NULL)
    {
        if ($this->id == self::DEFAULT_TOWN_ID)
        {
            $this->error('Город является системным. Его удаление запрещено!', 'system');
            return FALSE;
        }

        return TRUE;
    }    
    
    /**
     * Delete town
     */
    public function delete()
    {
        // Delete town places
        foreach (Model::fly('Model_Place')->find_all_by_town_id($this->id) as $place) {
            $place->delete();
        }
        
        // Delete from DB
        parent::delete();
    }    
}