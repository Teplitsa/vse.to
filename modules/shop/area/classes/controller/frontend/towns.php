<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Towns extends Controller_Frontend
{    
    /**
     * Prepare layout
     *
     * @param  string $layout_script
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        if ($layout_script === NULL)
        {
            if ($this->request->action == 'index')
            {
                $layout_script = 'layouts/map';
            }
        }
                
        return parent::prepare_layout($layout_script);
    }
    
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
    
    public function action_index()
    { 
        $towns = Model::fly('Model_Town')->find_all();
        
        $pattern = new View('frontend/places/map_place');

        $place = new Model_Place();

        foreach ($towns as $town)
        {
            $options = array();

            $places[$town->alias] = $place->find_all_by_town_id($town->id);
            
            $content = '';
            
            $glue= '';
            foreach ($places[$town->alias] as $place) {
                $pattern->place =$place;
                $content .= $glue.$pattern->render();
                $glue = '<br>';
            }
            
            Gmap::instance()->addMarkerByAddress($town->name,$town->name,$content);
        }
        
        $view = new View('frontend/towns/map');
        $view->towns = $towns;
        //$view->places = $places;
        
        $layout = $this->prepare_layout();
        $layout->content = $view;
        
        // Add breadcrumbs
        //$this->add_breadcrumbs();
        $this->request->response = $layout->render();
        
        return $view->render();       
         
    }
    
}
