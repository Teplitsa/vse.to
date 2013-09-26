<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Properties extends Controller_BackendCRUD
{
    /**
     * Configure actions
     * @var array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Property';
        $this->_form  = 'Form_Backend_Property';

        return array(
            'create' => array(
                'view_caption' => 'Создание характеристики'
            ),
            'update' => array(
                'view_caption' => 'Редактирование характеристики ":caption"'
            ),
            'delete' => array(
                'view_caption' => 'Удаление характеристики',
                'message' => 'Удалить характеристику ":caption"?'
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
        return $this->request->get_controller('catalog')->prepare_layout($layout_script);
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
     * Render all available section properties
     */
    public function action_index()
    {
        $this->request->response = $this->render_layout($this->widget_properties());
    }

    /**
     * Handles the selection of additional sections for product
     */
    public function action_sectiongroups_select()
    {
        if ( ! empty($_POST['ids']) && is_array($_POST['ids']))
        {
            $sectiongroup_ids = '';
            foreach ($_POST['ids'] as $sectiongroup_id)
            {
                $sectiongroup_ids .= (int) $sectiongroup_id . '_';
            }
            $sectiongroup_ids = trim($sectiongroup_ids, '_');

            $this->request->redirect(URL::uri_back(NULL, 1, array('cat_sectiongroup_ids' => $sectiongroup_ids)));
        }
        else
        {
            // No sections were selected
            $this->request->redirect(URL::uri_back());
        }
    }

    /**
     * This action is executed via ajax request after additional
     * sectiongroups for property have been selected.
     *
     * It redraws the "additional sectiongroups" form element accordingly
     */
    public function action_ajax_sectiongroups_select()
    {
        if ($this->request->param('cat_sectiongroup_ids') != '')
        {

            $action =  ((int)$this->request->param('id') == 0) ? 'create' : 'update';
            
            $property = $this->_model($action, 'Model_Property');

            $form    = $this->_form($action, $property);

            $component = $form->find_component('propsections');

            if ($component)
            {
                $this->request->response = $component->render();
            }
        }
    }    
    /**
     * Create new property
     *
     * @return Model_Property
     */
    protected function _model_create($model, array $params = NULL)
    {
        if (Model_Site::current()->id === NULL)
        {
            throw new Controller_BackendCRUD_Exception('Выберите магазин перед созданием характеристики!');
        }

        // New section for current site
        $property = new Model_Property();
        $property->site_id = (int) Model_Site::current()->id;

        return $property;
    }

    /**
     * Render list of section properties
     */
    public function widget_properties()
    {
        $site_id = Model_Site::current()->id;
        if ($site_id === NULL)
        {
            return $this->_widget_error('Выберите магазин!');
        }

        $order_by = $this->request->param('cat_prorder', 'position');
        $desc = (bool) $this->request->param('cat_prdesc', '0');

        $properties = Model::fly('Model_Property')->find_all_by_site_id($site_id, array(
            'order_by' => $order_by,
            'desc' => $desc
        ));

        // Set up view
        $view = new View('backend/properties');

        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->properties = $properties;

        return $view->render();
    }
}