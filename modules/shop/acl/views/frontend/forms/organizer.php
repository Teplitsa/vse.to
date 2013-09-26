<div id="OrgModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Добавить организацию</h3>
    </div>
    <?php echo $form->render_form_open();?>
        <div class="modal-body">
            <label for="name"><?php echo $form->get_element('name')->render_input(); ?></label>
            <?php echo $form->get_element('name')->render_alone_errors();?>
            <div class="row-fluid">
                <div class="span6">
                    <label for="town"><?php echo $form->get_element('town_id')->render_input();?>
                    &nbsp;<a class="help-pop"  href="#" title="" data-placement="bottom" data-original-title="Город в котором зарегистрирована организация">?</a></label>
                </div>                
            </div>
            <?php echo $form->get_element('town_id')->render_alone_errors();?>
            <div class="row-fluid">
                <div class="span6">
                    <label for="type"><?php echo $form->get_element('type')->render_input();?>
                    &nbsp;<a class="help-pop"  href="#" title="" data-placement="bottom" data-original-title="Род дейтельности организации">?</a></label>
                </div>                
            </div>
            <?php echo $form->get_element('type')->render_alone_errors();?>
            <label for="info"><?php echo $form->get_element('info')->render_input(); ?></label>
            <?php echo $form->get_element('info')->render_alone_errors();?>
            
            <label for="links"><?php echo $form->get_element('links')->render_input(); ?></label>
            <?php echo $form->get_element('links')->render_alone_errors();?>
            
            <?php echo $form->get_element('file')->render_label(); echo $form->get_element('file')->render_input(); ?>
            <?php echo $form->get_element('file')->render_alone_errors();?>            
            <div id="prev_<?php echo $form->get_element('file')->id?>" class="prev_container"></div><br/>
            
        </div>
        <div class="modal-footer">
            <?php echo $form->get_element('submit_organizer')->render_input(); ?>
        </div>
    <?php echo $form->render_form_close();?>
</div>
