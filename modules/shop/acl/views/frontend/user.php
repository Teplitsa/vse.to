<?php defined('SYSPATH') or die('No direct script access.'); ?>


<?php
// ----- Set up urls
$update_url = URL::to('frontend/acl/users/control', array('action'=>'update'));
?>

<div class="wrapper lecturer">
    <h1><?php echo $user->name?></h1>
    <div class="row-fluid">
        <div class="span6 bio">
            <?php echo Widget::render_widget('users', 'user_images', $user); ?>
            <?php if (is_array($user->webpages)) {
                  foreach ($user->webpages as $webpage) { ?>
                <a class="website" href="<?php echo $webpage?>"><?php echo $webpage?></a>
            <?php }} ?>
            <?php echo Widget::render_widget('users', 'links', $user); ?>
        </div>
        <div class="span6 content">
            <?php echo $user->info ?>
            <!-- <p class="who meta">Контактное лицо: <span><?php if ($user->name) echo "$user->name,"; ?><?php if ($user->phone) echo "$user->phone,"; ?><?php echo $user->email ?></span></p> -->
            <p class="typelec meta">Институция: <span><?php echo $user->organizer->full_name ?></span></p>
            <p class="address meta">Адрес институции: <span><?php echo $user->organizer->full_address ?></span></p>
        </div>
    </div>
    <div class="b-social"></div>
    <div class="action">
        <!--<a href="#messageModal" data-toggle="modal" class="button write-message">Написать сообщение</a> -->
        <?php if (Auth::granted('user_update') && ($user->id === Model_User::current()->id)) { ?>
        <a href="<?php echo $update_url ?>" class="link-edit"><i class="icon-pencil icon-white"></i></a>
        <?php } ?>          
    </div>
</div>
<?php echo Widget::render_widget('products','products', 'frontend/small_products'); ?>
<?php echo Widget::render_widget('telemosts','telemosts_by_owner',  Auth::instance()->get_user()); ?>
<?php echo Widget::render_widget('telemosts','app_telemosts_by_owner',  Auth::instance()->get_user()); ?>
<?php echo Widget::render_widget('telemosts','goes_by_owner',  Auth::instance()->get_user()); ?>

