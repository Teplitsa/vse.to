<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
    if ($pages_count < 2) {
        return;
    }
?>

Страницы:
<?php
    if ($rewind_to_first)
    {
        echo '<a href="' . URL::self(array($page_param => 0), $ignored_params) . '" class="rewind">&laquo;</a>';
    }

    for ($i = $l; $i <= $r; $i++)
    {
        if ($i == $page)
        {
            echo '<span class="active">' . ($i+1) .'</span>';
        }
        else
        {
            echo '<a href="' . URL::self(array($page_param => $i), $ignored_params) . '">' . ($i+1) . '</a>';
        }
    }

    if ($rewind_to_last)
    {
        echo '<a href="' . URL::self(array($page_param => $pages_count - 1), $ignored_params) . '" class="rewind">&raquo;</a>';
    }
?>

