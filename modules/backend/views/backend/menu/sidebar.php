<?php defined('SYSPATH') or die('No direct script access.'); ?>

<div class="panel">
    <?php
    if (isset($caption))
    {
        echo '<div class="panel_caption">' . $caption . '</div>';
    }
    ?>

    <div class="panel_content">
        <div class="area">
            <div class="area_content">
                <div class="content">


<div class="sidebar_menu">
    <?php
    foreach ($items as $item)
    {
        // Build menu item class
        $class = '';
        if ( ! empty($item['selected']))
        {
            $class .= ' selected';
        }
        if (isset($item['icon']))
        {
            $class .= ' icon_' . $item['icon'];
        }

        $class = trim($class);
        if ($class != '')
        {
            $class = ' class="' . trim($class) . '"';
        }

        echo
            '<a href="' . Model_Backend_Menu::url($item) .'"'
          .     (isset($item['title']) ? ' title="' . HTML::chars($item['title']) . '"' : '')
          .     $class
          . '>'
          .     $item['caption']
          . '</a>';
    }
    ?>
</div>

                </div>
            </div>
        </div>
    </div>
</div>
