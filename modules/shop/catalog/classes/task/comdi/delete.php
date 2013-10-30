<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Delete event through COMDI API
 */
class Task_Comdi_Delete extends Task_Comdi_Base
{
    public static $params = array(
        'key',
        'event_id',        
    );

    /**
     * Construct task
     */
    public function  __construct()
    {
        parent::__construct('http://my.webinar.ru');
        
        $this->default_params();
    }

    /**
     * Run the task
     */
    public function run()
    {
        $xml_response = parent::send('/api0/Delete.php',$this->params(self::$params));

        $id = ($xml_response['event_id'] == null)? null:(string)$xml_response['event_id'];        
        
        if ($id ===NULL) {
            $this->set_status_info('Событие не было удалено');
        } else {
            $this->set_status_info('Событие удалено');
        }        
        
        return $id;
    }
}
