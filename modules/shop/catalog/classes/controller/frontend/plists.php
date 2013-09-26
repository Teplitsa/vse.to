<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_PLists extends Controller_Frontend
{    
    /**
     * Render list of product lists
     */
    public function widget_plist($name)
    {
        $plist = new Model_PList();
        $plist->find_by_name_and_site_id($name, Model_Site::current()->id);
        if ( ! isset($plist->id))
            return ''; // Specified list not found

        $products = Model::fly('Model_Product')->find_all_by(
            array(
                'plist' => $plist,
                'active' => 1,
            ),
            array(
                'with_sections' => TRUE,
                'with_image' => 3
            )
        );

        // sections tree
        $brands     = Model::fly('Model_Section')->find_all_active_cached(1);
        $categories = Model::fly('Model_Section')->find_all_active_cached(2);
        
        $view = new View('frontend/plist');

        $view->cols = 5;

        $view->brands     = $brands;
        $view->categories = $categories;

        $view->plist = $plist;
        $view->products = $products;
        
        return $view->render();
    }
}