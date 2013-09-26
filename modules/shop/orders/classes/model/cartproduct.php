<?php defined('SYSPATH') or die('No direct script access.');

class Model_CartProduct extends Model
{
    /**
     * Log model changes
     * @var boolean
     */
    public $backup = TRUE;

    /**
     * Default product price
     *
     * @return float
     */
    public function default_price()
    {
        return new Money();
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
     * Set cartproduct price
     * 
     * @param Money $price
     */
    public function set_price(Money $price)
    {
        $this->_properties['price'] = clone $price;
    }

    /**
     * Get cartproduct price
     * 
     * @return Money
     */
    public function get_price()
    {
        if ( ! isset($this->_properties['price']))
        {
            $this->_properties['price'] = $this->default_price();
        }
        return clone $this->_properties['price'];
    }
    
    /**
     * Create product in cart from actual catalog product
     * 
     * @param Model_Product $product
     */
    public function from_product(Model_Product $product)
    {
       $this->product_id = $product->id;
       $this->marking    = $product->marking;
       $this->caption    = $product->caption;
       $this->price      = clone $product->price;

       $this->product = $product->properties();
    }

    /**
     * Return fly instance of the corresponding catalog product
     *
     * @return Model_Product
     */
    public function get_product()
    {
        $product = Model::fly('Model_Product');
        if ( ! empty($this->_properties['product']) && is_array($this->_properties['product']))
        {
            $product->init($this->_properties['product']);
        }
        return $product;
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
            $order->find((int) $this->cart_id);

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

        // Recalculate order summaries by saving it
        $this->order->save(FALSE, FALSE);

        $this->log_changes($this, $this->previous());
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
     * @param Model_CartProduct $new_orderproduct
     * @param Model_CartProduct $old_orderproduct
     */
    public function log_changes(Model_CartProduct $new_orderproduct, Model_CartProduct $old_orderproduct)
    {
        $fields = array(
            array('name' => 'caption', 'caption' => 'Статус'),
            array('name' => 'quantity', 'caption' => 'Количество')
        );
        
        $text = '';

        $created = ! isset($old_orderproduct->id);

        $has_changes = FALSE;

        if ($created)
        {
            $text .= '<strong>Добавлен товар в заказ № {{id-' . $new_orderproduct->cart_id . "}}</strong>\n";
        }
        else
        {
            $text .= '<strong>Изменён товар "' . HTML::chars($new_orderproduct->caption) . '" в заказе № {{id-' . $new_orderproduct->cart_id . "}}</strong>\n";
        }

        // Log field changes
        foreach ($fields as $field)
        {
            $name      = $field['name'];
            $displayed = isset($field['displayed']) ? $field['displayed'] : $name;
            $caption   = $field['caption'];

            if ($created)
            {
                $text .= Model_History::changes_text($caption, $new_orderproduct->$displayed);
            }
            elseif ($old_orderproduct->$name != $new_orderproduct->$name)
            {
                $text .= Model_History::changes_text($caption, $new_orderproduct->$displayed, $old_orderproduct->$displayed);
                $has_changes = TRUE;
            }
        }

        // ----- price
        if ($created)
        {
            $text .= Model_History::changes_text('Цена', $new_orderproduct->price);
        }
        elseif ($old_orderproduct->price->amount != $new_orderproduct->price->amount)
        {
            $text .= Model_History::changes_text('Цена', $new_orderproduct->price, $old_orderproduct->price);
            $has_changes = TRUE;
        }

        if ($created || $has_changes)
        {
            // Save text in history
            $history = new Model_History();
            $history->text = $text;
            $history->item_id = $new_orderproduct->cart_id;
            $history->item_type = 'order';
            $history->save();
        }
    }

    /**
     * Log the deletion of orderproduct
     *
     * @param Model_CartProduct $orderproduct
     */
    public function log_delete(Model_CartProduct $orderproduct)
    {
        $text = 'Удалён товар ' . $orderproduct->caption . ' из заказа № {{id-' . $orderproduct->cart_id . '}}';

        // Save text in history
        $history = new Model_History();
        $history->text = $text;
        $history->item_id = $orderproduct->cart_id;
        $history->item_type = 'order';
        $history->save();
    }
}