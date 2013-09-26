<div id="LectorModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Добавить лектора</h3>
    </div>
    
    <?php echo $form->render_form_open();?>
        <div class="modal-body">
            <label for="last_name"><?php echo $form->get_element('last_name')->render_input(); ?></label>
            <?php echo $form->get_element('last_name')->render_alone_errors();?>            
            <label for="first_name"><?php echo $form->get_element('first_name')->render_input(); ?></label>
            <?php echo $form->get_element('first_name')->render_alone_errors();?>            
            <label for="middle_name"><?php echo $form->get_element('middle_name')->render_input(); ?></label>
            <?php echo $form->get_element('middle_name')->render_alone_errors();?>
            
            <label for="info"><?php echo $form->get_element('info')->render_input(); ?></label>
            <?php echo $form->get_element('info')->render_alone_errors();?>

            <label for="links"><?php echo $form->get_element('links')->render_input(); ?></label>
            <?php echo $form->get_element('links')->render_alone_errors();?>
            
            <?php echo $form->get_element('file')->render_label(); echo $form->get_element('file')->render_input(); ?>
            <?php echo $form->get_element('file')->render_alone_errors();?>            
            <div id="prev_<?php echo $form->get_element('file')->id?>" class="prev_container"></div><br/>

        </div>
        <div class="modal-footer">
            <?php echo $form->get_element('submit_lecturer')->render_input(); ?>
        </div>
    <?php echo $form->render_form_close();?>
</div>
