<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
/* @var $form Form */
echo $form->render_form_open();
?>

<div class="label">
    <label>от</label>
    <?php echo $form->get_element('price_from')->render_input(); ?>
    <label>до</label>
    <?php echo $form->get_element('price_to')->render_input(); ?>
</div>

    <input type="image" src="<?php echo URL::base(FALSE) . 'public/css/img/choose-button.gif'; ?>" alt="выбрать" class="submit" />
<?php
echo $form->render_form_close();
?>