<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Towns extends Controller_Frontend
{
    /**
     * Render select_town bar
     */
    public function widget_select()
    {
        $town = Model_Town::current();

        if ( ! isset($town->id))
        {
            $this->_action_404('Указанный город не найден');
            return;
        }
        
        $towns = Model::fly('Model_Town')->find_all(array('order_by'=>'name','desc'=>'0'));

        $view = new View('frontend/towns/select');
        
        $view->towns = $towns;
        
        $view->town = $town;
        
        return $view->render();        
    }
    
    public function action_choose()
    {
        $town = Model_Town::current();
        
        if ( ! isset($town->id))
        {
            $this->_action_404('Указанный город не найден');
            return;
        }

        Cookie::set(Model_Town::TOWN_TOKEN, $town->alias, time() + Model_Town::TOWN_LIFETIME);            
        
        $this->request->redirect(URL::uri_to('frontend/catalog'));
    }
}
