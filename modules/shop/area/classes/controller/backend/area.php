<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Area extends Controller_Backend
{
    /**
     * Create layout and link module stylesheets
     *
     * @return Layout
     */
    
    public function prepare_layout($layout_script = NULL)
    {
        $layout = parent::prepare_layout($layout_script);

        $layout->add_style(Modules::uri('area') . '/public/css/backend/area.css');
        
        if ($this->request->action == 'place_select')
        {
            // Add place select js scripts
            $layout->add_script(Modules::uri('area') . '/public/js/backend/place_select.js');
            $layout->add_script(
                "var place_selected_url = '" . URL::to('backend/area', array('action' => 'place_selected', 'place_id' => '{{id}}')) . "';"
            , TRUE);
        }
        
        // Add area js scripts
        if ($this->request->action == 'select')
        {
            // Add towns select js scripts
            $layout->add_script(Modules::uri('area') . '/public/js/backend/towns_select.js');
        }

        return $layout;
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
        $layout->content = $view;
        return $layout->render();
    }

    /**
     * Select one product
     */
    
    public function action_place_select()
    { 
        $layout = $this->prepare_layout();

        if ($this->request->in_window())
        {
            $layout->caption = 'Выбор площадки на портале "' . Model_Site::current()->caption . '"';
            $layout->content = $this->widget_place_select();
        }
        else
        {
            $view = $this->widget_place_select();
            $view->caption = 'Выбор площадки на портале "' . Model_Site::current()->caption . '"';

            $layout->content = $view->render();
        }

        $this->request->response = $layout->render();
    }

    /**
     * Render products and sections for the selection of one product
     */
    
    public function widget_place_select()
    {

        $view = new View('backend/workspace_2col');

        $view->column1  = $this->request->get_controller('towns')->widget_towns('backend/towns/town_place_select');
        $view->column2  = $this->request->get_controller('places')->widget_places('backend/places/select');

        return $view;
    }

    /**
     * Generate the response for ajax request after place is selected
     * to inject new values correctly into the form
     */
    public function action_place_selected()
    {
        $place = new Model_Place();
        $place->find((int) $this->request->param('place_id'));
        $values = $place->values();
        $values['town'] = Model::fly('Model_Town')->find($place->town_id)->name;
        $this->request->response = JSON_encode($values);
    }
    
}
