<?php defined('SYSPATH') or die('No direct script access.');

class Breadcrumbs
{
    /**
     * Append a custom breadcrumb
     *
     * @param array $breadcrumb
     * @param Request $request
     */
    public static function append(array $breadcrumb, Request $request = NULL)
    {
        if ($request === NULL)
        {
            $request = Request::current();
        }
        
        $append = $request->get_value('breadcrumbs_append');
        if ($append === NULL)
        {
            $append = array();
        }

        $append[] = $breadcrumb;

        $request->set_value('breadcrumbs_append', $append);
    }

    /**
     * Get all breadcrumbs
     *
     * @param Request $request
     */
    public static function get_all(Request $request = NULL)
    {
        if ($request === NULL)
        {
            $request = Request::current();
        }
        
        $breadcrumbs = array();

        // Generate breadcrumbs from path to current node
        if (Model_Node::current()->id !== NULL)
        {
            foreach (Model_Node::current()->path as $node)
            {
                $breadcrumbs[] = array(
                    'id'      => $node->id,
                    'caption' => $node->caption,
                    'uri'     => $node->frontend_uri
                );
            }
        }
       
        $append = $request->get_value('breadcrumbs_append');
        if ( ! empty($append))
        {
            // Add custom breadcrumbs
            $breadcrumbs = array_merge($breadcrumbs, $append);
        }
        return $breadcrumbs;
    }
}

