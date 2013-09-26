<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Telemosts extends Controller_FrontendRES
{
    /**
     * Configure actions
     *
     * @return array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Telemost';
        $this->_form  = 'Form_Frontend_Telemost';
        $this->_view  = 'frontend/form';
        
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
     * Prepare layout
     *
     * @param  string $layout_script
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        return parent::prepare_layout($layout_script);
    }

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
    public function widget_app_telemosts_by_product($product,$script = 'frontend/app_telemosts_by_product')
    {
        $app_telemosts = $product->app_telemosts;
        // Set up view
        $view = new View($script);

        $view->product         = $product;
        $view->app_telemosts = $app_telemosts;        
        $view->telemosts = $telemosts;
        
        return $view->render();
    }

    public function widget_app_telemosts_by_owner($owner,$view = 'frontend/small_app_telemosts')
    {   
        $widget = new Widget($view);
        $widget->id = 'app_telemosts';
        $widget->ajax_uri = NULL;
        $widget->context_uri = FALSE; // use the url of clicked link as a context url
        
        // ----- List of products
        $telemost = Model::fly('Model_Telemost');
        
        $per_page = 1000;
        $count = $telemost->count_by_owner($owner->id);        
        $pagination = new Paginator($count, $per_page, 'rpage', 7,NULL,'frontend/catalog/ajax_app_telemosts',NULL,'ajax');
        
        $order_by = $this->request->param('cat_torder', 'created_at');
        $desc = (bool) $this->request->param('cat_tdesc', '0');

        $params['offset'] = $pagination->offset;
        $params['limit']  = $pagination->limit;        
        $params['order_by'] = $order_by;
        $params['desc']     = $desc;
        $params['owner'] = $owner;

        $telemosts = $telemost->find_all_by_active_and_visible(FALSE,TRUE,$params);
        
        $params['offset'] = $pagination->offset;
        $widget->order_by = $order_by;
        $widget->desc = $desc;
        
        $widget->telemosts = $telemosts;

        $widget->pagination = $pagination->render('pagination');
        
        return $widget;
    }
    
    /**
     * Redraw product images widget via ajax request
     */
    public function action_ajax_app_telemosts()
    {
        $request = Widget::switch_context();

        $user = Model_User::current();
        if ( ! isset($user->id))
        {
            //FlashMessages::add('', FlashMessages::ERROR);
            $this->_action_ajax();
            return;
        }

        $widget = $request->get_controller('telemosts')
                ->widget_app_telemosts_by_owner($user);

        $widget->to_response($this->request);
        $this->_action_ajax();
    }     
    

    public function widget_telemosts_by_owner($owner,$view = 'frontend/small_telemosts')
    {   
        $widget = new Widget($view);
        $widget->id = 'telemosts';
        $widget->ajax_uri = NULL;
        $widget->context_uri = FALSE; // use the url of clicked link as a context url
        
        // ----- List of products
        $telemost = Model::fly('Model_Telemost');

        $per_page = 1000;
        $count = $telemost->count_by_owner($owner->id);        
        $pagination = new Paginator($count, $per_page, 'tpage', 7,NULL,'frontend/catalog/ajax_telemosts',NULL,'ajax');

        $order_by = $this->request->param('cat_torder', 'created_at');
        $desc = (bool) $this->request->param('cat_tdesc', '0');

        $params['offset'] = $pagination->offset;
        $params['limit']  = $pagination->limit;        
        $params['order_by'] = $order_by;
        $params['desc']     = $desc;
        $params['owner'] = $owner;

        $telemosts = $telemost->find_all_by_active_and_visible(TRUE,TRUE,$params);

        // Set up view
        $params['offset'] = $pagination->offset;
        $widget->order_by = $order_by;
        $widget->desc = $desc;
        
        $widget->telemosts = $telemosts;

        $widget->pagination = $pagination->render('pagination');
        
        return $widget;
    }

    /**
     * Redraw product images widget via ajax request
     */
    public function action_ajax_telemosts()
    {
        $request = Widget::switch_context();

        $user = Model_User::current();
        if ( ! isset($user->id))
        {
            //FlashMessages::add('', FlashMessages::ERROR);
            $this->_action_ajax();
            return;
        }

        $widget = $request->get_controller('telemosts')
                ->widget_telemosts_by_owner($user);

        $widget->to_response($this->request);
        $this->_action_ajax();
    }     

    
    public function widget_goes_by_owner($owner,$view = 'frontend/small_goes')
    {           
        $go = Model::fly('Model_Go');

        $per_page = 1000;
        $count = $go->count_by_owner($owner->id);        
        $pagination = new Paginator($count, $per_page, 'mpage', 7,NULL,'frontend/catalog/ajax_goes',NULL,'ajax');

        $order_by = $this->request->param('cat_torder', 'created_at');
        $desc = (bool) $this->request->param('cat_tdesc', '0');

        $params['offset'] = $pagination->offset;
        $params['limit']  = $pagination->limit;        
        $params['order_by'] = $order_by;
        $params['desc']     = $desc;
        $params['owner'] = $owner;

        $goes = $go->find_all($params);
        $products = new Models('Model_Product',array());
        $i=0;
        foreach ($goes as $go) {
            $products[$i] = $go->telemost->product;
            $i++;
        }
        
        $params['offset'] = $pagination->offset;
        $view = new View($view);
        $view->order_by = $order_by;
        $view->desc = $desc;
        
        $view->products = $products;

        $view->pagination = $pagination->render('pagination');
        
        return $view->render();
    }

    /**
     * Redraw product images widget via ajax request
     */
    public function action_ajax_goes()
    {
        $request = Widget::switch_context();

        $user = Model_User::current();
        if ( ! isset($user->id))
        {
            //FlashMessages::add('', FlashMessages::ERROR);
            $this->_action_ajax();
            return;
        }

        $widget = $request->get_controller('telemosts')
                ->widget_goes_by_owner($user);

        $widget->to_response($this->request);
        $this->_action_ajax();
    }     
    /**
     * Render auth form
     */
    public function widget_request($product)
    {
        $user= Model_User::current();

        if ( $product->id===NULL || $user->id===NULL)
        {   
            $this->_action_404();
            return;
        }        
  
        $telemost = new Model_Telemost();
        
        $view = new View('frontend/telemosts/request');

        $form_request = new Form_Frontend_Telemost($telemost);
        if ($form_request->is_submitted())
        {
            // User is trying to log in
            if ($form_request->validate())
            {   
                $vals = $form_request->get_values();
                $vals['user_id'] = $user->id;

                if ($telemost->validate($vals))
                {                    
                    $telemost->values($vals);
                    $telemost->product_id = $product->id;

                    $telemost->save();
                    $this->request->redirect($product->uri_frontend());
                }
            }
        }
        $modal = Layout::instance()->get_placeholder('modal');
        $modal .= ' '.$form_request->render();
        Layout::instance()->set_placeholder('modal',$modal);
        return $view;
    }
    
    /**
     * Redraw product images widget via ajax request
     */
    public function action_ajax_telemost_select()
    {
        $telemost_id = (int) $this->request->param('telemost_id');
        
        $request = Widget::switch_context();

        $telemost = Model::fly('Model_Telemost')->find($telemost_id);

        $user = Model_User::current();
        
        if ( ! isset($telemost->id) && ! isset($user->id) && ($telemost->user_id == $user->id))
        {   
            $this->_action_404();
            return;
        }
        
        $telemost->active = TRUE;
        if ($telemost->validate_choose()) {
            $telemost->save();            
        }
        
        $widget = $request->get_controller('products')
                ->widget_product($telemost->product);

        $widget->to_response($this->request);
            
        $this->_action_ajax();        
    }    
    
}
