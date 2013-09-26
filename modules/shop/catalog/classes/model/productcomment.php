<?php defined('SYSPATH') or die('No direct script access.');

class Model_ProductComment extends Model
{
    /**
     * Back up properties before changing (for logging)
     */
    public $backup = TRUE;
    
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
     * Delete productcomment
     */
    public function delete()
    {
        $id = $this->id;

        //@FIXME: It's more correct to log AFTER actual deletion, but after deletion we have all model properties reset
        $this->log_delete($this);

        parent::delete();
    }

    /**
     * Log product comment changes
     *
     * @param Model_ProductComment $new_productcomment
     * @param Model_ProductComment $old_productcomment
     */
    public function log_changes(Model_ProductComment $new_productcomment, Model_ProductComment $old_productcomment)
    {
        $text = '';

        $created = ! isset($old_productcomment->id);

        $has_changes = FALSE;

        if ($created)
        {
            $text .= '<strong>Добавлен комментарий к событию № {{id-' . $new_productcomment->product_id . "}}</strong>\n";
        }
        else
        {
            $text .= '<strong>Изменён комментарий к событию № {{id-' . $new_productcomment->product_id . "}}</strong>\n";
        }

        // ----- text
        if ($created)
        {
            $text .= Model_History::changes_text('Текст', $new_productcomment->text);
        }
        elseif ($old_productcomment->text != $new_productcomment->text)
        {
            $text .= Model_History::changes_text('Текст', $new_productcomment->text, $old_productcomment->text);
            $has_changes = TRUE;
        }

        // ----- notify_client
        if ($created)
        {
            $text .= Model_History::changes_text('Оповещать представителя', $new_productcomment->notify_client ? 'да' : 'нет');
        }
        elseif ($old_productcomment->notify_client != $new_productcomment->notify_client)
        {
            $text .= Model_History::changes_text('Оповещать представителя',
                $new_productcomment->notify_client ? 'да' : 'нет',
                $old_productcomment->notify_client ? 'да' : 'нет'
            );
            $has_changes = TRUE;
        }

        if ($created || $has_changes)
        {
            // Save text in history
            $history = new Model_History();
            $history->text = $text;
            $history->item_id = $new_productcomment->product_id;
            $history->item_type = 'product';
            $history->save();
        }
    }

    /**
     * Log the deletion of productcomment
     *
     * @param Model_ProductComment $productcomment
     */
    public function log_delete(Model_ProductComment $productcomment)
    {
        $text = 'Удалён комментарий "' . $productcomment->text . '" к событию № {{id-' . $productcomment->product_id . '}}';

        // Save text in history
        $history = new Model_History();
        $history->text = $text;
        $history->item_id = $productcomment->product_id;
        $history->item_type = 'product';
        $history->save();
    }

}