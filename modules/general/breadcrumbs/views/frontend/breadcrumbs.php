<?php defined('SYSPATH') or die('No direct script access.'); ?>

<!-- Хлебные крошки -->
<div id="navBreadCrumb">
    <a href="<?php echo URL::site(''); ?>">Главная</a>

    <?php
    for ($i = 0; $i < count($breadcrumbs); $i++)
    {
        $breadcrumb = $breadcrumbs[$i];

        if ($i < count($breadcrumbs) - 1)
        {
            echo
                '&nbsp; »   <a href="' . URL::site($breadcrumb['uri']) . '">'
              .     HTML::chars($breadcrumb['caption'])
              . '</a>';
        }
        else
        {
            // Highlight last breadcrumb
            echo
                '&nbsp; »   <a href="' . URL::site($breadcrumb['uri']) . '">'
              .     '<strong>' . HTML::chars($breadcrumb['caption']) . '</strong>'
              . '</a>';
        }
    }
    ?>
</div>