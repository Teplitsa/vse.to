<?php defined('SYSPATH') or die('No direct script access.'); ?>
<div id="enterModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Вход</h3>
    </div>
    <?php echo $form->render_form_open();?>
        <div class="modal-body">
            <!--<p>Вход через социальные сети</p>
            <div class="soc-link">
                <a href="#" class="button fb">f</a>
                <a href="#" class="tw button ">t</a>
                <a href="#" class="button vk">v</a>
            </div>-->
            <label for="email"><?php echo $form->get_element('email')->render_input(); ?></label>
            <label for="pass"><?php echo $form->get_element('password')->render_input(); ?></label>
        </div>
        <div class="modal-footer">
            <?php echo $form->get_element('submit_login')->render_input(); ?>
        </div>
    <?php echo $form->render_form_close();?>
</div>