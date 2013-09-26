<?php defined('SYSPATH') or die('No direct script access.');

class TaskManager{

    /**
     * Start the specified task - check that it's not already running
     * and spawn new process in the background
     * 
     * @param class $task
     * @param array $params
     */
    public static function start($task, array $params = array())
    {   
        if (self::is_running($task))
        {
            FlashMessages::add('Задача уже запущена', FlashMessages::MESSAGE);
            //return;
        }
        
        //if (Kohana::$is_windows)
        {            
            // Under windows the task is executed not as a background process,
            // but as an ordinary request
            
            // Fake _GET params
            if ( ! empty($params))
            {
                foreach ($params as $k => $v)
                {
                    $_GET[$k] = $v;
                }
            }
            
            return self::execute($task);
        }

        $cmd = 'php ' . DOCROOT . '/index.php --uri=tasks/execute/' . $task;

        // Pass task parameters in a _GET string format
        if ( ! empty($params))
        {
            $get = '';
            foreach ($params as $name => $value)
            {
                $name = (string) $name;

                if ( ! preg_match('/^\w+$/', $name))
                    throw new Kohana_Exception('Task parameter name :name contains invalid characters', array(':name' => $name));

                if (is_object($value) || is_array($value))
                    throw new Kohana_Exception('Invalid parameter value type :type', array(':type' => gettype($value)));

                $get .= $name . '=' . urlencode($value) . '&';
            }
           
            $cmd .= ' --get="' . $get . '"';
        }

        $output = TMPPATH . '/task_output';
        $cmd .= ' > ' . $output . ' 2>&1 &';
        exec($cmd);
    }

    /**
     * Is task already runnning?
     * 
     * @param  string $task
     * @return boolen
     */
    public static function is_running($task)
    {        
        // Does pidfile exist?
        $pidfile = TMPPATH . "/$task.pid";

        if ( ! is_readable($pidfile))
            return FALSE; // No pidfile

        // Is there a process with specified id?
        $pid = trim(file_get_contents($pidfile));

        if ( !ctype_digit($pid))
            return FALSE; // Invalid pid

        $pid = (int) $pid;
            
        // Run `ps` to check that process with this pid is running
        if ( ! Kohana::$is_windows)
        {
            $cols = explode( "\n", trim(shell_exec("ps -p $pid | awk '{print $1}'")) );

            if (count($cols) < 2) //@FIXME: do not rely on count. There can be simply error messages. Ought to compare that output is actualy a valid pid
                return FALSE; // No running process with this pid
        }

        return TRUE;
    }

    /**
     * Execute the specified task (must be called in CLI mode)
     * 
     * @param string $name
     */
    public static function execute($name)
    {
        // Check that task is not already running once again
        //if (self::is_running($name))
            //return;
        
        // Create task
        $class = 'Task_' . ucfirst($name);

        $task = new $class;

        // Reset task status
        $task->set_status_info(NULL);
        $task->set_progress(0);

        // Create the pid file and save the pid of the process in it
        $pidfile = TMPPATH . "/$name.pid";
        file_put_contents($pidfile, getmypid());

        // Run task
        set_time_limit(0);
        ini_set('memory_limit', '128M');
        ignore_user_abort(TRUE);

        try {
            $result = $task->run();
        }
        catch (Exception $e)
        {
            $task->log($e->getMessage(), Log::FATAL);
            if ( ! ($e instanceof Database_Exception))
            {
                // Update status in db only if it's not a database exception
                $task->set_status_info(Kohana::exception_text($e));
            }
            @unlink($pidfile);

            // Re-throw the exception, so it will be written into the system log
            throw $e;
        }

        // Remove pid file
        unlink($pidfile);
        return $result;
    }

    /**
     * Set value for task
     *
     * @param string $task
     * @param string $key
     * @param mixed $value
     */
    public static function set_value($task, $key, $value)
    {
        if ($value !== NULL)
        {
            DbTable::instance('DbTable_TaskValues')->set_value($task, $key, $value);
        }
        else
        {
            self::unset_value($task, $key);
        }
    }

    /**
     * Get value for task
     * 
     * @param  string $task
     * @param  string $key
     * @return mixed
     */
    public static function get_value($task, $key)
    {
        return DbTable::instance('DbTable_TaskValues')->get_value($task, $key);
    }

    /**
     * Unset value for task
     * 
     * @param string $task
     * @param string $key
     */
    public static function unset_value($task, $key)
    {
        DbTable::instance('DbTable_TaskValues')->unset_value($task, $key);
    }
    

    /**
     * Get log object for the task
     *
     * @param  string $mode
     * @return Log_File
     */
    public static function log($task, $mode = Log_File::MODE_WRITE)
    {
        return new Log_File(APPPATH . '/logs/' . $task . '.log', $mode, 1);
    }

    /**
     * Read log messages for the specified task
     *
     * @param  string $task
     * @return array
     */
    public static function log_messages($task)
    {
        return self::log($task, Log_File::MODE_READ)->read();
    }

    /**
     * Get log stats (number of messages for each log level) for the specified task
     * 
     * @param  string $task
     * @return array
     */
    public static function log_stats($task)
    {
        return self::log($task, Log_File::MODE_READ)->stats();
    }
}