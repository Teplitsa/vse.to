<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
// Set up urls
$create_url = URL::to('backend/acl/groups', array('action'=>'create'), TRUE);
$update_url = URL::to('backend/acl/groups', array('action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/acl/groups', array('action'=>'delete', 'id' => '${id}'), TRUE);
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_group_add">Создать группу</a>
</div>

<div class="groups">
<table>
    <tr>
        <td>
            <?php
                // Highlight current group
                if ($group_id == 0) {
                    $selected = ' selected';
                } else {
                    $selected = '';
                }
            ?>
            <a
                href="<?php echo URL::self(array('group_id' => (string) 0)); ?>"
                class="group<?php echo $selected; ?>"
                title="Показать всех пользователей"
            >
                <strong>Все пользователи</strong>
            </a>
        </td>
        <td>&nbsp;</td>
<?php
    foreach ($groups as $group)
    :
        $_delete_url  = str_replace('${id}', $group->id, $delete_url);
        $_update_url = str_replace('${id}', $group->id, $update_url);
?>
    <tr>
        <td>
            <?php
                // Highlight current group
                if ($group->id == $group_id) {
                    $selected = ' selected';
                } else {
                    $selected = '';
                }
            ?>
            <a
                href="<?php echo URL::self(array('group_id' => (string) $group->id)); ?>"
                class="group<?php echo $selected; ?>"
                title="Показать пользователей из группы '<?php echo HTML::chars($group->name); ?>'"
            >
                <?php echo HTML::chars($group->name); ?>
            </a>
        </td>

        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать группу', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить группу', 'controls/delete.gif', 'Удалить'); ?>
        </td>

    </tr>
<?php
    endforeach; //foreach ($groups as $group)
?>
</table>
</div>
