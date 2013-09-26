<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$create_url = URL::to('backend/orders/statuses', array('action'=>'create'), TRUE);
$update_url = URL::to('backend/orders/statuses', array('action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/orders/statuses', array('action'=>'delete', 'id' => '${id}'), TRUE);
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Добавить статус</a>
</div>

<table class="table">
    <tr>
        <?php
        $columns = array(
            'caption' => 'Статус'
        );

        echo View_Helper_Admin::table_header($columns, 'orders_storder', 'orders_stdesc');
        ?>

        <th></th>
    </tr>

<?php
foreach ($statuses as $status)
:
    $_update_url = str_replace('${id}', $status->id, $update_url);
    $_delete_url = str_replace('${id}', $status->id, $delete_url);
?>
    <tr>
        <?php
        foreach (array_keys($columns) as $field):
        ?>
            <td>
            <?php
            if (isset($status->$field) && trim($status->$field) !== '') {
                echo HTML::chars($status->$field);
            } else {
                echo '&nbsp';
            }
            ?>
            </td>

        <?php
        endforeach;
        ?>
            
        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать статус заказа', 'controls/edit.gif', 'Редактировать'); ?>
            <?php
            if ( ! $status->system)
            {
                echo View_Helper_Admin::image_control($_delete_url, 'Удалить статус заказа', 'controls/delete.gif', 'Удалить');
            }
            else
            {
                echo View_Helper_Admin::image('controls/delete_disabled.gif');
            }
            ?>
        </td>
    </tr>
<?php
endforeach; //foreach ($statuses as $status)
?>
</table>