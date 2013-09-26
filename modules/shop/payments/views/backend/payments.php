<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$create_url = URL::to('backend/payments', array('action'=>'create'), TRUE);
$update_url = URL::to('backend/payments', array('controller' => 'payment_${module}', 'action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/payments', array('controller' => 'payment_${module}', 'action'=>'delete', 'id' => '${id}'), TRUE);
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Добавить способ оплаты</a>
</div>

<table class="table">
    <tr class="header">
        <th>&nbsp;&nbsp;&nbsp;</th>

        <?php
        $columns = array(
            'caption'         => 'Название',
            'module_caption'  => 'Модуль'
        );

        echo View_Helper_Admin::table_header($columns, 'orders_storder', 'orders_stdesc');
        ?>
    </tr>

<?php
foreach ($payments as $payment)
:
    $_update_url = str_replace(array('${module}', '${id}'), array($payment->module, $payment->id), $update_url);
    $_delete_url = str_replace(array('${module}', '${id}'), array($payment->module, $payment->id), $delete_url);
?>
    <tr>
        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить способ оплаты', 'controls/delete.gif', 'Удалить'); ?>
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать способ оплаты', 'controls/edit.gif', 'Редактировать'); ?>
        </td>

        <?php
        foreach (array_keys($columns) as $field):
        ?>
            <td>
            <?php
            if (isset($payment->$field) && trim($payment->$field) !== '') {
                echo HTML::chars($payment->$field);
            } else {
                echo '&nbsp';
            }
            ?>
            </td>

        <?php
        endforeach;
        ?>
    </tr>
<?php
endforeach;
?>
</table>