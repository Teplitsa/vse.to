<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Delivery_Courier extends Controller_Backend_Delivery
{
    /**
     * Prepare a view for update action
     *
     * @param  Model $model
     * @param  Form $form
     * @param  array $params
     * @return View
     */
    protected function  _view_update(Model $model, Form $form, array $params = NULL)
    {
        $params['view'] = 'backend/delivery/courier';

        $view = $this->_view('update', $model, $form, $params);

        // Render list of courier delivery zones
        $view->zones = $this->request->get_controller('courierzones')->widget_courierzones($model);

        return $view;
    }
}