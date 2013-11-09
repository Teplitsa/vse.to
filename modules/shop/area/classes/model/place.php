<?php defined('SYSPATH') or die('No direct script access.');

class Model_Place extends Model
{
    const LINKS_LENGTH = 36;

    const ISPEED_LOW    = 1;
    const ISPEED_MEDIUM = 2;
    const ISPEED_HIGH   = 3;
    
    public static $_ispeed_options = array(
        self::ISPEED_LOW      => '< 400 Kb/s',
        self::ISPEED_MEDIUM   => '400 Kb/s - 800 Kb/s',
        self::ISPEED_HIGH     => '> 800 Kb/s'
    );
    
    public function image($size = NULL) {
        $image_info = array();
        $image = Model::fly('Model_Image')->find_by_owner_type_and_owner_id('place', $this->id, array(
            'order_by' => 'position',
            'desc'     => FALSE
        ));
        if ($size) {
            $field_image = 'image'.$size;
            $field_width = 'width'.$size;
            $field_height = 'height'.$size;

            $image_info['image'] = $image->$field_image;
            $image_info['width'] = $image->$field_width;
            $image_info['height'] = $image->$field_height;
        }
        return $image_info;
    }
    
    public function validate_update(array $newvalues = NULL)
    {
        return $this->validate_create($newvalues);
    }    

    public function validate_create(array $newvalues = NULL)
    {
        if (Modules::registered('gmaps3')) {
            if (isset($newvalues['town_id'])) {
                $town = Model::fly('Model_Town')->find($newvalues['town_id']);
                $newvalues['address'] = $town->name.' '.$newvalues['address'];
            }
            $geoinfo = Gmaps3::instance()->get_from_address($newvalues['address']);
            
            $err = 0;
            if (!$geoinfo) {
                FlashMessages::add('Площадка не найдена и не будет отображена на карте!',FlashMessages::ERROR);
                $err = 1;
            }

            if (!$err) {            
                if (count($geoinfo->address_components) < 6) {
                    FlashMessages::add('Площадка не найдена и не будет отображена на карте!',FlashMessages::ERROR);
                    $err = 1;
                }
            }
    
            if (!$err) {            
                if (!isset($geoinfo->geometry->location->lat)) {
                    FlashMessages::add('Площадка не найдена и не будет отображена на карте!',FlashMessages::ERROR);
                    $err = 1;
                }
            }
            
            if (!$err) {            
                if (!isset($geoinfo->geometry->location->lng)) {
                    FlashMessages::add('Площадка не найдена и не будет отображена на карте!',FlashMessages::ERROR);
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

    public function save($force_create = NULL) {
        // Create alias from name
        /*if (!$this->id)*/ $this->alias = $this->make_alias();
        return parent::save($force_create);
    }
    
    /**
     * Delete product
     */
    public function delete()
    {
        // Delete product images
        Model::fly('Model_Image')->delete_all_by_owner_type_and_owner_id('place', $this->id);

        // Delete from DB
        parent::delete();
    }
    
    
}
