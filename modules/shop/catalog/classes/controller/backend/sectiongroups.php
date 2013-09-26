<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_SectionGroups extends Controller_BackendCRUD
{
    /**
     * Configure actions
     *
     * @return array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_SectionGroup';
        $this->_form  = 'Form_Backend_SectionGroup';
        $this->_view  = 'backend/form';
        
        return array(
            'create' => array(
                'view_caption' => 'Создание группы категорий'
            ),
            'update' => array(
                'view_caption' => 'Редактирование группы категорий ":caption"'
            ),
            'delete' => array(
                'view_caption' => 'Удаление группы категорий',
                'message' => 'Удалить группу категорий ":caption", все категории и события из этой группы?'
            )
        );
    }

    /**
     * Create layout (proxy to catalog controller)
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        $layout = $this->request->get_controller('catalog')->prepare_layout($layout_script);
        if ($this->request->action == 'select')
        {
            // Add sections select js scripts
            $layout->add_script(Modules::uri('catalog') . '/public/js/backend/sectiongroups_select.js');
        }
        
        return $layout; 
    }

    /**
     * Render layout (proxy to catalog controller)
     *
     * @param  View|string $content
     * @param  string $layout_script
     * @return string
     */
    public function render_layout($content, $layout_script = NULL)
    {
        return $this->request->get_controller('catalog')->render_layout($content, $layout_script);
    }

    /**
     * Render all available section groups for current site
     */
    public function action_index()
    {
        $this->request->response = $this->render_layout($this->widget_sectiongroups());
    }

    /**
     * Select several sections
     */
    public function action_select()
    {
        $layout = $this->prepare_layout();

        if ($this->request->in_window())
        {
            $layout->caption = 'Выбор категорий на портале "' . Model_Site::current()->caption . '"';
            $layout->content = $this->widget_sectiongroups('backend/sectiongroups/select');
            $this->request->response = $layout->render();
        }
        else
        {
            $this->request->response = $this->render_layout($this->widget_sectiongroups('backend/sectiongroups/select'));
        }
    }
    
    /**
     * Create new section group
     *
     * @param  string|Model $model
     * @param  array $params
     * @return Model_SectionGroup
     */
    protected function _model_create($model, array $params = NULL)
    {
        if (Model_Site::current()->id === NULL)
        {
            throw new Controller_BackendCRUD_Exception('Выберите магазин перед созданием группы категорий!');
        }
        
        // New section for current site
        $sectiongroup = new Model_SectionGroup();
        $sectiongroup->site_id = (int) Model_Site::current()->id;
        
        return $sectiongroup;
    }

    /**
     * Renders list of sectiongroups
     *
     * @return string
     */
    public function widget_sectiongroups($view = 'backend/sectiongroups')
    {
        $site_id = Model_Site::current()->id;
        if ($site_id === NULL)
        {
            return $this->_widget_error('Выберите магазин!');
        }
        
        $sectiongroup = new Model_SectionGroup();

        $order_by = $this->request->param('cat_sgorder', 'id');
        $desc     = (bool) $this->request->param('cat_sgdesc', '0');

        $sectiongroups = $sectiongroup->find_all_by_site_id($site_id, array(
            'columns'  => array('id', 'caption'),
            'order_by' => $order_by,
            'desc'     => $desc
        ));

        $view = new View($view);

        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->sectiongroups = $sectiongroups;

        return $view->render();
    }
}
