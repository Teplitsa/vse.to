<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Places extends Controller_Frontend
{  
    /**
     * Select several towns
     */
    public function action_select()
    {
        $layout = $this->prepare_layout();

        if ($this->request->in_window())
        {
            $layout->caption = 'Выбор площадки на портале "' . Model_Site::current()->caption . '"';
            $layout->content = $this->widget_select();
            $this->request->response = $layout->render();
        }
        else
        {
            $this->request->response = $this->render_layout($this->widget_select());
        }
    }
    
    /**
     * Render list of towns to select several towns
     * @param  integer $select
     * @return string
     */
    public function widget_select($select = FALSE)
    {
        $site_id = Model_Site::current()->id;
        if ($site_id === NULL)
        {
            return $this->_widget_error('Выберите портал!');
        }

        // ----- Render section for current section group
        $order_by = $this->request->param('acl_oorder', 'name');
        $desc     = (bool) $this->request->param('acl_odesc', '0');

        $organizers = Model::fly('Model_Place')->find_all(array(
            'order_by' => $order_by,
            'desc'     => $desc
        ));
   
        // Set up view
        $view = new View('backend/organizers/select');

        $view->order_by = $order_by;
        $view->desc = $desc;
        $view->organizers = $organizers;

        return $view->render();
    }
    
    /**
     * Display autocomplete options for a postcode form field
     */
    public function action_ac_place_name()
    {                   
        
        $place_name = isset($_POST['value']) ? trim(UTF8::strtolower($_POST['value'])) : NULL;
        $place_name = UTF8::str_ireplace("ё", "е", $place_name);
                
        if ($place_name == '')
        {
            $this->request->response = '';
            return;
        }

        $limit = 7;
        
        $places  = Model::fly('Model_Place')->find_all_like_name($place_name,array('limit' => $limit));
        
        $items = array();
        
        $pattern = new View('frontend/place_ac');
        
        $i=0;
        foreach ($places as $place)
        {
            $name = $place->name;
            $id = $place->id;
            
            $image_info = $place->image(4);
            
            $pattern->name = $name;
            $pattern->num = $i; 
            $pattern->image_info = $place->image(4);
            
            $items[] = array(
                'caption' => $pattern->render(),
                'value' => array('name' => $name, 'id' => $id) 
            );
            $i++;
        }

        $items[] = array('caption' => '<a data-toggle="modal" href="#PlaceModal" class="active">Добавить новую площадку</a>');
        
        $this->request->response = json_encode($items);
    }
    
}
