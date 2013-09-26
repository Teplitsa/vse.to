<?php defined('SYSPATH') or die('No direct script access.'); ?>

 <?php echo $form->render_form_open(); ?>

<table border="0" cellpadding="0" cellspacing="0">
<tbody>
    <tr>
        <td class="selecttown_input-name-value">
            <?php echo $form->get_element('name')->render_input(); ?>
        </td>
        <td>
            <?php echo $form->get_element('submit')->render_input(); ?>                        
        </td>
    </tr>
</tbody>
</table>
<?php
echo $form->render_form_close();
?>
