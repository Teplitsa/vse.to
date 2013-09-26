<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
echo $form->render_messages();

echo Form_Helper::open($form->action, $form->attributes());

echo $form->render_hidden();
echo $form->render_components();

echo Form_Helper::close();

//$form->render_js();
?>