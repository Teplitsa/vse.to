<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Breadcrumbs extends Controller_Frontend
{
    /**
     * Renders breadcrumbs
     */
    public function widget_breadcrumbs()
    {
        // Set up view
        $view = new View('frontend/breadcrumbs');
        $view->breadcrumbs = Breadcrumbs::get_all();
        return $view->render();
    }
}
