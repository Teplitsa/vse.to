<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Products extends Controller_FrontendRES
{
    
    // -----------------------------------------------------------------------
    // MENU WIDGETS
    // -----------------------------------------------------------------------
    /**
     * Render search bar
     */
    public function widget_search()
    {
        $sectiongroup = Model_SectionGroup::current();
        
        $form = new Form_Frontend_Search();

        $form->get_element('search_text')->default_value = URL::decode($this->request->param('search_text'));

        if ($form->is_submitted() && $form->validate())
        {
            $search_text = URL::encode($form->get_value('search_text'));
            $this->request->redirect(URL::uri_to('frontend/catalog/search', array('search_text' => $search_text)));
        }
        
        return $form->render();
    }
    
    public function widget_format_select()
    {   
        $format = $this->request->param('format',NULL);
        
        $formats = Model_Product::$_format_options;

        $view = new View('frontend/products/format_select');
        
        $view->formats = $formats;
        
        $view->format = $format;
        
        return $view->render();        
    }    

    public function widget_theme_select()
    {           
        $theme = $this->request->param('theme',NULL);
        
        $themes = Model_Product::$_theme_options;

        $view = new View('frontend/products/theme_select');
        
        $view->themes = $themes;
        
        $view->theme = $theme;
        
        return $view->render();        
    }    
    
    public function widget_calendar_select()
    {   
        $calendar = $this->request->param('calendar',NULL);
        
        $calendars = Model_Product::$_calendar_options;
        
        if (Modules::registered('jquery')) {
            jQuery::add_scripts();
            Layout::instance()->add_script(Modules::uri('jquery') . '/public/js/datetimesimple.js');
            Layout::instance()->add_script(Modules::uri('catalog') . '/public/js/frontend/datesearch.js');            
        }    
        $view = new View('frontend/products/calendar_select');

        Layout::instance()->add_script(
            "var datesearch_url='" . URL::to('frontend/catalog/search', array('date'=>'{{d}}'), TRUE) . "';\n\n",TRUE);
                
        
                
        $view->form = new Form_Frontend_Datesearch();

        $view->calendars = $calendars;
        
        $view->calendar = $calendar;
        
        return $view->render();        
    }      
    
    
    // -----------------------------------------------------------------------
    // INDEX PAGE
    // -----------------------------------------------------------------------
    
    /**
     * Render list of products in section
     */
    public function action_index()
    {   
        $view = new View('frontend/workspace');

        $view->content = $this->widget_list_products();

        $layout = $this->prepare_layout();
        $layout->content = $view;
        
        // Add breadcrumbs
        //$this->add_breadcrumbs();
        $this->request->response = $layout->render();
    }
    
    public function widget_list_products() {        
        $section = Model_Section::current();
        
        if ( ! isset($section->id))
        {
            $this->_action_404('Указанный раздел не найден');
            return;
        }
        $product = Model::fly('Model_Product');

        // build search condition
        $search_params = array(
            'section' => $section,
            'active'  => -1,
            'section_active' => 1,
        );
        $town_alias = Cookie::get(Model_Town::TOWN_TOKEN, Model_Town::ALL_TOWN);
        if($town_alias == Model_Town::ALL_TOWN)
           $search_params['all_towns'] = true;
        
        $format = $this->request->param('format',NULL);
        if ($format) $search_params['format'] = $format;

        $calendar = $this->request->param('calendar',NULL);
        if ($calendar) $search_params['calendar'] = $calendar;
        
        $theme = $this->request->param('theme',NULL);
        if ($theme) $search_params['theme'] = $theme;
        list($search_condition, $params) = $product->search_condition($search_params);

        // count & find products by search condition
        $pages = (int)$this->request->param('page',1);

        $per_page = 50*$pages;//4*$pages;

        $count = $product->count_by($search_condition, $params);

        $pagination = new Pagination($count, $per_page, 'page', 7);
        $pagination->offset = 0;
        $order_by = $this->request->param('cat_porder', 'datetime');
        $desc = false;//(bool) $this->request->param('cat_pdesc', '0');

        $params['offset'] = $pagination->offset;
        $params['limit']  = $pagination->limit;
        $params['order_by'] = $order_by;
        $params['desc'] = $desc;

        $params['with_image'] = 3;
        $params['with_sections'] = TRUE;

        $products = $product->find_all_by($search_condition, $params);
        // Set up view
        $view = new View('frontend/products/list');

        $view->order_by = $order_by;
        $view->desc = $desc;
        //$view->properties = $properties;
        $view->products = $products;

        $view->pagination = $pagination->render('pagination_load');

        return $view->render();
    }

    /**
     * Search products
     */
    public function action_search()
    {
        $view = new View('frontend/workspace');

        if ($this->request->param('tag',NULL))
            $view->content = $this->widget_search_products_by_tags();
        else        
            $view->content = $this->widget_search_products();

        $layout = $this->prepare_layout();
        $layout->content = $view;
        
        // Add breadcrumbs
        //$this->add_breadcrumbs();
        $this->request->response = $layout->render();
    }    
    

    public function widget_search_products_by_tags($view_file = 'frontend/products/search')
    {
        $tag_alias = $this->request->param('tag',NULL);
        
        $products = array();
        
        if (!$tag_alias) {
            $this->_action_404();
            return;            
        }
        
        $tag = new Model_Tag();
        $search_condition['alias'] = $tag_alias;
        $search_condition['owner_type'] = 'product';

        $town_alias = Cookie::get(Model_Town::TOWN_TOKEN);
        if($town_alias == Model_Town::ALL_TOWN)
           $search_params['all_towns'] = true;

        $pages = (int)$this->request->param('page',1); 
        $per_page = 4*$pages;
        $count = $tag->count_by($search_condition);

        $pagination = new Pagination($count, $per_page, 'page', 7);
        $pagination->offset = 0;
        $order_by = $this->request->param('cat_porder', 'price');
        $desc = (bool) $this->request->param('cat_pdesc', '1');

        $params['offset'] = $pagination->offset;
        $params['limit']  = $pagination->limit;
        $params['order_by'] = $order_by;
        $params['desc'] = $desc;

        $params['with_image'] = 3;
        $params['with_sections'] = TRUE;

        $tags = $tag->find_all_by($search_condition);

        $ids = array();
        foreach ($tags as $tag) {
            $ids[] =$tag->owner_id;
        }

        if (count($ids)) {
            $products = Model::fly('Model_Product')->find_all_by(array('ids' => $ids),$params);
        }    
        
        // Set up view
        $view = new View($view_file);
        $view->order_by = $order_by;
        $view->desc = $desc;
        $view->cols = 3;
        
        //$view->properties = $properties;
        $view->products = $products;

        $view->pagination = $pagination->render('pagination_load');

        // Add breadcrumbs
        //$this->add_breadcrumbs();

        return $view->render();
        
        
    }       
    
    public function widget_search_products(array $search_params = NULL,$view_file = 'frontend/products/search')
    {
        // ---------------------------------------------------------------------
        // ------------------------ search params ------------------------------
        // ---------------------------------------------------------------------
        $search_text = URL::decode($this->request->param('search_text'));
        $search_date = $this->request->param('date',NULL);
        
        $product = Model::fly('Model_Product');

        // build search condition
        if (!$search_params) $search_params = array(); 
            
        $search_params['search_fields'] = array('caption', 'description');
        $search_params['search_text'] = $search_text;
        $search_params['search_date'] = $search_date;            
        $search_params['active'] = 1;
        $search_params['section_active'] = 1;
        $search_params['site_id'] = Model_Site::current()->id;    

        $town_alias = Cookie::get(Model_Town::TOWN_TOKEN);
        if($town_alias == Model_Town::ALL_TOWN)
           $search_params['all_towns'] = true;
        
        list($search_condition, $params) = $product->search_condition($search_params);

        // count & find products by search condition
        $pages = (int)$this->request->param('page',1); 
        $per_page = 4*$pages;
        $count = $product->count_by($search_condition, $params);
        $pagination = new Pagination($count, $per_page, 'page', 7);
        $pagination->offset = 0;
        $order_by = $this->request->param('cat_porder', 'price');
        $desc = (bool) $this->request->param('cat_pdesc', '1');

        $params['offset'] = $pagination->offset;
        $params['limit']  = $pagination->limit;
        $params['order_by'] = $order_by;
        $params['desc'] = $desc;

        $params['with_image'] = 3;
        $params['with_sections'] = TRUE;
        
        $products = $product->find_all_by($search_condition, $params);

        // Set up view
        $view = new View($view_file);
        $view->order_by = $order_by;
        $view->desc = $desc;
        $view->cols = 3;

        if ($search_date !== NULL) {
            $date_str = l10n::rdate(Kohana::config('datetime.date_format_front'),$search_date);  
            $view->date_str = $date_str;
        }
        
        //$view->properties = $properties;
        $view->products = $products;

        $view->pagination = $pagination->render('pagination_load');

        // Add breadcrumbs
        //$this->add_breadcrumbs();

        return $view->render();
        //$this->request->response = $this->render_layout($view->render());
    }

    public function widget_small_product(Model_Product $product)
    {
        $widget = new Widget('frontend/products/small_product');
        $widget->id = 'product_' . $product->id;
        $widget->context_uri = FALSE; // use the url of clicked link as a context url

        $telemosts = $product->get_telemosts(Model_Town::current());
        
        $go = new Model_Go();
        $telemost = new Model_Telemost(); 
        $will_go = 0;
        $already_go = 0;
        $user= Model_User::current();
        foreach ($telemosts as $telemost) {
            if ($user->id) $go->find_by_telemost_id($telemost->id,array('owner' => $user));            
            if ($go->id) $already_go = 1;
            $will_go += $go->count_by(array('telemost_id' => $telemost->id));
        }
        $already_req = 0;
        if ($user->id) {
            $telemost->find_by_product_id($product->id,array('owner' => $user));
            if ($telemost->id) $already_req = 1;
        }
        $widget->already_go = $already_go;
        $widget->already_req = $already_req;
        $widget->will_go = $will_go;
        $widget->product   = $product;

        return $widget;
    }
    
    /**
     * Redraw product images widget via ajax request
     */
    public function action_ajax_small_product()
    {
        $request = Widget::switch_context();

        $product = Model_Product::current();
        if ( ! isset($product->id))
        {
            FlashMessages::add('Событие не найдено', FlashMessages::ERROR);
            $this->_action_ajax();
            return;
        }

        $widget = $request->get_controller('products')
                ->widget_small_product($product);

        $widget->to_response($this->request);
        $this->_action_ajax();
    }         
        
    /**
     * Redraw product images widget via ajax request
     */
    public function action_ajax_smallproduct_choose()
    {
        $request = Widget::switch_context();

        $product = Model_Product::current();

        $user = Model_User::current();
        
        if ( ! isset($product->id) && ! isset($user->id))
        {   
            $this->_action_404();
            return;
        }

        $go = new Model_Go();
        $telemosts = $product->get_telemosts(Model_Town::current());
        
        if (count($telemosts) > 1) {
            
        } elseif (count($telemosts) == 1)  {
            $telemost = $telemosts->current();
            
            $go = new Model_Go();
            $go->telemost_id = $telemost->id;
            $go->user_id = $user->id;
            $go->save();

            $widget = $request->get_controller('products')
                    ->widget_small_product($product);

            $widget->to_response($this->request);
            
            $this->_action_ajax();            
        } else {
            $this->_action_404();
            return;            
        }        
    }    
    /**
     * Redraw product images widget via ajax request
     */
    public function action_ajax_smallproduct_unchoose()
    {
        $request = Widget::switch_context();

        $product = Model_Product::current();

        $user = Model_User::current();
        
        if ( ! isset($product->id) && ! isset($user->id))
        {   
            $this->_action_404();
            return;
        }

        $go = new Model_Go();
        $telemosts = $product->get_telemosts(Model_Town::current());
        
        if (count($telemosts) > 1) {
            
        } elseif (count($telemosts) == 1)  {
            $telemost = $telemosts->current();
            
            $go->find_by_telemost_id($telemost->id,array('owner' => $user));
            
            $go->delete();
            
            $widget = $request->get_controller('products')
                    ->widget_small_product($product);

            $widget->to_response($this->request);
            
            $this->_action_ajax();            
        } else {
            $this->_action_404();
            return;            
        }        
    }     

    /**
     * Redraw product images widget via ajax request
     */
    public function action_ajax_smallproduct_unrequest()
    {
        $request = Widget::switch_context();

        $product = Model_Product::current();

        $user = Model_User::current();
        
        if ( ! isset($product->id) && ! isset($user->id))
        {   
            $this->_action_404();
            return;
        }

        $telemost = new Model_Telemost();
        $telemost->find_by_product_id($product->id, array('owner' => $user));
        
        if ($telemost->id) {
            $telemost->delete();
        }
            
        $widget = $request->get_controller('products')
                ->widget_small_product($product);

        $widget->to_response($this->request);

        $this->_action_ajax();            
    }         
    
    // Parsing
    public function action_ajax_parsing()
    {
        $parseurl = $_GET['parseurl'];

        $result =  array('parseurl' => $parseurl, 'status'=>'notsupported');
        
        if(strpos($parseurl,'theoryandpractice.ru') !== FALSE)
        {
            $html = Remote::get($parseurl);
            
            $html = str_replace(array('<nobr>','</nobr>','&nbsp;'),array('','',' '),$html);
            
            $matches = array();
            preg_match('|<span class="type type-[a-z]+?">([^<]+?)</span><em>([^<]+?)</em>|', $html, $matches);
            $title = html_entity_decode($matches[2], ENT_COMPAT, 'UTF-8');
            $format = html_entity_decode($matches[1], ENT_COMPAT, 'UTF-8');
            
            preg_match('|<time class="time" datetime="([^"]+?)" itemprop="startDate">|', $html, $matches);
            $dateTime = new DateTime($matches[1]);
            $dateTime = $dateTime->format('d-m-Y H:i');
                    
            preg_match('|<div class="description" itemprop="description">(.+?)</div>|s', $html, $matches);
            $rawDesc = $matches[1];
            $descWithUrls = preg_replace('|<a href="([^"]+?)">([^<]+?)</a>|i', '$2 - $1', $rawDesc);
            $desc = strip_tags(html_entity_decode($descWithUrls, ENT_COMPAT, 'UTF-8'));

            $imageUrl = '';
            if(strpos($html,'<figure class="poster">') !== FALSE)
            {
                // <figure class="poster"><span class="img"><img alt="Международный стартап" itemprop="image" src="https://tnp-production.s3.amazonaws.com/uploads/image_unit/000/024/198/image/base_8be2e36ea1.jpg"></span><figcaption><span>Автор картинки: Jag Nagra</span></figcaption></figure>
                preg_match('|<img alt="([^"]+)" itemprop="image" src="([^"]+)"|s', $html, $matches);
                $imageUrl = html_entity_decode($matches[2], ENT_COMPAT, 'UTF-8');
            }
            
            $result['status'] = 'success';
            $result['event'] = array(
                'time'=> $dateTime,
                'title' => $title,
                'format' => mb_strtolower(trim($format)),
                'desc' => $desc,
                'image_url' => $imageUrl
            );          
        }
        
        $this->request->response['data'] = $result;
        $this->_action_ajax();
    }
    
    
    // -----------------------------------------------------------------------
    // PRODUCT PAGE
    // -----------------------------------------------------------------------
    public function action_product()
    {
        $product = Model_Product::current();
        
        if ( ! isset($product->id))
        {
            
            $this->_action_404('Указанное событие не найдено');
            return;
        }
        
        $view = new View('frontend/product');
        $view->product = $product;
        
        $layout = $this->prepare_layout();

        $layout->content = $view;

        $this->request->response = $layout->render();
    }    
    
    /**
     * Render product
     */    
    public function widget_product(Model_Product $product)
    {
        $widget = new Widget('frontend/products/product');
        $widget->id = 'product_' . $product->id;
        $widget->context_uri = FALSE; // use the url of clicked link as a context url

        $user = Model_User::current();
        
        // was this event already selected for telemost?
        $telemost = new Model_Telemost();
        $already_req = 0;
        if ($user->id) {
            $telemost->find_by_product_id($product->id,array('owner' => $user));
            if ($telemost->id) $already_req = 1;
        }

        // was the corresponding telemost was already choosen to visit?
        $telemosts = $product->get_telemosts(Model_Town::current());
        $go = new Model_Go();
        $will_go = 0;
        $already_go = 0;
        foreach ($telemosts as $telemost) {
            if ($user->id) $go->find_by_telemost_id($telemost->id,array('owner' => $user));            
            if ($go->id) $already_go = 1;
            $will_go += $go->count_by(array('telemost_id' => $telemost->id));
        }
        // navigation to start the event
        $stage = $this->request->param('stage',NULL);

        $today_datetime = new DateTime("now");
        $today_datetime->add(new DateInterval('PT30M'));
        
        $nav_turn_on = ($today_datetime->getTimestamp() > $product->datetime->getTimestamp())? TRUE:FALSE;
       
        if ($nav_turn_on && $product->stage() == Model_Product::ACTIVE_STAGE && $product->get_telemost_provider() == Model_Product::HANGOTS)
            $product->change_stage(Model_Product::START_STAGE);
        
        $result = FALSE;

        if ($stage) $result = $product->change_stage($stage);

        $actual_stage = ($result)?$stage:$product->stage();

        
        $widget->already_req = $already_req;
        $widget->already_go = $already_go;
        $widget->will_go = $will_go;
        $widget->stage   = $actual_stage;
        $widget->user_stage = $stage;
        $widget->nav_turn_on = $nav_turn_on;
        $widget->section_description = Model_Section::current()->full_description;
        $widget->product = $product;
        $widget->telemosts = $product->telemosts;
        $widget->app_telemosts = $product->app_telemosts;
        return $widget;
    }    
    
    /**
     * Render images for product (when in product card)
     *
     * @param  Model_Product $product
     * @return Widget
     */
    public function widget_product_images(Model_Product $product)
    {
        $widget = new Widget('frontend/products/product_images');
        $widget->id = 'product_' . $product->id . '_images';
        $widget->ajax_uri = URL::uri_to('frontend/catalog/product/images');
        $widget->context_uri = FALSE; // use the url of clicked link as a context url

        $images = Model::fly('Model_Image')->find_all_by_owner_type_and_owner_id('product', $product->id, array(
            'order_by' => 'position',
            'desc'     => FALSE
        ));
        if ($images->valid()) {
            $image_id = (int) $this->request->param('image_id');
            if ( ! isset($images[$image_id]))
            {
                $image_id = $images->at(0)->id;
            }
        
            $widget->image_id = $image_id; // id of current image
            $widget->product  = $product;
        }
        $widget->images   = $images;
        
        return $widget;
    }
    
    /**
     * Redraw product images widget via ajax request
     */
    public function action_ajax_product()
    {
        $request = Widget::switch_context();

        $product = Model_Product::current();
        if ( ! isset($product->id))
        {
            FlashMessages::add('Событие не найдено', FlashMessages::ERROR);
            $this->_action_ajax();
            return;
        }

        $widget = $request->get_controller('products')
                ->widget_product($product);

        $widget->to_response($this->request);
        $this->_action_ajax();
    }
    
    /**
     * Redraw product images widget via ajax request
     */
    public function action_ajax_product_choose()
    {
        $request = Widget::switch_context();

        $product = Model_Product::current();

        $user = Model_User::current();
        
        if ( ! isset($product->id) && ! isset($user->id))
        {   
            $this->_action_404();
            return;
        }

        $go = new Model_Go();
        $telemosts = $product->get_telemosts(Model_Town::current());
        
        if (count($telemosts) > 1) {
            
        } elseif (count($telemosts) == 1)  {
            $telemost = $telemosts->current();
            
            $go = new Model_Go();
            $go->telemost_id = $telemost->id;
            $go->user_id = $user->id;
            $go->save();

            $widget = $request->get_controller('products')
                    ->widget_product($product);

            $widget->to_response($this->request);
            
            $this->_action_ajax();            
        } else {
            $this->_action_404();
            return;            
        }        
    }     

    /**
     * Redraw product images widget via ajax request
     */
    public function action_ajax_product_unchoose()
    {
        $request = Widget::switch_context();

        $product = Model_Product::current();

        $user = Model_User::current();
        
        if ( ! isset($product->id) && ! isset($user->id))
        {   
            $this->_action_404();
            return;
        }

        $go = new Model_Go();
        $telemosts = $product->get_telemosts(Model_Town::current());
        
        if (count($telemosts) > 1) {
            
        } elseif (count($telemosts) == 1)  {
            $telemost = $telemosts->current();
            
            $go->find_by_telemost_id($telemost->id,array('owner' => $user));
            
            $go->delete();
            
            $widget = $request->get_controller('products')
                    ->widget_product($product);

            $widget->to_response($this->request);
            
            $this->_action_ajax();            
        } else {
            $this->_action_404();
            return;            
        }        
    }     

    /**
     * Redraw product images widget via ajax request
     */
    public function action_ajax_product_images()
    {
        $request = Widget::switch_context();

        $product = Model_Product::current();
        if ( ! isset($product->id))
        {
            FlashMessages::add('Событие не найдено', FlashMessages::ERROR);
            $this->_action_ajax();
            return;
        }

        $widget = $request->get_controller('products')
                ->widget_product_images($product);

        $widget->to_response($this->request);
        $this->_action_ajax();
    }

    public function action_cancel()
    {
        $product_id = $this->request->param('id',NULL);
        $user = Model_User::current();
        
        if (!$product_id || !$user->id) {
            $this->_action_404();
            return;                          
        }
        $product = Model::fly('Model_Product')->find_by_id($product_id,array('owner' => $user));
        
        if ($product->id) { 
            $product->backup();
            $product->visible=0;
            $product->save();
            $this->request->redirect(URL::uri_to('frontend/acl/users/control',array('action' => 'control')));
        } else {
            $this->_action_404();
            return;              
        }
        
    }
    
    public function action_fullscreen()
    {
        $product = Model_Product::current();
        
        $user = Model_User::current();
        
        if (!$product->id || !$user->id) {
            $this->_action_404();        
            return;
        }
        
        if ($product->user_id == $user->id) {
            $aim = $product;
            if ($product->stage() != Model_Product::START_STAGE) { 
                $result = $product->change_stage(Model_Product::START_STAGE);
            }
        } else {
            $aim = Model::fly('Model_Telemost')->find_by_product_id($product->id,array('owner' => $user));
        }
        
        if (!$aim->id) {
            $this->_action_404();        
            return;            
        }
             
        $view = new View('frontend/products/fullscreen');
        $view->aim = $aim;
        $view->product = $product;
        
        $layout = $this->prepare_layout();

        $layout->content = $view;

        $this->request->response = $layout->render();
        
    }
    
    // -----------------------------------------------------------------------
    // USER PAGE
    // -----------------------------------------------------------------------

    /**
     * Renders list of products
     *
     * @param  boolean $seelct
     * @return string
     */
    public function widget_products($view = 'frontend/products',$apage = NULL)
    {
        $widget = new Widget($view);
        $widget->id = 'products';
        $widget->ajax_uri = NULL;
        $widget->context_uri = FALSE; // use the url of clicked link as a context url
        
        // ----- List of products
        $product = Model::fly('Model_Product');
        $owner = Auth::instance()->get_user();

        $per_page = 1000;
        $count = $product->count_by_owner($owner->id);        
        $pagination = new Paginator($count, $per_page, 'apage', 7,$apage,'frontend/catalog/ajax_products',NULL,'ajax');
        
        $order_by = $this->request->param('cat_porder', 'datetime');
        $desc = (bool) $this->request->param('cat_pdesc', '1');

        $params['offset'] = $pagination->offset;
        $params['limit']  = $pagination->limit;        
        $params['order_by'] = $order_by;
        $params['desc']     = $desc;
        $params['with_image'] = 2;
        $params['with_sections'] = TRUE;        
        $params['owner'] = $owner;

        $products = $product->find_all_by_visible(TRUE,$params);
        
        $will_goes = array();
        
        foreach ($products as $product) {
            $telemosts = $product->get_telemosts(Model_Town::current());
            $go = new Model_Go();
            $will_go = 0;
            foreach ($telemosts as $telemost) {
                $will_go += $go->count_by(array('telemost_id' => $telemost->id));
            }
            $will_goes[$product->id] = $will_go;
        }
        $params['offset'] = $pagination->offset;
        $widget->order_by = $order_by;
        $widget->desc = $desc;
        $widget->products = $products;
        $widget->will_goes = $will_goes;
        $widget->pagination = $pagination->render('pagination');

        return $widget;
    }    

    /**
     * Redraw product images widget via ajax request
     */
    public function action_ajax_products()
    {
        $apage = $this->request->param('apage',NULL);
        $request = Widget::switch_context();

        $user = Model_User::current();
        if ( ! isset($user->id))
        {
            //FlashMessages::add('', FlashMessages::ERROR);
            $this->_action_ajax();
            return;
        }

        $widget = $request->get_controller('products')
                ->widget_products('frontend/small_products',$apage);

        $widget->to_response($this->request);
        $this->_action_ajax();
    }     
    
    // -----------------------------------------------------------------------
    // PRODUCT CONTROL PAGE
    // -----------------------------------------------------------------------
    
    public function _view_create(Model_Product $model, Form_Frontend_Product $form, array $params = NULL)
    {
        $place = new Model_Place();

        $form_place = new Form_Frontend_Place($place);
        if ($form_place->is_submitted())
        {
            $form_place->validate();

            // User is trying to log in
            if ($form_place->validate())
            {   
                $vals = $form_place->get_values();

                if ($place->validate($vals))
                {                    
                    
                    $place->values($vals);
                    $place->save();
                    
                    $form->get_element('place_name')->set_value($place->name);
                    $form->get_element('place_id')->set_value($place->id);
                }
            }
        }
        
        $modal = Layout::instance()->get_placeholder('modal');
        $modal = $form_place->render().' '.$modal;
        
        $lecturer = new Model_Lecturer();

        $form_lecturer = new Form_Frontend_Lecturer($lecturer);

        if ($form_lecturer->is_submitted())
        {
            // User is trying to log in
            if ($form_lecturer->validate())
            {   
                $vals = $form_lecturer->get_values();

                if ($lecturer->validate($vals))
                {                    
                    $lecturer->values($vals);
                    $lecturer->save();
                    $form->get_element('lecturer_name')->set_value($lecturer->name);
                    $form->get_element('lecturer_id')->set_value($lecturer->id);
                }
            }
        }
        $modal .= ' '.$form_lecturer->render();

        $organizer = new Model_Organizer();

        $form_organizer = new Form_Frontend_Organizer($organizer);
        if ($form_organizer->is_submitted())
        {
            $form_organizer->validate();

            // User is trying to log in
            if ($form_organizer->validate())
            {   
                $vals = $form_organizer->get_values();

                if ($organizer->validate($vals))
                {                    
                    
                    $organizer->values($vals);
                    $organizer->save();
                    
                    $form->get_element('organizer_name')->set_value($organizer->name);
                    $form->get_element('organizer_id')->set_value($organizer->id);
                }
            }
        }
        $modal = $form_organizer->render().' '.$modal;

        Layout::instance()->set_placeholder('modal',$modal);
  
        $view = new View('frontend/products/control');
        $view->product = $model;
        $view->form = $form;

        return $view;
    }
    
    public function _view_update(Model_Product $model, Form_Frontend_Product $form, array $params = NULL)
    {
        $place = new Model_Place();

        $form_place = new Form_Frontend_Place($place);
        if ($form_place->is_submitted())
        {
            $form_place->validate();

            // User is trying to log in
            if ($form_place->validate())
            {   
                $vals = $form_place->get_values();

                if ($place->validate($vals))
                {                    
                    
                    $place->values($vals);
                    $place->save();
                    
                    $form->get_element('place_name')->set_value($place->name);
                    $form->get_element('place_id')->set_value($place->id);
                }
            }
        }
        
        $modal = Layout::instance()->get_placeholder('modal');
        $modal = $form_place->render().' '.$modal;        
        
        $lecturer = new Model_Lecturer();

        $form_lecturer = new Form_Frontend_Lecturer($lecturer);

        if ($form_lecturer->is_submitted())
        {
            // User is trying to log in
            if ($form_lecturer->validate())
            {   
                $vals = $form_lecturer->get_values();

                if ($lecturer->validate($vals))
                {                    
                    $lecturer->values($vals);
                    $lecturer->save();
                    $form->get_element('lecturer_name')->set_value($lecturer->name);
                    $form->get_element('lecturer_id')->set_value($lecturer->id);
                }
            }
        }
        $modal .= ' '.$form_lecturer->render();

        $organizer = new Model_Organizer();

        $form_organizer = new Form_Frontend_Organizer($organizer);
        if ($form_organizer->is_submitted())
        {
            $form_organizer->validate();

            // User is trying to log in
            if ($form_organizer->validate())
            {   
                $vals = $form_organizer->get_values();

                if ($organizer->validate($vals))
                {                    
                    
                    $organizer->values($vals);
                    $organizer->save();
                    
                    $form->get_element('organizer_name')->set_value($organizer->name);
                    $form->get_element('organizer_id')->set_value($organizer->id);
                }
            }
        }
        $modal = $form_organizer->render().' '.$modal;
        Layout::instance()->set_placeholder('modal',$modal);

        $view = new View('frontend/products/control');
        $view->product = $model;
        $view->form = $form;

        return $view;
    }
    
    /**
     * Configure actions
     * @return array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Product';
        
            $this->_view  = 'frontend/form_adv';
       
            $this->_form = 'Form_Frontend_Product';
            return array(
                'create' => array(
                    'view_caption' => 'Создание события',
                ),
                'update' => array(  
                    'view_caption' => 'Редактирование события ":caption"',
                    'message_ok' => 'Укажите дополнительные характеристики события'
                ),
                'delete' => array(
                    'view_caption' => 'Удаление анонса',
                    'message' => 'Удалить анонс ":caption"?'
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
        if ($layout_script === NULL)
        {
            if ($this->request->action == 'product')
            {
                $layout_script = 'layouts/catalog';
            }
            if ($this->request->action == 'fullscreen')
            {
                $layout_script = 'layouts/only_vision';
            }            
        }
                
        return parent::prepare_layout($layout_script);
    }

    /**
     * Prepare product for create/update/delete action
     *
     * @param  string $action
     * @param  string|Model_Product $product
     * @param  array $params
     * @return Model_Product
     */
    protected function  _model($action, $product, array $params = NULL)
    {
        $product = parent::_model($action, $product, $params);

        if ($action == 'create')
        {
            $product->section_id = Model_Section::EVENT_ID;
            $product->user_id = Model_User::current()->id;
            $product->active = 1;
        }
        return $product;
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
        $model->visible = 0;
        
        $model->save();

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
        foreach ($models as $model)
        {
            $model->visible = 0;
            
            $model->save();
        }

        $this->request->redirect($this->_redirect_uri('multi_delete', $model, $form, $params));
    }        
    
    public function action_control() {
        $view = new View('frontend/workspace');

        $view->content = $this->widget_products();

        $layout = $this->prepare_layout();
        $layout->content = $view;
        
        $this->request->response = $layout->render();        
    }
    
    public function action_eventcontrol() {
        
        $view = new View('frontend/workspace');

        $view->content = $this->widget_products('frontend/events');

        $layout = $this->prepare_layout();
        $layout->content = $view;
        
        $this->request->response = $layout->render();        
    }    
    
    /**
     * Handles the selection of additional sections for product
     */
    public function action_sections_select()
    {
        if ( ! empty($_POST['ids']) && is_array($_POST['ids']))
        {
            $section_ids = '';
            foreach ($_POST['ids'] as $section_id)
            {
                $section_ids .= (int) $section_id . '_';
            }
            $section_ids = trim($section_ids, '_');
            $this->request->redirect(URL::uri_back(NULL, 1, array('cat_section_ids' => $section_ids)));
        }
        else
        {
            // No sections were selected
            $this->request->redirect(URL::uri_back());
        }
    }

    public function action_set_hangouts_url($key, $url)
    {
        $key = $_GET['key'];
        $url = $_GET['url'];
        
        $result = array('status'=>'ok');
        
        if($key != Model_Product::HANGOUTS_STOP_KEY)
        {
            $product = Model::fly('Model_Product')->find_by_hangouts_secret_key($key);
            
            if($product->id)
            {
                $product->hangouts_url = $url;
                $product->save();
            }
            else
            {
                $result['status'] = 'notfound';
            }
            
        }
        else
        {
            $result['status'] = 'error';
        }
        
        $this->request->response['data'] = $result;
        $this->_action_ajax();
    }
    
    /**
     * This action is executed via ajax request after additional
     * sections for product have been selected.
     *
     * It redraws the "additional sections" form element accordingly
     */
    public function action_ajax_sections_select()
    {
        if ($this->request->param('cat_section_ids') != '')
        {
            $action =  ((int)$this->request->param('id') == 0) ? 'create' : 'update';

            $product = $this->_model($action, 'Model_Product');
            $form    = $this->_form($action, $product);

            $component = $form->find_component('additional_sections[' . (int) $this->request->param('cat_sectiongroup_id') . ']');
            if ($component)
            {
                $this->request->response = $component->render();
            }
        }
    }
    
    /**
     * Redraw product properties via ajax request
     */
    public function action_ajax_properties()
    {
        $product = new Model_Product();
        $product->find((int) $this->request->param('id'));

        if (isset($_GET['section_id']))
        {
            $product->section_id = (int) $_GET['section_id'];
        }
        
        // switch form according to the sectiongroup type            
        $type_id = $this->request->param('type_id', NULL);
        if ($type_id !== NULL)
        {
            $type_id = (int) $type_id;
            if (!isset($this->_forms[$type_id])) {
                throw new Controller_BackendCRUD_Exception('Неправильные параметры запроса');
            }
            $form_class = $this->_forms[$type_id]; 
        }
        
        $form = new $form_class($product);

        $component = $form->find_component('props');
        
        if ($component)
        {
            $this->request->response = $component->render();
        }
    }    
    
    public function widget_create()
    {
        $widget = new Widget('frontend/products/product_create');
        $widget->id = 'product_' . $product->id . '_create';
        //$widget->ajax_uri = URL::uri_to('frontend/catalog/product/action');
        $widget->context_uri = FALSE; // use the url of clicked link as a context url
        $widget->sectiongroup = Model_SectionGroup::current(); 
        return $widget;
        
    }
   
    /**
     * Generate redirect url
     *
     * @param  string $action
     * @param  Model $model
     * @param  Form $form
     * @param  array $params
     * @return string
     */
    protected function  _redirect_uri($action, Model $model = NULL, Form $form = NULL, array $params = NULL)
    {
        if ($action == 'create' || $action == "update")
        {
            return URL::uri_to('frontend/acl/users/control',array('action' => 'control'));
        }

        
        return parent::_redirect_uri($action, $model, $form, $params);
    }    
    
    /**
     * Add breadcrumbs for current action
     */
    public function add_breadcrumbs(array $request_params = array())
    {
        if (empty($request_params)) {
            list($name, $request_params) = URL::match(Request::current()->uri);
        }
        
        if ($request_params['action'] == 'search')
        {
            Breadcrumbs::append(array(
                'uri' => URL::uri_self(array()),
                'caption' => 'Результаты поиска'));
        }
        
        if ($request_params['action'] == 'control')
        {
            Breadcrumbs::append(array(
                'uri' => URL::uri_to('frontend/catalog/products/control',array('action' => 'control')),
                'caption' => 'Управление анонсами'));
        }
        
        if ($request_params['action'] == 'eventcontrol')
        {
            Breadcrumbs::append(array(
                'uri' => URL::uri_to('frontend/catalog/products/control',array('action' => 'eventcontrol')),
                'caption' => 'Управление событиями'));            
        }

        if ($request_params['action'] == 'index')
        {
            $sectiongroup = Model_SectionGroup::current();
            if ( ! isset($sectiongroup->id))
                return;

            Breadcrumbs::append(array(
                'uri'     => $sectiongroup->uri_frontend(),
                'caption' => $sectiongroup->caption
            ));

            $section = Model_Section::current();
            if ( ! isset($section->id))
                return;

            // find all parents for current section and append the current section itself
            $sections = $section->find_all_active_cached($sectiongroup->id);
            $parents = $sections->parents($section, FALSE);
            foreach ($parents as $parent)
            {
                Breadcrumbs::append(array(
                    'uri'     => $parent->uri_frontend(),
                    'caption' => $parent->caption
                ));
            }

            Breadcrumbs::append(array(
                'uri'     => $section->uri_frontend(),
                'caption' => $section->caption
            ));        
        }
    }
    

    public function widget_calendar()
    {
        $calendar = Calendar::instance();
        $search_date_url = URL::to('frontend/catalog/search', array('date'=>'{{d}}'), TRUE);
        $calendar->addDateLinks($search_date_url,'{{d}}');
        return $calendar->genUMonth(time(), true);
    }    
}
