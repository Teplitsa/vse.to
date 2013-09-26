<?php defined('SYSPATH') or die('No direct script access.'); ?>

 <?php echo $form->render_form_open(); ?>

<table class="table smallmsg">
<tbody>
    <tr>
    <td>
    <table class="smallmsg_up">
        <tbody>
        <tr>
            <td class="smallmsg_sender">
                <?php
                    $sender = Auth::instance()->get_user();
                    echo Widget::render_widget('users', 'user_card', $sender,array('image'));
                ?>
            </td>            
            <td class="smallmsg_message">
                <?php echo $form->get_element('message')->render_input(); ?>
            </td>
            <td class="smallmsg_receiver">
                <?php
                    $receiver_id = ($sender->id == $form->model()->sender_id) ? $form->model()->receiver_id : $form->model()->sender_id;
                    $receiver = Model::fly('Model_User')->find($receiver_id);
                    echo Widget::render_widget('users', 'user_card', $receiver,array('image'));
                ?>
            </td>            
        </tr>
        </tbody>
    </table>
    </td>
    </tr>
    <tr>
    <td>
    <table class="smallmsg_down">
        <tbody>
        <tr>            
            <td class="smallmsg_notify">
                <?php echo $form->get_element('notify')->render_label(); ?>
            </td>
            <td class="smallmsg_notify">
                <?php echo $form->get_element('notify')->render_input(); ?>
            </td>            
            <td class="smallmsg_button">         
                <input type="submit"  value="Отправить" class="submit" />
            </td>
        </tr>
        </tbody>
    </table>            
    </td>  
    </tr>
</tbody>
</table>
<?php
echo $form->render_form_close();
?>
   
