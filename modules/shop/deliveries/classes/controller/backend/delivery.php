<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Base class for delivery module backend controller
 */
abstract class Controller_Backend_Delivery extends Controller_BackendCRUD
{
    /**
     * Set up default actions for payment module controller
     */
    public function setup_actions()
    {
        $module = substr(get_class($this), strlen('Controller_Backend_Delivery_'));

        $this->_model = "Model_Delivery_$module";
        $this->_form  = "Form_Backend_Delivery_$module";

        return array(
            'create' => array(
                'view_caption' => 'Добавление способа доставки'
            ),
            'update' => array(
                'view_caption' => 'Редактирование способа доставки ":caption"'
            ),
            'delete' => array(
                'view_caption' => 'Удаление способа доставки',
                'message' => 'Удалить способ доставки ":caption"?'
            )
        );
    }

    /**
     * Create layout (proxy to orders controller)
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        return $this->request->get_controller('orders')->prepare_layout($layout_script);
    }

    /**
     * Render layout (proxy to orders controller)
     *
     * @param  View|string $content
     * @param  string $layout_script
     * @return string
     */
    public function render_layout($content, $layout_script = NULL)
    {
        return $this->request->get_controller('orders')->render_layout($content, $layout_script);
    }

    /**
     * Create action
     */
    public function action_create()
    {
        // Redirect two steps back
        $this->_action('create', array('redirect_uri' => URL::uri_back(NULL, 2)));
    }
}