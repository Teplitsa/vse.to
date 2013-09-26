<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
// Set up urls
$create_url = URL::to('backend/courierzones', array('action'=>'create', 'delivery_id' => $delivery_id), TRUE);
$update_url = URL::to('backend/courierzones', array('action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/courierzones', array('action'=>'delete', 'id' => '${id}'), TRUE);

$up_url   = URL::to('backend/courierzones', array('action'=>'up', 'id' => '${id}'), TRUE);
$down_url = URL::to('backend/courierzones', array('action'=>'down', 'id' => '${id}'), TRUE);
if ( ! $desc)
{
    list($up_url, $down_url) = array($down_url, $up_url);
}

$multi_action_uri = URL::uri_to('backend/courierzones', array('action'=>'multi'), TRUE);
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Добавить зону доставки</a>
</div>

<?php
if ( ! count($zones))
    // No zones
    return;
?>

<?php echo View_Helper_Admin::multi_action_form_open($multi_action_uri); ?>
<table class="table">
    <tr class="header">
        <th>&nbsp;&nbsp;&nbsp;</th>

        <?php
            $columns = array(
                'name'  => 'Название',
                'price' => 'Стоимость',
            );
            echo View_Helper_Admin::table_header($columns);
        ?>
    </tr>

<?php
foreach ($zones as $zone)
:
    $_update_url = str_replace('${id}', $zone->id, $update_url);
    $_delete_url = str_replace('${id}', $zone->id, $delete_url);
    $_up_url     = str_replace('${id}', $zone->id, $up_url);
    $_down_url   = str_replace('${id}', $zone->id, $down_url);

?>
    <tr>        
        <td class="ctl">
            <?php echo View_Helper_Admin::multi_action_checkbox($zone->id); ?>
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить зону', 'controls/delete.gif', 'Удалить'); ?>
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать зону', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_up_url, 'Переместить вверх', 'controls/up.gif', 'Вверх'); ?>
            <?php echo View_Helper_Admin::image_control($_down_url, 'Переместить вниз', 'controls/down.gif', 'Вниз'); ?>
        </td>

        <?php
        foreach (array_keys($columns) as $field)
        {
            echo '<td>';
            if (isset($zone->$field) && trim($zone->$field) !== '')
            {
                echo HTML::chars($zone->$field);
            } 
            else
            {
                echo '&nbsp';
            }
            echo '</td>';
        }
        ?>
    </tr>
<?php
endforeach; //foreach ($zones as $zone)
?>
</table>

<?php
echo View_Helper_Admin::multi_actions(array(
    array('action' => 'multi_delete', 'label' => 'Удалить', 'class' => 'button_delete')
));
?>

<?php echo View_Helper_Admin::multi_action_form_close(); ?>