<?php defined('SYSPATH') or die('No direct script access.');

class Model_Link extends Model
{
    const MAXLENGTH   = 63; // Maximum length for the link value
    
    /**
     * Default property type
     * 
     * @return integer
     */
    public function default_site_id()
    {
        return Model_SIte::current()->id;
    }

    /**
     * Delete link
     */
    public function delete()
    {
        // Delete all user values for this link
        Model_Mapper::factory('Model_LinkValue_Mapper')->delete_all_by_link_id($this, $this->id);
        
        // Delete the link
        $this->mapper()->delete($this);
    }

    /**
     * Validate creation/updation of link
     *
     * @param  array $newvalues
     * @return boolean
     */
    public function validate(array $newvalues)
    {
        // Check that link name is unque
        if ( ! isset($newvalues['name']))
        {
            $this->error('Вы не указали имя!', 'name');
            return FALSE;
        }

        if ($this->exists_another_by_name($newvalues['name']))
        {
            $this->error('Внешняя ссылка с таким именем уже существует!', 'name');
            return FALSE;
        }

        return TRUE;
    }
        
}