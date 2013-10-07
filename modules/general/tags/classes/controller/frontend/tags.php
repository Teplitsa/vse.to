<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Tags extends Controller_Frontend
{

    /**
     * Display autocomplete options for a postcode form field
     */
    public function action_ac_tag()
    {
        
        $tag = isset($_POST['value']) ? trim(UTF8::strtolower($_POST['value'])) : NULL;

        if ($tag == '')
        {
            $this->request->response = '';
            return;
        }

        $limit = 7;
       
        $tags  = Model::fly('Model_Tag')->find_all_like_name($tag,array('limit' => $limit));
             
        if ( ! count($tags))
        {
            $this->request->response = '';
            return;
        }

        $items = array();
        
        $pattern = new View('backend/tag_ac');
        
        $num = 0;
        foreach ($tags as $tag)
        {            
            $name = $tag->name;
            $pattern->name = $name;
            $pattern->num = $num;
            
            $items[] = array(
                'caption' => $pattern->render(),
                'value' => array('name' => $name) 
            );
            
            $num++;
        }

        $this->request->response = json_encode($items);
    }
}