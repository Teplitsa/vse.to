<?php defined('SYSPATH') or die('No direct script access.');

class Model_PostOffice extends Model
{
    /**
     * Get region name for the postoffice
     * If region name is the same as the city name (Москва, ...) than it returns ''
     *
     * @return string
     */
    public function get_region_name()
    {
        $region_name = isset($this->_properties['region_name']) ? $this->_properties['region_name'] : '';

        if ($region_name == $this->city)
        {
            return '';
        }
        else
        {
            return $region_name;
        }
    }

    /**
     * Import list of post offices from an uploaded file
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

            $country_id = Model_Country::default_country_id();
            $region     = Model::fly('Model_Region');
            $postoffice = Model::fly('Model_PostOffice');

            // hash region ids to save one db query
            $region_hash = array();

            // Read file (assuming a UTF-8 encoded csv format)
            $delimiter = ',';
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
                
                if (count($fields) < 11)
                {
                    $this->error('Invalid file format. Expected 11 columns in a row, but ' . count($field) . ' found');
                    return;
                }

                // Regions: insert new or find existing
                $region_name = UTF8::strtolower(trim($fields[4]));

                if (isset($region_hash[$region_name]))
                {
                    $region_id = $region_hash[$region_name];
                }
                else
                {
                    $region->find_by_name($region_name);
                    if ($region->id === NULL)
                    {
                        // Region with given name not found - create a new one
                        $region->country_id = $country_id;
                        $region->name       = $region_name;
                        $region->save();
                    }

                    $region_id = $region->id;
                    $region_hash[$region_name] = $region_id;
                }

                // Fields
                $name = UTF8::strtolower(trim($fields[1]));
                $city = UTF8::strtolower(trim($fields[7]));
                if ($city == '')
                {
                    // City of federal importance
                    $city = $region_name;
                }
                $postcode = UTF8::strtolower(trim($fields[0]));


                // Does the post office with given postcode already exist?
                $postoffice->find_by_postcode($postcode);

                if ( ! isset($postoffice->id))
                {
                    // Insert new post office
                    $postoffice->init(array(
                        'country_id' => $country_id,
                        'region_id'  => $region_id,

                        'name'     => $name,
                        'city'     => $city,
                        'postcode' => $postcode
                    ));
                    $postoffice->save();
                }
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
}