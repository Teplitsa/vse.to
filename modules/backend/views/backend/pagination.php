<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
    if ($pages_count < 2) {
        return;
    }
?>

<div class="pages">
    Страницы:
<?php
    if ($rewind_to_first)
    {
        echo '<a href="' . URL::self(array($page_param => 0)) . '" class="rewind">&laquo;</a>';
    }

    for ($i = $l; $i <= $r; $i++)
    {
        if ($i == $page)
        {
            echo '<span>' . ($i+1) .'</span>';
        }
        else
        {
            echo '<a href="' . URL::self(array($page_param => $i)) . '">' . ($i+1) . '</a>';
        }
    }

    if ($rewind_to_last)
    {
        echo '<a href="' . URL::self(array($page_param => $pages_count - 1)) . '" class="rewind">&raquo;</a>';
    }


    echo '<a class="all" href="' . URL::self(array($page_param => 'all')) . '">все</a>';
?>

</div>