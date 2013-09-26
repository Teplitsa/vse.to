<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Traversable models collection
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Models implements Iterator, Countable, ArrayAccess
{
    /**
     * Name of the model which values are stored
     * @var string
     */
    protected $_model_class;

    /**
     * An instance of model which is returned when the collection is iterated
     * @var Model
     */
    protected $_model;

    /**
     * Name of the column that serves as a primary key
     * @var string
     */
    protected $_pk = FALSE;

    /**
     * @var array
     */
    protected $_properties_array = array();

    /**
     * Construct container from array of properties
     *
     * @param string $model_class
     * @param array  $properties_array
     * @param string $key name of the primary key field
     */
    public function  __construct($model_class, array $properties_array, $pk = FALSE)
    {
        $this->_model_class = $model_class;
        $this->_pk = $pk;
        $this->properties($properties_array);
    }

    /**
     * Set/get array of model properties
     *
     * @param  array  $properties_array
     * @return array
     */
    public function properties(array $properties_array = NULL)
    {
        if ($properties_array !== NULL)
        {
            $this->_properties_array = $properties_array;
        }
        return $this->_properties_array;
    }

    /**
     * Get properties array
     * 
     * @return array
     */
    public function as_array()
    {
        return $this->_properties_array;
    }

    /**
     * Get the instance of model
     * 
     * @return Model
     */
    public function model()
    {
        if ($this->_model === NULL)
        {
            $this->_model = new $this->_model_class;
        }
        return $this->_model;
    }

    // -------------------------------------------------------------------------
    // Iterator
    // -------------------------------------------------------------------------
    public function current()
    {
        if (current($this->_properties_array) !== FALSE)
        {
            $this->model()->init(current($this->_properties_array));
            return $this->model();
        }
        else
        {
            return FALSE;
        }
    }

    public function key()
    {
        return key($this->_properties_array);
    }

    public function next()
    {
        next($this->_properties_array);
    }

    public function rewind()
    {
        reset($this->_properties_array);
    }

    public function valid()
    {
        return (current($this->_properties_array) !== FALSE);
    }

    // -------------------------------------------------------------------------
    // Countable
    // -------------------------------------------------------------------------
    public function count()
    {
        return count($this->_properties_array);
    }

    // -------------------------------------------------------------------------
    // ArrayAccess
    // -------------------------------------------------------------------------
	function offsetExists($offset)
    {
        return array_key_exists($offset, $this->_properties_array);
    }

	function offsetGet($offset)
    {
        if ( ! array_key_exists($offset, $this->_properties_array) || ! is_array($this->_properties_array[$offset]))
        {
            return FALSE;
        }
        else
        {
            $this->model()->init($this->_properties_array[$offset]);
            return $this->model();
        }
    }

	function offsetSet($offset, $value)
    {
        if ($value instanceof Model)
        {
            $value = $value->properties();
        }
        
        if ($offset === NULL)
        {
            // Appending new model
            if ($this->_pk !== FALSE && isset($value[$this->_pk]))
            {
                $this->_properties_array[$value[$this->_pk]] = $value;
            }
            else
            {
                $this->_properties_array[] = $value;
            }
        }
        else
        {
            // Modifying properties of existing model
            $this->_properties_array[$offset] = $value;
        }        
    }

	function offsetUnset($offset)
    {
        unset($this->_properties_array[$offset]);
    }

    // -------------------------------------------------------------------------
    // Additional
    // -------------------------------------------------------------------------
    /**
     * Return the model at the specified index
     *
     * @param  integer $i
     * @return Model
     */
    public function at($i)
    {
        if ($this->_pk === FALSE)
        {
            $this->model()->init($this->_properties_array[$i]);
            return $this->model();
        }
        else
        {
            $pks = array_keys($this->_properties_array);
            if (isset($pks[$i]))
            {
                return $this[$pks[$i]];
            }
            else
            {
                return FALSE;
            }
        }
    }
}
