<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
  
$calendar_url = URL::to('frontend/catalog/products', array('calendar' => '{{calendar}}'), TRUE); ?>

<li class="dropdown">
    <?php if ($calendar) {
        $main_calendar_url = URL::to('frontend/catalog/products', array('calendar' => $calendar), TRUE); ?>
<li  class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="<?php echo $main_calendar_url?>"><?php echo Model_Product::$_calendar_options[$calendar]?> <b class="caret"></b></a>       
    <?php } else { ?>
        <li  class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="">ограничение по времери<b class="caret"></b></a>
    <?php } ?>        
    <ul class="dropdown-menu" role="menu" aria-labelledby="drop10">
        <?php foreach ($calendars as $c_id => $c_name) {
            if ($c_name == $calendar) continue;
            $_calendar_url = str_replace('{{calendar}}', $c_id, $calendar_url); ?>
            <li><a role="menuitem" tabindex="-1" href="<?php echo $_calendar_url ?>"><?php echo $c_name ?></a></li>
        <?php }?>
    </ul>
</li>