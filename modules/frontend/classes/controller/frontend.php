<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Frontend controller
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
abstract class Controller_Frontend extends Controller
{
    /**
     * @return boolean
     */
    public function before()
    {
        // Site is required for frontend application
        if (Model_Site::current()->id === NULL)
        {
            $this->_action_404('Портал не найден');
            return FALSE;
        }
        
        $request = Request::current();

        if (!method_exists($this, 'action_'.$request->action)) {
            $this->_action_404('Страница не найдена');
            return FALSE;            
        }
        
        if (Modules::registered('acl'))
        {
            $privilege_name = Model_Privilege::privilege_name($this->request);           

            if ($privilege_name && !Auth::granted($privilege_name)) {
                $this->_action_404('Доступ запрещен');
                return FALSE;
            }
            
            $user = Auth::instance()->get_user();
            //$timezone = isset($user->town->timezone)? $user->town->timezone: 'Europe/Moscow';
            $timezone = 'Europe/Moscow';
            date_default_timezone_set($timezone);

        }

        // Breadcrumbs        
        $history = TRUE;
        $i = 0 ;
        $controllers = array();
        while ($history) {
            
            if ($i == 0)
            {
                // First - take history from the current url
                $history = $request->param('history');
            }
            else
            {
                // Parse route to retrieve the history param
                list($name, $request_params) = URL::match($history);

                if (isset($request_params['history']))
                {
                    $history = $request_params['history'];
                }
                else
                {
                    $history = NULL;
                }
            }
            $i++;
            if ($history !== NULL)
            {
                list($name, $request_params) = URL::match($history);
                if (isset($request_params['controller'])) {
                    $controllers[] = array($request_params['controller'],$request_params);
                }
            }
        }

        $controllers = array_reverse($controllers);

        foreach ($controllers as $controller_info) {
            list($controller,$request_params) = $controller_info;
            $request->get_controller($controller)->add_breadcrumbs($request_params);        
        }
        
        $request->get_controller($request->controller)->add_breadcrumbs();                      

        return TRUE;
    }
    
    /**
     * Creates and prepares layout to be used for frontend controller
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        $node = Model_Node::current();

        if ($this->request->in_window())
        {
            $layout_script = 'layouts/window';

        }
        
        if ($layout_script === NULL)
        {
            if (isset($node->layout))
            {
                // Selected layout from current node
                $layout_script = 'layouts/' . $node->layout;
            }
            else
            {
                $layout_script = 'layouts/default';
            }
        }
        $layout = parent::prepare_layout($layout_script);

        // Add meta tags for current node
        if ($node->id !== NULL)
        {
            if ( ! isset($layout->caption))
            {
                $layout->caption = $node->caption;
            }

            if ($node->meta_title != '') {
                $layout->add_title($node->meta_title);
            } else {
                $layout->add_title($node->caption);
            }

            $layout->add_description($node->meta_description);
            $layout->add_keywords($node->meta_keywords);
        }

        // Add standart javascripts for widgets
        Widget::add_scripts();
        jQuery::add_scripts();        
        // Add standart js scripts
        $layout->add_script(Modules::uri('frontend') . '/public/js/bootstrap.min.js');
        //$layout->add_script('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
        $layout->add_script(Modules::uri('frontend') . '/public/js/jquery.preimage.js');        
        $layout->add_script(
            '$(document).ready(function() {$('."'.file').preimage(); });" 
        , TRUE);
        

//        // Render flash messages
//        $layout->messages = '';
//        foreach (FlashMessages::fetch_all() as $message)
//        {
//            $layout->messages .= View_Helper::flash_msg($message['text'], $message['type']);
//        }

        return $layout;
    }
    
    /**
     * Render 404 error
     *
     * @param string $message
     */
    protected function _action_404($message = 'Указанный путь не найден')
    {
        $ctrl_errors = $this->request->get_controller('Controller_Errors');
        $ctrl_errors->action_error($this->request->uri, 404, $message);
    }
    
    public function add_breadcrumbs(array $request_params = array()) {

    }
}