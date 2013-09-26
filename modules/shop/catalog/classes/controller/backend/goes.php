<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Goes extends Controller_BackendRES
{
    /**
     * Configure actions
     *
     * @return array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Go';
        $this->_form  = 'Form_Backend_Go';
        $this->_view  = 'backend/form';
        
        return array(
            'create' => array(
                'view_caption' => 'Создание "Я пойду"'
            ),
            'delete' => array(
                'view_caption' => 'Удаление "Я пойду"',
                'message' => 'Удалить "Я пойду"?'
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
        return $this->request->get_controller('telemosts')->prepare_layout($layout_script);
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
        return $this->request->get_controller('telemosts')->render_layout($content, $layout_script);
    }

    /**
     * Prepare telemost model for create action
     *
     * @param  string|Model_Telemost $telemost
     * @param  array $params
     * @return Model_Telemost
     */
    protected function  _model_create($go, array $params = NULL)
    {
        $go = parent::_model('create', $go, $params);

        $telemost_id = (int) $this->request->param('telemost_id');
        if ( ! Model::fly('Model_Telemost')->exists_by_id($telemost_id))
        {
            throw new Controller_BackendCRUD_Exception('Указанный телемост не существует!');
        }

        $go->telemost_id = $telemost_id;

        return $go;
    }

    /**
     * Renders list of telemost goes
     *
     * @param  Model_Telemost $telemost
     * @return string
     */
    public function widget_goes($telemost)
    {
        $goes = $telemost->goes;

        // Set up view
        $view = new View('backend/goes');

        $view->telemost = $telemost;
        $view->goes = $goes;        

        return $view->render();
    }
}