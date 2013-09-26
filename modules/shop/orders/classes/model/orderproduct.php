<?php defined('SYSPATH') or die('No direct script access.');

class Model_OrderProduct extends Model
{
    /**
     * Back up properties before changing (for logging)
     */
    public $backup_properties = TRUE;

    
    /**
     * Default product price
     * 
     * @return float
     */
    public function default_price()
    {
        return 0.0;
    }

    /**
     * Default product quantity
     *
     * @return integer
     */
    public function default_quantity()
    {
        return 1;
    }

    /**
     * Default product weight
     *
     * @return float
     */
    public function default_weight()
    {
        return 0.0;
    }

    /**
     * Set orderproduct values from actual product in catalog
     * 
     * @param Model_Product $product
     */
    public function from_product(Model_Product $product)
    {
        $this->caption = $product->caption;
        $this->price   = $product->price;
    }

    /**
     * Get order this product belongs to
     * 
     * @return Model_Order
     */
    public function get_order()
    {
        if ( ! isset($this->_properties['order']))
        {
            $order = new Model_Order();
            $order->find((int) $this->order_id);

            $this->_properties['order'] = $order;
        }
        return $this->_properties['order'];
    }
    
    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------
    /**
     * Save model and log changes
     *
     * @param boolean $force_create
     */
    public function save($force_create = FALSE)
    {
        parent::save($force_create);

        // Recalculate order summaries
        Model_Order::recalculate_summaries($this->order_id);

        $old_orderproduct = new Model_OrderProduct($this->_old_properties);

        $this->log_changes($this, $old_orderproduct);
    }

    /**
     * Delete orderproduct
     */
    public function delete()
    {
        $id = $this->id;
        
        //@FIXME: It's more correct to log AFTER actual deletion, but after deletion we have all model properties reset
        $this->log_delete($this);

        parent::delete();

        // Recalculate order summaries
        Model_Order::recalculate_summaries($id);
    }

    /**
     * Log order product changes
     *
     * @param Model_OrderProduct $new_orderproduct
     * @param Model_OrderProduct $old_orderproduct
     */
    public function log_changes(Model_OrderProduct $new_orderproduct, Model_OrderProduct $old_orderproduct)
    {
        $text = '';

        $created = ! isset($old_orderproduct->id);

        $has_changes = FALSE;

        if ($created)
        {
            $text .= '<strong>Добавлен товар в заказ № {{id-' . $new_orderproduct->order_id . "}}</strong>\n";
        }
        else
        {
            $text .= '<strong>Изменён товар "' . HTML::chars($new_orderproduct->caption) . '" в заказе № {{id-' . $new_orderproduct->order_id . "}}</strong>\n";
        }

        // ----- caption
        if ($created)
        {
            $text .= Model_History::changes_text('Название', $new_orderproduct->caption);
        }
        elseif ($old_orderproduct->caption != $new_orderproduct->caption)
        {
            $text .= Model_History::changes_text('Название', $new_orderproduct->caption, $old_orderproduct->caption);
            $has_changes = TRUE;
        }

        // ----- price
        if ($created)
        {
            $text .= Model_History::changes_text('Цена', $new_orderproduct->price);
        }
        elseif ($old_orderproduct->price != $new_orderproduct->price)
        {
            $text .= Model_History::changes_text('Цена', $new_orderproduct->price, $old_orderproduct->price);
            $has_changes = TRUE;
        }

        // ----- weight
        if ($created)
        {
            $text .= Model_History::changes_text('Вес', $new_orderproduct->weight);
        }
        elseif ($old_orderproduct->weight != $new_orderproduct->weight)
        {
            $text .= Model_History::changes_text('Вес', $new_orderproduct->weight, $old_orderproduct->weight);
            $has_changes = TRUE;
        }

        // ----- quantity
        if ($created)
        {
            $text .= Model_History::changes_text('Количество', $new_orderproduct->quantity);
        }
        elseif ($old_orderproduct->quantity != $new_orderproduct->quantity)
        {
            $text .= Model_History::changes_text('Количество', $new_orderproduct->quantity, $old_orderproduct->quantity);
            $has_changes = TRUE;
        }

        if ($created || $has_changes)
        {
            // Save text in history
            $history = new Model_History();
            $history->text = $text;
            $history->item_id = $new_orderproduct->order_id;
            $history->item_type = 'order';
            $history->save();
        }
    }

    /**
     * Log the deletion of orderproduct
     *
     * @param Model_OrderProduct $orderproduct
     */
    public function log_delete(Model_OrderProduct $orderproduct)
    {
        $text = 'Удалён товар ' . $orderproduct->caption . ' из заказа № {{id-' . $orderproduct->order_id . '}}';

        // Save text in history
        $history = new Model_History();
        $history->text = $text;
        $history->item_id = $orderproduct->order_id;
        $history->item_type = 'order';
        $history->save();
    }

}