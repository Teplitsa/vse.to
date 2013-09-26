<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
if ( ! count($privileges))
{
    // No menu nodes
    return;
}
?>

<ul>
    <?php
    foreach ($privileges as $privilege)
    {
        if (!$privilege->readable) continue;
        echo
            '<li><a href="' . URL::site($privilege->frontend_uri) . '"' . '>'
          .     HTML::chars($privilege->caption)
          . '</a></li>';
    }
    ?>
</ul>