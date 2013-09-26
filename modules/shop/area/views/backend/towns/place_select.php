<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
//Set up urls

$tab_url = URL::self(array('are_town_alias' => '${alias}'));
$create_url    = URL::to('backend/area/towns', array('action'=>'create'), TRUE);
$update_url = URL::to('backend/area/towns', array('action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/area/towns', array('action'=>'delete', 'id' => '${id}'), TRUE);
$multi_action_uri = URL::uri_to('backend/area/towns', array('action'=>'multi'), TRUE);
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_user_add">Добавить Город</a>
</div>

<?php
if ( ! count($towns))
    // No users
    return;
?>

<?php echo View_Helper_Admin::multi_action_form_open($multi_action_uri); ?>
<table class="table">
    <tr class="header">
        <th><?php echo View_Helper_Admin::multi_action_select_all(); ?></th>

        <?php
        $columns = array(
            'name'      => 'Название'
        );

        echo View_Helper_Admin::table_header($columns, 'are_torder', 'are_tdesc');
        ?>

        <th></th>
    </tr>

<?php
foreach ($towns as $town)
:
    $_tab_url = str_replace('${alias}', $town->alias, $tab_url);
    $_delete_url = str_replace('${id}', $town->id, $delete_url);
    $_update_url = str_replace('${id}', $town->id, $update_url);
?>
    <tr>
        <td class="multi_ctl">
            <?php echo View_Helper_Admin::multi_action_checkbox($town->id); ?>
        </td>

        <?php
        foreach (array_keys($columns) as $field):
        ?>
            <td>
            <?php
            if ($field == 'name') {
                echo  '<a href="' . $_tab_url . '">' . HTML::chars($town->name) . '</a>';
            } elseif (isset($town->$field) && trim($town->$field) !== '') {
                echo HTML::chars($town->$field);
            } else {
                echo '&nbsp';
            }
            ?>
            </td>

        <?php
        endforeach;
        ?>

        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать город', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить город', 'controls/delete.gif', 'Удалить'); ?>
        </td>
    </tr>
<?php
endforeach; //foreach ($towns as $town)
?>
</table>

<?php
if (isset($pagination))
{
    echo $pagination;
}
?>

<?php
echo View_Helper_Admin::multi_actions(array(
    array('action' => 'multi_delete', 'label' => 'Удалить', 'class' => 'button_delete')
));
?>

<?php echo View_Helper_Admin::multi_action_form_close(); ?>