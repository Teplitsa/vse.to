<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Request and response wrapper.
 *
 * Controllers are now bound to the request and can be obtained using get_controller() method.
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Request extends Kohana_Request {

    /**
     * Controllers loaded for current request
     * @var array
     */
    protected $_controllers;

    /**
     * Forward information
     * @var array
     */
    protected $_forward;

    /**
     * Registry that holds per-request data
     * @var array
     */
    protected $_registry;

    /**
     * This request should be rendered as in window
     * 
     * @return boolean
     */
    public function in_window()
    {
        return ( ! empty($_GET['window']));
    }

    /**
     * Forward the request to another action
     *
     * @param string $controller
     * @param string $action
     * @param  array  $params
     * @return Request
     */
    public function forward($controller, $action, array $params = NULL)
    {
        $this->_forward = array(
            'controller' => $controller,
            'action'     => $action,
            'params'     => $params
        );
        return $this;
    }

    /**
     * Cancel request forwarding
     *
     * @return Request
     */
    public function dont_forward()
    {
        $this->_forward = NULL;
        return $this;
    }

    /**
     * Is this request forwarded?
     * 
     * @return boolean
     */
    public function is_forwarded()
    {
        return ( ! empty($this->_forward));
    }

    /**
     * Returns an instance of controller with specified class name.
     *
     * Instance of controller is created only once and than stored in the request.
     * That implies that the controller's lifetime is the same as the request's lifetime.
     *
     * If $class is NULL, than an instance of current request controller is returned.
     *
     * @param  string $class
     * @return Controller
     */
    public function get_controller($class = NULL)
    {
        // Downcase class name
        $class = strtolower($class);

        if ($class === NULL)
        {
            // Construct the class name of current controller

            // Create the class prefix
            $class = 'controller_';

            if ($this->directory)
            {
                // Add the directory name to the class prefix
                $class .= str_replace(array('\\', '/'), '_', trim($this->directory, '/')).'_';
            }

            $class .= $this->controller;
        }
        elseif (strpos($class, 'controller_') === FALSE)
        {
            // Short name of the controller
            if ($this->directory)
            {
                // Add the directory name to the controller
                $class = str_replace(array('\\', '/'), '_', trim($this->directory, '/')) . '_' . $class;
            }

            $class = 'controller_' . $class;
        }

        if ( ! isset($this->_controllers[$class]))
        {
            // Create new controller instance via reflection
            $reflection_class = new ReflectionClass($class);
            $this->_controllers[$class] = $reflection_class->newInstance($this);
        }

        return $this->_controllers[$class];
    }

    /**
     * Execute the request
     * 
     * @return Request
     */
	public function execute()
	{
		// Create the class prefix
		$prefix = 'controller_';

		if ($this->directory)
		{
			// Add the directory name to the class prefix
			$prefix .= str_replace(array('\\', '/'), '_', trim($this->directory, '/')).'_';
		}

		if (Kohana::$profiling)
		{
			// Set the benchmark name
			$benchmark = '"'.$this->uri.'"';

			if ($this !== Request::$instance AND Request::$current)
			{
				// Add the parent request uri
				$benchmark .= ' « "'.Request::$current->uri.'"';
			}

			// Start benchmarking
			$benchmark = Profiler::start('Requests', $benchmark);
		}

		// Store the currently active request
		$previous = Request::$current;

		// Change the current request to this request
		Request::$current = $this;


        // Start with the current request's controller and action
        // Determine the action to use
        $action = empty($this->action) ? Route::$default_action : $this->action;

        $this->forward($this->controller, $action);

        $forward_count = 0;
        try {
            while ($this->is_forwarded() && ($forward_count < 100))
            {
                $forward_count++;

                $this->controller = $this->_forward['controller'];
                $this->action     = $this->_forward['action'];

                // Substitute params
                if (isset($this->_forward['params']))
                {
                    $this->_params = $this->_forward['params'];
                }

                $this->dont_forward();

                // Get controller instance
                $controller = $this->get_controller($this->controller);

                // Load the controller class using reflection
                $class = new ReflectionClass($controller);

                // Execute the "before action" method
                $do_execute = $class->getMethod('before')->invoke($controller);

                // Request was forwarded in 'before' method
                if ($this->is_forwarded())
                    continue;

                // Execute the main action with the parameters
                if ($do_execute)
                {
                    $class->getMethod('action_'.$this->action)->invokeArgs($controller, $this->_params);
                }

                // Request was forwarded during action execution
                if ($this->is_forwarded())
                    continue;

                // Execute the "after action" method
                $class->getMethod('after')->invoke($controller);
            }

            if ($forward_count >= 100)
            {
                throw new Kohana_Exception('Too many request forwards.');
            }
        }
		catch (Exception $e)
		{
			// Restore the previous request
			Request::$current = $previous;

			if (isset($benchmark))
			{
				// Delete the benchmark, it is invalid
				Profiler::delete($benchmark);
			}

			if ($e instanceof ReflectionException)
			{
				// Reflection will throw exceptions for missing classes or actions
				$this->status = 404;
			}
			else
			{
				// All other exceptions are PHP/server errors
				$this->status = 500;
			}

			// Re-throw the exception
			throw $e;
		}

		// Restore the previous request
		Request::$current = $previous;

		if (isset($benchmark))
		{
			// Stop the benchmark
			Profiler::stop($benchmark);
		}

		return $this;
	}

    /**
     * Set value in request's registry
     *
     * @param  string $name
     * @param  mixed $value
     * @return Request
     */
    public function set_value($name, $value)
    {
        $this->_registry[$name] = $value;
        return $this;
    }

    /**
     * Get value from request's registry
     * 
     * @param  string $name
     * @param  mixed $default
     * @return mixed
     */
    public function get_value($name, $default = NULL)
    {
        if (isset($this->_registry[$name]))
        {
            return $this->_registry[$name];
        }
        else
        {
            return $default;
        }
    }

    /**
     * Check whether the specified value is set in the request's registry
     *
     * @param  string $name
     * @return mixed
     */
    public function has_value($name)
    {
        return isset($this->_registry[$name]);
    }
}
