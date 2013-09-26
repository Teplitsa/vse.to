<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Towns extends Controller_BackendCRUD
{
    /**
     * Setup actions
     * 
     * @return array
     */
    public function setup_actions()
    {        
        $this->_model = 'Model_Town';
        $this->_form  = 'Form_Backend_Town';
        $this->_view  = 'backend/form_adv';        

        return array(
            'create' => array(
                'view_caption' => 'Создание города'
            ),
            'update' => array(
                'view_caption' => 'Редактирование города'
            ),
            'delete' => array(
                'view_caption' => 'Удаление города',
                'message' => 'Удалить город ":name" '
            ),
            'multi_delete' => array(
                'view_caption' => 'Удаление городов',
                'message' => 'Удалить выбранные города?'
            )
        );
    }    
    /**
     * Create layout (proxy to area controller)
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {   
        return $this->request->get_controller('area')->prepare_layout($layout_script);
    }

    /**
     * Render layout (proxy to area controller)
     *
     * @param  View|string $content
     * @param  string $layout_script
     * @return string
     */
    public function render_layout($content, $layout_script = NULL)
    {
        return $this->request->get_controller('area')->render_layout($content, $layout_script);
    }
    
    /**
     * Render all available section properties
     */
    public function action_index()
    {
        $this->request->response = $this->render_layout($this->widget_towns('backend/towns/place_select'));
    }   
   
    /**
     * Handles the selection of access towns for event
     */
    public function action_towns_select()
    {
        if ( ! empty($_POST['ids']) && is_array($_POST['ids']))
        {
            $town_ids = '';
            foreach ($_POST['ids'] as $town_id)
            {
                $town_ids .= (int) $town_id . '_';
            }
            $town_ids = trim($town_ids, '_');

            $this->request->redirect(URL::uri_back(NULL, 1, array('access_town_ids' => $town_ids)));
        }
        else
        {
            // No towns were selected
            $this->request->redirect(URL::uri_back());
        }
    }    

    /**
     * Import towns
     */
    /*public function action_index()
    {
        $form = new Form_Backend_ImportTowns();

        if ($form->is_submitted() && $form->validate())
        {
            $model = Model::fly('Model_Town');
            $model->import($form->get_component('file')->value);

            if ( ! $model->has_errors())
            {
                // Upload was successfull
                $this->request->redirect($this->request->uri . '?flash=ok');
            }
            else
            {
                // Add import errors to form
                $form->errors($model->errors());
            }
        }

        if (isset($_GET['flash']) && $_GET['flash'] == 'ok')
        {
            
        }

        $view = new View('backend/form');
        $view->caption  = 'Импорт списка городов';
        $view->form = $form;

        $this->request->response = $this->render_layout($view);
    }*/

    /**
     * Display autocomplete options for a city form field
     */
    public function action_ac_city()
    {
        $town_str = isset($_POST['value']) ? trim(UTF8::strtolower($_POST['value'])) : NULL;
        $town_str = UTF8::str_ireplace("ё", "е", $town);

        if ($town == '')
        {
            $this->request->response = '';
            return;
        }

        $limit = 7;

        $town  = Model::fly('Model_Town');
        $towns = $town->find_all_like_town($town, array(
            'order_by' => 'town',
            'desc'     => FALSE,
            'limit'    => $limit
        ));

        if ( ! count($towns))
        {
            $this->request->response = '';
            return;
        }

        $items = array();
        foreach ($towns as $town)
        {
            $caption = $town->town;

            // Highlight found part
            $caption = str_replace($town, '<strong>' . $town .'</strong>', $caption);

            $items[] = array(
                'caption' => $caption,
                'value' => UTF8::ucfirst($town->phonecode)
            );
        }

        $this->request->response = json_encode($items);
    }
    
    /**
     * Select several towns
     */
    public function action_select()
    {
        $layout = $this->prepare_layout();

        if ($this->request->in_window())
        {
            $layout->caption = 'Выбор городов на портале "' . Model_Site::current()->caption . '"';
            $layout->content = $this->widget_towns();
            $this->request->response = $layout->render();
        }
        else
        {
            $this->request->response = $this->render_layout($this->widget_select());
        }
    }
    
    /**
     * Render list of towns to select several towns
     * @return string
     */
    public function widget_towns($view_script = 'backend/towns/select') {
        $site_id = Model_Site::current()->id;
        if ($site_id === NULL)
        {
            return $this->_widget_error('Выберите портал!');
        }

        // ----- Render section for current section group
        $order_by = $this->request->param('are_torder', 'name');
        $desc     = (bool) $this->request->param('are_tdesc', '0');
        $town_alias = (int) $this->request->param('are_town_alias');

        $per_page = 20;
        $town = Model::fly('Model_Town');
        $count = $town->count();
        $pagination = new Pagination($count, $per_page);

        $towns = Model::fly('Model_Town')->find_all(array(
            'offset'   => $pagination->offset,
            'limit'    => $pagination->limit,            
            'order_by' => $order_by,
            'desc'     => $desc
        ));
   
        // Set up view
        $view = new View($view_script);

        $view->order_by = $order_by;
        $view->desc = $desc;
        $view->towns = $towns;
        $view->town_alias = $town_alias;

        $view->pagination = $pagination->render('backend/pagination');

        return $view->render();        
    }    
     
}
