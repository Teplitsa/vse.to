<?php defined('SYSPATH') or die('No direct script access.'); ?>


<?php
//Set up urls

$create_url    = URL::to('backend/acl/lecturers', array('action'=>'create'), TRUE);
$update_url = URL::to('backend/acl/lecturers', array('action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/acl/lecturers', array('action'=>'delete', 'id' => '${id}'), TRUE);

$multi_action_uri = URL::uri_to('backend/acl/lecturers', array('action'=>'multi'), TRUE);
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_user_add">Создать лектора</a>
</div>

<?php
if ( ! count($lecturers))
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
            'last_name'  => 'Фамилия',            
            'first_name'  => 'Имя'
        );

        echo View_Helper_Admin::table_header($columns, 'acl_lorder', 'acl_ldesc');
        ?>

        <th></th>
    </tr>

<?php
foreach ($lecturers as $lecturer)
:
    $image_info = $lecturer->image(4);    
    $_delete_url = str_replace('${id}', $lecturer->id, $delete_url);
    $_update_url = str_replace('${id}', $lecturer->id, $update_url);
?>
    <tr>
        <td class="multi_ctl">
            <?php echo View_Helper_Admin::multi_action_checkbox($lecturer->id); ?>
        </td>

        <?php
        foreach (array_keys($columns) as $field):
        ?>
            <td>
            <?php
            if ($field === 'image') {
                echo HTML::image('public/data/' . $image_info['image'], array('width' => $image_info['width'],'height' => $image_info['height'])) . '</a></td>';                
            }
            if (isset($lecturer->$field) && trim($lecturer->$field) !== '') {
                echo HTML::chars($lecturer->$field);
            } else {
                echo '&nbsp';
            }
            ?>
            </td>

        <?php
        endforeach;
        ?>

        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать лектора', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить лектора', 'controls/delete.gif', 'Удалить'); ?>
        </td>
    </tr>
<?php
endforeach; //foreach ($lecturers as $lecturer)
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