<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$create_url = URL::to('backend/sites', array('action'=>'create'), TRUE);
$update_url = URL::to('backend/sites', array('action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/sites', array('action'=>'delete', 'id' => '${id}'), TRUE);
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Новый магазин</a>
</div>

<?php
if ( ! count($sites))
    return;
?>

<table class="table">
    <tr class="header">
        <?php
        $columns = array(
            'caption' => 'Название',
            'url'     => 'Адрес'
        );

        echo View_Helper_Admin::table_header($columns, 'sites_order', 'sites_desc');
        ?>
        
        <th></th>
    </tr>

<?php
foreach ($sites as $site)
:
    $_update_url = str_replace('${id}', $site->id, $update_url);
    $_delete_url = str_replace('${id}', $site->id, $delete_url);
?>
    <tr>
        <?php
        foreach (array_keys($columns) as $field):
        ?>
            <td>
            <?php
            if (isset($site->$field) && trim($site->$field) !== '') {
                echo HTML::chars($site->$field);
            } else {
                echo '&nbsp';
            }
            ?>
            </td>

        <?php
        endforeach;
        ?>

        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать магазин', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить магазин', 'controls/delete.gif', 'Удалить'); ?>
        </td>
    </tr>
<?php
endforeach;
?>
</table>
