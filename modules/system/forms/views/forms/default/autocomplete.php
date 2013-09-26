<?php defined('SYSPATH') or die('No direct script access.'); ?>

<div class="autocomplete">
    <?php
    for ($i = 0; $i < count($items); $i++)
    {
        $item = $items[$i];
        
        echo
            '<div class="item">'
          . '   <div class="item_i">' . $i . '</div>'
          . '   <div class="item_value">' . HTML::chars($item['value']) . '</div>'
          . '   <div class="item_caption">' . $item['caption'] . '</div>'
          . '</div>';
    }
    ?>
</div>