<?php defined('SYSPATH') or die('No direct script access.'); ?>

<div class="wrapper lecturer">
    <h1><?php echo "Лектор: ".$lecturer->name?></h1>
    <div class="row-fluid">
        <div class="span6 bio">
            <?php echo Widget::render_widget('lecturers', 'lecturer_images', $lecturer); ?>
            <?php if (is_array($lecturer->links)) {
                  foreach ($lecturer->links as $link) { ?>
                <a class="website" href="<?php echo $link?>"><?php echo $link?></a>
            <?php }} ?>
        </div>
        <div class="span6 content">
            <?php echo $lecturer->info ?>
        </div>
    </div>
    <div class="b-social"></div>
</div>
<?php echo Widget::render_widget('products','search_products',array('lecturer_id' => $lecturer->id), 'frontend/small_products_lecturer'); ?>
