<div id="<?php echo "requestModal_".$form->get_element('product_alias')->value?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Добавить заявку на телемост</h3>
        <!--<small>Cityname, hh:mm, dd.mm.yyy</small>-->
    </div>
    <?php echo $form->render_form_open();?>
        <div class="modal-body">
            <div class="row-fluid">
                <div class="span6">
                    <label for="place"><?php echo $form->get_element('place_id')->render_input();?>
                    &nbsp;<a class="help-pop"  href="#" title="" data-placement="bottom" data-original-title="Выберите площадку из предложенных или добавьте новую  в список, написав письмо с названием и адресом площадки на адрес tov.dinast@gmail.com.">?</a>
                    </label>
                </div>                
            </div>
            <label for="info"><?php echo $form->get_element('info')->render_input(); ?>
            &nbsp;<a class="help-pop"  href="#" title="" data-placement="bottom" data-original-title="Тут можно написать о локальном событии, которое будет до или после трансляции (например, дискуссия с приглашённым экспертом, дебаты или просмотр фильма), или о чём-то другом.">?</a>
            </label>
        </div>
        <div class="modal-footer">
            <?php echo $form->get_element('submit_request')->render_input(); ?>
        </div>
    <?php echo $form->render_form_close();?>
</div>
