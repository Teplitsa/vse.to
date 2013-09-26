<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$view_url = URL::to('frontend/news', array('action'=>'view', 'id' => '{{id}}'), TRUE);
?>

<!-- news list -->
<?php
if ( ! count($news))
    // No news
    return;
?>

<?php
foreach ($news as $newsitem)
:
    $_view_url = str_replace('{{id}}', $newsitem->id, $view_url);
?>
    <div class="item">
        <a href="<?php echo $_view_url; ?>">
            <?php echo HTML::chars($newsitem->caption); ?>
        </a>
    </div>
<?php
endforeach;
?>

<div class="link"><a href="<?php echo URL::to('frontend/news'); ?>">Архив новостей</a></div>