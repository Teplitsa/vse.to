<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php

$choose_url = URL::to('frontend/area/towns', array('action'=>'choose', 'are_town_alias' => '{{alias}}'), TRUE);
$main_town_url = URL::to('frontend/area/towns', array('action'=>'choose', 'are_town_alias' => $town->alias), TRUE);
?>
<li class="dropdown">
    <a class="dropdown-toggle" data-toggle="dropdown" href="<?php echo $main_town_url?>"><?php echo $town->name ?><b class="caret"></b></a>
    <ul class="dropdown-menu" role="menu" aria-labelledby="drop10">
        <?php foreach ($towns as $t) {
            if ($t->id == $town->id) continue;
            $_choose_url = str_replace('{{alias}}', $t->alias, $choose_url); ?>
            <li><a role="menuitem" tabindex="-1" href="<?php echo $_choose_url ?>"><?php echo $t->name ?></a></li>
        <?php }?>
    </ul>
</li>