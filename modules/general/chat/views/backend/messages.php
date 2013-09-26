<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
if ( !isset($messages) || ! count($messages))
    // No menus
    return;
?>

<?php
if (isset($pagination))
{
    echo $pagination;
}
?>

<table class="messages table">
    <tr class="header">        
        <?php
        $columns = array(
            'sender_id' => array(
                'label' => 'Отправитель',
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
    </tr>

<?php
foreach ($messages as $message)
:
?>
    <tr>        
        <?php
        foreach (array_keys($columns) as $field)
        {
            switch ($field)
            {
                case 'message_preview':
                    echo '<td id="message">'.HTML::chars($message->$field) . '</a></td>';
                    break;
                case 'sender_id':
                    $sender = Model::fly('Model_User')->find($message->user_id);
                    echo '<td>';
                    echo Widget::render_widget('users', 'user_card', $sender);
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
    </tr>
<?php
endforeach;
?>
</table>

<?php 
echo $form->render(); ?>

