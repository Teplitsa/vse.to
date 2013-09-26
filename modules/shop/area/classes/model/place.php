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
