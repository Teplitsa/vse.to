<?php defined('SYSPATH') or die('No direct script access.');

class Model_Flashblock extends Model
{
    const MAXLENGTH   = 63; // Maximum length for the property value
    
    const FLASH_DIR = 'public/user_data/';
    
    public function get_url() {
        return URL::site().self::FLASH_DIR.$this->_properties['file'];
    }
    /**
     * Is block visible by default for new nodes?
     *
     * @return boolean
     */
    public function default_default_visibility()
    {
        return TRUE;
    }

    /**
     * Default site id for block
     * 
     * @return id
     */
    public function default_site_id()
    {
        return Model_Site::current()->id;
    }

    /**
     * Get nodes visibility info for this block
     * 
     * @return array(node_id => visible)
     */
    public function get_nodes_visibility()
    {
        if ( ! isset($this->_properties['nodes_visibility']))
        {
            $result = Model_Mapper::factory('FlashblockNode_Mapper')
                ->find_all_by_flashblock_id($this, (int) $this->id, array('as_array' => TRUE));

            $nodes_visibility = array();
            foreach ($result as $visibility)
            {
                $nodes_visibility[$visibility['node_id']] = $visibility['visible'];
            }
            $this->_properties['nodes_visibility'] = $nodes_visibility;
        }
        return $this->_properties['nodes_visibility'];
    }
    
    public function register() {
        
        $arr['flashvars']   = $this->flashvars;
        $arr['params']      = $this->params;
        $arr['attributes']  = $this->attributes;
        $arr['name']        = $this->name;
        $arr['url']         = $this->url;
        $arr['width']       = $this->width;
        $arr['height']      = $this->height;
        $arr['version']     = $this->version;
        
        return swfObject::register($arr);
    }

   public function validate(array $newvalues) {
        // Upload file
        if (isset($newvalues['file'])) {
            $file_info = $newvalues['file'];
        }        
        
        $file_path = Upload::save($file_info,$file_info['name'],self::FLASH_DIR);
        if (!$file_path)
        {
            $this->error('Фаил не может быть загружен!', 'flashblock');
            return FALSE;
        }
       
        return true;
   } 
    /**
     * Save block properties and block nodes visibility information
     *
     * @param  boolean $force_create
     * @return Model_Menu
     */
    public function save($force_create = FALSE)
    {
        $this->file = $this->file['name'];         
        
        parent::save($force_create);

        // Update nodes visibility info for this menu
        if ($this->id !== NULL && is_array($this->nodes_visibility))
        {
            Model_Mapper::factory('FlashblockNode_Mapper')->update_nodes_visibility($this, $this->nodes_visibility);
        }

        return $this;
    }

}
