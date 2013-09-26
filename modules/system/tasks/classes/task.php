<?php defined('SYSPATH') or die('No direct script access.');

abstract class Task
{
    /**
     * Task name
     * @var string
     */
    protected $_name;
    
    /**
     * Default values for task parameteres
     * @var array
     */
    protected $_default_params = array();

    /**
     * @var Log
     */
    protected $_log;

    /**
     * Construct task
     */
    public function  __construct()
    {
        // Determinte task name by class name
        $name = strtolower(get_class($this));
        if (strpos($name, 'task_') === 0)
        {
            $name = substr($name, strlen('task_'));
        }
        $this->_name = $name;

        // Create task log
        $this->_log = TaskManager::log($name);
    }
    
    /**
     * Get task name
     *
     * @return string
     */
    public function get_name()
    {
        return $this->_name;
    }

    /**
     * Set task value
     *
     * @param string $key
     * @param mixed $value
     */
    public function set_value($key, $value)
    {
        TaskManager::set_value($this->get_name(), $key, $value);
    }

    /**
     * Get task value
     *
     * @param  string $key
     * @return mixed
     */
    public function get_value($key)
    {
        return TaskManager::get_value($this->get_name(), $key);
    }

    /**
     * Unset task value
     *
     * @param  string $key
     */
    public function unset_value($key)
    {
        TaskManager::unset_value($this->get_name(), $key);
    }

    /**
     * Update task progress
     * 
     * @param float $progress
     */
    public function set_progress($progress)
    {
        $this->set_value('progress', $progress);
    }

    /**
     * Update task status info
     *
     * @param string $status_info
     */
    public function set_status_info($status_info)
    {
        $this->set_value('status_info', $status_info);
    }

    /**
     * Get / set task default parameters
     *
     * @param  array $default_params
     * @return array
     */
    public function default_params(array $default_params = NULL)
    {
        if ($default_params !== NULL)
        {
            $this->_default_params = $default_params;
        }
        else
        {
            return $this->_default_params;
        }
    }

    /**
     * Get the task parameter value
     * 
     * @param  string $name
     * @param  mixed $default
     * @return mixed
     */
    public function param($name, $default = NULL)
    {
        if (isset($_GET[$name]))
        {
            return $_GET[$name];
        }
        elseif (isset($this->_default_params[$name]))
        {
            return $this->_default_params[$name];
        }
        else
        {
            return $default;
        }
    }
    
    /**
     * Get the task parameters value
     * 
     * @param  array params
     * @return mixed
     */
    public function params(array $params)
    {
        foreach ($params as $param) {
            $data[$param] = $this->param($param);
        }
        return $data;
    }    
    /**
     * Add message to tasg log
     *
     * @param integer $level
     * @param string $message
     */
    public function log($message, $level = Log::INFO)
    {
        $this->_log->message($message, $level);
    }

    /**
     * Run the task
     */
    abstract public function run();
}