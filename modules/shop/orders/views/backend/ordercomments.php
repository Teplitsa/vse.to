<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$create_url = URL::to('backend/orders/comments', array('action'=>'create', 'order_id' => $order->id), TRUE);
$update_url = URL::to('backend/orders/comments', array('action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/orders/comments', array('action'=>'delete', 'id' => '${id}'), TRUE);
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Добавить комментарий</a>
</div>

<table class="ordercomments">
<?php
foreach ($ordercomments as $comment)
:
    $_update_url = str_replace('${id}', $comment->id, $update_url);
    $_delete_url = str_replace('${id}', $comment->id, $delete_url);
?>
    <tr>
        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать заказ', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить заказ', 'controls/delete.gif', 'Удалить'); ?>
        </td>
        <td class="ordercomment">
            <div>
                <span class="date">[<?php echo date('Y-m-d H:i:s', $comment->created_at); ?>]</span>
                <?php echo $comment->user_name; ?>
                <?php
                if ($comment->notify_client)
                {
                    echo '<span class="notify_client">с оповещением клиента</span>';
                }
                ?>
            </div>
            <div>
                <?php echo HTML::chars($comment->text); ?>
            </div>
        </td>
    </tr>
<?php
endforeach;
?>
</table>