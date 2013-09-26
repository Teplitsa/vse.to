<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_ProductComments extends Controller_BackendRES
{
    /**
     * Configure actions
     *
     * @return array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_ProductComment';
        $this->_form  = 'Form_Backend_ProductComment';
        $this->_view  = 'backend/form';
        
        return array(
            'create' => array(
                'view_caption' => 'Создание комментария к событию № :product_id'
            ),
            'update' => array(
                'view_caption' => 'Редактирование комментария к событию'
            ),
            'delete' => array(
                'view_caption' => 'Удаление комментария к событию',
                'message' => 'Удалить комментарий к событию : caption?'
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
     * Prepare productcomment model for create action
     *
     * @param  string|Model_ProductComment $productcomment
     * @param  array $params
     * @return Model_ProductComment
     */
    protected function  _model_create($productcomment, array $params = NULL)
    {
        $productcomment = parent::_model('create', $productcomment, $params);

        $product_id = (int) $this->request->param('product_id');
        if ( ! Model::fly('Model_Product')->exists_by_id($product_id))
        {
            throw new Controller_BackendCRUD_Exception('Указанное событие не существует!');
        }

        $productcomment->product_id = $product_id;

        return $productcomment;
    }

    /**
     * Renders list of product comments
     *
     * @param  Model_Product $product
     * @return string
     */
    public function widget_productcomments($product)
    {
        $productcomments = $product->comments;

        // Set up view
        $view = new View('backend/productcomments');

        $view->product         = $product;
        $view->productcomments = $productcomments;

        return $view->render();
    }
}