<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Base class for payment module backend controller
 */
abstract class Controller_Backend_Payment extends Controller_BackendCRUD
{
    /**
     * Set up default actions for payment module controller
     */
    public function setup_actions()
    {
        $module = substr(get_class($this), strlen('Controller_Backend_Payment_'));
        
        return array(
            'create' => array(
                'model' => "Model_Payment_$module",
                'form' => "Form_Backend_Payment_$module",
                'view' => 'backend/form',
                'view_caption' => 'Добавление способа оплаты'
            ),
            'update' => array(
                'model' => "Model_Payment_$module",
                'form' => "Form_Backend_Payment_$module",
                'view' => 'backend/form',
                'view_caption' => 'Редактирование способа оплаты ":caption"'
            ),
            'delete' => array(
                'model' => "Model_Payment_$module",
                'view' => 'backend/form',
                'view_caption' => 'Удаление способа оплаты',
                'message' => 'Удалить способ оплаты ":caption"?'
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
        $this->_action('create', array('redirect_uri' => URL::uri_back(NULL, 2))); // Redirect two steps back
    }
}