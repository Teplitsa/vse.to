<?php echo $form->render_form_open();?>
    <a href="#" id="parsing-fill-btn">Заполнить поля автоматически</a>
    
    <?php if ($form->model()->id) { ?>
    <p class="title">Редактирование анонса события</p>
    <?php }else { ?>
    <p class="title">Добавление анонса события</p>
    <?php } ?>
    <?php echo $form->render_messages(); ?>
    
    <h1 class="main-title"><span>О событии</span></h1>
    <fieldset class="b-f-main">
        <div class="b-input"><label for="i-title">Название</label><?php echo $form->get_element('caption')->render_input();?></div>
        <?php echo $form->get_element('caption')->render_alone_errors();?>
        
        <div class="b-input"><label for="">Лектор</label><?php echo $form->get_element('lecturer_name')->render_input();?></div>
        <?php echo $form->get_element('lecturer_name')->render_alone_autoload();?>
        <?php echo $form->get_element('lecturer_name')->render_alone_errors();?>
        <?php echo $form->get_element('lecturer_id')->render_input();?>
        <div class="b-input"><label for="">Организация</label><?php echo $form->get_element('organizer_name')->render_input();?></div>
        <?php echo $form->get_element('organizer_name')->render_alone_autoload();?>
        <?php echo $form->get_element('organizer_name')->render_alone_errors();?>
        <?php echo $form->get_element('organizer_id')->render_input();?>        
        
        <div class="b-input"><label for="">Площадка</label><?php echo $form->get_element('place_name')->render_input();?></div>
        <?php echo $form->get_element('place_name')->render_alone_autoload();?>
        <?php echo $form->get_element('place_name')->render_alone_errors();?>
        <?php echo $form->get_element('place_id')->render_input();?>        
    </fieldset>
    <fieldset class="b-f-date">
        <div class="b-input"><label for="">Дата</label><?php echo $form->get_element('datetime')->render_input();?>
        </div><div class="b-select"><label for="">Длительность</label><?php echo $form->get_element('duration')->render_input();?></div>

        <?php echo $form->get_element('datetime')->render_alone_errors();?>
        <?php echo $form->get_element('duration')->render_alone_errors();?>
    </fieldset>
    
    <fieldset class="b-f-theme">
        <div class="b-select"><label for="">Тема</label><?php echo $form->get_element('theme')->render_input();?>
        </div><div class="b-select"><label for="">Формат события</label><?php echo $form->get_element('format')->render_input();?></div>

        <?php echo $form->get_element('theme')->render_alone_errors();?>
        <?php echo $form->get_element('format')->render_alone_errors();?>
        
    </fieldset>
    <div class="b-input b-tegs"><label for=""><?php echo $form->get_element('tags')->render_label();?></label><?php echo $form->get_element('tags')->render_input();?></div>
    <?php echo $form->get_element('tags')->render_alone_autoload();?>
    <?php echo $form->get_element('tags')->render_alone_errors();?>        
    
    <div class="b-txt"><label for="">О событии</label><?php echo $form->get_element('description')->render_input();?></div>
    <?php echo $form->get_element('description')->render_alone_errors();?>

    <fieldset class="b-f-image">
        <div class="b-input"><?php echo $form->get_element('file')->render_label(); echo $form->get_element('file')->render_input(); ?></div>
        <?php echo $form->get_element('file')->render_alone_errors();?>
        <?php if ($form->model()->id) echo $form->get_element('images')->render_input();?>        
        <div id="prev_<?php echo $form->get_element('file')->id?>" class="prev_container"></div>
        <?php echo $form->get_element('image_url')->render_input();?>
    </fieldset>
    
    <h1 class="main-title"><span>О трансляции</span></h1>
    <fieldset class="b-f-tvbridge">
        <span class="title">Интерактивность</span>
        <div class="pull-left">
            <?php echo $form->get_element('interact')->render_input();?>
        </div>
    </fieldset>
    <?php echo $form->get_element('interact')->render_alone_errors();?>

    <div class="b-txt b-require">
        <label for="">Стоимость лицензии</label>
        <?php echo $form->get_element('price')->render_input();?> руб.
        &nbsp;<a class="help-pop"  href="#" title="" data-placement="top" data-original-title="Если Вы планируете выдавать лицензии на трансляцию Вашего события, укажите стоимость одной лицензии.">?</a>
    </div>
    <?php echo $form->get_element('require')->render_alone_errors();?>    
    
    <fieldset class="b-f-chose">
        <span class="title">Количество телемостов</span>
        <div class="pull-left">
            <?php echo $form->get_element('numviews')->render_input();?>
            &nbsp;<a class="help-pop"  href="#" title="" data-placement="right" data-original-title="Это максимальное количество городов, в которых могут быть телемосты Вашего события. Количество телемостов нельзя будет поменять после публикации анонса. Обратите внимание: чем больше телемостов, тем меньше возможности для обратной связи.">?</a>
        </div>
    </fieldset>
    <?php echo $form->get_element('numviews')->render_alone_errors();?>            
    
    <fieldset class="b-f-chose">
        <span class="title">Кто выбирает</span>
        &nbsp;<a class="help-pop"  href="#" title="" data-placement="bottom" data-original-title="Алгоритм (в порядке очередности заявок) - первые поданные заявки будут одобрены автоматически. Алгоритм (случайный выбор) - алгоритм выберет случайные заявки. Автор анонса - Вы сами выберете из заявок. Это нужно сделать в течение 3 дней после публикации анонса.  Если вы не выберете заявки к этому сроку, заявки будут отобраны автоматически. ">?</a>
        <div class="pull-left">
            <?php echo $form->get_element('choalg')->render_input();?>
        </div>
    </fieldset>
    <?php echo $form->get_element('choalg')->render_alone_errors();?>            
    
    <div class="b-txt b-require">
        <label for="">Требования к площадке</label>
        <?php echo $form->get_element('require')->render_input();?>
        &nbsp;<a class="help-pop"  href="#" title="" data-placement="top" data-original-title="Если представителю понадобится специфическая техника или особенный зал, если вы хотите видеть определённых зрителей - эти и другие требования укажите тут.">?</a>
    </div>
    <?php echo $form->get_element('require')->render_alone_errors();?>    

    <div class="form-action">
        <?php if ($form->has_element('cancel_product')) echo $form->get_element('cancel_product')->render_input(); ?>        
        <?php echo $form->get_element('submit_product')->render_input(); ?>
    </div>
<?php echo $form->render_form_close();?>

