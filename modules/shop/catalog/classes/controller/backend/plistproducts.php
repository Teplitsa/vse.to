<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_PListProducts extends Controller_BackendCRUD
{
    /**
     * Configure actions
     * @var array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_PListProduct';
        //$this->_form  = 'Form_Backend_PList';

        return array(
            'create' => array(
                'view_caption' => 'Добавление товара в список'
            ),
            'delete' => array(
                'view_caption' => 'Удаление товара из списка',
                'message' => 'Удалить товар из списка?'
            ),
            'multi_delete' => array(
                'view_caption' => 'Удаление товаров из списка',
                'message' => 'Удалить выбранные товары из списка?'
            )
        );
    }


    /**
     * Create layout (proxy to catalog controller)
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        return $this->request->get_controller('catalog')->prepare_layout($layout_script);
    }

    /**
     * Render layout (proxy to catalog controller)
     *
     * @param  View|string $content
     * @param  string $layout_script
     * @return string
     */
    public function render_layout($content, $layout_script = NULL)
    {
        return $this->request->get_controller('catalog')->render_layout($content, $layout_script);
    }

    /**
     * Add product to list by product id
     */
    public function action_add()
    {
        $plist_id = (int) $this->request->param('plist_id');

        if (Request::$method != 'POST' || empty($_POST['ids']) || ! is_array($_POST['ids']))
        {
            // No products were selected - silently redirect back
            $this->request->redirect(URL::uri_back());
        }


        $plist = new Model_PList();
        $plist->find($plist_id);
        if ($plist->id === NULL)
        {
            $this->_action_error('Указанный список товаров не найден!');
            return;
        }

        $plistproduct = new Model_PListProduct();

        foreach ($_POST['ids'] as $product_id)
        {
            $product_id = (int) $product_id;
            if (Model::fly('Model_Product')->exists_by_id($product_id))
            {
                // Add product only if it's not already in list
                if ( ! $plistproduct->exists_by_plist_id_and_product_id($plist_id, $product_id))
                {
                    $plistproduct->init();
                    $plistproduct->plist_id    = $plist_id;
                    $plistproduct->product_id = $product_id;
                    $plistproduct->save();
                }
            }
        }

        FlashMessages::add('Товары добавлены успешно');
        $this->request->redirect(URL::uri_back());
    }

    /**
     * Render list of products in list
     *
     * @param Model_PList $plist
     */
    public function widget_plistproducts(Model_PList $plist)
    {
        $site_id = Model_Site::current()->id;
        if ($site_id === NULL)
        {
            return $this->_widget_error('Выберите магазин!');
        }

        $plistproduct = new Model_PlistProduct();

        $per_page = 20;
        $count = $plistproduct->count_by_plist_id((int) $plist->id);
        $pagination = new Paginator($count, $per_page);

        $order_by = $this->request->param('cat_lporder', 'position');
        $desc = (bool) $this->request->param('cat_lpdesc', '0');

        $plistproducts = $plistproduct->find_all_by_plist_id((int) $plist->id,
            array(
                'offset'   => $pagination->offset,
                'limit'    => $pagination->limit,
                'order_by' => $order_by,
                'desc'     => $desc
            )
        );

        // Set up view
        $view = new View('backend/plistproducts');

        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->plist = $plist;
        $view->plistproducts = $plistproducts;

        $view->pagination = $pagination;

        return $view->render();
    }
}