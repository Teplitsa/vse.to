<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Countries extends Controller_Backend
{
    /**
     * Create layout and link module stylesheets
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        $layout = parent::prepare_layout($layout_script);
        $layout->caption = 'Страны и регионы';
        return $layout;
    }

    /**
     * Import post offices
     */
    public function action_index()
    {
        $form = new Form_Backend_ImportPostOffices();

        if ($form->is_submitted() && $form->validate())
        {
            $model = Model::fly('Model_PostOffice');
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
        $view->caption  = 'Импорт списка объектов почтовой связи';
        $view->form = $form;

        $this->request->response = $this->render_layout($view);
    }

    /**
     * Display autocomplete options for a postcode form field
     */
    public function action_ac_postcode()
    {
        $postcode = isset($_POST['value']) ? trim(UTF8::strtolower($_POST['value'])) : NULL;
        $postcode = UTF8::str_ireplace("ё", "е", $postcode);

        if ($postcode == '')
        {
            $this->request->response = '';
            return;
        }

        $limit = 7;

        $postoffice  = Model::fly('Model_PostOffice');
        $postoffices = $postoffice->find_all_like_postcode($postcode, array('limit' => $limit));

        if ( ! count($postoffices))
        {
            $this->request->response = '';
            return;
        }

        $items = array();
        foreach ($postoffices as $postoffice)
        {
            $caption = $postoffice->postcode;

            // Add additional info (city, region)
            $info = UTF8::ucfirst($postoffice->city);
            if ($postoffice->region_name != '')
            {
                $info .= ', ' . UTF8::ucfirst($postoffice->region_name);
            }
            $caption .= " ($info)";

            $items[] = array(
                'caption' => $caption,
                'value' => $postoffice->postcode
            );
        }

        $this->request->response = json_encode($items);
    }

    /**
     * Display autocomplete options for a city form field
     */
    public function action_ac_city()
    {
        $city = isset($_POST['value']) ? trim(UTF8::strtolower($_POST['value'])) : NULL;
        $city = UTF8::str_ireplace("ё", "е", $city);

        if ($city == '')
        {
            $this->request->response = '';
            return;
        }

        $limit = 7;

        $postoffice  = Model::fly('Model_PostOffice');
        $postoffices = $postoffice->find_all_like_city($city, array(
            'order_by' => 'city',
            'desc'     => FALSE,
            'limit'    => $limit
        ));

        if ( ! count($postoffices))
        {
            $this->request->response = '';
            return;
        }

        $items = array();
        foreach ($postoffices as $postoffice)
        {
            $caption = $postoffice->city;

            // Highlight found part
            $caption = str_replace($city, '<strong>' . $city .'</strong>', $caption);

            // Add additional info (postcode, region)
            $info = $postoffice->postcode;
            if ($postoffice->region_name != '')
            {
                $info .= ', ' . UTF8::ucfirst($postoffice->region_name);
            }
            $caption .= " ($info)";

            $items[] = array(
                'caption' => $caption,
                'value' => UTF8::ucfirst($postoffice->city)
            );
        }

        $this->request->response = json_encode($items);
    }
}
