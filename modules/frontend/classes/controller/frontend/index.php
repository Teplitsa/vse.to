<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Index extends Controller_Frontend
{
    /**
     * Render flash messages
     * 
     * @return string
     */
    public function widget_flashmessages()
    {
        $messages = FlashMessages::fetch_all();
        $view = new View('frontend/flashmessages');
        $view->messages = $messages;
        return $view->render();
    }
}
