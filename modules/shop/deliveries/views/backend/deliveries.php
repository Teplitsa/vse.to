<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$create_url = URL::to('backend/deliveries', array('action'=>'create'), TRUE);
$update_url = URL::to('backend/deliveries', array('controller' => 'delivery_${module}', 'action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/deliveries', array('controller' => 'delivery_${module}', 'action'=>'delete', 'id' => '${id}'), TRUE);
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Добавить способ доставки</a>
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
foreach ($deliveries as $delivery)
:
    $_update_url = str_replace(array('${module}', '${id}'), array($delivery->module, $delivery->id), $update_url);
    $_delete_url = str_replace(array('${module}', '${id}'), array($delivery->module, $delivery->id), $delete_url);
?>
    <tr>
        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить способ доставки', 'controls/delete.gif', 'Удалить'); ?>
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать способ доставки', 'controls/edit.gif', 'Редактировать'); ?>
        </td>

        <?php
        foreach (array_keys($columns) as $field):
        ?>
            <td>
            <?php
            if (isset($delivery->$field) && trim($delivery->$field) !== '') {
                echo HTML::chars($delivery->$field);
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
endforeach; //foreach ($deliveries as $delivery)
?>
</table>