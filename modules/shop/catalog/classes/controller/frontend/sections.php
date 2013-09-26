<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Sections extends Controller_Frontend
{
    public function prepare_layout($layout_script = NULL)
    {
        $layout = parent::prepare_layout($layout_script);

        $layout->add_style(Modules::uri('catalog') . '/public/css/frontend/catalog.css');

        if ($this->request->action == 'select')
        {
            // Add sections select js scripts
            $layout->add_script(Modules::uri('catalog') . '/public/js/frontend/sections_select.js');
        }

        return $layout;
    }
    
    /**
     * Render a list of sections
     */
    public function action_index()
    {
        $sectiongroup = Model_SectionGroup::current();
        
        if ( ! isset($sectiongroup->id))
        {
            $this->_action_404('Указанная группа категорий не найдена');
            return;
        }

        // Find all active sections for given sectiongroup
        $sections = Model::fly('Model_Section')->find_all_active_cached($sectiongroup->id);

        $view = new View('frontend/sections/list');
        $view->sectiongroup = $sectiongroup;
        $view->sections = $sections;

        // Add breadcrumbs
        //$this->add_breadcrumbs();
        
        $this->request->response = $this->render_layout($view->render());
    }
    
    /**
     * Renders catalog sections menu
     *
     * @return string
     */
    public function widget_menu()
    {
        $sectiongroup = Model_SectionGroup::current();        
        
        $section       = Model_Section::current();
        // Obtain information about which sections are expanded from session
        // The returned array is an array of id's of unfolded sections
        $unfolded = Session::instance()->get('sections', array());
        // Select only sections that are visible (i.e. their parents are unfolded)
        $sections = $section->find_all_unfolded($sectiongroup->id, NULL, $unfolded, array(
            'order_by' => 'lft',
            'desc'     => '0',
            'as_tree'  => true
        ));

        $sectiongroups = $sectiongroup->find_all_cached();
        
        $view = new View('frontend/sections/menu');

        $view->section_id = $section->id;

        $view->sectiongroup = $sectiongroup;
        
        $view->unfolded = $unfolded;

        $view->sections = $sections;

        $view->sectiongroups = $sectiongroups;
        
        $toggle_url = URL::to('frontend/catalog/section', array(
            'sectiongroup_name' => $sectiongroup->name,    
            'path' => '{{path}}',
            'toggle' => '{{toggle}}'
            ))
            . '?context=' . $this->request->uri
            . "';";

        $layout = $this->prepare_layout();
        $layout->add_script(Modules::uri('catalog') . '/public/js/frontend/catalog.js');
        $layout->add_script(
            "var branch_toggle_url = '"
              . $toggle_url
        , TRUE);
        
        return $view->render();
    }
    
    /**
     * Save sections branch visibility (folded or not?) in session
     *
     * Executed either via ajax or normally
     */
    public function action_toggle()
    {
        $section = Model_Section::current();

        $toggle = $this->request->param('toggle', '');

        if ($toggle != '' && $section->id !== NULL)
        {            
            $branch_id = $section->id;
            $unfolded = Session::instance()->get('sections', array());
            if ($toggle == 'on' && ! in_array($branch_id, $unfolded))
            {
                // Unfold the branch - add section id to the list of unfolded sections
                $unfolded[] = $branch_id;
            }
            else
            {
                // Fold the branch - remove section id from the list of unfolded sections
                $k = array_search($branch_id, $unfolded);
                if ($k !== FALSE)
                {
                    unset($unfolded[$k]);
                }
            }
            Session::instance()->set('sections', $unfolded);

            if (Request::$is_ajax && $toggle == 'on')
            {
                // Render branch of sections
                if ( ! empty($_GET['context']))
                {
                    // Switch request context (render as if a controller is accessed via url = $context)
                    $context = $_GET['context'];

                    $request = new Request($context);
                    Request::$current = $request;
                    
                    $controller = $request->get_controller('sections');
                    $this->request->response = $controller->widget_sections_branch($section);
                }
                else
                {
                    // Render in current context
                    $this->request->response = $this->widget_sections_branch($section);
                }
            }
        }
        if ( ! Request::$is_ajax)
        {
            $this->request->redirect(URL::uri_back());
        }
    }

    /**
     * Select several sections
     */
    public function action_select()
    {    
        $layout = $this->prepare_layout();

        if ($this->request->in_window())
        {
            $layout->caption = 'Выбор каталога событий "' . Model_Site::current()->caption . '"';
            $layout->content = $this->widget_select();
            $this->request->response = $layout->render();
        }
        else
        {
            $this->request->response = $this->render_layout($this->widget_select());
        }       
    }  
    
    /**
     * Render list of section to select several section for current section group
     *
     * @param  integer $select
     * @return string
     */
    public function widget_select($select = FALSE)
    {
        $site_id = Model_Site::current()->id;
        if ($site_id === NULL)
        {
            return $this->_widget_error('Выберите портал!');
        }

        $sectiongroup = Model_SectionGroup::current();
        $section      = Model_Section::current();


        // ----- Render section for current section group
        $order_by = $this->request->param('cat_sorder', 'lft');
        $desc     = (bool) $this->request->param('cat_sdesc', '0');

        $sections = $section->find_all_by_sectiongroup_id($sectiongroup->id, array(
            'columns'  => array('id', 'lft', 'rgt', 'level', 'caption', 'active', 'section_active'),
            'order_by' => $order_by,
            'desc'     => $desc
        ));
   
        // Set up view
        $view = new View('frontend/sections/select');

        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->sectiongroup_id = $sectiongroup->id;
        $view->section_id      = $section->id;

        $view->sections = $sections;

        return $view->render();
    }
    
    /**
     * Render a branch of sections (used to redraw tree via ajax requests)
     *
     * @param Model_Section $parent
     */
    public function widget_sections_branch(Model_Section $parent)
    {        
        $sectiongroup = Model_SectionGroup::current();        
        
        // Obtain information about which sections are expanded from session
        // The returned array is an array of id's of unfolded sections
        $unfolded = Session::instance()->get('sections', array());
        // Select only sections that are visible (i.e. their parents are unfolded)
        $sections = $parent->find_all_unfolded(NULL, $parent, $unfolded, array(
            'order_by' => 'lft',
            'desc'     => '0',
            'as_tree'  => true
        ));
        
        $view_script = 'frontend/sections/menu_ajax';
        
        $view = new View($view_script);

        $view->parent = $parent;
        $view->unfolded = $unfolded;

        $view->sections = $sections;
        $view->sectiongroup_name = $sectiongroup->name;

        return $view->render();
    }    
    /**
     * Renders catalog sections menu
     *
     * @return string
     */
    /*
    public function widget_menu()
    {        
        $current = Model_Section::current();
        if ( ! isset($current->id))
        {
            return 'Указанный раздел не найден';
        }

        $sections = $current->find_all_active_cached($current->sectiongroup_id);
        
        // Find the root parent for current section
        $root = clone $sections->ancestor($current, 1);

        // Set up view
        $view = new View('frontend/sections/menu');

        $view->current  = $current;
        $view->root     = $root;
        $view->sections = $sections;
        $view->sectiongroup = Model_SectionGroup::current();

        return $view->render();
    }
    */
    
    /**
     * Add breadcrumbs for current action
     */
    public function add_breadcrumbs(array $request_params = array())
    {
        if (empty($request_params)) {
            list($name, $request_params) = URL::match(Request::current()->uri);
        }
        
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
