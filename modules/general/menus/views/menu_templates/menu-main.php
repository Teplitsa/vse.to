<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
if ( ! count($nodes))
{
    // No menu nodes
    return;
}
?>

<ul class="main-menu">
    <?php
    foreach ($nodes as $node)
    {
        // Build menu item class
        $selected = isset($path[$node->id]);        

        echo
            '<li class= '.$selected.' ><a href="' . URL::site($node->frontend_uri) .'"'
          .     (isset($item['title']) ? ' title="' . HTML::chars($item['title']) . '"' : '')
          . '>'
          .     HTML::chars($node->caption)
          . '</a></li>';
    }
    ?>
</ul>