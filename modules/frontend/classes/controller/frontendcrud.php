<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Admin controller with basic CRUD functionality
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
abstract class Controller_FrontendCRUD extends Controller_Frontend
{
    /**
     * Actions configuration
     * @var array
     */
    protected $_actions;

    /**
     * Default model for actions
     * @var string
     */
    protected $_model;
    /**
     * Default form for actions
     * @var string
     */
    protected $_form;
    /**
     * Default view for actions
     * @var string
     */
    protected $_view = 'frontend/form';

    /**
     * @param Kohana_Request $request
     */
    public function  __construct(Kohana_Request $request)
    {
        parent::__construct($request);

        if ($this->_actions === NULL)
        {
            $this->_actions = $this->setup_actions();
        }
    }

    /**
     * Setup controller actions
     *
     * @return array
     */
    public function setup_actions()
    {
        return array();
    }

    // -------------------------------------------------------------------------
    // Models
    // -------------------------------------------------------------------------
    /**
     * Prepare model for action
     *
     * @param  string $action
     * @param  array $params
     * @return Model
     */
    protected function _prepare_model($action, array $params = NULL)
    {
        
        $model = isset($params['model']) ? $params['model'] : $this->_model;
        if ( ! isset($model))
        {
            throw new Kohana_Exception('Model is not defined for action :action in :controller',
                array(':action' => $action, ':controller' => get_class($this)));
        }

        if (method_exists($this, "_$model" . "_$action"))
        {
            // Use user-defined method to prepare the model (model-specific)
            $method = "_$model" . "_$action";
            $model = $this->$method($params);
        }
        elseif (method_exists($this, "_model_$action"))
        {
            // Use user-defined method to prepare the model
            $method = "_model_$action";
            $model = $this->$method($model, $params);
        }
        else
        {            
            $model = $this->_model($action, $model, $params);
        }

        return $model;
    }

    /**
     * Default method to prepare the model
     * Override it to customize model initialization
     *
     * @param  string $action
     * @param  string|Model $model
     * @param  array $params
     * @return Model
     */
    protected function _model($action, $model, array $params = NULL)
    {
        if ( ! is_object($model))
        {
            // Create model instance
            $model = new $model;
        }
        
        if ($action != 'create')
        {
            // Find model by id for all actions but create

            // Model id
            if (isset($params['id']))
            {
                $id = $params['id'];
            }
            else
            {
                $id_param = isset($params['id_param']) ? $params['id_param'] : 'id';
                $id = (int) $this->request->param($id_param);
            }

            // Find model by id
            $model->find($id);

            if ($model->id === NULL)
            {
                // Model was not found
                $msg = isset($params['msg_not_found']) ? $params['msg_not_found'] : 'Указанный объект не найден';

                throw new Controller_FrontendCRUD_Exception($this->_format($msg, $model));
            }
        }

        return $model;
    }

    /**
     * Prepare models for multi-action
     *
     * @param  string $action
     * @param  array $params
     * @return array array(Model)
     */
    protected function _prepare_models($action, array $params = NULL)
    {
        $model = isset($params['model']) ? $params['model'] : $this->_model;
        if ( ! isset($model))
        {
            throw new Kohana_Exception('Model is not defined for action :action in :controller',
                array(':action' => $action, ':controller' => get_class($this)));
        }

        if (method_exists($this, "_$model" . "_$action"))
        {
            // Use user-defined method to prepare the model (model-specific)
            $method = "_$model" . "_$action";
            $models = $this->$method($params);
        }
        elseif (method_exists($this, "_models_$action"))
        {
            // Use user-defined method to prepare the model
            $method = "_models_$action";
            $models = $this->$method($model, $params);
        }
        else
        {
            $models = $this->_models($action, $model, $params);
        }

        return $models;
    }

    /**
     * Default method to prepare the models for multi-action
     * Override it to customize model initialization
     *
     * @param  string $action
     * @param  string $model
     * @param  array $params
     * @return array array(Model)
     */
    protected function _models($action, $model, array $params = NULL)
    {
        // Model ids
        if (isset($params['ids']) && is_array($params['ids']))
        {
            $ids = $params['ids'];
        }
        elseif (isset($_POST['ids']))
        {
            if (is_array($_POST['ids']))
            {
                $ids = $_POST['ids'];
            }
            else
            {
                $ids = explode('_', (string) ($_POST['ids']));
            }
        }
        else
        {
            $ids = $this->request->param('ids', '');
            $ids = explode('_', $ids);
        }

        $models = array();
        foreach ($ids as $id)
        {
            $model = new $model;
            $model->find((int) $id);

            if (isset($model->id))
            {
                $models[] = $model;
            }
        }

        return $models;
    }

    // -------------------------------------------------------------------------
    // Forms
    // -------------------------------------------------------------------------
    /**
     * Prepare form for action.
     * Override it to customize form initialization
     *
     * @param  string $action
     * @param  Model $model
     * @param  array $params
     * @return Form
     */
    protected function _prepare_form($action, Model $model, array $params = NULL)
    {
        $method = "_form_$action";
        if (method_exists($this, $method))
        {
            // Use user-defined method to prepare the form
            $form = $this->$method($model, $params);
        }
        else
        {
            $form = $this->_form($action, $model, $params);
        }

        return $form;
    }

    /**
     * Default method to prepare the form
     *
     * @param  string action
     * @param  Model $model
     * @param  array $params
     * @return Form
     */
    protected function _form($action, Model $model, array $params = NULL)
    {
        $form = isset($params['form']) ? $params['form'] : $this->_form;
        if ( ! isset($form))
        {
            throw new Kohana_Exception('Form is not defined for action :action in :controller',
                array(':action' => $action, ':controller' => get_class($this)));
        }

        // Create the form and bind the model to it
        $form = new $form($model);

        // Display flash message
        if (isset($_GET['flash']))
        {            
            $flash = (string) $_GET['flash'];

            if (isset($this->_actions[$action]["message_$flash"]))
            {
                $msg = $this->_actions[$action]["message_$flash"];
            }
            else
            {
                $msg = 'Действие выполнено успешно';
            }
            $form->message($this->_format($msg, $model));
        }

        return $form;
    }

    /**
     * Default form for delete action
     *
     * @param  Model $model
     * @param  array $params
     * @return Form
     */
    protected function _form_delete(Model $model, array $params = NULL)
    {
        $msg = isset($params['message']) ? $params['message'] : 'Действительно удалить?';
        $msg = $this->_format($msg, $model);

        $form = new Form_Frontend_Confirm($msg);

        return $form;
    }

    /**
     * Prepare form for multi-action.
     * Override it to customize form initialization
     *
     * @param  string $action
     * @param  array $models array(Model)
     * @param  array $params
     * @return Form
     */
    protected function _prepare_form_multi($action, array $models, array $params = NULL)
    {
        if ( ! count($models))
        {
            $msg = isset($params['message_empty']) ? $params['message_empty'] : 'Выберите хотя бы один элемент!';
            $form = new Form_Frontend_Error($msg);
            return $form;
        }

        $method = "_form_$action";
        if (method_exists($this, $method))
        {
            // Use user-defined method to prepare the form
            $form = $this->$method($models, $params);
        }
        else
        {
            $form = $this->_form_multi($action, $models, $params);
        }

        // If ids were obtained through $_POST - save them in serialized form in hidden input
        // change form's target action
        if (isset($_POST['ids']))
        {
            $form->action = URL::uri_to(NULL, array('action' => $action, 'history' => $this->request->param('history')), TRUE);
            if (is_array($_POST['ids']))
            {
                $ids = implode('_', $_POST['ids']);
            }
            else
            {
                $ids = (string) $_POST['ids'];
            }
            $element = new Form_Element_Hidden('ids');
            // Override full name, so it is not prefixed with forms name
            $element->full_name = 'ids';
            $element->value = $ids;
            $form->add_component($element);
        }

        return $form;
    }

    /**
     * Default method to prepare the form for multi action
     *
     * @param  string action
     * @param  array $models array(Model)
     * @param  array $params
     * @return Form
     */
    protected function _form_multi($action, array $models, array $params = NULL)
    {
        $form = isset($params['form']) ? $params['form'] : $this->_form;
        if ( ! isset($form))
        {
            throw new Kohana_Exception('Form is not defined for action :action in :controller',
                array(':action' => $action, ':controller' => get_class($this)));
        }

        // Create the form
        $form = new $form();

        // Display flash message
        if (isset($_GET['flash']))
        {
            $flash = (string) $_GET['flash'];

            if (isset($this->_actions[$action]["message_$flash"]))
            {
                $msg = $this->_actions[$action]["message_$flash"];
            }
            else
            {
                $msg = 'Действие выполнено успешно';
            }
            $form->message($msg);
        }

        return $form;
    }

    /**
     * Default form for multi-delete action
     *
     * @param  array $models array(Model)
     * @param  array $params
     * @return Form
     */
    protected function _form_multi_delete(array $models, array $params = NULL)
    {
        $msg = isset($params['message']) ? $params['message'] : 'Действительно удалить?';
        $msg = str_replace(':count', count($models), $msg);
        $form = new Form_Frontend_Confirm($msg);

        return $form;
    }

    // -------------------------------------------------------------------------
    // Validation
    // -------------------------------------------------------------------------
    /**
     * Validate the specified action
     *
     * @param  string $action
     * @param  Model  $model
     * @param  Form   $form
     * @param  array  $params
     * @return boolean
     */
    protected function _validate($action, Model $model, Form $form, array $params = NULL)
    {
        // Validate form
        $form_result = $form->validate();

        // Validate model
        if (isset($params['model_validate_method']))
        {
            $method = $params['model_validate_method'];
        }
        else
        {
            $method = "validate_$action";
        }

        $data = $form->get_values();
        $model_result = $model->$method($data);

        if ( ! $model_result)
        {
            // Add model validation errors to the form
            $form->errors($model->errors());
        }
        return ($model_result && $form_result);
    }

    /**
     * Validate the specified multi-action
     *
     * @param  string $action
     * @param  array  $models array(Model)
     * @param  Form   $form
     * @param  array  $params
     * @return boolean
     */
    protected function _validate_multi($action, array $models, Form $form, array $params = NULL)
    {
        // Validate form
        return $form->validate();
    }

    /**
     * Validate the multi_delete
     *
     * @param  array  $models array(Model)
     * @param  Form   $form
     * @param  array  $params
     * @return boolean
     */
    protected function _validate_multi_delete(array $models, Form $form, array $params = NULL)
    {
        // Validate form
        $form_result = $form->validate();

        // Validate models
        $models_result = TRUE;
        foreach ($models as $model)
        {
            if ( ! $model->validate_delete())
            {
                $form->errors($model->errors());
                $models_result = FALSE;
            }
        }

        return ($models_result && $form_result);
    }

    // -------------------------------------------------------------------------
    // Views
    // -------------------------------------------------------------------------
    /**
     * Prepare view for action
     *
     * @param  string $action
     * @param  Model $model
     * @param  Form $form
     * @param  array $params
     * @return View
     */
    protected function _prepare_view($action, Model $model, Form $form, array $params = NULL)
    {
        $method = "_view_$action";
        if (method_exists($this, $method))
        {
            // Use user-defined method to prepare the view
            $view = $this->$method($model, $form, $params);
        }
        else
        {
            $view = $this->_view($action, $model, $form, $params);
        }
        return $view;
    }

    /**
     * Default view for action
     * Override it to customize view initialization
     *
     * @param  string action
     * @param  Model $model
     * @param  Form  $form
     * @param  array $params
     * @return View
     */
    protected function _view($action, Model $model, Form $form, array $params = NULL)
    {
        $view = isset($params['view']) ? $params['view'] : $this->_view;
        if ( ! isset($view))
        {
            throw new Kohana_Exception('View script is not defined for action :action in :controller',
                array(':action' => $action, ':controller' => get_class($this)));
        }

        $view = new View($view);

        // View caption
        if (isset($params['view_caption']))
        {
            $view->caption = $this->_format($params['view_caption'], $model);
        }

        // Set form
        $view->form = $form;

        return $view;
    }

    /**
     * Prepare view for multi-action
     *
     * @param  string $action
     * @param  array $models array(Model)
     * @param  Form $form
     * @param  array $params
     * @return View
     */
    protected function _prepare_view_multi($action, array $models, Form $form, array $params = NULL)
    {
        $method = "_view_$action";
        if (method_exists($this, $method))
        {
            // Use user-defined method to prepare the view
            $view = $this->$method($models, $form, $params);
        }
        else
        {
            $view = $this->_view_multi($action, $models, $form, $params);
        }
        return $view;
    }

    /**
     * Default view for multi-action
     * Override it to customize view initialization
     *
     * @param  string action
     * @param  array $models array(Model)
     * @param  Form  $form
     * @param  array $params
     * @return View
     */
    protected function _view_multi($action, array $models, Form $form, array $params = NULL)
    {
        $view = isset($params['view']) ? $params['view'] : $this->_view;
        if ( ! isset($view))
        {
            throw new Kohana_Exception('View script is not defined for action :action in :controller',
                array(':action' => $action, ':controller' => get_class($this)));
        }

        $view = new View($view);

        // View caption
        if (isset($params['view_caption']))
        {
            $view->caption = $params['view_caption'];
        }

        // Render form
        $view->form = $form;

        return $view;
    }

    // -------------------------------------------------------------------------
    // Action handlers
    // -------------------------------------------------------------------------
    /**
     * Execute the specified action
     *
     * @param string $action
     * @param Model $model
     * @param Form $form
     * @param array $params
     */
    protected function _execute($action, Model $model, Form $form, array $params = NULL)
    {
        throw new Kohana_Exception(':action handler is not defined in :controller',
            array(':action' => $action, ':controller' => get_class($this)));
    }

    /**
     * Method is called before execution of $action
     *
     * @param string $action
     * @param Model $model
     * @param Form $form
     * @param array $params
     */
    protected function _before_execute($action, Model $model, Form $form, array $params = NULL)
    {
    }
    
    /**
     * Method is called after succesfull execution of $action
     *
     * @param string $action
     * @param Model $model
     * @param Form $form
     * @param array $params
     */
    protected function _after_execute($action, Model $model, Form $form, array $params = NULL)
    {
        // Invalidate cache tags, if any
        if ( ! empty($params['cache_tags']))
        {
            $cache = Cache::instance();
            foreach ($params['cache_tags'] as $tag)
            {
                $cache->delete_tag($tag);
            }
        }
    }

    /**
     * Execute the specified multi-action
     *
     * @param string $action
     * @param array $models array(Model)
     * @param Form $form
     * @param array $params
     */
    protected function _execute_multi($action, array $models, Form $form, array $params = NULL)
    {
        throw new Kohana_Exception(':action handler is not defined in :controller',
            array(':action' => $action, ':controller' => get_class($this)));
    }

    /**
     * Method is called after succesfull execution of multi $action
     *
     * @param string $action
     * @param array $model array(Model)
     * @param Form $form
     * @param array $params
     */
    protected function _after_execute_multi($action, array $models, Form $form, array $params = NULL)
    {
        // Invalidate cache tags, if any
        if ( ! empty($params['cache_tags']))
        {
            $cache = Cache::instance();
            foreach ($params['cache_tags'] as $tag)
            {
                $cache->delete_tag($tag);
            }
        }
    }

    /**
     * Create model
     *
     * @param Model $model
     * @param Form $form
     * @param array $params
     */
    protected function _execute_create(Model $model, Form $form, array $params = NULL)
    {
	$this->_before_execute('create', $model, $form, $params);        
        
        $model->backup();

        $model->values($form->get_values());

        $model->save();

        // Saving failed
        if ($model->has_errors())
        {
            $form->errors($model->errors());
            return;
        }

        $this->_after_execute('create', $model, $form, $params);

        $this->request->redirect($this->_redirect_uri('create', $model, $form, $params));
    }

    /**
     * Update model
     *
     * @param Model $model
     * @param Form $form
     * @param array $params
     */
    protected function _execute_update(Model $model, Form $form, array $params = NULL)
    {
	$this->_before_execute('update', $model, $form, $params);        
        
        $model->backup();
        $model->values($form->get_values());
        $model->save();

        // Saving failed
        if ($model->has_errors())
        {
            $form->errors($model->errors());
            return;
        }

        $this->_after_execute('update', $model, $form, $params);

        $this->request->redirect($this->_redirect_uri('update', $model, $form, $params) . '?flash=ok');
    }

    /**
     * Delete model
     *
     * @param Model $model
     * @param Form $form
     * @param array $params
     */
    protected function _execute_delete(Model $model, Form $form, array $params = NULL)
    {
	$this->_before_execute('delete', $model, $form, $params);
                
        $model->delete();

        // Deleting failed
        if ($model->has_errors())
        {
            $form->errors($model->errors());
            return;
        }

        $this->_after_execute('delete', $model, $form, $params);

        $this->request->redirect($this->_redirect_uri('delete', $model, $form, $params));
    }

    /**
     * Delete multiple models
     *
     * @param array $models array(Model)
     * @param Form $form
     * @param array $params
     */
    protected function _execute_multi_delete(array $models, Form $form, array $params = NULL)
    {
        $result = TRUE;

        foreach ($models as $model)
        {
            $model->delete();

            // Deleting failed
            if ($model->has_errors())
            {
                $form->errors($model->errors());
                $result = FALSE;
            }
        }

        if ( ! $result)
            return; // Deleting of at least one model failed...

        $this->_after_execute_multi('multi_delete', $models, $form, $params);

        $this->request->redirect($this->_redirect_uri('multi_delete', $model, $form, $params));
    }

    /**
     * Move model up
     *
     * @param Model $model
     * @param array $params
     */
    protected function _execute_up(Model $model, array $params = NULL)
    {
        $model->up();
        $this->request->redirect($this->_redirect_uri('up', $model, NULL, $params));
    }

    /**
     * Move model down
     *
     * @param Model $model
     * @param array $params
     */
    protected function _execute_down(Model $model, array $params = NULL)
    {
        $model->down();
        $this->request->redirect($this->_redirect_uri('down', $model, NULL, $params));
    }

    /**
     * Generate redirect url for action
     *
     * @param  string $action
     * @param  Model $model
     * @param  Form $form
     * @param  array $params
     * @return string
     */
    protected function _redirect_uri($action, Model $model = NULL, Form $form = NULL, array $params = NULL)
    {
        switch ($action)
        {
            case 'create': case 'delete': case 'multi_delete': case 'up': case 'down':
                $redirect_uri = isset($params['redirect_uri']) ? $params['redirect_uri'] : URL::uri_back();
                break;

            case 'update':
                $redirect_uri = isset($params['redirect_uri']) ? $params['redirect_uri'] : $this->request->uri;
                break;

            default:
                $redirect_uri = '';
        }
        return $redirect_uri;
    }

    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------
    /**
     * Do the POST action
     *
     * @param string $action
     * @param array $params
     */
    protected function _action($action, array $params = array())
    {
        try {

            if (isset($this->_actions[$action]))
            {
                $params = array_merge($this->_actions[$action], $params);
            }
            if (isset($params['action']))
            {
                // Change action
                $action = $params['action'];
            }

            // Prepare model for action
            $model = $this->_prepare_model($action, $params);

            // Prepare form for action
            $form = $this->_prepare_form($action, $model, $params);
            
            // It's a POST action (create, update, delete, ...)
            if ($form->is_submitted())
            {
                // ----- Execute the action
                $action_to_do = $action;

                // Is there a sub-action executed?
                if ( ! empty($params['subactions']))
                {
                    foreach ($params['subactions'] as $subaction)
                    {
                        if ($form->has_component($subaction) && $form->get_value($subaction))
                        {
                            $action_to_do = $subaction;
                            break;
                        }
                    }
                }

                // Validate action
                $method = "_validate_$action_to_do";
                if (method_exists($this, $method))
                {
                    // Use user-defined method to validate the action
                    $result = $this->$method($model, $form, $params);
                }
                else
                {
                    $result = $this->_validate($action_to_do, $model, $form, $params);
                }
                if ($result)
                {
                    // Execute
                    $method = "_execute_$action_to_do";
                    if (method_exists($this, $method))
                    {
                        // Use user-defined method to execute the action
                        $result = $this->$method($model, $form, $params);
                    }
                    else
                    {
                        $result = $this->_execute($action_to_do, $model, $form, $params);
                    }
                }
            }

            // Prepare view for action
            $view = $this->_prepare_view($action, $model, $form, $params);
            
            // Render view & layout
            $this->request->response = $this->render_layout($view);
        }
        catch (Controller_FrontendCRUD_Exception $e)
        {
            // An error happened during action execution
            $this->_action_error($e->getMessage());
        }
    }

    /**
     * Do the POST multi-action: action peformed on the list of models
     *
     * @param string $action
     * @param array $params
     */
    protected function _action_multi($action, array $params = array())
    {
        try {

            if (isset($this->_actions[$action]))
            {
                $params = array_merge($this->_actions[$action], $params);
            }

            if (isset($params['action']))
            {
                // Change action
                $action = $params['action'];
            }

            // Prepare models for multi-action
            $models = $this->_prepare_models($action, $params);

            // Prepare form for multi-action
            $form = $this->_prepare_form_multi($action, $models, $params);

            // It's a POST action (create, update, delete, ...)
            if ($form->is_submitted())
            {
                // ----- Execute the action

                // Validate action
                $method = "_validate_$action";
                if (method_exists($this, $method))
                {
                    // Use user-defined method to validate the action
                    $result = $this->$method($models, $form, $params);
                }
                else
                {
                    $result = $this->_validate_multi($action, $models, $form, $params);
                }

                if ($result)
                {
                    // Execute
                    $method = "_execute_$action";
                    if (method_exists($this, $method))
                    {
                        // Use user-defined method to execute the action
                        $result = $this->$method($models, $form, $params);
                    }
                    else
                    {
                        $result = $this->_execute_multi($action, $models, $form, $params);
                    }
                }
            }

            // Prepare view for multi-action
            $view = $this->_prepare_view_multi($action, $models, $form, $params);

            // Render view & layout
            $this->request->response = $this->render_layout($view);
        }
        catch (Controller_FrontendCRUD_Exception $e)
        {
            // An error happened during action execution
            $this->_action_error($e->getMessage());
        }
    }

    /**
     * Do the GET action
     *
     * @param string $model
     * @param array $params
     */
    protected function _action_get($action, array $params = array())
    {
        try {

            if (isset($this->_actions[$action]))
            {
                $params = array_merge($this->_actions[$action], $params);
            }

            if (isset($params['action']))
            {
                // Change action
                $action = $params['action'];
            }

            // Prepare model for action
            $model = $this->_prepare_model($action, $params);

            // Execute (should always redirect)
            $method = "_execute_$action";
            if (method_exists($this, $method))
            {
                // Use user-defined method to execute the action
                $result = $this->$method($model, $params);
            }
            else
            {
                $result = $this->_execute($action, $model, $params);
            }

        }
        catch (Controller_FrontendCRUD_Exception $e)
        {
            // An error happened during action execution
            $this->_action_error($e->getMessage());
        }
    }

    /**
     * Create new model
     */
    public function action_create()
    {
        $this->_action('create');
    }

    /**
     * Update model
     */
    public function action_update()
    {        
        $this->_action('update');
    }

    /**
     * Delete model
     */
    public function action_delete()
    {
        $this->_action('delete');
    }

    /**
     * Move model up
     */
    public function action_up()
    {
        $this->_action_get('up');
    }

    /**
     * Move model down
     */
    public function action_down()
    {
        $this->_action_get('down');
    }

    /**
     * Handle ajax validation
     */
    public function action_validate()
    {
        try {

            // Action to validate
            $action = $this->request->param('v_action');

            $params = array();
            if (isset($this->_actions[$action]))
            {
                $params = $this->_actions[$action];
            }

            if (isset($params['action']))
            {
                // Change action
                $action = $params['action'];
            }

            // Prepare model for action
            $model = $this->_prepare_model($action, $params);

            // Prepare form for action
            $form = $this->_prepare_form($action, $model, $params);

            // Validate action
            $method = "_validate_$action";
            if (method_exists($this, $method))
            {
                // Use user-defined method to validate the action
                $result = $this->$method($model, $form, $params);
            }
            else
            {
                $result = $this->_validate($action, $model, $form, $params);
            }

            // Return errors (if any) in JSON format
            $this->request->response = json_encode($form->errors());

        }
        catch (Exception $e)
        {
            // An error happened during action execution
            $this->request->response = json_encode(array($e->getMessage()));
        }
    }

    // -------------------------------------------------------------------------
    // Multi-actions
    // -------------------------------------------------------------------------
    /**
     * Multi-action: action with the list of models
     */
    public function action_multi()
    {
        // What action to peform?
        if (empty($_POST['action']) || ! is_array($_POST['action']))
        {
            $this->request->redirect(URL::uri_back ());
        }

        $action = array_keys($_POST['action']);
        $action = $action[0];

        // Model ids
        if (isset($_POST['ids']) && is_array($_POST['ids']))
        {
            $ids = $_POST['ids'];
        }
        else
        {
            $ids = array();
        }
        if (count($ids) > 20)
        {
            // Too many ids selected - do not redirect, forward as post action
            $this->request->forward(get_class($this), $action);            
        }
        else
        {
            $ids = implode('_', $ids);

            // Redirect to action
            $this->request->redirect(URL::uri_to(NULL, array('action' => $action, 'ids' => $ids, 'history' => $this->request->param('history')), TRUE));
        }
    }

    /**
     * Delete multiple models
     *
     * @param array $ids
     */
    public function action_multi_delete()
    {
        $this->_action_multi('multi_delete');
    }

    // -------------------------------------------------------------------------
    // Misc
    // -------------------------------------------------------------------------
    /**
     * Replace placeholders in string by actual values from model.
     *
     * @param  string $str
     * @param  Model $model
     * @return string
     */
    protected function _format($str, Model $model = NULL)
    {
        if ($model === NULL)
        {
            return $str;
        }

        preg_match_all('/:(\w+)/', $str, $matches, PREG_SET_ORDER);
        foreach ($matches as $match)
        {
            $param = $match[1];
            $str = str_replace($match[0], HTML::chars($model->$param), $str);
        }

        return $str;
    }
}
