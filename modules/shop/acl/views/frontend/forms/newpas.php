<?php defined('SYSPATH') or die('No direct script access.'); ?>
    <div class="modal-header">
        <h3 id="myModalLabel">Новый пароль</h3>
    </div>

    <?php echo $form->render_form_open();?>
        <div class="modal-body">
            <p>Введите новый пароль</p>
            <label for="pass"><?php echo $form->get_element('password')->render_input(); ?></label>

        </div>
        <div class="modal-footer">
            <?php echo $form->get_element('submit_newpas')->render_input(); ?>
        </div>
    <?php echo $form->render_form_close();?>