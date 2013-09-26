<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Model implements ArrayAccess
{

    /**
     * Array of created model instances
     * @var array array(model)
     */
    protected static $_instances;
    
    /**
     * Create an instance for model or return the existing instance
     * If properties are given then model is initialized with this properties
     *
     * @param  string $model_class
     * @param  array $properties;
     * @return Model
     */
    public static function fly($model_class, array $properties = NULL)
    {
        $model_class = strtolower($model_class);

        if ( ! isset(Model::$_instances[$model_class]))
        {
            Model::$_instances[$model_class] = new $model_class;
        }

        if ($properties !== NULL)
        {
            Model::$_instances[$model_class]->init($properties);
        }

        return Model::$_instances[$model_class];
    }

    /**
     * Model properties.
     * Can be accessed via setters and getters
     * @var array
     */
    protected $_properties;

    /**
     * Save the model at every properties() call to [$_previous]
     * It can be used to detect changes when model is validated or saved
     *
     * Also this may be an array with additional properties, which should be
     * retrieved from the model being backed up
     *
     * @var boolean | array
     */
    public $backup = FALSE;

    /*
     * @var array|Model
     */
    protected $_previous = array();

    /**
     * Stores the data mapper for model or the name of class, that should be used for data mapper
     * @var Model_Mapper | string
     */
    protected $_mapper;

    /**
     * Validation errors, warnings & other messages
     * @var array
     */
    protected $_messages = array();

    /**
     * Creates model instance
     *
     * @param array $properties
     */
    public function __construct(array $properties = array())
    {
        $this->init($properties);
    }

    // -------------------------------------------------------------------------
    // Properties & values
    // -------------------------------------------------------------------------
    /**
     * Resets model and initializes it with the given properties
     *
     * @param array $properties
     */
    public function init(array $properties = array())
    {
        $this->properties($properties);
    }

    /**
     * Model properties setter function - can be used to set model properties
     *
     * If corresponding method set_$name() exists (not the double underscore), then set_$name($value) is called.
     * Otherwise key with name $name is simply set to $value in @see _properties
     *
     * @param string $name Name of model property to set
     * @param <type> $value Value for model property
     * @return Model_Abstract
     */
    public function __set($name, $value)
    {
        $method = 'set_' . strtolower($name);
        if (method_exists($this, $method))
        {
            $this->$method($value);
        }
        else
        {
            $this->_properties[$name] = $value;
        }
        return $this;
    }

    /**
     * Model property getter function - returns value of property $name.
     *
     * If corresponding method get_$name() exists (not the underscore), then get_$name() is returned.
     * Otherwise returns value of key in @see _properties
     *
     * @param string $name Name of model property to get
     * @return <type>
     */
    public function __get($name)
    {
        //$name = strtolower($name);
        $getter     = "get_$name";
        $defaulter  = "default_$name";

        if (method_exists($this, $getter))
        {
            return $this->$getter();
        }
        elseif (isset($this->_properties[$name]))
        {
            return $this->_properties[$name];
        }
        elseif (empty($this->_properties['id']) && method_exists($this, $defaulter))
        {
            $this->_properties[$name] = $this->$defaulter();
            return $this->_properties[$name];
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Check that specified property of model is set
     *
     * @param  string $name
     * @return boolean
     */
    public function  __isset($name)
    {
        return ($this->$name !== NULL);
    }

    /**
     * Set/get model properties using setters/getters
     *
     * @param  array $values
     * @return array
     */
    public function values(array $values = NULL)
    {
        if ($values !== NULL)
        {
            // Set values
            foreach ($values as $name => $value)
            {
                $this->__set($name, $value);
            }
            return;
        }
        else
        {
            // Get values
            $values = array();
            foreach ($this->_properties as $name => $value)
            {
                $values[$name] = $this->__get($name);
            }
            return $values;
        }
    }

    /**
     * Sets /gets an array of properties at once, skipping getters and setters.
     * Used when restoring model from database.
     *
     * @param  array $properties
     * @return array
     */
    public function properties(array $properties = NULL)
    {
        if ($properties !== NULL)
        {
            $this->_properties = $properties;
        }
        return $this->_properties;
    }

    /**
     * Back up current model (used when applying new values before creating/updating)
     */
    public function backup()
    {
        if ( ! empty($this->backup))
        {
            $this->_previous = $this->properties();

            if (is_array($this->backup))
            {
                foreach ($this->backup as $name)
                {
                    $this->_previous[$name] = $this->$name;
                }
            }
        }
    }

    /**
     * Return the backed up model
     */
    public function previous()
    {
        if ( ! ($this->_previous instanceof Model))
        {
            $class = get_class($this);
            $this->_previous = new $class($this->_previous);
        }
        return $this->_previous;
    }

    // -------------------------------------------------------------------------
    // Validation
    // -------------------------------------------------------------------------
    /**
     * Is it ok to create new model with the specified values
     * 
     * @param  array $newvalues
     * @return boolean
     */
    public function validate_create(array $newvalues)
    {
        return $this->validate($newvalues);
    }
    
    /**
     * Is it ok to update model with the specified values
     * 
     * @param  array $newvalues
     * @return boolean
     */
    public function validate_update(array $newvalues)
    {
        return $this->validate($newvalues);
    }

    /**
     * Generic validation (used to validate creation and updating by default)
     * 
     * @param  array $newvalues
     * @return boolean
     */
    public function validate(array $newvalues)
    {
        return TRUE;
    }

    /**
     * Is it ok to delete a model?
     *
     * @param  $newvalues Additional values
     * @return boolean
     */
    public function validate_delete(array $newvalues = NULL)
    {
        return TRUE;
    }

    // -------------------------------------------------------------------------
    // Errors & messages
    // -------------------------------------------------------------------------
    /**
     * Add a message (error, warning) to this model
     * 
     * @param string $text
     * @param string $field
     * @param integer $type
     * @return Model
     */
    public function message($text, $field = NULL, $type = FlashMessages::MESSAGE)
    {
        $this->_messages[] = array(
            'text'  => $text,
            'field' => $field,
            'type'  => $type
        );
        
        return $this;
    }
    
    /**
     * Add an error to this model
     * 
     * @param  string $text
     * @param  string $field
     * @return Model
     */
    public function error($text, $field = NULL)
    {
        return $this->message($text, $field, FlashMessages::ERROR);
    }

    /**
     * Get all model errors at once
     *
     * @return array
     */
    public function errors()
    {
        $errors = array();
        foreach ($this->_messages as $message)
        {
            if ($message['type'] == FlashMessages::ERROR)
            {
                $errors[] = $message;
            }
        }
        return $errors;
    }

    /**
     * Does this model have errors?
     *
     * @return boolean
     */
    public function has_errors()
    {
        foreach ($this->_messages as $message)
        {
            if ($message['type'] == FlashMessages::ERROR)
            {
                return TRUE;
            }
        }
        return FALSE;
    }

    // -------------------------------------------------------------------------
    // Methods
    // -------------------------------------------------------------------------
    /**
     * Sets/gets data mapper for model
     *
     * @param  string|Model_Mapper $mapper
     * @return Model_Mapper
     */
    public function mapper($mapper = NULL)
    {
        if ($mapper !== NULL)
        {
            // Set mapper
            $this->_mapper = $mapper;
        }
        else
        {
            // Get mapper
            if (is_object($this->_mapper))
            {
                return $this->_mapper;
            }

            if ($this->_mapper === NULL)
            {
                $mapper_class = get_class($this) . '_Mapper';
            }
            else
            {
                $mapper_class = (string) $this->_mapper;
            }

            $this->_mapper = Model_Mapper::factory($mapper_class);

            return $this->_mapper;
        }
    }

    /**
     * Default params for find_all_by_...() functions
     * Is called from mapper
     *
     * @return array
     */
    public function params_for_find_all()
    {
        return NULL;
    }

    /**
     * Proxy method calls to mapper with model instance prepended to the arguments
     *
     * @param  string $name
     * @param  array $arguments
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        $mapper = $this->mapper();

        // Prepend model to method arguments
        array_unshift($arguments, $this);

        // Call the mapper method with the same name
        return call_user_func_array(array($mapper, $name), $arguments);
    }

    /**
     * Save the model
     * [!] Do not remove! PHP 5.2 doesn't use __call magic for parent:: methods
     *
     * @param  boolean $force_create Force creation of model even if model id is specified
     * @return integer
     */
    public function save($force_create = FALSE)
    {
        return $this->mapper()->save($this, $force_create);
    }

    /**
     * Force the creation of model
     * Usefull if it's necessary to specify id / primary key manually for a new model
     *
     * @return integer
     */
    public function create()
    {
        return $this->save(TRUE);
    }

    /**
     * Delete the model
     * [!] Do not remove! PHP 5.2 doesn't use __call magic for parent:: methods
     */
    public function delete()
    {
        return $this->mapper()->delete($this);
    }

    // -------------------------------------------------------------------------
    // ArrayAccess
    // -------------------------------------------------------------------------
    function offsetExists($name)
    {
        return ($this->$name !== NULL);
    }

    function offsetGet($name)
    {
        return $this->$name;
    }

    function offsetSet($name, $value)
    {
        $this->$name = $value;
    }

	function offsetUnset($name)
    {
        unset($this->_properties[$name]);
    }
    
}