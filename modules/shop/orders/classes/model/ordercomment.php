<?php defined('SYSPATH') or die('No direct script access.');

class Model_OrderComment extends Model
{
    /**
     * Back up properties before changing (for logging)
     */
    public $backup = TRUE;
    
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

        $this->log_changes($this, $this->previous());
    }

    /**
     * Delete ordercomment
     */
    public function delete()
    {
        $id = $this->id;

        //@FIXME: It's more correct to log AFTER actual deletion, but after deletion we have all model properties reset
        $this->log_delete($this);

        parent::delete();
    }

    /**
     * Log order comment changes
     *
     * @param Model_OrderComment $new_ordercomment
     * @param Model_OrderComment $old_ordercomment
     */
    public function log_changes(Model_OrderComment $new_ordercomment, Model_OrderComment $old_ordercomment)
    {
        $text = '';

        $created = ! isset($old_ordercomment->id);

        $has_changes = FALSE;

        if ($created)
        {
            $text .= '<strong>Добавлен комментарий к заказу № {{id-' . $new_ordercomment->order_id . "}}</strong>\n";
        }
        else
        {
            $text .= '<strong>Изменён комментарий к заказу № {{id-' . $new_ordercomment->order_id . "}}</strong>\n";
        }

        // ----- text
        if ($created)
        {
            $text .= Model_History::changes_text('Текст', $new_ordercomment->text);
        }
        elseif ($old_ordercomment->text != $new_ordercomment->text)
        {
            $text .= Model_History::changes_text('Текст', $new_ordercomment->text, $old_ordercomment->text);
            $has_changes = TRUE;
        }

        // ----- notify_client
        if ($created)
        {
            $text .= Model_History::changes_text('Оповещать клиента', $new_ordercomment->notify_client ? 'да' : 'нет');
        }
        elseif ($old_ordercomment->notify_client != $new_ordercomment->notify_client)
        {
            $text .= Model_History::changes_text('Оповещать клиента',
                $new_ordercomment->notify_client ? 'да' : 'нет',
                $old_ordercomment->notify_client ? 'да' : 'нет'
            );
            $has_changes = TRUE;
        }

        if ($created || $has_changes)
        {
            // Save text in history
            $history = new Model_History();
            $history->text = $text;
            $history->item_id = $new_ordercomment->order_id;
            $history->item_type = 'order';
            $history->save();
        }
    }

    /**
     * Log the deletion of ordercomment
     *
     * @param Model_OrderComment $ordercomment
     */
    public function log_delete(Model_OrderComment $ordercomment)
    {
        $text = 'Удалён комментарий "' . $ordercomment->text . '" к заказу № {{id-' . $ordercomment->order_id . '}}';

        // Save text in history
        $history = new Model_History();
        $history->text = $text;
        $history->item_id = $ordercomment->order_id;
        $history->item_type = 'order';
        $history->save();
    }

}