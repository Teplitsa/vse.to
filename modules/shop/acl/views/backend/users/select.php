<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
// ----- Set up urls
// Submit results to previous url
$users_select_uri = URL::uri_back();

?>

<?php
echo View_Helper_Admin::multi_action_form_open($users_select_uri, array('name' => 'users_select'));
?>

<table class="users_select table">
    <tr class="header">
        <th><?php echo View_Helper_Admin::multi_action_select_all(); ?></th>

        <?php
            $columns = array(
                'email' => 'E-mail'
            );
            echo View_Helper_Admin::table_header($columns, 'acl_uorder', 'acl_udesc');
        ?>
    </tr>

<?php
foreach ($users as $user)
:

?>
    <tr>
        <td class="multi_ctl">
            <?php echo View_Helper_Admin::multi_action_checkbox($user->id); ?>
        </td>

    <?php
        foreach (array_keys($columns) as $field)
        {
            switch ($field)
            {
                case 'name':
                    echo
                        '<td>'
                      .             HTML::chars($user->$field)
                      . '</td>';
                    break;
                default:
                    echo '<td>';

                    if (isset($user->$field) && trim($user->$field) !== '') {
                        echo HTML::chars($user[$field]);
                    } else {
                        echo '&nbsp';
                    }

                    echo '</td>';
            }
        }
    ?>
    </tr>
<?php
endforeach;
?>
</table>

<?php
echo View_Helper_Admin::multi_actions(array(
    array('action' => 'users_select', 'label' => 'Выбрать', 'class' => 'button_select')
));
?>

<?php echo View_Helper_Admin::multi_action_form_close(); ?>