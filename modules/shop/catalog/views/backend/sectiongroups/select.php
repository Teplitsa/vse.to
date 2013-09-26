<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
// ----- Set up urls
// Submit results to previous url
$sectiongroups_select_uri = URL::uri_back();
?>

<?php
echo View_Helper_Admin::multi_action_form_open($sectiongroups_select_uri, array('name' => 'sectiongroups_select'));
?>

<table class="sectiongroups_select table">
    <tr class="header">
        <th><?php echo View_Helper_Admin::multi_action_select_all(); ?></th>

        <?php
            $columns = array(
                'caption' => 'Название',
            );
            echo View_Helper_Admin::table_header($columns, 'cat_sorder', 'cat_sdesc');
        ?>
    </tr>

<?php
foreach ($sectiongroups as $sectiongroup)
:
?>
    <tr>
        <td class="multi_ctl">
            <?php echo View_Helper_Admin::multi_action_checkbox($sectiongroup->id); ?>
        </td>

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

    </tr>
<?php
endforeach;
?>
</table>

<?php
echo View_Helper_Admin::multi_actions(array(
    array('action' => 'sectiongroups_select', 'label' => 'Выбрать', 'class' => 'button_select')
));
?>

<?php echo View_Helper_Admin::multi_action_form_close(); ?>