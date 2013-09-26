<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php

$theme_url = URL::to('frontend/catalog/products', array('theme' => '{{theme}}'), TRUE); ?>

<li class="dropdown">
    <?php if ($theme) {
        $main_theme_url = URL::to('frontend/catalog/products', array('theme' => $theme), TRUE); ?>
        <li  class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="<?php echo $main_theme_url?>"><?php echo Model_Product::$_theme_options[$theme]?><b class="caret"></b></a>       
    <?php } else { ?>
        <li  class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="">тематика события<b class="caret"></b></a>
    <?php } ?>        
    <ul class="dropdown-menu" role="menu" aria-labelledby="drop10">
        <?php foreach ($themes as $f_id => $f_name) {
            if ($f_name == $theme) continue;
            $_theme_url = str_replace('{{theme}}', $f_id, $theme_url); ?>
            <li><a role="menuitem" tabindex="-1" href="<?php echo $_theme_url ?>"><?php echo $f_name ?></a></li>
        <?php }?>
    </ul>
</li>