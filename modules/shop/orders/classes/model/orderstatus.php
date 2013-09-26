<?php defined('SYSPATH') or die('No direct script access.');

class Model_OrderStatus extends Model
{
    // Statuses with ids from 1 to MAX_SYSTEM_ID are considered as system
    const MAX_SYSTEM_ID   = 100;

    // Predefined order status ids
    const STATUS_NEW      = 1;
    const STATUS_COMPLETE = 2;
    const STATUS_CANCELED = 3;

    /**
     * Get status caption by id
     *
     * @param  integer $id
     * @return string
     */
    public static function caption($id)
    {
        // Obtain CACHED[!] collection of all statuses
        $statuses = Model::fly(__CLASS__)->find_all(array('key' => 'id'));
        
        if (isset($statuses[$id]))
        {
            return $statuses[$id]->caption;
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Prohibit to change the "system" property
     * 
     * @param boolean $value
     */
    public function set_system($value)
    {
        // Intentionally blanks
    }

    /**
     * Validate status deletion
     *
     * @param  array $newvalues
     * @return boolean
     */
    public function validate_delete(array $newvalues = NULL)
    {
        if ($this->system)
        {
            $this->error('Свойство является системным. Его удаление запрещено!');
            return FALSE;
        }

        return parent::validate_delete($newvalues);
    }
}