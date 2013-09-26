<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Catalog extends Controller_Backend
{
    /**
     * Create layout and link module stylesheets
     *
     * @return Layout
     */
    
    public function prepare_layout($layout_script = NULL)
    {
        $layout = parent::prepare_layout($layout_script);

        $layout->add_style(Modules::uri('catalog') . '/public/css/backend/catalog.css');

        // Add catalog js scripts
        if ($this->request->action == 'index')
        {
            $layout->add_script(Modules::uri('catalog') . '/public/js/backend/catalog.js');
            $layout->add_script(
                "var branch_toggle_url = '"
                  . URL::to('backend/catalog/sections', array(
                        'action' => 'toggle', 'id' => '{{id}}', 'toggle' => '{{toggle}}')
                    )
                  . '?context=' . $this->request->uri
                  . "';"
            , TRUE);
        }

        if ($this->request->action == 'product_select')
        {
            // Add product select js scripts
            $layout->add_script(Modules::uri('catalog') . '/public/js/backend/product_select.js');
            $layout->add_script(
                "var product_selected_url = '" . URL::to('backend/catalog', array('action' => 'product_selected', 'product_id' => '{{id}}')) . "';"
            , TRUE);
        }

        if ($this->request->action == 'sections_select')
        {
            // Add sections select js scripts
            $layout->add_script(Modules::uri('catalog') . '/public/js/backend/sections_select.js');
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
     * Render additional catalog actions
     */
    public function action_index()
    {
        $updatestats_form = new Form_Backend_Catalog_UpdateStats();
        if ($updatestats_form->is_submitted())
        {
            Model::fly('Model_Section')->mapper()->update_products_count();

            $updatestats_form->flash_message('Показатели пересчитаны');
            $this->request->redirect($this->request->uri);
        }

        $generatealiases_form = new Form_Backend_Catalog_GenerateAliases();
        if ($generatealiases_form->is_submitted())
        {
            TaskManager::start('generatealiases');

            $this->request->redirect($this->request->uri);
        }

        $updatelinks_form = new Form_Backend_Catalog_UpdateLinks();
        if ($updatelinks_form->is_submitted())
        {
            TaskManager::start('updatelinks');

            $this->request->redirect($this->request->uri);
        }
        
        $content = 
            $updatestats_form->render()
          . $generatealiases_form->render()
          . $updatelinks_form->render();

        $this->request->response = $this->render_layout($content);
    }

    /**
     * Select one product
     */
    
    public function action_product_select()
    {
        $layout = $this->prepare_layout();

        if ($this->request->in_window())
        {
            $layout->caption = 'Выбор анонса на портале "' . Model_Site::current()->caption . '"';
            $layout->content = $this->widget_product_select();
        }
        else
        {
            $view = $this->widget_product_select();
            $view->caption = 'Выбор анонса на портале "' . Model_Site::current()->caption . '"';

            $layout->content = $view->render();
        }

        $this->request->response = $layout->render();
    }

    /**
     * Generate the response for ajax request after product is selected
     * to inject new values correctly into the form
     */
    /*
    public function action_product_selected()
    {
        $product = new Model_Product();
        $product->find((int) $this->request->param('product_id'));

        if (isset($product->id))
        {
            $values = array(
                'caption' => $product->caption,
                'price'   => $product->price->amount,
                'weight'  => $product->weight
            );
            $this->request->response = JSON_encode($values);
        }
    }
    */
    /**
     * Render products and sections for the selection of one product
     */
    
    public function widget_product_select()
    {
        $view = new View('backend/workspace_2col');

        $view->column1  = $this->request->get_controller('sections')->widget_sections_menu('backend/sections_product_select');
        $view->column2  = $this->request->get_controller('products')->widget_products('backend/product_select');

        return $view;
    }


    
}
