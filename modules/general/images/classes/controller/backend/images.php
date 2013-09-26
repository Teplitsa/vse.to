<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Images extends Controller_BackendCRUD
{
    /**
     * Configure actions
     * @var array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Image';
        $this->_form  = 'Form_Backend_Image';
        
        return array(
            'create' => array(
                'view_caption' => 'Создание изображения'
            ),
            'update' => array(
                'view_caption' => 'Редактирование изображения'
            ),
            'delete' => array(
                'view_caption' => 'Удаление изображения',
                'message' => 'Удалить изображение?'
            )
        );
    }

    /**
     * Render layout
     *
     * @param  View|string $content
     * @param  string $layout_script
     * @return string
     */
    public function render_layout($content, $layout_script = NULL)
    {
        $view = new View('backend/workspace');
        $view->content = $content;

        $layout = $this->prepare_layout($layout_script);
        $layout->caption = 'Изображения';
        $layout->content = $view;
        return $layout->render();
    }

    /**
     * Prepare image for update & delete actions
     *
     * @param  string $action
     * @param  string|Model $model
     * @param  array $params
     * @return Model_image
     */
    protected function _model($action, $model, array $params = NULL)
    {
        $image = parent::_model($action, $model, $params);

        if ($action == 'create')
        {
            // Set up onwer for image being created
            $image->owner_type = $this->request->param('owner_type');
            $image->owner_id   = $this->request->param('owner_id');
        }

        // Set up config for image in action
        $image->config = $this->request->param('config');

        return $image;
    }

    /**
     * Renders list of images for specified owner
     *
     * @param  string  $owner_type
     * @param  integer $owner_id
     * @return string
     */
    public function widget_images($owner_type, $owner_id, $config)
    {
        // Add styles to layout
        Layout::instance()->add_style(Modules::uri('images') . '/public/css/backend/images.css');

        $order_by = $this->request->param('img_order_by', 'position');
        $desc     = (boolean) $this->request->param('img_desc', '0');
        
        $images = Model::fly('Model_Image')->find_all_by_owner_type_and_owner_id(
            $owner_type, $owner_id, array('order_by' => $order_by, 'desc' => $desc)
        );

        $view = new View('backend/images');

        $view->images     = $images;
        $view->owner_type = $owner_type;
        $view->owner_id   = $owner_id;
        $view->config     = $config;

        $view->desc = $desc;
        
        return $view;
    }

    /**
     * Render one image for specified owner
     *
     * @param  string  $owner_type
     * @param  integer $owner_id
     * @return string
     */
    public function widget_image($owner_type, $owner_id, $config)
    {
        // Add styles to layout
        Layout::instance()->add_style(Modules::uri('images') . '/public/css/backend/images.css');

        $image = new Model_Image();
        $image->find_by_owner_type_and_owner_id($owner_type, $owner_id);

        $image->owner_type = $owner_type;
        $image->owner_id   = $owner_id;
        $image->config     = $config;

        $view = new View('backend/image');

        $view->image = $image;

        return $view;
    }
}