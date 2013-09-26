<?php defined('SYSPATH') or die('No direct script access.'); ?>

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
    </tr>
<?php
    endforeach; //foreach ($groups as $group)
?>
</table>
</div>
