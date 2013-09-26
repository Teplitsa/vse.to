<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Tasks extends Controller_Backend
{
    /**
     * Prepare layout
     * 
     * @param  string $layout_script
     * @return Layout
     */
    public function  prepare_layout($layout_script = NULL)
    {
        $layout = parent::prepare_layout($layout_script);
        $layout->add_style(Modules::uri('tasks') . '/public/css/backend/tasks.css');

        switch ($this->request->action)
        {
            case 'log':
                $layout->caption = 'Лог задачи';
                break;
            default:
                $layout->caption = 'Задачи';
        }
        
        return $layout;
    }
    /**
     * Render task status
     */
    public function widget_status($task)
    {
        jQuery::add_scripts();
        Layout::instance()
            ->add_style(Modules::uri('tasks') . '/public/css/backend/tasks.css')
            ->add_script(Modules::uri('tasks') . '/public/js/backend/tasks.js');

        if (TaskManager::is_running($task))
        {
            $status = 'Выполняется';
            $progress = TaskManager::get_value($task, 'progress');
        }
        else
        {
            $status = '---';
            $progress = 0;
        }
        $status_info = TaskManager::get_value($task, 'status_info');

        $log_stats = TaskManager::log_stats($task);

        $widget = new Widget('backend/task_status');
        $widget->id = $task . '_status';
        $widget->class = 'widget_task';
        $widget->ajax_uri = URL::uri_to('backend/tasks', array('task' => $task));

        $widget->task     = $task;
        $widget->status   = $status;
        $widget->progress = $progress;
        $widget->status_info = $status_info;
        $widget->log_stats = $log_stats;
        
        return $widget;
    }

    /**
     * Redraw task status via ajax request
     */
    public function action_ajax_status()
    {
        $task = $this->request->param('task');
        if ($task != '')
        {
            $widget = $this->widget_status($task);
            $widget->to_response($this->request);
        }

        $this->_action_ajax();
    }

    /**
     * Display task log
     */
    public function action_log()
    {
        $task = $this->request->param('task');
        if ($task == '')
            return $this->_action_error ('Некорректное имя задачи');

        $view = new View('backend/task_log');
        $view->messages = TaskManager::log_messages($task);

        $this->request->response = $this->render_layout($view);
    }
    
}