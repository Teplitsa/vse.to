<?php defined('SYSPATH') or die('No direct script access.'); ?>

<table class="product" cellpadding="0" cellspacing="0"><tr>
<td class="photo">
    <?php echo Widget::render_widget('lecturers', 'lecturer_images', $lecturer); ?>
</td>

<td class="aboutProduct">
<div class="lh20">

<h1 class ="user_profile_name">    
    <?php echo "$lecturer->last_name $lecturer->first_name $lecturer->middle_name"?>
</h1>

<?php if ($lecturer->organization != '') { ?>    
<p class="user_profile_prop"><?php echo 'Организация: '.$lecturer->organization ?></p>
<?php } ?>
<?php if ($lecturer->position != '') { ?>    
<p class="user_profile_prop"><?php echo 'Должность: '.$lecturer->position ?></p>
<?php } ?>

<?php
?>
    <div class="biggerText product_desc">
        <?php
        if ($lecturer->info != '')
        {
            echo $lecturer->info;
        }
        ?>
        <br class="clearBoth">
    </div>
    <br class="clearBoth">
</div>
</td>
</tr></table>

<?php defined('SYSPATH') or die('No direct script access.'); ?>
