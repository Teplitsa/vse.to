<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_News extends Controller_BackendCRUD
{
    /**
     * Setup actions
     *
     * @return array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Newsitem';
        $this->_form  = 'Form_Backend_Newsitem';

        return array(
            'create' => array(
                'view_caption' => 'Создание новости'
            ),
            'update' => array(
                'view_caption' => 'Редактирование новости ":caption"'
            ),
            'delete' => array(
                'view_caption' => 'Удаление новость',
                'message' => 'Удалить новость ":caption"?'
            )
        );
    }

    /**
     * @return boolean
     */
    public function before()
    {
        if ( ! parent::before())
        {
            return FALSE;
        }

        if ($this->request->is_forwarded())
            // Request was forwarded in parent::before()
            return TRUE;

        // Check that there is a site selected
        if (Model_Site::current()->id === NULL)
        {
            $this->_action_error('Выберите сайт для работы с новостями!');
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Create layout and link module stylesheets
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        $layout = parent::prepare_layout($layout_script);
        return $layout;
    }

    /**
     * Default action
     */
	public function action_index()
	{
        $view = new View('backend/workspace');

        $view->caption = 'Список новостей';
        $view->content = $this->widget_news();

        $this->request->response = $this->render_layout($view);
	}

    /**
     * Renders news list
     *
     * @return string
     */
    public function widget_news()
    {
        $newsitem = new Model_Newsitem();

        $order_by = $this->request->param('news_order', 'date');
        $desc     = (bool) $this->request->param('news_desc', '1');

        // Select all news for current site
        $site_id = Model_Site::current()->id;

        $per_page = 20;
        $count = $newsitem->count_by_site_id($site_id);
        $pagination = new Pagination($count, $per_page);
        
        $news = $newsitem->find_all_by_site_id($site_id, array(
            'offset'   => $pagination->offset,
            'limit'    => $pagination->limit,
            'order_by' => $order_by,
            'desc'     => $desc,

            'columns'  => array('id', 'date', 'caption')
        ));

        // Set up view
        $view = new View('backend/news');

        $view->order_by = $order_by;
        $view->desc     = $desc;

        $view->news     = $news;

        $view->pagination = $pagination;
        
        return $view;
    }
}
