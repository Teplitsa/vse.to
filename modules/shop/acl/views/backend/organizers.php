<?php defined('SYSPATH') or die('No direct script access.'); ?>


<?php
//Set up urls

$create_url    = URL::to('backend/acl/organizers', array('action'=>'create'), TRUE);
$update_url = URL::to('backend/acl/organizers', array('action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/acl/organizers', array('action'=>'delete', 'id' => '${id}'), TRUE);

$multi_action_uri = URL::uri_to('backend/acl/organizers', array('action'=>'multi'), TRUE);
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_user_add">Создать организацию</a>
</div>

<?php
if ( ! count($organizers))
    // No users
    return;
?>

<?php echo View_Helper_Admin::multi_action_form_open($multi_action_uri); ?>
<table class="table">
    <tr class="header">
        <th><?php echo View_Helper_Admin::multi_action_select_all(); ?></th>

        <?php
        $columns = array(
            'image'      => 'Фото',
            'name'  => 'Название',            
            'type_name'  => 'Тип'
        );

        echo View_Helper_Admin::table_header($columns, 'acl_oorder', 'acl_odesc');
        ?>

        <th></th>
    </tr>

<?php
foreach ($organizers as $organizer)
:
    $image_info = $organizer->image(4);    
    $_delete_url = str_replace('${id}', $organizer->id, $delete_url);
    $_update_url = str_replace('${id}', $organizer->id, $update_url);
?>
    <tr>
        <td class="multi_ctl">
            <?php echo View_Helper_Admin::multi_action_checkbox($organizer->id); ?>
        </td>

        <?php
        foreach (array_keys($columns) as $field):
        ?>
            <td>
            <?php
            if ($field === 'image') {
                echo HTML::image('public/data/' . $image_info['image'], array('width' => $image_info['width'],'height' => $image_info['height'])) . '</a></td>';                
            }
            if (isset($organizer->$field) && trim($organizer->$field) !== '') {
                echo HTML::chars($organizer->$field);
            } else {
                echo '&nbsp';
            }
            ?>
            </td>

        <?php
        endforeach;
        ?>

        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать организацию', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить организацию', 'controls/delete.gif', 'Удалить'); ?>
        </td>
    </tr>
<?php
endforeach; //foreach ($organizers as $organizer)
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