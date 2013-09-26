<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
if (isset($links)) {
    
echo '<div class="soc-link">';    
foreach ($links as $link) {
    $link_name = $link->name;
    if (!empty($user->$link_name)) {
        echo  
               '<a href="'.$user->$link_name.'" class="button '.$link->name.'">'.strtolower(substr($link->caption,0,1)).'</a>';   
        
    }
}
echo '</div>';

} ?>
