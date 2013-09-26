<?php defined('SYSPATH') or die('No direct script access.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Файловый менеджер</title>
    <meta http-equiv="content-type" content="text/html; charset=<?php echo Kohana::$charset; ?>" />

    <?php echo HTML::style(Modules::uri('backend') . '/public/css/backend/backend.css'); ?>
    <?php echo HTML::style(Modules::uri('backend') . '/public/css/backend/forms.css'); ?>
    <?php echo HTML::style(Modules::uri('backend') . '/public/css/backend/tables.css'); ?>
    <?php echo HTML::style(Modules::uri('backend') . '/public/css/backend/backend_ie6.css', NULL, FALSE, 'if lte IE 6'); ?>
    
    <?php echo $view->placeholder('styles'); ?>

    <!-- TinyMCE popup scripts -->
    <?php echo HTML::script(Modules::uri('tinymce') . '/public/js/tiny_mce/tiny_mce_popup.js'); ?>

    <!-- File manager dialogue -->
    <?php echo HTML::script(Modules::uri('filemanager') . '/public/js/tiny_mce/filemanager.js'); ?>
</head>
<body>
    <div class="filemanager">
        <?php echo $content; ?>
    </div>
</body>
</html>