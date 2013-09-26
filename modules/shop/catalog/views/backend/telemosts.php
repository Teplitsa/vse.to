<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$create_url = URL::to('backend/products/telemosts', array('action'=>'create', 'product_id' => $product->id), TRUE);
$update_url = URL::to('backend/products/telemosts', array('action'=>'update', 'id' => '${id}'), TRUE);
$choose_url = URL::to('backend/products/telemosts', array('action'=>'choose', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/products/telemosts', array('action'=>'delete', 'id' => '${id}'), TRUE);
?>

<div class="caption">Заявки на телемосты</div>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Добавить заявку</a>
</div>

<div class="images">
<?php
foreach ($app_telemosts as $telemost)
:
    $_choose_url = str_replace('${id}', $telemost->id, $choose_url);    
    $_update_url = str_replace('${id}', $telemost->id, $update_url);
    $_delete_url = str_replace('${id}', $telemost->id, $delete_url);
?>
    <div class="image">
        <div class="ctl">
            <?php echo View_Helper_Admin::image_control($_choose_url, 'Принять заявку на телемост', 'controls/link.gif', 'Принять'); ?>            
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать заявку на телемост', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить заявку на телемост', 'controls/delete.gif', 'Удалить'); ?>
        </div>
        <div class="img">
            <table><tr>
                <td>Площадка:<td><?php echo $telemost->place->town_name?>: <?php echo $telemost->place->name ?>
                <tr><td>Адрес:<td><?php echo $telemost->place->address; ?>
                <tr><td>Организатор<td><?php echo $telemost->user->organizer->name; ?>
                <tr><td>Координатор<td><?php echo $telemost->user->name; ?>
            </table>
        </div>
    </div>
<?php
endforeach;
?>
</div>

<div class="caption">Телемосты</div>


<div class="images">
<?php
foreach ($telemosts as $telemost)
:
    $_update_url = str_replace('${id}', $telemost->id, $update_url);
    $_delete_url = str_replace('${id}', $telemost->id, $delete_url);
?>
    <div class="image">
        <div class="ctl">
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать телемост', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить телемост', 'controls/delete.gif', 'Удалить'); ?>
        </div>
        <div class="img">
            <table><tr>
                <td>Площадка:<td><?php echo $telemost->place->town_name?>: <?php echo $telemost->place->name ?>
                <tr><td>Адрес:<td><?php echo $telemost->place->address; ?>
                <tr><td>Организатор<td><?php echo $telemost->user->organizer->name; ?>
                <tr><td>Координатор<td><?php echo $telemost->user->name; ?>        
            </table>
            <?php echo Widget::render_widget('goes','goes',  $telemost); ?>
        </div>
    </div>
<?php
endforeach;
?>
</div>