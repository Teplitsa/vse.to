<?php echo $form->render_form_open();?>
    <fieldset class="b-f-date">
        <div class="b-input"><label for=""></label><?php echo $form->get_element('datesearch')->render_input();?>
        <?php echo $form->get_element('datetime')->render_alone_errors();?>
    </fieldset>
    <div class="form-action">
        <?php echo $form->get_element('submit_datesearch')->render_input(); ?>
    </div>
<?php echo $form->render_form_close();?>

