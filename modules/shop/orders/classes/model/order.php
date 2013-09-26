<?php defined('SYSPATH') or die('No direct script access.');

class Model_Order extends Model
{
    /**
     * Back up properties before changing (for logging)
     */
    public $backup = TRUE;


    /**
     * Default site id for order
     * 
     * @return integer
     */
    public function default_site_id()
    {
        return Model_Site::current()->id;
    }

    /**
     * Default status id for new orders
     * 
     * @return integer
     */
    public function default_status_id()
    {
        return Model_OrderStatus::STATUS_NEW;
    }

    /**
     * Get status caption
     *
     * @return string
     */
    public function get_status_caption()
    {
        return Model_OrderStatus::caption($this->status_id);
    }

    /**
     * Site caption
     *
     * @return string
     */
    public function get_site_caption()
    {
        return Model_Site::caption($this->site_id);
    }

    /**
     * Return the date when the order was created
     * @return string
     */
    public function get_date_created()
    {
        return date('Y-m-d', $this->created_at);
    }

    /**
     * Return the date and time when the order was created
     * @return string
     */
    public function get_datetime_created()
    {
        return date('Y-m-d H:i:s', $this->created_at);
    }

    /**
     * Get products for this order
     */
    public function get_products()
    {
        if ( ! isset($this->_properties['products']))
        {
            $this->_properties['products'] = Model::fly('Model_CartProduct')->find_all_by_cart_id((int) $this->id);
        }
        return $this->_properties['products'];
    }

    /**
     * Get comments for this order
     */
    public function get_comments()
    {
        return Model::fly('Model_OrderComment')->find_all_by_order_id((int) $this->id);
    }

    // -------------------------------------------------------------------------
    // Summaries
    // -------------------------------------------------------------------------
    /**
     * Calculate total price of all products in order
     *
     * @return Money
     */
    public function calculate_sum()
    {
        $sum = new Money();

        foreach ($this->products as $product)
        {
            $sum->add($product->price->mul($product->quantity));
        }

        return $sum;
    }

    /**
     * Get "bare" total price of all products in order (without any additional fees)
     *
     * @return Money
     */
    public function get_sum()
    {
        if ( ! isset($this->_properties['sum']))
        {
            $sum = $this->calculate_sum();
            $this->_properties['sum'] = $sum;
        }
        return $this->_properties['sum'];
    }

    /**
     * Get final total sum for the order
     * 
     * @return Money
     */
    public function get_total_sum()
    {
        return $this->sum;
    }

    /**
     * Get total quantity of all products in order
     *
     * @return integer
     */
    public function get_total_quantity()
    {
        $total_quantity = 0;
        foreach ($this->products as $cartproduct)
        {
            $total_quantity += $cartproduct->quantity;
        }
        return $total_quantity;
    }
    
    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------
    /**
     * Save model and log changes
     *
     * @param boolean $force_create
     * @param boolean $log_changes
     */
    public function save($force_create = FALSE, $log_changes = TRUE)
    {
        // Recalculate summaries, so they are stored in model properties and will be stored in db
        $this->sum = $this->calculate_sum();
        
        parent::save($force_create);

        // Save new products (used when order is submitted in frontend)
        foreach ($this->products as $cartproduct)
        {
            if ( ! isset($cartproduct->id))
            {
                $cartproduct->cart_id = $this->id;
                $cartproduct->save();
            }
        }

        if ($log_changes)
        {
            $this->log_changes($this, $this->previous());
        }
    }

    /**
     * Delete order
     */
    public function delete()
    {
        $id = $this->id;

        // Delete products from order
        Model::fly('Model_CartProduct')->delete_all_by_cart_id($this->id);
        
        //@TODO: delete comments for this order
        parent::delete();

        $this->log_delete($id);
    }

    /**
     * Send e-mail notification about created order
     */
    public function notify()
    {
        try {
            $settings = Model_Site::current()->settings;

            $email_to     = isset($settings['email']['to'])        ? $settings['email']['to']        : '';
            $email_from   = isset($settings['email']['from'])      ? $settings['email']['from']      : '';
            $email_sender = isset($settings['email']['sender'])    ? $settings['email']['sender']    : '';
            $signature    = isset($settings['email']['signature']) ? $settings['email']['signature'] : '';

            if ($email_sender != '')
            {
                $email_from = array($email_from => $email_sender);
            }

            if ($email_to != '' || $this->email != '')
            {
                // Init mailer
                SwiftMailer::init();
                $transport = Swift_MailTransport::newInstance();
                $mailer = Swift_Mailer::newInstance($transport);

                if ($email_to != '')
                {
                    // --- Send message to administrator

                    $message = Swift_Message::newInstance()
                        ->setSubject('Новый заказ с сайта ' . URL::base(FALSE, TRUE))
                        ->setFrom($email_from)
                        ->setTo($email_to);


                    // Message body
                    $twig = Twig::instance();

                    $template = $twig->loadTemplate('mail/order_admin');

                    $message->setBody($template->render(array(
                        'order'    => $this,
                        'products' => $this->products
                    )));
                    // Send message
                    $mailer->send($message);
                }

                if ($this->email != '')
                {
                    // --- Send message to client
                    var_dump($email_from);
                    die();
                    
                    $message = Swift_Message::newInstance()
                        ->setSubject('Ваш заказ в интернет магазине ' . URL::base(FALSE, TRUE))
                        ->setFrom($email_from)
                        ->setTo($this->email);

                    // Message body
                    $twig = Twig::instance();

                    $template = $twig->loadTemplate('mail/order_client');

                    $body = $template->render(array(
                        'order'    => $this,
                        'products' => $this->products
                    ));

                    if ($signature != '')
                    {
                        $body .= "\n\n\n" . $signature;
                    }

                    $message->setBody($body);

                    // Send message
                    $mailer->send($message);
                }
            }
        }
        catch (Exception $e)
        {
            if (Kohana::$environment !== Kohana::PRODUCTION)
            {
                throw $e;
            }
        }
    }

    //--------------------------------------------------------------------------
    // Log
    //--------------------------------------------------------------------------
    /**
     * Log order changes
     *
     * @param Model_Order $new_order
     * @param Model_Order $old_order
     */
    public function log_changes(Model_Order $new_order, Model_Order $old_order)
    {
        $fields = array(
            array('name' => 'status_id', 'displayed' => 'status_caption', 'caption' => 'Статус'),
            array('name' => 'name', 'caption' => 'Имя пользователя'),
            array('name' => 'phone', 'caption' => 'Телефон'),
            array('name' => 'email', 'caption' => 'E-Mail'),
            array('name' => 'address', 'caption' => 'Адрес доставки'),
            array('name' => 'comment', 'caption' => 'Комментарий к заказу'),
        );
        
        $text = '';

        $created = ! isset($old_order->id);
        
        $has_changes = FALSE;

        if ($created)
        {
            $text .= '<strong>Создан новый заказ № {{id-' . $new_order->id . "}}</strong>\n";
        }
        else
        {
            $text .= '<strong>Изменён заказ № {{id-' . $new_order->id . "}}</strong>\n";
        }

        // Log field changes
        foreach ($fields as $field)
        {
            $name      = $field['name'];
            $displayed = isset($field['displayed']) ? $field['displayed'] : $name;
            $caption   = $field['caption'];
            
            if ($created)
            {
                $text .= Model_History::changes_text($caption, $new_order->$displayed);
            }
            elseif ($old_order->$name != $new_order->$name)
            {
                $text .= Model_History::changes_text($caption, $new_order->$displayed, $old_order->$displayed);
                $has_changes = TRUE;
            }
        }

        if ($created || $has_changes)
        {
            // Save text in history
            $history = new Model_History();
            $history->text = $text;
            $history->item_id = $new_order->id;
            $history->item_type = 'order';
            $history->save();
        }
    }

    /**
     * Log the deletion of order
     * 
     * @param integer $order_id
     */
    public function log_delete($order_id)
    {
        $text = 'Удалён заказ № {{id-' . $order_id . '}}';

        // Save text in history
        $history = new Model_History();
        $history->text = $text;
        $history->item_id = $order_id;
        $history->item_type = 'order';
        $history->save();
    }
}