<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Helper for rendering pages
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Pagination {

    protected $_count;
    protected $_per_page;
    protected $_page_param;
    protected $_at_once     = 7;

    public $offset=NULL;
    public $limit=NULL;

    protected $_page;
    protected $_pages_count;

    protected $_r;
    protected $_l;

    protected $_ignored_params = array();

    public function  __construct($count = 0, $per_page = 10, $page_param = 'page', $at_once = 7, $page = NULL)
    {
        $this->_count       = (int) $count;
        $this->_per_page    = (int) $per_page;
        $this->_page_param  = (string) $page_param;
        $this->_at_once     = (int) $at_once;

        $this->_page = $page;

        $this->calculate();
    }

    /**
     * Set/get ignored params for pagination
     *
     * @param  array $ignored_params
     * @return array
     */
    public function ignored_params(array $ignored_params = NULL)
    {
        if ($ignored_params !== NULL)
        {
            $this->_ignored_params = $ignored_params;
        }
        return $this->_ignored_params;
    }

    public function calculate()
    {
        if ($this->_page === NULL)
        {
            $page = Request::instance()->param($this->_page_param);
        }
        else
        {
            $page = $this->_page;
        }

        if ($page === 'all')
        {
            $this->_per_page = 0;
            $page = 0;
        }
        else
        {
            $page = (int) $page;
        }
        
        if ($this->_count > 0 && $this->_per_page > 0)
        {
            $pages_count = ceil($this->_count*$page / $this->_per_page);
            
            /*if ($page >= $pages_count)
            {
                $page = $pages_count - 1;
            }*/
        }
        else
        {
            $pages_count = 0;
        }

        $this->_page = $page;

        $this->_pages_count = $pages_count;

        if ($this->offset === NULL) $this->offset = $this->_page * $this->_per_page;
        if ($this->limit === NULL)  $this->limit = $this->_per_page;

		if ($this->_pages_count < 2)
        {
            return;
        }

		if ($this->_pages_count <= $this->_at_once)
        {
            $l= 0;
            $r= $this->_pages_count - 1;
        }
		else
        {
			$l= $this->_page - floor($this->_at_once/2);
			$r= $l + $this->_at_once-1;

			if ($l <= 0)
            {
                $l= 0;
                $r= $this->_at_once - 1;
            }

			if ($r >= $this->_pages_count - 1)
            {
                $r= $this->_pages_count - 1;
                $l= $this->_pages_count - $this->_at_once;
            }
		}

        $this->_l = $l;
        $this->_r = $r;
    }

    public function render($file = 'pagination')
    {
        $view = new View($file);

        $view->page_param = $this->_page_param;
        $view->page       = $this->_page;
        $view->pages_count = $this->_pages_count;

        $view->l = $this->_l;
        $view->r = $this->_r;

        $view->rewind_to_first = ($this->_l > 0);
        $view->rewind_to_last  = ($this->_r < $this->_pages_count - 1);

        $view->ignored_params = $this->_ignored_params;

        $from = $this->_page * $this->_per_page + 1;
        $to   = $from + $this->_per_page - 1;
        if ($to > $this->_count)
        {
            $to = $this->_count;
        }
        $view->count = $this->_count;
        $view->from  = $from;
        $view->to    = $to;

		return $view->render();
    }

    public function __toString()
    {
        return $this->render();
    }
}