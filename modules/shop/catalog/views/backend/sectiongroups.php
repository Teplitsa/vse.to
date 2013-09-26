<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$create_url = URL::to('backend/catalog/sectiongroups', array('action'=>'create'), TRUE);
$update_url = URL::to('backend/catalog/sectiongroups', array('action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/catalog/sectiongroups', array('action'=>'delete', 'id' => '${id}'), TRUE);
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Создать группу категорий</a>
</div>

<?php
if ( ! count($sectiongroups))
    return;
?>

<table class="table">
    <tr class="header">
        <?php
        $columns = array(
            'caption' => 'Название'
        );

        echo View_Helper_Admin::table_header($columns, 'cat_sgorder', 'cat_sgdesc');
        ?>
        
        <th></th>
    </tr>

<?php
foreach ($sectiongroups as $sectiongroup)
:
    $_update_url = str_replace('${id}', $sectiongroup->id, $update_url);
    $_delete_url = str_replace('${id}', $sectiongroup->id, $delete_url);
?>
    <tr>
        <?php
        foreach (array_keys($columns) as $field):
        ?>
            <td>
            <?php
            if (isset($sectiongroup->$field) && trim($sectiongroup->$field) !== '') {
                echo HTML::chars($sectiongroup->$field);
            } else {
                echo '&nbsp';
            }
            ?>
            </td>

        <?php
        endforeach;
        ?>

        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать группу категорий', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить группу категорий', 'controls/delete.gif', 'Удалить'); ?>
        </td>
    </tr>
<?php
endforeach;
?>
</table>
