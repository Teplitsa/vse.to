<div id="PlaceModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Добавить площадку</h3>
    </div>
    
    <?php echo $form->render_form_open();?>
        <div class="modal-body">
            <label for="name"><?php echo $form->get_element('name')->render_input(); ?></label>
            <?php echo $form->get_element('name')->render_alone_errors();?>

            <label for="town_id"><?php echo $form->get_element('town_id')->render_input(); ?></label>
            <?php echo $form->get_element('town_id')->render_alone_errors();?>

            <label for="address"><?php echo $form->get_element('address')->render_input(); ?></label>
            <?php echo $form->get_element('address')->render_alone_errors();?>

            <label for="description"><?php echo $form->get_element('description')->render_input(); ?></label>
            <?php echo $form->get_element('description')->render_alone_errors();?>
     
            <label for="ispeed">Доступ к интернету:         <?php echo $form->get_element('ispeed')->render_input(); ?></label>
            <?php echo $form->get_element('ispeed')->render_alone_errors();?>

            <label for="links"><?php echo $form->get_element('links')->render_input(); ?></label>
            <?php echo $form->get_element('links')->render_alone_errors();?>
            
            <?php echo $form->get_element('file')->render_label(); echo $form->get_element('file')->render_input(); ?>
            <?php echo $form->get_element('file')->render_alone_errors();?>            
            <div id="prev_<?php echo $form->get_element('file')->id?>" class="prev_container"></div><br/>

        </div>
        <div class="modal-footer">
            <?php echo $form->get_element('submit_place')->render_input(); ?>
        </div>
    <?php echo $form->render_form_close();?>
</div>
