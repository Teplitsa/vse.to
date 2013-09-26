<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Create event through COMDI API
 */
class Task_Comdi_Register extends Task_Comdi_Base
{   
    public static $params = array(
        'key',
        'event_id',
        'username',
        'role',
        'email'        
    );

    /**
     * Default parameters for this task
     * @var array
     */
    public static $default_params = array(
        'role' => 'user'        
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
        $xml_response = parent::send('/api0/Register.php',$this->params(self::$params));
        
        $uri = NULL;
        
        if (isset($xml_response->guest))
            $uri = ($xml_response->guest['uri'] == NULL)?NULL:(string)$xml_response->guest['uri'];
        
        if ($uri === NULL) {
            $this->set_status_info('Пользователь не был добавлен');
        } else {
            $this->set_status_info('Пользователь добавлен');
        }
        
        return $uri;
    }
}
