<?php defined('SYSPATH') or die('No direct script access.'); ?>

<div class="towns">
<table>
    <tr>
        <td>
            <?php
                // Highlight current group
                if ($town_alias == 0) {
                    $selected = ' selected';
                } else {
                    $selected = '';
                }
            ?>
            <a
                href="<?php echo URL::self(array('are_town_alias' => (string) '')); ?>"
                class="town<?php echo $selected; ?>"
                title="Показать все площадки"
            >
                <strong>Все площадки</strong>
            </a>
        </td>
        <td>&nbsp;</td>
<?php
    foreach ($towns as $town)
    :
?>
    <tr>
        <td>
            <?php
                // Highlight current group
                if ($town->alias == $town_alias) {
                    $selected = ' selected';
                } else {
                    $selected = '';
                }
            ?>
            <a
                href="<?php echo URL::self(array('are_town_alias' => (string) $town->alias)); ?>"
                class="town<?php echo $selected; ?>"
                title="Показать площадки города '<?php echo HTML::chars($town->name); ?>'"
            >
                <?php echo HTML::chars($town->name); ?>
            </a>
        </td>
    </tr>
<?php
    endforeach; //foreach ($towns as $town)
?>
</table>
</div>
