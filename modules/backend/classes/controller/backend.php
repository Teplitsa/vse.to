<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Backend extends Controller
{
    /**
     * Allow only access to admin controllers onlу for authenticated users
     */
    public function before()
    {
        if (Modules::registered('acl') && ! Auth::granted('backend_access'))
        {
            $this->request->forward('acl', 'login');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Creates and prepares layout to be used for admin controllers
     * Adds default admin stylesheets
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        if ($layout_script === NULL)
        {
            if ($this->request->in_window())
            {
                $layout_script = 'layouts/backend/window';
            }
            else
            {
                $layout_script = 'layouts/backend/default';
            }
        }

        $layout = parent::prepare_layout($layout_script);

        // Add standart js scripts
        jQuery::add_scripts();
        $layout->add_script(Modules::uri('backend') . '/public/js/backend.js');
        $layout->add_script(Modules::uri('backend') . '/public/js/windows.js');
        
        $layout->add_script(Modules::uri('widgets') . '/public/js/widgets.js');

        return $layout;
    }

    
    /**
     * Displays an error message
     *
     * @param string $msg
     * @param string $view
     * @param array  $params
     */
    protected function _action_error($msg = 'Произошла ошибка!', $view = 'backend/form', $params = array())
    {
        if ( ! ($view instanceof View))
        {
            // Create view
            $view = new View((string) $view);
        }
        $view->caption = 'Ошибка';
        $view->form = $this->_widget_error($msg);
        $this->request->response = $this->render_layout($view);
    }

    /**
     * Renders a form with an error message
     *
     * @param  string $msg
     * @return string
     */
    protected function _widget_error($msg = 'Произошла ошибка!')
    {
        $form = new Form_Backend_Error($msg);
        return $form;
    }
}
