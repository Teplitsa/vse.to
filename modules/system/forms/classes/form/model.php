<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form with associated model
 */
class Form_Model extends Form {

    /**
     * Reference to model
     * @var Model
     */
    protected $_model;

    /**
     * Create form
     *
     * @param Model  $model Reference to model
     * @param string $name
     */
    public function  __construct(Model $model = NULL, $name = NULL)
    {
        $this->model($model);
        
        parent::__construct($name);
    }

    /**
     * Set/get associated model
     * 
     * @param  Model $model
     * @return Model
     */
    public function model(Model $model = NULL)
    {
        if ($model !== NULL)
        {
            $this->_model = $model;
        }
        return $this->_model;
    }

    /**
     * Get data from associated model
     *
     * @param  string $key
     * @return mixed
     */
    public function get_data($key)
    {
        $data = $this->model();
        
        if (strpos($key, '[') !== FALSE)
        {
            // Form element name is a hash key
            $path = str_replace(array('[', ']'), array('.', ''), $key);
            $value = Arr::path($data, $path);
        }
        elseif (isset($data[$key]))
        {
            $value = $data[$key];
        }
        else
        {
            $value = NULL;
        }
        return $value;
    }
}