<?php defined('SYSPATH') or die('No direct script access.'); ?>

<div class="panel editfile_form">
    <div class="caption"><?php echo HTML::chars($caption) ?></div>
    <div class="content">
        <?php
            if (isset($flash_msg))
            {
                echo View_Helper::flash_msg($flash_msg, $flash_msg_class);
            }
        ?>

        <?php echo $form->render(); ?>

    </div>
</div>

