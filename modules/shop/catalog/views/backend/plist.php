<?php defined('SYSPATH') or die('No direct script access.'); ?>

<div class="simple_panel">
    <?php
    if (isset($caption))
    {
        echo '<div class="simple_panel_caption">' . $caption . '</div>';
    }
    ?>

    <div class="simple_panel_content">
        <?php echo $form->render(); ?>
    </div>
</div>

<div class="caption">Товары в списке</div>
<?php echo $plistproducts; ?>