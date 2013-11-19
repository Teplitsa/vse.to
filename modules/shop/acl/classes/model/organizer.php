<?php defined('SYSPATH') or die('No direct script access.');

class Model_Organizer extends Model
{
    const LINKS_LENGTH = 36;

    const TYPE_BIBLIOTEKA   = 1;
    const TYPE_KNMAGAZIN = 2;
    const TYPE_СLUB   = 3;
    const TYPE_RESTORAN = 4;
    const TYPE_INSTITUT   = 5;
    const TYPE_CULCENTER = 6;
    const TYPE_TVGROUP   = 7;
    
    const DEFAULT_ORGANIZER_ID = 1; 
    const DEFAULT_ORGANIZER_NAME = 'VSE.TO'; 
    
    protected static $_types = array(
        Model_Organizer::TYPE_BIBLIOTEKA   => 'библиотека',
        Model_Organizer::TYPE_KNMAGAZIN   => 'книжный магазин',
        Model_Organizer::TYPE_СLUB   => 'клуб',
        Model_Organizer::TYPE_RESTORAN   => 'ресторан',
        Model_Organizer::TYPE_INSTITUT   => 'институт',
        Model_Organizer::TYPE_CULCENTER   => 'культурный центр',
        Model_Organizer::TYPE_TVGROUP   => 'творческая группа'
    );
    
    static $organizers = NULL;
    
    public static function organizers()
    {
        
        if (self::$organizers === NULL) {
            self::$organizers =array();
            $results = Model::fly('Model_Organizer')->find_all(array(
                'order_by'=> 'name',
                'columns'=>array('id','name'),
                'as_array' => TRUE));
            foreach ($results as $result) {
                self::$organizers[$result['id']] = $result['name'];
            }
        }
        return self::$organizers;
    }
    
    public function get_full_name()
    {
        return $this->type_name.' '.$this->name;
    }
   
    public function get_full_address()
    {
        return $this->town->name.', '.$this->address;
    } 
    
    public function get_town() {
        if ( ! isset($this->_properties['town']))
        {
            $town = new Model_Town();
            $town->find((int) $this->town_id);
            $this->_properties['town'] = $town;
        }
        return $this->_properties['town'];        
    }   
    /**
     * Get all possible types
     * 
     * @return array
     */
    public function get_types()
    {
        return self::$_types;
    }
    
    public function get_type_name()
    {
        return self::$_types[$this->type];
    }
    
    public function image($size = NULL) {
        $image_info = array();
        $image = Model::fly('Model_Image')->find_by_owner_type_and_owner_id('organizer', $this->id, array(
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
                    Model::fly('Model_Image')->delete_all_by_owner_type_and_owner_id('organizer', $this->id);

                    $image = new Model_Image();
                    $image->file = $this->file;
                    $image->owner_type = 'organizer';
                    $image->owner_id = $this->id;
                    $image->config = 'user';
                    $image->save();
                }
            }
        }
    }
    
    public function validate_update(array $newvalues = NULL)
    {
        return $this->validate_create($newvalues);
    }    

    public function validate_create(array $newvalues = NULL)
    {
        if (!$this->validate_email($newvalues))
            return FALSE;
        
        return TRUE;
    }    
          
    /**
     * Validate organizer email
     * 
     * @param  array $newvalues
     * @return boolean
     */
    public function validate_email(array $newvalues)
    {
        if (isset($newvalues['email']) && !empty($newvalues['email']))
        {
            if ($this->exists_another_by_email($newvalues['email']))
            {
                $this->error('Организация с таким e-mail уже существует!', 'email');
                return FALSE;
            }
        }

        return TRUE;
    }        
    
    
    
    /**
     * Is group valid to be deleted?
     *
     * @param  array $newvalues
     * @return boolean
     */
    public function validate_delete(array $newvalues = NULL)
    {
        if ($this->id == self::DEFAULT_ORGANIZER_ID)
        {
            $this->error('Организация является системной. Её удаление запрещено!', 'system');
            return FALSE;
        }

        return TRUE;
    }    
    /**
     * Delete product
     */
    public function delete()
    {
        // Delete product images
        Model::fly('Model_Image')->delete_all_by_owner_type_and_owner_id('organizer', $this->id);
        
        // Delete from DB
        parent::delete();
    }
    
    
}
