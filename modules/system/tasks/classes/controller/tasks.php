<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Tasks extends Controller
{

    /**
     * Exectute the task
     */
    public function action_execute()
    {
        if ( ! Kohana::$is_cli)
        {
            throw new Kohana_Exception('Tasks can be executed only from command line');
        }
        
        $task = $this->request->param('task');
        if ( ! preg_match('/^\w+$/', $task))
        {
            throw new Kohana_Exception('Invalid task name :task', array(':task' => $task));
        }
        
        TaskManager::execute($task);
    }
}