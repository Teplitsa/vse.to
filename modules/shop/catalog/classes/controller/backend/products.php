<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Products extends Controller_BackendRES
{
    /**
     * Configure actions
     * @return array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Product';
        
            $this->_view  = 'backend/form_adv';
       
            $this->_form = 'Form_Backend_Product';
            return array(
                'create' => array(
                    'view_caption' => 'Создание анонса',
                ),
                'update' => array(  
                    'view_caption' => 'Редактирование анонса ":caption"',
                    'message_ok' => 'Укажите дополнительные характеристики анонса'
                ),
                'delete' => array(
                    'view_caption' => 'Удаление анонса',
                    'message' => 'Удалить анонс ":caption"?'
                ),
                'multi_delete' => array(
                    'view_caption' => 'Удаление анонсов',
                    'message' => 'Удалить выбранные анонсы?',
                    'message_empty' => 'Выберите хотя бы один анонс!'
                ),                            
            );
    }

    /**
     * Create layout and link module stylesheets
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        $layout = parent::prepare_layout($layout_script);

        $layout->add_style(Modules::uri('catalog') . '/public/css/backend/catalog.css');

        // Add catalog js scripts
        if ($this->request->action == 'index' || $this->request->action == 'products_select')
        {
            $layout->add_script(Modules::uri('catalog') . '/public/js/backend/catalog.js');
            $layout->add_script(
                "var branch_toggle_url = '"
                  . URL::to('backend/catalog/sections', array(
                        'action' => 'toggle', 'id' => '{{id}}', 'toggle' => '{{toggle}}')
                    )
                  . '?context=' . $this->request->uri
                  . "';"
            , TRUE);
        }

        if ($this->request->action == 'product_select')
        {
            // Add product select js scripts
            $layout->add_script(Modules::uri('catalog') . '/public/js/backend/product_select.js');
            $layout->add_script(
                "var product_selected_url = '" . URL::to('backend/catalog', array('action' => 'product_selected', 'product_id' => '{{id}}')) . "';"
            , TRUE);
        }
        
        return $layout;
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
        }
        return $product;
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
        if ($action == 'create')
        {
            return URL::uri_self(array('action'=>'update', 'id' => $model->id, 'history' => $this->request->param('history'))) . '?flash=ok';
        }

        if ($action == 'update')
        {
            return URL::uri_to('backend/catalog/products');
        }
        if ($action == 'multi_link')
        {
            return URL::uri_back();
        }
        
        return parent::_redirect_uri($action, $model, $form, $params);
    }

    
    /**
     * Link several products to sections
     */
    public function action_multi_link()
    {
        $this->_action_multi('multi_link');
    }

    /**
     * Link several products to sections
     * 
     * @param array $models
     * @param Form $form
     * @param array $params
     */
    protected function _execute_multi_link(array $models, Form $form, array $params = NULL)
    {
        $values = $form->get_values();

        // Find sections to which products will be linked
        $sections = array();
        $captions = '';

        foreach ($values['section_ids'] as $sectiongroup_id => $section_id)
        {
            if ( ! empty($section_id))
            {                
                $section = new Model_Section();
                $section->find((int) $section_id, array('columns' => array('id', 'lft', 'rgt', 'level', 'sectiongroup_id', 'caption')));
                
                if (isset($section->id))
                {
                    $sections[$section->id] = $section;
                    $captions .= '"' . $section->caption . '" , ';
                }
            }
        }

        if ($values['mode'] == 'link_main' && count($sections) > 1)
        {
            $form->error('Пожалуйста выберите только одну группу категорий!');
            return;
        }

        $result = TRUE;

        foreach ($models as $model)
        {
            foreach ($sections as $section)
            {
                $model->link($values['mode'], $section);
            }

            if ($model->has_errors())
            {
                $form->errors($model->errors());
                $result = FALSE;
            }
        }

        Model::fly('Model_Section')->mapper()->update_products_count();

        if ( ! $result)
            return;

        $captions = trim($captions, ' ,');
        switch ($values['mode'])
        {
            case 'link':
                FlashMessages::add('События были привязаны к разделам ' . $captions);
                break;
            case 'link_main':
                FlashMessages::add('Раздел ' . $captions . ' был сделан основным для выбранных событий');
                break;
            case 'unlink':
                FlashMessages::add('События были успешно отвязаны от разделов ' . $captions);
                break;
        }
        
        $this->request->redirect($this->_redirect_uri('multi_link', $model, $form, $params));
    }

    
    protected function _form($action,Model $model,array $params = NULL) {
        $form = parent::_form($action,$model,$params);

        if ($action == 'create' || $action == 'update')
        {        
            $lecturer_id = $this->request->param('lecturer_id', NULL);
            if ($lecturer_id !== NULL)
            {
                $form->get_element('lecturer_id')->set_value($lecturer_id);
                $form->get_element('lecturer_name')->set_value(Model::fly('Model_Lecturer')->find($lecturer_id)->name);
            }
        }
        return $form;
    }
        
    /**
     * Render list of products and sections menu
     */
    public function action_index()
    {
        if (Model_SectionGroup::current()->id === NULL)
        {
            $this->_action_error('Создайте хотя бы одну группу категорий!');
            return;
        }

        $view = new View('backend/workspace_2col');

        $view->column1 = $this->request->get_controller('sections')->widget_sections_menu();
        $view->column2 = $this->widget_products();

        $layout = $this->prepare_layout();
        $layout->content = $view;
        $this->request->response = $layout->render();
    }

    /**
     * Renders list of products
     *
     * @param  boolean $seelct
     * @return string
     */
    public function widget_products($view = 'backend/products')
    {
        // current site
        $site_id = Model_Site::current()->id;
        if ($site_id === NULL)
        {
            return $this->_widget_error('Выберите портал!');
        }

        $sectiongroup = Model_SectionGroup::current();
        $section      = Model_Section::current();

        // products search form
        $search_form = $this->widget_search();

        // ----- List of products
        $product = Model::fly('Model_Product');

        // build search condition
        $search_params = $search_form->get_values();
        $search_params['search_fields'] = array( 'caption', 'description');

        //$search_params['site_id'] = $site_id;
        $search_params['sectiongroup'] = $sectiongroup;
        
        $search_params['section'] = $section;
		
		
		
        list($search_condition, $params) = $product->search_condition($search_params);
		
        // count & find products by search condition
        $per_page = 20;
        $count = $product->count_by($search_condition, $params);

        $pagination = new Paginator($count, $per_page);

        $order_by = $this->request->param('cat_porder', 'caption');
        $desc = (bool) $this->request->param('cat_pdesc', '0');

        $params['offset']   = $pagination->offset;
        $params['limit']    = $pagination->limit;
        $params['order_by'] = $order_by;
        $params['desc']     = $desc;

        $products = $product->find_all_by($search_condition, $params);
		
        // Set up view
        $view = new View($view);

        $view->search_form = $search_form;

        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->section = $section;
        $view->sectiongroup = $sectiongroup;
        
        
        $view->products = $products;

        $view->pagination = $pagination->render('backend/pagination');

        return $view->render();
    }

    /**
     * Render product search form
     *
     * @return Form_Backend_SearchProducts
     */
    public function widget_search()
    {
        $search_form = new Form_Backend_SearchProducts();

        $search_form->set_defaults(array(
            'search_text' => URL::decode($this->request->param('search_text')),
            'active'      => $this->request->param('active')
        ));

        if ($search_form->is_submitted())
        {
            $params = $search_form->get_values();
            $params['search_text'] = URL::encode($params['search_text']);

            $this->request->redirect(URL::uri_self($params));
        }

        return $search_form;
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
        
        $form_class = $this->_form;
        
        $form = new $form_class($product);

        $component = $form->find_component('props');
        
        if ($component)
        {
            $this->request->response = $component->render();
        }
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
     * This action is executed via ajax request after access
     * towns for event have been selected.
     *
     * It redraws the "access towns" form element accordingly
     */
    public function action_ajax_towns_select()
    {
        if ($this->request->param('access_town_ids') != '')
        {   
            $action =  ((int)$this->request->param('id') == 0) ? 'create' : 'update';
                        
            $product = $this->_prepare_model($action);

            $form  = $this->_prepare_form($action, $product);
            
            $component = $form->find_component('access_towns');
            if ($component)
            {
                $this->request->response = $component->render();
            }
        }
    }       

    /**
     * This action is executed via ajax request after access
     * organizers for event have been selected.
     *
     * It redraws the "access organizers" form element accordingly
     */
    public function action_ajax_organizers_select()
    {
        if ($this->request->param('access_organizer_ids') != '')
        {
              
            $action =  ((int)$this->request->param('id') == 0) ? 'create' : 'update';
            
            $product = $this->_prepare_model($action);
            $form  = $this->_prepare_form($action, $product);

            $component = $form->find_component('access_organizers');
            
            if ($component)
            {
                $this->request->response = $component->render();
            }
        }
    }       

    /**
     * This action is executed via ajax request after access
     * organizers for event have been selected.
     *
     * It redraws the "access organizers" form element accordingly
     */
    public function action_ajax_users_select()
    {
        if ($this->request->param('access_user_ids') != '')
        {
              
            $action =  ((int)$this->request->param('id') == 0) ? 'create' : 'update';
            
            $product = $this->_prepare_model($action);
            $form  = $this->_prepare_form($action, $product);

            $component = $form->find_component('access_users');
            
            if ($component)
            {
                $this->request->response = $component->render();
            }
        }
    }    
    
    /**
     * Select several products
     */
    public function action_products_select()
    {
        $layout = $this->prepare_layout();

        if ($this->request->in_window())
        {
            $layout->caption = 'Выбор событий на портале "' . Model_Site::current()->caption . '"';
            $layout->content = $this->widget_products_select();
        }
        else
        {
            $view = $this->widget_products_select();
            $view->caption = 'Выбор событий на портале "' . Model_Site::current()->caption . '"';

            $layout->content = $view->render();
        }

        $this->request->response = $layout->render();
    }

    /**
     * Render products and sections for the selection of one product
     */
    public function widget_products_select()
    {
        $view = new View('backend/workspace_2col');

        $view->column1  = $this->request->get_controller('sections')->widget_sections_menu();
        $view->column2  = $this->widget_products('backend/products/select');

        return $view;
    }
    
}