<?php defined('SYSPATH') or die('No direct script access.'); ?>
    <div class="modal-header">
        <h3 id="myModalLabel">Вход</h3>
    </div>

    <?php echo $form->render_form_open();?>
        <div class="modal-body">
            <?php
            
            $stat = Request::current()->param('stat',NULL);
            switch ($stat) {
                case 'ok': ?>
            <p class="repeat-pass">На указанный Вами адрес было отправлено письмо с ссылкой для смены пароля.</p>    
            <?php break; 
            case 'try': ?>        
            <p class="repeat-pass">Пароль успешно изменен.<br>Введите авторизационные данные, чтобы войти на портал.</p>                
            <?php break;
            default: ?>
            <p class="repeat-pass">Ошибка в адресе или пароле. Попробуйте ещё раз.</p>            
            <?php }  ?>
             
            <!--<p>Вход через социальные сети</p>
            <div class="soc-link">
                <a href="#" class="button fb">f</a>
                <a href="#" class="tw button ">t</a>
                <a href="#" class="button vk">v</a>
            </div>-->
            <label for="email"><?php echo $form->get_element('email')->render_input(); ?></label>
            <label for="pass"><?php echo $form->get_element('password')->render_input(); ?></label>
            <?php echo Widget::render_widget('acl', 'pasrecovery'); ?>
        </div>
        <div class="modal-footer">
            <?php echo $form->get_element('submit_login')->render_input(); ?>
        </div>
    <?php echo $form->render_form_close();?>
