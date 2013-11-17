<?php defined('SYSPATH') or die('No direct script access.');

class Model_Lecturer extends Model
{
    const LINKS_LENGTH = 64;
    
    /**
     * Return user name
     * 
     * @return string
     */
    public function get_name()
    {
        $name = '';
        if ($this->first_name) $name.=$this->first_name;
        if ($this->last_name) $name.=' '.$this->last_name;
        
        return $name;
    }
    
    
    public function image($size = NULL) {
        $image_info = array();
        $image = Model::fly('Model_Image')->find_by_owner_type_and_owner_id('lecturer', $this->id, array(
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

    public function save($force_create = FALSE) {
        parent::save($force_create);
        
        if (is_array($this->file)) {
            $file_info = $this->file;
            if (isset($file_info['name'])) {
                if ($file_info['name'] != '') {
                    // Delete organizer images
                    Model::fly('Model_Image')->delete_all_by_owner_type_and_owner_id('lecturer', $this->id);

                    $image = new Model_Image();
                    $image->file = $this->file;
                    $image->owner_type = 'lecturer';
                    $image->owner_id = $this->id;
                    $image->config = 'user';
                    $image->save();                    
                }
            }
        }
    }
    
    /**
     * Delete product
     */
    public function delete()
    {
        // Delete product images
        Model::fly('Model_Image')->delete_all_by_owner_type_and_owner_id('lecturer', $this->id);

        // Delete from DB
        parent::delete();
    }
    
}
