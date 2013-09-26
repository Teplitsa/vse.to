<?php defined('SYSPATH') or die('No direct script access.'); ?>
<?php require('_header.php'); ?>

<div id="container">

    <?php echo $content; ?>
    
    <div id="tops"><div id="out">
        <?php echo Widget::render_widget('plists', 'plist', 'top5_ch_3'); ?>
        <?php echo Widget::render_widget('plists', 'plist', 'top5_ch_3_6'); ?>
        <?php echo Widget::render_widget('plists', 'plist', 'top5_boys'); ?>
        <?php echo Widget::render_widget('plists', 'plist', 'top5_girls'); ?>
    </div></div>
</div>

<?php require('_footer.php'); ?>