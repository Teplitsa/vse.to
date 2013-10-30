<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Create event through COMDI API
 */
class Task_Comdi_Create extends Task_Comdi_Base
{   
    public static $params = array(
        'key',
        'name',
        'time',
        'description',
        'access',
        'maxAllowedUsers'
    );
    /**
     * Default parameters for this task
     * @var array
     */
    public static $default_params = array(
        'access'            => 'open',
        'maxAllowedUsers'   => 50,
    );

    /**
     * Construct task
     */
    public function  __construct()
    {
        parent::__construct('http://my.webinar.ru');
        
        $this->default_params(self::$default_params);
    }

    /**
     * Run the task
     */
    public function run()
    {        

        $xml_response = parent::send('/api0/Create.php',$this->params(self::$params));
        
        $id = ($xml_response['event_id'] == null)? null:(string)$xml_response['event_id'];        
        
        if ($id === NULL) {
            $this->set_status_info('Событие не было создано');
        } else {
            $this->set_status_info('Событие создано');
        }
        
        return $id;
    }
}
