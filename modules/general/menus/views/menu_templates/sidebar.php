<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
if ( ! count($nodes))
{
    // No menu nodes
    return;
}
?>

<ul>
    <?php
    foreach ($nodes as $node)
    {
        $selected = isset($path[$node->id]);

        echo
            '<li><a href="' . URL::site($node->frontend_uri) . '"'
          .       ' class="' . Text::alternate('m_market', 'w_sales', 'i_sales')
          .                    ($selected ? ' current' : '') . '"'
          .      '>'
          .     HTML::chars($node->caption)
          . '</a></li>';
    }
    ?>

    <li class="last"></li>
</ul>