<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$create_url = URL::to('backend/orders/products', array('action'=>'create', 'order_id' => $order->id), TRUE);
$update_url = URL::to('backend/orders/products', array('action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/orders/products', array('action'=>'delete', 'id' => '${id}'), TRUE);
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Добавить товар в заказ</a>
</div>

<?php
if ( ! count($cartproducts))
    return;
?>

<table class="light_table">
    <tr class="table_header">
        <?php
        $columns = array(
            'marking'  => 'Артикул',
            'caption'  => 'Название',
            'price'    => 'Цена',
            'quantity' => 'Кол-во'
        );

        echo View_Helper_Admin::table_header($columns, 'orders_prorder', 'orders_prdesc');
        ?>
        
        <th></th>
    </tr>

<?php
foreach ($cartproducts as $cartproduct)
:
    $_update_url = str_replace('${id}', $cartproduct->id, $update_url);
    $_delete_url = str_replace('${id}', $cartproduct->id, $delete_url);
?>
    <tr class="<?php echo Text::alternate('odd', 'even'); ?>">
        <?php
        foreach (array_keys($columns) as $field):
        ?>
            <td>
            <?php
            if (isset($cartproduct->$field) && trim((string) $cartproduct->$field) !== '') {
                echo HTML::chars($cartproduct->$field);
            } else {
                echo '&nbsp';
            }
            ?>
            </td>

        <?php
        endforeach;
        ?>

        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать товар в заказе', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить товар из заказа', 'controls/delete.gif', 'Удалить'); ?>
        </td>
    </tr>
<?php
endforeach;
?>

    <tr class="table_footer">
        <td class="r" colspan="2">Итого:</td>
        <td colspan="3"><strong><?php echo $order->sum; ?></strong></td>
    </tr>
</table>