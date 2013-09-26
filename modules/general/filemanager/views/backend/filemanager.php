<?php defined('SYSPATH') or die('No direct script access.'); ?>

<h1>Файловый менеджер</h1>

<?php
    echo View_Helper_Admin::tabs(array(
            'files' => 'Файлы',
            'css'    => 'Оформление',
            'templates' => 'Шаблоны'
        ), $root, NULL, 'root', array('path' => NULL)); ?>

<div class="panel">
    <div class="content">
        <?php echo $files; ?>
    </div>
</div>

