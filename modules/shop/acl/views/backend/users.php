<?php defined('SYSPATH') or die('No direct script access.'); ?>


<?php
//Set up urls
if (isset($group) && $group->id > 0) {
    $create_url    = URL::to('backend/acl/users', array('action'=>'create', 'group_id' => $group->id), TRUE);
} else {
    $create_url    = URL::to('backend/acl/users', array('action'=>'create'), TRUE);
}
$update_url = URL::to('backend/acl/users', array('action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/acl/users', array('action'=>'delete', 'id' => '${id}'), TRUE);

$multi_action_uri = URL::uri_to('backend/acl/users', array('action'=>'multi'), TRUE);
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_user_add">Создать пользователя</a>
</div>

<?php
if (isset($group))
{
    echo '<div class="group_name">' . HTML::chars($group->name) . '</div>';
}
?>

<?php
if ( ! count($users))
    // No users
    return;
?>

<?php echo View_Helper_Admin::multi_action_form_open($multi_action_uri); ?>
<table class="table">
    <tr class="header">
        <th><?php echo View_Helper_Admin::multi_action_select_all(); ?></th>

        <?php
        $columns = array(
            'email' => 'E-mail',
            //'name'  => 'Имя',
            'logins'  => 'Посещения',
            'active'  => 'Акт.'            
        );

        echo View_Helper_Admin::table_header($columns, 'acl_uorder', 'acl_udesc');
        ?>

        <th></th>
    </tr>

<?php
foreach ($users as $user)
:
    $_delete_url = str_replace('${id}', $user->id, $delete_url);
    $_update_url = str_replace('${id}', $user->id, $update_url);
?>
    <tr>
        <td class="multi_ctl">
            <?php echo View_Helper_Admin::multi_action_checkbox($user->id); ?>
        </td>

        <?php
        foreach (array_keys($columns) as $field) {
            switch ($field)
            {
                
                case 'active':
                    echo '<td class="c">';
                                      
                    if ( ! empty($user->$field)) {
                        echo View_Helper_Admin::image('controls/on.gif', 'Да');
                    } else {
                        echo View_Helper_Admin::image('controls/off.gif', 'Нет');
                    }
                    echo '</td>';
                    break;
                case 'logins':
                    echo '<td class="nowrap">';
                    if ($user->logins) {
                        echo $user->logins.' '.l10n::plural($user->logins, 'вход', 'входов', 'входа').', последний '.$user->last_login_str;
                    }
                    echo '</td>';
                    break;

                default:
                    echo '<td class="nowrap">';

                    if (isset($user->$field) && trim($user->$field) !== '') {
                        echo HTML::chars($user[$field]);
                    } else {
                        echo '&nbsp';
                    }

                    echo '</td>';
            }
        
        

        } ?>

        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать пользователя', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить пользователя', 'controls/delete.gif', 'Удалить'); ?>
        </td>
    </tr>
<?php
endforeach; //foreach ($users as $user)
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