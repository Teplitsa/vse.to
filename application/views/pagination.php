<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
    if ($pages_count < 2) {
        return;
    }
?>

<div class="pages">
    <span class="stats">Показано <?php echo "$from-$to из $count"; ?></span>
    <strong>Страницы:</strong>
<?php
    if ($rewind_to_first)
    {
        if ($route === NULL)
            echo '<a href="' . URL::self(array($page_param => 0), $ignored_params) . '" class="rewind '. $class.'">&laquo;</a>';
        else {
            $params[$page_param] = 0;
            echo '<a href="' . URL::to($route,$params) . '" class="rewind '. $class.'">&laquo;</a>';
        }
    }

    for ($i = $l; $i <= $r; $i++)
    {
        if ($i == $page)
        {
            echo '<span class="active">' . ($i+1) .'</span>';
        }
        else
        {
            if ($route === NULL)           
               echo '<a href="' . URL::self(array($page_param => $i), $ignored_params) . '" class="'. $class.'">' . ($i+1) . '</a>';
           else { 
               $params[$page_param] = $i;
               echo '<a href="' . URL::to($route,$params) . '" class="'. $class.'">' . ($i+1) . '</a>';
           }
        }
    }

    if ($rewind_to_last)
    {
        if ($route === NULL)           
            echo '<a href="' . URL::self(array($page_param => $pages_count - 1), $ignored_params) . '" class="rewind '. $class.'">&raquo;</a>';
       else { 
           $params[$page_param] = $pages_count - 1;
           echo '<a href="' . URL::to($route,$params) . '" class="rewind '. $class.'">&raquo;</a>';
       }
        
    }

?>

</div>