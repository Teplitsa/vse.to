<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Sections extends Controller_BackendCRUD
{
    /**
     * Configure actions
     *
     * @return array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Section';
        $this->_form  = 'Form_Backend_Section';
        $this->_view  = 'backend/form_adv';

        return array(
            'create' => array(
                'view_caption' => 'Создание раздела'
            ),
            'update' => array(
                'view_caption' => 'Редактирование раздела ":caption"'
            ),
            'delete' => array(
                'view_caption' => 'Удаление раздела',
                'message' => 'Удалить раздел ":caption" и все события, для которых он является основным?'
            ),
            'multi_delete' => array(
                'view_caption' => 'Удаление разделов',
                'message' => 'Удалить выбранные разделы и события, для которых они являются основными?',
                'message_empty' => 'Выберите хотя бы один раздел!'
            )
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
        if ($this->request->action == 'index')
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

        if ($this->request->action == 'select')
        {
            // Add sections select js scripts
            $layout->add_script(Modules::uri('catalog') . '/public/js/backend/sections_select.js');
        }

        return $layout;
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
        $layout->content = $view;
        return $layout->render();
    }

    /**
     * Render a list of sections
     */
    public function action_index()
    {
        $this->request->response = $this->render_layout($this->widget_sections());
    }

    /**
     * Create new section
     *
     * @param  string|Model $model
     * @param  array $params
     * @return Model_Section
     */
    protected function _model_create($model, array $params = NULL)
    {
        if (Model_Site::current()->id === NULL)
        {
            throw new Controller_BackendCRUD_Exception('Выберите магазин перед созданием раздела!');
        }

        $sectiongroup_id = (int) $this->request->param('cat_sectiongroup_id');
        if ( ! Model::fly('Model_SectionGroup')->exists_by_id_and_site_id($sectiongroup_id, Model_Site::current()->id))
        {
            throw new Controller_BackendCRUD_Exception('Указана не существуюущая группа категорий!');
        }

        // New section in current section
        $section = new Model_Section();
        $section->parent_id = (int) $this->request->param('cat_section_id');

        // and for current section group
        $section->sectiongroup_id = $sectiongroup_id;

        return $section;
    }

    /**
     * Delete section
     */
    protected function _execute_delete(Model $section, Form $form, array $params = NULL)
    {
        list($hist_route, $hist_params) = URL::match(URL::uri_back());

        // Id of selected section
        $section_id = isset($hist_params['cat_section_id']) ? (int) $hist_params['cat_section_id'] : 0;

        if ($section->id === $section_id || $section->is_parent_of($section_id))
        {
            // If current selected section or its parent is deleted - redirect back to root
            unset($hist_params['cat_section_id']);
            $params['redirect_uri'] = URL::uri_to($hist_route, $hist_params);
        }

        parent::_execute_delete($section, $form, $params);
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
            // We have to double-reload models, because the structure of the whole table is modified
            // after deletion and we need correct values for 'lft' and 'rgt' fields before calling ->delete()
            // Also, this model may already be deleted from db because its' parent was deleted earlier
            $model->find($model->id);

            if (isset($model->id))
            {
                $model->delete();
            }

            // Deleting failed
            if ($model->has_errors())
            {
                $form->errors($model->errors());
                $result = FALSE;
            }
        }

        if ( ! $result)
            return; // Deleting of at least one model failed...

        $this->_after_execute('multi_delete', $model, $form, $params);

        $this->request->redirect($this->_redirect_uri('multi_delete', $model, $form, $params));
    }

    /**
     * Select several sections
     */
    public function action_select()
    {
        $layout = $this->prepare_layout();

        if ($this->request->in_window())
        {
            $layout->caption = 'Выбор разделов на портале "' . Model_Site::current()->caption . '"';
            $layout->content = $this->widget_select();
            $this->request->response = $layout->render();
        }
        else
        {
            $this->request->response = $this->render_layout($this->widget_select());
        }
    }

    /**
     * Renders list of sections
     *
     * @param  integer $select
     * @return string
     */
    public function widget_sections()
    {
        $site_id = Model_Site::current()->id;
        if ($site_id === NULL)
        {
            return $this->_widget_error('Выберите магазин!');
        }

        $sectiongroup = Model_SectionGroup::current();
        $section      = Model_Section::current();


        // ----- Render section for current section group
        $order_by = $this->request->param('cat_sorder', 'lft');
        $desc     = (bool) $this->request->param('cat_sdesc', '0');

        // Obtain information about which sections are expanded from session
        // The returned array is an array of id's of unfolded sections
        $unfolded = Session::instance()->get('sections', array());

        // Select only sections that are visible (i.e. their parents are unfolded)
        $sections = $section->find_all_unfolded($sectiongroup->id, NULL, $unfolded, array(
            'columns'  => array('id', 'lft', 'rgt', 'level', 'caption', 'active', 'section_active'),
            'order_by' => $order_by,
            'desc'     => $desc,

            'as_tree'  => true
        ));
        
        /*
        // Load the whole sections tree
        $sections = $section->find_all_by_sectiongroup_id($sectiongroup->id, array(
            'columns'  => array('id', 'lft', 'rgt', 'level', 'caption', 'active', 'section_active'),
            'order_by' => $order_by,
            'desc'     => $desc,

            'as_tree'  => true
        ));
         */

        $view = new View('backend/sections/list');

        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->sectiongroup_id = $sectiongroup->id;
        $view->section_id      = $section->id;

        $view->unfolded = $unfolded;

        $view->sections = $sections;

        $view->sectiongroups = $sectiongroup->find_all_by_site_id($site_id);

        return $view->render();
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
            return $this->_widget_error('Выберите магазин!');
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
        $view = new View('backend/sections/select');

        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->sectiongroup_id = $sectiongroup->id;
        $view->section_id      = $section->id;

        $view->sections = $sections;

        return $view->render();
    }

    /**
     * Renders sections menu
     *
     * @return string
     */
    public function widget_sections_menu()
    {
        $site_id = Model_Site::current()->id;
        if ($site_id === NULL)
        {
            return $this->_widget_error('Выберите магазин!');
        }

        $sectiongroup = Model_SectionGroup::current();
        $section      = Model_Section::current();

        // ----- Render section for current section group
        $order_by = $this->request->param('cat_sorder', 'lft');
        $desc     = (bool) $this->request->param('cat_sdesc', '0');

        // Obtain information about which sections are expanded from session
        // The returned array is an array of id's of unfolded sections
        $unfolded = Session::instance()->get('sections', array());
        // Select only sections that are visible (i.e. their parents are unfolded)
        $sections = $section->find_all_unfolded($sectiongroup->id, NULL, $unfolded, array(
            'columns'  => array('id', 'lft', 'rgt', 'level', 'caption', 'active', 'section_active'),
            'order_by' => $order_by,
            'desc'     => $desc,

            'as_tree'  => true
        ));

        $view = new View('backend/sections/menu');

        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->sectiongroup_id = $sectiongroup->id;
        $view->section_id      = $section->id;

        $view->unfolded = $unfolded;

        $view->sectiongroups = $sectiongroup->find_all_by_site_id($site_id);
        $view->sections = $sections;


        return $view->render();
    }

    /**
     * Save sections branch visibility (folded or not?) in session
     *
     * Executed either via ajax or normally
     */
    public function action_toggle()
    {
        $branch_id = (int) $this->request->param('id');
        $toggle = $this->request->param('toggle', '');

        $section = new Model_Section;
        $section->find($branch_id);

        if ($toggle != '' && $section->id !== NULL)
        {
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
     * Render a branch of sections (used to redraw tree via ajax requests)
     *
     * @param Model_Section $parent
     */
    public function widget_sections_branch(Model_Section $parent)
    {
        $site_id = (int) Model_Site::current()->id;

        $order_by = $this->request->param('cat_sorder', 'lft');
        $desc     = (bool) $this->request->param('cat_sdesc', '0');

        // @TODO:
        //$section_id = (int) $this->request->param('cat_section_id');

        // Obtain information about which sections are expanded from session
        // The returned array is an array of id's of unfolded sections
        $unfolded = Session::instance()->get('sections', array());
        // Select only sections that are visible (i.e. their parents are unfolded)
        $sections = $parent->find_all_unfolded(NULL, $parent, $unfolded, array(
            'columns'  => array('id', 'lft', 'rgt', 'level', 'caption', 'active', 'section_active'),
            'order_by' => $order_by,
            'desc'     => $desc,
            'as_tree'  => TRUE
        ));
        // Set up view
        if ($this->request->controller == 'products')
        {
            $view_script = 'backend/sections/menu_ajax';
        }
        else
        {
            $view_script = 'backend/sections/list_ajax';
        }

        $view = new View($view_script);

        $view->order_by = $order_by;
        $view->desc = $desc;
        // @TODO:
        //$view->section_id = $section_id;

        $view->parent = $parent;
        $view->unfolded = $unfolded;

        $view->sections = $sections;

        return $view->render();
    }
}
