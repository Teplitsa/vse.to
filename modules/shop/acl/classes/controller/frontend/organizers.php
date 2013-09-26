<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Organizers extends Controller_Frontend
{
    /**
     * Prepare layout
     *
     * @param  string $layout_script
     * @return Layout
     */
    public function prepare_layout($layout_script = 'layouts/acl')
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
    
    public function action_index() {
        //TODO CARD of the USER
    }

    
    /**
     * Display autocomplete options for a postcode form field
     */
    public function action_ac_organizer_name()
    {
        $organizer_name = isset($_POST['value']) ? trim(UTF8::strtolower($_POST['value'])) : NULL;
        $organizer_name = UTF8::str_ireplace("ё", "е", $organizer_name);

        if ($organizer_name == '')
        {
            $this->request->response = '';
            return;
        }

        $limit = 7;
       
        $organizers  = Model::fly('Model_Organizer')->find_all_like_name($organizer_name,array('limit' => $limit));
             
//        if ( ! count($organizers))
//        {
//            $this->request->response = '';
//            return;
//        }

        $items = array();
        
        $pattern = new View('frontend/organizer_ac');
        
        $i=0;
        foreach ($organizers as $organizer)
        {
            $name = $organizer->name;
            $id = $organizer->id;
            
            $image_info = $organizer->image(4);
            
            $pattern->name = $name;
            $pattern->num = $i; 
            $pattern->image_info = $organizer->image(4);
            
            $items[] = array(
                'caption' => $pattern->render(),
                'value' => array('name' => $name, 'id' => $id) 
            );
            $i++;
        }

        $items[] = array('caption' => '<a data-toggle="modal" href="#OrgModal" class="active">Добавить новую организацию</a>');
        
        $this->request->response = json_encode($items);
    }
}
