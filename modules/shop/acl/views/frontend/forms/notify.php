<?php defined('SYSPATH') or die('No direct script access.'); ?>
<div id="notifyModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>Пожалуйста</h3>
    </div>
    <?php echo $form->render_form_open();?>
        <div class="modal-body">
            <label for="text"><?php echo $form->get_element('text')->render_input(); ?></label>
        </div>
        <div class="modal-footer">
            <button type="button" class="button_notify button-modal button" data-dismiss="modal" aria-hidden="true">Вернуться</button>
            
        </div>
    <?php echo $form->render_form_close();?>
</div>