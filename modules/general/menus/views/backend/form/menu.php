<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
echo
    Form_Helper::open($form->action, $form->attributes())
  .     $form->render_messages()
  .     $form->render_hidden();
?>

<table class="content_layout"><tr>
    <td class="content_layout" style="width: 360px; padding: 0px 20px 0px 0px;">
        <dl>
            <?php echo $form->render_components(NULL, array('default_visibility', 'nodes_visibility')); ?>
        </dl>
    </td>

    <td class="content_layout" style="width: 360px; padding: 0px 0px 0px 20px;">
        <dl>
            <?php echo $form->render_components(array('default_visibility', 'nodes_visibility')); ?>
        </dl>
    </td>
</tr></table>

<?php
echo Form_Helper::close();
?>