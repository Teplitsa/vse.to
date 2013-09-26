<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_CatImport extends Controller_Backend
{
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
     * Import catalog
     */
    public function action_index()
    {
        if (Model_Site::current()->id === NULL)
        {
            $this->_action_error('Выберите магазин, в который требуется импортировать каталог');
            return;
        }

        $html = '';

        // ----- pricelist
        $form_pricelist = new Form_Backend_CatImport_Pricelist();

        if ($form_pricelist->is_submitted() && $form_pricelist->validate())
        {
            // move uploaded pricelist file
            $tmp_file = File::upload($form_pricelist->get_value('file'));

            TaskManager::start('import_pricelist', array(
                'supplier' => $form_pricelist->get_value('supplier'),
                'price_factor' => $form_pricelist->get_value('price_factor'),
                'file'     => $tmp_file
            ));

            $this->request->redirect($this->request->uri);
        }

        $view = new View('backend/form');
        $view->caption = 'Импорт прайслиста';

        $view->form = $form_pricelist;
        $html .= $view->render();
        
        // ----- structure
        $form_structure = new Form_Backend_CatImport_Structure();
        $form_structure->set_defaults(Task_Import_Structure::$default_params);

        if ($form_structure->is_submitted() && $form_structure->validate())
        {
            $params = $form_structure->get_values(); 
            unset($params['submit']);

            // move uploaded structure file
            $params['file'] = File::upload($form_structure->get_value('file'));

            TaskManager::start('import_structure',$params);
            
            $this->request->redirect($this->request->uri);
        }

        $view = new View('backend/form');
        $view->caption = 'Импорт структуры каталога';

        $view->form = $form_structure;
        $html .= $view->render();
        
        $this->request->response = $this->render_layout($html);
    }
    
}
