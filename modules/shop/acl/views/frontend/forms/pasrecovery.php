<?php defined('SYSPATH') or die('No direct script access.'); ?>
<div id="pasrecoveryModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Восстановление пароля</h3>
    </div>
    <?php echo $form->render_form_open();?>
        <div class="modal-body">
            <br>
            <label for="email"><?php echo $form->get_element('email')->render_input(); ?></label>
            <br>
        </div>
        <div class="modal-footer">
            <?php echo $form->get_element('submit_pasrecovery')->render_input(); ?>
        </div>
    <?php echo $form->render_form_close();?>
</div>