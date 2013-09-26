<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_News extends Controller_Frontend
{
    /**
     * Render list of news
     * 
     * @return View
     */
    public function action_index()
    {
        $site_id = Model_Site::current()->id;
        
        $newsitem = new Model_Newsitem();

        $order_by = $this->request->param('news_order', 'date');
        $desc     = (bool) $this->request->param('news_desc', '1');

        // ----- Selected years for which there are news
        $years = $newsitem->find_all_years_by_site_id($site_id);

        $current_year = (int) $this->request->param('year', 0);       
        if ($current_year == 0 && count($years))
        {
            // Select the latest available year if not explicitly specified in url params
            $current_year = $years[0];
        }

        if (count($years) && ! in_array($current_year, $years))
        {
            // Invalid year was specified
            $this->_action_404();
            return;
        }

        // ----- Selected months for which there are news in selected year
        $months = $newsitem->find_all_months_by_year_and_site_id($current_year, $site_id);

        $current_month = (int) $this->request->param('month', 0);
        if ($current_month == 0 && count($months))
        {
            // Select the latest available month if not explicitly specified in url params
            $current_month = $months[count($months) - 1];
        }
        
        if (count($months) && ! in_array($current_month, $months))
        {
            // Invalid month was specified
            $this->_action_404();
            return;
        }

        // ----- List of news
        // Select all news for selected year and month
        $per_page = 1000;
        $count = $newsitem->count_by_site_id($site_id);
        $pagination = new Pagination($count, $per_page);

        $news = $newsitem->find_all_by_year_and_month_and_site_id($current_year, $current_month, $site_id, array(
            'offset'   => $pagination->offset,
            'limit'    => $pagination->limit,
            'order_by' => $order_by,
            'desc'     => $desc,
            
            'columns'  => array('id', 'date', 'caption', 'short_text')
        ));

        // Set up view
        $view = new View('frontend/news');

        $view->years         = $years;
        $view->months        = $months;
        $view->current_year  = $current_year;
        $view->current_month = $current_month;

        $view->news     = $news;        
        $view->pagination = $pagination;

        // 3-step view
        //$view2 = new View('content/news');
        //$view2->caption = Model_Node::current()->caption;
        //$view2->content = $view;

        $this->request->response = $this->render_layout($view);
    }

    /**
     * View the full text of the selected news item
     */
    public function action_view()
    {
        $newsitem = new Model_Newsitem();
        $newsitem->find_by_id_and_site_id((int) $this->request->param('id'), (int) Model_Site::current()->id);

        if ( ! isset($newsitem->id))
        {
            $this->_action_404();
            return;
        }

        $this->request->get_controller('breadcrumbs')->append_breadcrumb(array(
            'caption' => $newsitem->caption
        ));

        // 3-step view
        $view = new View('content/text');
        $view->content = $newsitem->text;

        // Layout
        $layout = $this->prepare_layout();

        // Append title
        $layout->add_title($newsitem->caption);
        
        $layout->content = $view;
        $layout->date    = $newsitem->date;

        $this->request->response = $layout->render();
    }

    /**
     * Render list of recent news
     * 
     * @return View
     */
    public function widget_recent()
    {
        $site_id = Model_Site::current()->id;
        
        $newsitem = new Model_Newsitem();

        $order_by = $this->request->param('news_order', 'date');
        $desc     = (bool) $this->request->param('news_desc', '1');

        // ----- List of recent news
        $limit = 3;
        $news = $newsitem->find_all_by_site_id($site_id, array(
            'limit'    => $limit,
            'order_by' => $order_by,
            'desc'     => $desc,
            
            'columns'  => array('id', 'date', 'caption', 'short_text')
        ));

        // Set up view
        $view = new View('frontend/recent_news');
        $view->news     = $news;        
        return $view;
    }
}
