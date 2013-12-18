<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Abstract base class for a task, which is supposed to cooperate with comdi web service
 */
abstract class Task_Comdi_Base extends Task
{
    
    public function default_params(array $default_params = NULL)
    {
        $default_params['key'] = Kohana::config('comdi.key');        
        return parent::default_params($default_params);
        
    }
    
    /**
     * Mapping keys from Model_Product to COMDI API
     * @var array
     */
    public static $mapping = array(
        'event_id'             => 'event_id',        
        'caption'              => 'name',
        'datetime'             => 'time',
        'description'          => 'description',
        'access'               => 'access',
        'numviews'      => 'maxAllowedUsers',
        'role'                 => 'role',        
        'username'             => 'username',
        'email'                => 'email',        
    );     
    
    public static function mapping(array $values) {
        foreach (self::$mapping as $key => $value) {
            if (!isset($values[$key])) continue;
            switch ($key) {
                case 'datetime':
                    //$t = new DateTime();
                    //$t->setTimezone(new DateTimeZone("Europe/Moscow"));
                    //var_dump($t);
                    //die();
                    $data[$value] = $values[$key]->getTimeStamp()+ $values[$key]->getOffset();
                    break;
                case 'description':
                    $filter = new Form_Filter_Crop(100);
                    $data[$value] = $filter->filter($values[$key]);
                    break;
                case 'event_id':
                    $data[$value] = (int)$values[$key];
                    break;             
                default:
                    $data[$value] = $values[$key];
                    break;
            }
        }
        return $data;
    }
    /**
     * Temporary directory to store downloaded images and other files
     * @var string
     */
    protected $_tmp_dir;

    /**
     * Base url, used by _get method
     * @var string
     */
    protected $_base_url;

    /**
     * Construct task
     *
     * @param string $base_url
     * @param string $tmp_dir
     */
    public function  __construct($base_url, $tmp_dir = NULL)
    {
        parent::__construct();

        $this->default_params();
        
        $this->_base_url = $base_url;

        if ($tmp_dir === NULL)
        {
            // Build tmp directory from class name
            $tmp_dir = strtolower(get_class($this));
            if (strpos($tmp_dir, 'task_')   === 0) $tmp_dir = substr($tmp_dir, strlen('task_'));

            $tmp_dir = TMPPATH . '/' . $tmp_dir;
        }
        if ( ! is_dir($tmp_dir))
        {
            mkdir($tmp_dir, 0777);
        }
        $this->_tmp_dir = $tmp_dir;
    }

    /**
     * Set/get base url
     *
     * @param  string $base_url
     * @return string
     */
    public function base_url($base_url = NULL)
    {
        if ($base_url !== NULL)
        {
            $this->_base_url = trim($base_url, ' /\\');
        }
        return $this->_base_url;
    }

    public function send($url,array $data = array())
    {
        //static $ch;
        //if ($ch === NULL)
        //{
            $ch = curl_init();
        //}

        if ($url[0] == '/')
        {
            $url = $this->_base_url . $url;
        }
        
            
        $url = str_replace(' ', '%20', $url);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);        

        //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); //timeout in seconds
        $response = curl_exec($ch);

        $code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check if any error occured
        if(curl_errno($ch))
        { 
            return FALSE;
        }
        if (($code == 200) && ($response !== FALSE))
        {   
            $xml_response = simplexml_load_string($response);

            curl_close($ch); 
            return  $xml_response;
        }
        else
        {
            return FALSE;
        }
    }
}