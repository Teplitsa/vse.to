<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Tags extends Controller_BackendCRUD
{
    /**
     * Configure actions
     * @var array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Tag';
        $this->_form  = 'Form_Backend_Tag';
        
        return array(
            'create' => array(
                'view_caption' => 'Создание тега'
            ),
            'update' => array(
                'view_caption' => 'Редактирование тега'
            ),
            'delete' => array(
                'view_caption' => 'Удаление тега',
                'message' => 'Удалить тег?'
            )
        );
    }

    /**
     * Render layout
     *
     * @param  View|string $content
     * @param  string $layout_script
     * @return string
     */
    public function render_layout($content, $layout_script = NULL)
    {
        $view = new View('backend/workspace');
        $view->content = $content;

        $layout = $this->prepare_layout($layout_script);
        $layout->caption = 'Теги';
        $layout->content = $view;
        return $layout->render();
    }

    /**
     * Prepare tag for update & delete actions
     *
     * @param  string $action
     * @param  string|Model $model
     * @param  array $params
     * @return Model_Tag
     */
    protected function _model($action, $model, array $params = NULL)
    {
        $tag = parent::_model($action, $model, $params);

        if ($action == 'create')
        {
            // Set up onwer for tag being created
            $tag->owner_type = $this->request->param('owner_type');
            $tag->owner_id   = $this->request->param('owner_id');
        }

        // Set up config for tag in action
        $tag->config = $this->request->param('config');

        return $tag;
    }

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
        
        $num=0;
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
    
    
    /**
     * Renders list of tags for specified owner
     *
     * @param  string  $owner_type
     * @param  integer $owner_id
     * @return string
     */
    public function widget_tags($owner_type, $owner_id, $config)
    {
        // Add styles to layout
        Layout::instance()->add_style(Modules::uri('tags') . '/public/css/backend/tags.css');

        $order_by = $this->request->param('tag_order_by', 'position');
        $desc     = (boolean) $this->request->param('tag_desc', '0');
        
        $tags = Model::fly('Model_Tag')->find_all_by_owner_type_and_owner_id(
            $owner_type, $owner_id, array('order_by' => $order_by, 'desc' => $desc)
        );

        $view = new View('backend/tags');

        $view->tags     = $tags;
        $view->owner_type = $owner_type;
        $view->owner_id   = $owner_id;
        $view->config     = $config;

        $view->desc = $desc;
        
        return $view;
    }
}