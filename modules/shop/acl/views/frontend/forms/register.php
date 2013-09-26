<?php defined('SYSPATH') or die('No direct script access.'); ?>
<div id="regModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Регистрация</h3>
    </div>
    <?php echo $form->render_form_open();?>
        <div class="modal-body">
            <br>
            <label for="email"><?php echo $form->get_element('email')->render_input(); ?></label>
            <label for="pass"><?php echo $form->get_element('password')->render_input(); ?></label>
            <label for="pass_"><?php echo $form->get_element('password2')->render_input(); ?></label>
            
            <?php echo $form->get_element('email')->render_input(); ?>
            <?php echo $form->get_element('organizer_id')->render_input(); ?>
            <?php echo $form->get_element('organizer_name')->render_input(); ?>
            <?php echo $form->get_element('town_id')->render_input(); ?>
            <?php echo $form->get_element('group_id')->render_input(); ?>
            <?php $update_url = URL::to('frontend/acl/users/control', array('action'=>'create')); ?>

            <p class="foget-pass"><a href="<?php echo $update_url ?>">Зарегистрироваться как представитель?</a></p>
            
        </div>
        <div class="modal-footer">
            <?php echo $form->get_element('submit_register')->render_input(); ?>
        </div>
    <?php echo $form->render_form_close();?>
</div>