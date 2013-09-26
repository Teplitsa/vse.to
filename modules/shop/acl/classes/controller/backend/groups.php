<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Groups extends Controller_BackendCRUD
{
    /**
     * Setup actions
     *
     * @return array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Group';
        $this->_form  = 'Form_Backend_Group';

        return array(
            'create' => array(
                'view_caption' => 'Создание группы'

            ),
            'update' => array(
                'view_caption' => 'Редактирование группы ":name"'
            ),
            'delete' => array(
                'view_caption' => 'Удаление группы',
                'message' => 'Удалить группу ":name" и всех пользователей из неё?'
            )
        );
    }

    /**
     * Create layout (proxy to acl controller)
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        return $this->request->get_controller('acl')->prepare_layout($layout_script);
    }

    /**
     * Render layout (proxy to acl controller)
     *
     * @param  View|string $content
     * @param  string $layout_script
     * @return string
     */
    public function render_layout($content, $layout_script = NULL)
    {
        return $this->request->get_controller('acl')->render_layout($content, $layout_script);
    }

    /**
     * Renders list of groups
     *
     * @return string Html
     */
    public function widget_groups($view = 'backend/groups')
    {
        $group = new Model_Group();

        $order_by = $this->request->param('acl_gorder', 'id');
        $desc = (bool) $this->request->param('acl_gdesc', '0');
        $group_id = (int) $this->request->param('group_id');

        $groups = $group->find_all(array('order_by' => $order_by, 'desc' => $desc));

        $view = new View($view);
        $view->order_by = $order_by;
        $view->desc = $desc;
        $view->group_id = $group_id;

        $view->groups = $groups;

        return $view->render();
    }
}
