<div id="ImgModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Загузка Фото</h3>
    </div>
    <?php echo $form->render_form_open();?>
        <div class="modal-body">
            <label for="file"><?php echo $form->get_element('file')->render_input(); ?></label>
        </div>
        <div class="modal-footer">
            <?php echo $form->get_element('submit_image')->render_input(); ?>
        </div>
    <?php echo $form->render_form_close();?>
</div>
