<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$create_url = URL::to('backend/menus', array('action'=>'create'), TRUE);
$update_url = URL::to('backend/menus', array('action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/menus', array('action'=>'delete', 'id' => '${id}'), TRUE);
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Создать меню</a>
</div>

<?php
if ( ! count($menus))
    // No menus
    return;
?>

<table class="table">
    <tr class="header">
        <th>&nbsp;&nbsp;&nbsp;</th>

        <?php
        $columns = array(
            'name'    => 'Имя',
            'caption' => 'Название'
        );

        echo View_Helper_Admin::table_header($columns, 'menus_order', 'menus_desc');
        ?>
    </tr>

<?php
foreach ($menus as $menu)
:
    $_delete_url = str_replace('${id}', $menu->id, $delete_url);
    $_update_url = str_replace('${id}', $menu->id, $update_url);
?>
    <tr>
        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить меню', 'controls/delete.gif', 'Удалить'); ?>
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать меню', 'controls/edit.gif', 'Редактировать'); ?>
        </td>

        <?php
        foreach (array_keys($columns) as $field):
        ?>
            <td>
            <?php
            if (isset($menu->$field) && trim($menu->$field) !== '') {
                echo HTML::chars($menu->$field);
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
endforeach; //foreach ($menus as $menu)
?>
</table>