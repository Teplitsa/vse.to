<?php defined('SYSPATH') or die('No direct script access.'); ?>

<ul class="main-menu">
    <?php
    foreach ($items as $item)
    {
        // Build menu item class
        $class = '';
        if ( ! empty($item['selected']))
        {
            $class .= ' current';
        }        
        
        echo
            '<li class= '.$class.' ><a href="' . Model_Backend_Menu::url($item) .'"'
          .     (isset($item['title']) ? ' title="' . HTML::chars($item['title']) . '"' : '')
          . '>'
          .     $item['caption']
          . '</a></li>';
    }
    ?>
</ul>