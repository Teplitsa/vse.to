<?php defined('SYSPATH') or die('No direct script access.'); ?>

<div class="wrapper main-list">

Изменение информации:
 <?php echo $form->render_form_open();?>
			<table>
			<tr><td><label for="pass">Имя</label></td><td><?php echo $form->get_element('first_name')->render_input(); ?></td></tr>
            <tr><td><label for="organizer_name">Фамилия</label></td><td><?php echo $form->get_element('last_name')->render_input(); ?></td></tr>
			<tr><td><label for="pass">Пароль</label></td><td><?php echo $form->get_element('password')->render_input(); ?></td></tr>
            <tr><td><label for="email">Емаил</label></td><td><?php echo $form->get_element('email')->render_input(); ?></td></tr>
			<tr><td><label for="town_id">Город</label></td><td><?php echo $form->get_element('town_id')->render_input(); ?></td></tr>
            <tr><td><label for="organizer_name">Организация</label></td><td><?php echo $form->get_element('organizer_name')->render_input(); ?></td></tr>
			<tr><td><label for="email">О себе</label></td><td><?php echo $form->get_element('info')->render_input(); ?></td></tr>
            <tr><td><?php echo $form->get_element('submit_register')->render_input(); ?></td></tr>
			</table>
    <?php echo $form->render_form_close();?>
</div>	