<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$create_url = URL::to('backend/orders', array('action'=>'create'), TRUE);
$update_url = URL::to('backend/orders', array('action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/orders', array('action'=>'delete', 'id' => '${id}'), TRUE);

$multi_action_uri = URL::uri_to('backend/orders', array('action'=>'multi'), TRUE);
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Новый заказ</a>
</div>

<?php
if ( ! count($orders))
    // No orders
    return;
?>

<?php echo View_Helper_Admin::multi_action_form_open($multi_action_uri); ?>
<table class="table">
    <tr class="header">
        <th><?php echo View_Helper_Admin::multi_action_select_all(); ?></th>

        <?php
        $columns = array(
            'id' => 'Номер',
            'date_created' => array('label' => 'Дата', 'sort_field' => 'created_at'),
            //'site_caption' => 'Магазин',
            'name' => 'Покупатель',
            //'delivery_caption' => 'Доставка',
            //'payment_caption' => 'Оплата',
            'status_caption' => 'Статус',
            'sum' => 'Сумма заказа'
        );

        echo View_Helper_Admin::table_header($columns, 'orders_order', 'orders_desc');
        ?>

        <th></th>
    </tr>

<?php
foreach ($orders as $order)
:
    $_update_url = str_replace('${id}', $order->id, $update_url);
    $_delete_url = str_replace('${id}', $order->id, $delete_url);
?>
    <tr class="<?php echo Text::alternate('odd', 'even'); ?>">
        <td class="multi_ctl">
            <?php echo View_Helper_Admin::multi_action_checkbox($order->id); ?>
        </td>
        <?php
        foreach (array_keys($columns) as $field):
        ?>
            <td>
            <?php
            if (isset($order->$field) && trim((string) $order->$field) !== '') {
                echo HTML::chars($order->$field);
            } else {
                echo '&nbsp';
            }
            ?>
            </td>

        <?php
        endforeach;
        ?>
        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать заказ', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить заказ', 'controls/delete.gif', 'Удалить'); ?>
        </td>
    </tr>
<?php
endforeach; //foreach ($orders as $order)
?>
</table>

<?php
if (isset($pagination))
{
    echo $pagination->render('backend/pagination');
}
?>

<?php
echo View_Helper_Admin::multi_actions(array(
    array('action' => 'multi_delete', 'label' => 'Удалить', 'class' => 'button_delete'),
    array('action' => 'merge', 'label' => 'Объединить', 'class' => 'button_merge')
));
?>

<?php echo View_Helper_Admin::multi_action_form_close(); ?>