<?php defined('SYSPATH') or die('No direct script access.'); ?>

<div class="panel">
    <div class="panel_header">
        <?php 
        if (isset($caption))
        {
            echo '<div class="panel_caption"><div>' . $caption . '</div></div>';
        }
        ?>

        <?php echo $form->render_tabs(); ?>
    </div>

    <div class="panel_content">
        <?php echo $form->render(); ?>
    </div>
</div>