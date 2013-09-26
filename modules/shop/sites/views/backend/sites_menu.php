<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
Layout::instance()->add_style(Modules::uri('sites') . '/public/css/backend/sites.css');

$site_url = URL::to('backend', array('site_id'=>'{{id}}'));
?>

<div class="sites_menu">
<?php
foreach ($sites as $site)
{
    $_site_url = str_replace('{{id}}', $site->id, $site_url);

    if ($site->id == $current->id)
    {
        // Highlight current site
        echo
            '<a href="' . $_site_url . '" class="selected">' . $site->caption . '</a>';
    }
    else
    {
        echo
            '<a href="' . $_site_url . '">' . $site->caption . '</a>';
    }
}
?>
</div>
