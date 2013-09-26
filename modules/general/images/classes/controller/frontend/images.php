<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Images extends Controller_FrontendCRUD
{
    /**
     * Configure actions
     * @var array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Image';
        $this->_form  = 'Form_Frontend_Image';
        
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
        $view = new View('frontend/workspace');
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
        Layout::instance()->add_style(Modules::uri('images') . '/public/css/frontend/images.css');

        $order_by = $this->request->param('img_order_by', 'position');
        $desc     = (boolean) $this->request->param('img_desc', '0');
        
        $images = Model::fly('Model_Image')->find_all_by_owner_type_and_owner_id(
            $owner_type, $owner_id, array('order_by' => $order_by, 'desc' => $desc)
        );

        $view = new View('frontend/images');

        $view->images     = $images;
        $view->owner_type = $owner_type;
        $view->owner_id   = $owner_id;
        $view->config     = $config;

        $view->desc = $desc;
        
        return $view;
    }

    /**
     * Redraw product images widget via ajax request
     */
    public function action_ajax_create()
    {
        $request = Widget::switch_context();
//
//        $product = Model_Product::current();
//
//        $user = Model_User::current();
//        
//        if ( ! isset($product->id) && ! isset($user->id))
//        {   
//            $this->_action_404();
//            return;
//        }
//
//        $telemost = new Model_Telemost();
//        $telemost->find_by_product_id($product->id, array('owner' => $user));
//        
//        if ($telemost->id) {
//            $telemost->delete();
//        }
//            
        $owner_type = $this->request->param('owner_type',NULL);
        $owner_id   = $this->request->param('owner_id',NULL);
        $config = $this->request->param('config');
        
        $widget = $request->get_controller('images')
                ->widget_small_images($owner_type,$owner_id,$config);

        $widget->to_response($this->request);

        $this->_action_ajax();            
    }    
    
    /**
     * Renders list of images for specified owner
     *
     * @param  string  $owner_type
     * @param  integer $owner_id
     * @return string
     */
    public function widget_small_images($owner_type, $owner_id, $config)
    {
        $widget = new Widget('frontend/small_images');
        $widget->id = 'image_' . $owner_id;
        $widget->context_uri = FALSE; // use the url of clicked link as a context url     
        
        // Add styles to layout
        Layout::instance()->add_style(Modules::uri('images') . '/public/css/frontend/images.css');

        $order_by = $this->request->param('img_order_by', 'position');
        $desc     = (boolean) $this->request->param('img_desc', '0');
        
        $images = Model::fly('Model_Image')->find_all_by_owner_type_and_owner_id(
            $owner_type, $owner_id, array('order_by' => $order_by, 'desc' => $desc)
        );

        $widget->images     = $images;
        $widget->owner_type = $owner_type;
        $widget->owner_id   = $owner_id;
        $widget->config     = $config;

        $widget->desc = $desc;
        
        return $widget;
    }
    
    public function widget_create($owner_type, $owner_id, $config)
    {
        $view = new View('frontend/create');
        $image = new Model_Image();
        $form_image_ajax = new Form_Frontend_ImageAJAX();
        if ($form_image_ajax->is_submitted())
        {
            // User is trying to log in
            if ($form_image_ajax->validate())
            {   
                $vals = $form_image_ajax->get_values();
                $vals['owner_type'] = $owner_type;
                $vals['owner_id'] = $owner_id;
                $vals['config'] = $config;

                if ($image->validate($vals))
                {                    
                    $image->values($vals);
                    $image->save();
                }
            }
        }
        $modal = Layout::instance()->get_placeholder('modal');
        $modal .= ' '.$form_image_ajax->render();
        Layout::instance()->set_placeholder('modal',$modal);
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
        Layout::instance()->add_style(Modules::uri('images') . '/public/css/frontend/images.css');

        $image = new Model_Image();
        $image->find_by_owner_type_and_owner_id($owner_type, $owner_id);

        $image->owner_type = $owner_type;
        $image->owner_id   = $owner_id;
        $image->config     = $config;

        $view = new View('frontend/image');

        $view->image = $image;

        return $view;
    }
    
}