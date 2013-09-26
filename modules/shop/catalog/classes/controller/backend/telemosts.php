<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Telemosts extends Controller_BackendRES
{
    /**
     * Configure actions
     *
     * @return array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Telemost';
        $this->_form  = 'Form_Backend_Telemost';
        $this->_view  = 'backend/form';
        
        return array(
            'create' => array(
                'view_caption' => 'Создание телемоста'
            ),
            'update' => array(
                'view_caption' => 'Редактирование телемоста'
            ),
            'delete' => array(
                'view_caption' => 'Удаление телемоста',
                'message' => 'Удалить телемост?'
            )
        );
    }

    /**
     * Create layout (proxy to products controller)
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        return $this->request->get_controller('products')->prepare_layout($layout_script);
    }

    /**
     * Render layout (proxy to products controller)
     *
     * @param  View|string $content
     * @param  string $layout_script
     * @return string
     */
    public function render_layout($content, $layout_script = NULL)
    {
        return $this->request->get_controller('products')->render_layout($content, $layout_script);
    }

    /**
     * Prepare telemost model for create action
     *
     * @param  string|Model_Telemost $telemost
     * @param  array $params
     * @return Model_Telemost
     */
    protected function  _model_create($telemost, array $params = NULL)
    {
        $telemost = parent::_model('create', $telemost, $params);

        $product_id = (int) $this->request->param('product_id');
        if ( ! Model::fly('Model_Product')->exists_by_id($product_id))
        {
            throw new Controller_BackendCRUD_Exception('Указанный анонс не существует!');
        }

        $telemost->product_id = $product_id;

        return $telemost;
    }

    /**
     * Renders list of product telemosts
     *
     * @param  Model_Product $product
     * @return string
     */
    public function widget_telemosts($product)
    {
        $app_telemosts = $product->app_telemosts;
        $telemosts = $product->telemosts;

        // Set up view
        $view = new View('backend/telemosts');

        $view->product         = $product;
        $view->app_telemosts = $app_telemosts;        
        $view->telemosts = $telemosts;

        return $view->render();
    }
    
    /**
     * Choose telemost application
     */
    public function action_choose()
    {
        $this->_action_get('choose');
    }
    
    /**
     * Choose telemost application
     *
     * @param Model $model
     * @param Form $form
     * @param array $params
     */
    protected function _execute_choose(Model $model, array $params = NULL)
    {        
        $model->active = TRUE;
        if ($model->validate_choose()) {
            $model->save();            
        }
        $this->request->redirect(URL::uri_back());
        
        
        /*$telemost_id = (int) $this->request->param('id');
        
        $telemost = Model::fly('Model_Telemost')->find($telemost_id);
        if ( ! $telemost->id)
        {
            throw new Controller_BackendCRUD_Exception('Указанная заявка на телемост не существует!');
        }        
        $telemost->active = TRUE;
        $telemost->save();
        $this->request->redirect(URL::uri_back());
        */
    }
}