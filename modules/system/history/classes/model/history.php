<?php defined('SYSPATH') or die('No direct script access.');

class Model_History extends Model
{
    /**
     * Generate value change text
     *
     * @param  string $label
     * @param  mixed $new_value
     * @param  mixed $old_value
     * @return string
     */
    public static function changes_text($label, $new_value, $old_value = NULL)
    {
        return $label . ': <em>'  . HTML::chars($old_value) . '</em> &raquo; <em>' . HTML::chars($new_value) . '</em>' . "\n";
    }

    /**
     * @return integer
     */
    public function default_site_id()
    {
        return Model_Site::current()->id;
    }

    /**
     * @return integer
     */
    public function default_user_id()
    {
        return Auth::instance()->get_user()->id;
    }

    /**
     * @return string
     */
    public function default_user_name()
    {
        return Auth::instance()->get_user()->name;
    }

    /**
     * Get history entry text ready for output
     */
    public function get_html()
    {
        $text = $this->text;

        // Replace the numbers of existing orders with links to this orders
        if (preg_match_all('/{{id-(\d+)}}/i', $text, $matches, PREG_SET_ORDER))
        {
            $order = Model::fly('Model_Order');
            
            foreach ($matches as $match)
            {
                $order_id = (int) $match[1];
                if ($order->exists_by_id($order_id))
                {
                    $url = URL::to('backend/orders', array('action' => 'update', 'id' => $order_id), TRUE);
                    $text = str_replace($match[0], '<a href="' . $url . '">' . $order_id . '</a>', $text);
                }
                else
                {
                    $text = str_replace($match[0], $order_id, $text);
                }
            }
        }

        return nl2br($text);
    }
}