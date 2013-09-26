<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_CourierZones extends Controller_BackendCRUD
{
    /**
     * Configure actions
     *
     * @return array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_CourierZone';
        $this->_form  = 'Form_Backend_CourierZone';

        return array(
            'create' => array(
                'view_caption' => 'Создание новой зоны доставки курьером'
            ),
            'update' => array(
                'view_caption' => 'Редактирование зоны доставки курьером ":name"'
            ),
            'delete' => array(
                'view_caption' => 'Удаление товара',
                'message'      => 'Удалить зону доставки курьером ":name"?'
            ),
            'multi_delete' => array(
                'view_caption' => 'Удаление зон доставки курьером',
                'message' => 'Удалить выбранные зоны?',
                'message_empty' => 'Выберите хотя бы одну зону!'
            )
        );
    }

    /**
     * Create layout (proxy to delivery_courier controller)
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        return $this->request->get_controller('delivery_coureir')->prepare_layout($layout_script);
    }

    /**
     * Render layout (proxy to delivery_courier controller)
     *
     * @param  View|string $content
     * @param  string $layout_script
     * @return string
     */
    public function render_layout($content, $layout_script = NULL)
    {
        return $this->request->get_controller('delivery_courier')->render_layout($content, $layout_script);
    }

    /**
     * Create new courier zone model
     *
     * @param  string|Model $model
     * @param  array $params
     * @return Model_CourierZone
     */
    protected function _model_create($model, array $params = NULL)
    {
        // New zone for current delivery type
        $delivery = new Model_Delivery_Courier();

        $delivery->find((int) $this->request->param('delivery_id'));
        if ( ! isset($delivery->id))
        {
            throw new Controller_BackendCRUD_Exception('Указанный способ доставки не найден!');
        }

        $zone = new Model_CourierZone();
        $zone->delivery_id = $delivery->id;
        return $zone;
    }

    /**
     * Render list of courier delivery zones
     *
     * @param Model_Delivery_Courier $delivery
     */
    public function widget_courierzones(Model_Delivery_Courier $delivery)
    {
        $order_by = 'position';
        $desc = FALSE;

        $zones = Model::fly('Model_CourierZone')->find_all_by_delivery_id($delivery->id, array(
            'order_by' => $order_by,
            'desc'     => $desc
        ));

        // Set up view
        $view = new View('backend/delivery/courierzones');

        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->delivery_id = $delivery->id;
        $view->zones = $zones;

        return $view->render();
    }
}