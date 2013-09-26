<div id="requestModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Добавить заявку на телемост</h3>
        <small>Cityname, hh:mm, dd.mm.yyy</small>
    </div>
    <?php echo $form->render_form_open();?>
        <div class="modal-body">
            <div class="row-fluid">
                <div class="span6">
                    <label for="place"><?php echo $form->get_element('place_id')->render_input();?>
                    &nbsp;<a class="help-pop"  href="#" title="" data-placement="bottom" data-original-title="A much longer tooltip belongs right here to demonstrate the max-width we apply.">?</a></label>
                </div>                
            </div>
            <label for="info"><?php echo $form->get_element('info')->render_input(); ?></label>
        </div>
        <div class="modal-footer">
            <?php echo $form->get_element('submit_request')->render_input(); ?>
        </div>
    <?php echo $form->render_form_close();?>
</div>
