<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Create event through COMDI API
 */
class Task_Comdi_Status extends Task_Comdi_Base
{   
    public static $params = array(
        'key',
        'event_id',
    );

    /**
     * Default parameters for this task
     * @var array
     */
    public static $default_params = array(
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
        $xml_response = parent::send('/api0/GetStatus.php',$this->params(self::$params));

        $stage = ($xml_response['stage'] == null)? null:strtolower((string)$xml_response['stage']); 

        if ($stage === NULL) {
            $this->set_status_info('Статус мероприятия не известен');
        } else {
            $this->set_status_info('Статус мероприятия '.$stage);
        }
        
        return $stage;
    }
}
