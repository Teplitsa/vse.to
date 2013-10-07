<div class="wrapper main-list">

<?php echo $form->render_form_open();?>
    <?php if ($form->model()->id) { ?>
    <p class="title">Редактирование учетной записи</p>
    <?php }else { ?>
    <p class="title">Регистрация нового пользователя</p>
    <?php } ?>
    <?php echo $form->render_messages(); ?>

    <h1 class="main-title"><span>Учетная запись</span></h1>
    <fieldset class="d-f-main">
        <div class="b-input"><label for="i-title"><?php echo $form->get_element('email')->render_label();?></label><?php echo $form->get_element('email')->render_input();?></div>
        <?php echo $form->get_element('email')->render_alone_errors();?>
        <div class="b-input"><label for="i-title"><?php echo $form->get_element('password')->render_label();?></label><?php echo $form->get_element('password')->render_input();?></div>
        <?php echo $form->get_element('password')->render_alone_errors();?>        
        <?php if ($form->model()->id == NULL) { ?>        
            <div class="b-input"><label for="i-title"><?php echo $form->get_element('password2')->render_label();?></label><?php echo $form->get_element('password2')->render_input();?></div>
            <?php echo $form->get_element('password2')->render_alone_errors();?>
        <?php } ?>
        
    </fieldset>
    
    <h1 class="main-title"><span>Личные данные</span></h1>
    <fieldset class="d-f-pers">
        <div class="b-input"><label for=""><?php echo $form->get_element('last_name')->render_label();?></label><?php echo $form->get_element('last_name')->render_input();?></div>
        <?php echo $form->get_element('last_name')->render_alone_errors();?>
        
        <div class="b-select"><label for=""><?php echo $form->get_element('first_name')->render_label();?></label><?php echo $form->get_element('first_name')->render_input();?></div>
        <?php echo $form->get_element('first_name')->render_alone_errors();?>

    </fieldset>
    
    <fieldset class="d-f-add">
        <div class="b-select"><label for=""></label></div>
        <div class="b-select"><label for=""><?php echo $form->get_element('middle_name')->render_label();?></label><?php echo $form->get_element('middle_name')->render_input();?></div>
        <?php echo $form->get_element('middle_name')->render_alone_errors();?>        
    </fieldset>
    
    <fieldset class="d-f-org">    
        <div class="b-input"><label for="">Организация</label><?php echo $form->get_element('organizer_name')->render_input();?></div>
        <?php echo $form->get_element('organizer_name')->render_alone_autoload();?>
        <?php echo $form->get_element('organizer_name')->render_alone_errors();?>
        <?php echo $form->get_element('organizer_id')->render_input();?>        
    </fieldset>
    <fieldset class="d-f-main">
        <div class="b-input"><label for="i-title"><?php echo $form->get_element('phone')->render_label();?></label><?php echo $form->get_element('phone')->render_input();?></div>
        <?php echo $form->get_element('phone')->render_alone_errors();?>
        <div class="b-input"><label for="i-title"><?php echo $form->get_element('town_id')->render_label();?></label><?php echo $form->get_element('town_id')->render_input();?></div>
        <?php echo $form->get_element('town_id')->render_alone_errors();?>        
    </fieldset>
    
    <fieldset class="d-f-about">
        <div class="b-txt"><label for=""><?php echo $form->get_element('info')->render_label();?></label><?php echo $form->get_element('info')->render_input();?></div>
        <?php echo $form->get_element('info')->render_alone_errors();?>
    </fieldset>
    
    <fieldset class="b-f-image">
        <div class="b-input"><?php echo $form->get_element('file')->render_label(); echo $form->get_element('file')->render_input(); ?></div>
        <?php echo $form->get_element('file')->render_alone_errors();?>
        <?php if ($form->model()->id) echo $form->get_element('images')->render_input();?>        
        <div id="prev_<?php echo $form->get_element('file')->id?>" class="prev_container"></div>        
    </fieldset>
    
    <h1 class="main-title"><span>Ссылки</span></h1>
    <fieldset class="d-f-links">
<?php 
    foreach ($form->model()->links as $link)
    { ?>
        <div class="b-input <?php echo $link->name;?>" alt="ddd"><?php echo $form->get_element($link->name)->render_input();?></div>
        <?php echo $form->get_element($link->name)->render_alone_errors();?>
<?php        
    }    
?>
    </fieldset>
        
    <div class="form-action">
        <?php echo $form->get_element('submit_user')->render_input(); ?>
    </div>
    
<?php echo $form->render_form_close();?>

</div>