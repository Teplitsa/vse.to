<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
  
$format_url = URL::to('frontend/catalog/products', array('format' => '{{format}}'), TRUE); ?>

<li class="dropdown">
    <?php if ($format) {
        $main_format_url = URL::to('frontend/catalog/products', array('format' => $format), TRUE); ?>
<li  class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="<?php echo $main_format_url?>"><?php echo Model_Product::$_format_options[$format]?> <b class="caret"></b></a>       
    <?php } else { ?>
        <li  class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="">формат события<b class="caret"></b></a>
    <?php } ?>        
    <ul class="dropdown-menu" role="menu" aria-labelledby="drop10">
        <?php foreach ($formats as $f_id => $f_name) {
            if ($f_name == $format) continue;
            $_format_url = str_replace('{{format}}', $f_id, $format_url); ?>
            <li><a role="menuitem" tabindex="-1" href="<?php echo $_format_url ?>"><?php echo $f_name ?></a></li>
        <?php }?>
    </ul>
</li>