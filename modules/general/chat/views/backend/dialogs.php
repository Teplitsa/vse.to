<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$create_url = URL::to('backend/messages', array('action'=>'create'), TRUE);
$messages_url = URL::to('backend/messages', array('action'=>'index','dialog_id' => '${id}'), TRUE);
$delete_url = URL::to('backend/dialogs', array('action'=>'delete', 'id' => '{{id}}'), TRUE);
$multi_action_uri = URL::uri_to('backend/dialogs', array('action'=>'multi'), TRUE);
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Новое сообщение</a>
</div>

<?php if ($dialogs->valid()) { ?>

<?php echo View_Helper_Admin::multi_action_form_open($multi_action_uri); ?>
<table class="dialogs table">
    <tr class="header">
        <th><?php echo View_Helper_Admin::multi_action_select_all(); ?></th>
        
        <?php
        $columns = array(
            'user_id' => array(
                'label' => 'Собеседник',
                'sortable' => FALSE                
            ),
            'message_preview'   => array(
                'label'  => 'Сообщение',
                'sortable' => FALSE
            ),
            'date' => array(
                'label' => 'Дата',
                'sortable' => FALSE
            )
        );

        echo View_Helper_Admin::table_header($columns);
        ?>
        
        <th>&nbsp;&nbsp;&nbsp;</th>
    </tr>
<?php
foreach ($dialogs as $dialog)
:
    $_delete_url = str_replace('{{id}}', $dialog->id, $delete_url);    
    $_messages_url = str_replace('${id}', $dialog->id, $messages_url);
?>
    <tr>
        <td class="multi_ctl">
            <?php echo View_Helper_Admin::multi_action_checkbox($dialog->id); ?>
        </td>
        
        <?php
        $message = Model::fly('Model_Message')->find_by_dialog_id($dialog->id,array(
            'order_by'  => 'created_at',
            'desc'      => '0'));
        
        foreach (array_keys($columns) as $field)
        {
            switch ($field)
            {
                case 'message_preview':
                    echo '<td id="message"><a href="' . $_messages_url . '" class="messages_select" id="dialog_' . $dialog->id. '">' 
                    . HTML::chars($message->$field) . '</a></td>';
                    break;
                case 'user_id':
                    $user = $dialog->opponent;
                    echo '<td>';
                    echo Widget::render_widget('users', 'user_card', $user);
                    echo '</td>';
                    
                    break;
                default:
                    echo '<td>';
                    if (isset($message->$field) && trim($message->$field) !== '') {
                        echo HTML::chars($message[$field]);
                    } else {
                        echo '&nbsp';
                    }
                    echo '</td>';
            }
        }
        ?>
        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить диалог', 'controls/delete.gif', 'Удалить'); ?>
        </td>        
    </tr>
<?php
endforeach;
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
<?php } ?>

    