<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
if ( ! count($nodes))
{
    // No menu nodes
    return;
}
?>

<ul id="submenu">
    <?php
    foreach ($nodes as $node)
    {
        $selected = isset($path[$node->id]);

        echo
            '<li><a href="' . URL::site($node->frontend_uri) . '"' . ($selected ? ' class="current"' : '') . '>'
          .     HTML::chars($node->caption)
          . '</a></li>';
    }
    ?>

</ul>