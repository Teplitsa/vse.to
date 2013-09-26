<?php defined('SYSPATH') or die('No direct script access.'); ?>

<div class="simple_panel">
    <?php
    if (isset($caption))
    {
        echo '<div class="simple_panel_caption">' . $caption . '</div>';
    }
    ?>
    
    <?php echo $form->render(); ?>
</div>