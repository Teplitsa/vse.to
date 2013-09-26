<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
if ( ! count($path))
{
    return;
}
?>


<?php
$i = count($path);

foreach ($path as $item)
{
    $i--;

    if ($i > 0)
    {
        echo
            '<a href="' . Model_Backend_Menu::url($item) .'"'
          .     (isset($item['title']) ? ' title="' . HTML::chars($item['title']) . '"' : '')
          . '>'
          .     $item['caption']
          . '</a>';

        echo '&nbsp;&raquo;&nbsp;';
    }
    else
    {
        echo '<span>' . $item['caption'] . '</span>';
    }
}
?>