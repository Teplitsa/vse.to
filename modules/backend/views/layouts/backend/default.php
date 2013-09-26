<?php defined('SYSPATH') or die('No direct script access.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title><?php echo HTML::chars($view->placeholder('title')); ?></title>

    <meta http-equiv="content-type" content="text/html; charset=<?php echo Kohana::$charset; ?>" />
    <meta http-equiv="Keywords" content="<?php echo HTML::chars($view->placeholder('keywords')); ?>" />
    <meta http-equiv="Description" content="<?php echo HTML::chars($view->placeholder('description')); ?>" />
    
    <?php echo HTML::style(Modules::uri('backend') . '/public/css/backend/backend.css'); ?>
    <?php echo HTML::style(Modules::uri('backend') . '/public/css/backend/forms.css'); ?>
    <?php echo HTML::style(Modules::uri('backend') . '/public/css/backend/tables.css'); ?>
    <?php echo HTML::style(Modules::uri('backend') . '/public/css/backend/backend_ie8.css', NULL, FALSE, 'if lte IE 8'); ?>
    <?php echo HTML::style(Modules::uri('backend') . '/public/css/backend/backend_ie6.css', NULL, FALSE, 'if lte IE 6'); ?>

    <?php echo $view->placeholder('styles'); ?>
    <?php echo $view->placeholder('scripts'); ?>
</head>
<body>

<!-- bocker -->
<div id="blocker"></div>

<!-- header -->
<div class="header">
    <?php echo Widget::render_widget('acl', 'logout'); ?>
    
    <!-- sites_menu -->
    <?php echo Widget::render_widget('sites', 'menu'); ?>

    <!-- top_menu -->
    <?php echo Widget::render_widget('menu', 'menu', 'main', 'backend/menu/top_menu'); ?>
</div>

<!-- main -->
<div class="payload">
    <!-- top_submenu -->
    <?php echo Widget::render_widget('menu', 'menu', 'main', 'backend/menu/top_submenu', 1); ?>
    <?php echo Widget::render_widget('menu', 'menu', 'sidebar', 'backend/menu/top_submenu', 1); ?>

    <!-- layout -->
    <table class="layout"><tr>
        <!-- main -->
        <td class="main">
            <div class="workspace">
                <div class="workspace_caption">
                    <?php echo Widget::render_widget('menu', 'breadcrumbs', 'main'); ?>
                    <?php if (isset($caption)) echo HTML::chars($caption); ?>
                </div>

                <?php echo Widget::render_widget('index', 'flashmessages'); ?>
                <?php echo $content; ?>
            </div>
        </td>
    </tr></table><!-- layout -->
</div><!-- main -->

<!-- footer -->
<div class="footer"></div>

<?php
if (Kohana::$profiling)
{
    //echo View::factory('profiler/stats')->render();
}
?>

</body>
</html>
