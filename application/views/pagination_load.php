<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
    if (($pages_count < 2) || ($pages_count<=$page)) {
        return;
    }
?>

<div class="b-show-more">
    <?php 
    $next = (int)$page+1;
    echo '<a href="' . URL::self(array($page_param => $next), $ignored_params) . '">'.'подгрузить еще'.'</a>';
    ?>
</div>
