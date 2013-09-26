<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$url = URL::to('backend/catalog', array('cat_sectiongroup_id'=>'{{id}}'));
?>

<div class="sectiongroup_tabs">
<div class="tabs">
<?php
foreach ($sectiongroups as $sectiongroup)
{
    $_url = str_replace('{{id}}', $sectiongroup->id, $url);

    if ($sectiongroup->id == $current->id)
    {
        echo '<a href="' . $_url . '" class="selected">' . HTML::chars($sectiongroup->caption) . '</a>';
    }
    else
    {
        echo '<a href="' . $_url . '">' . HTML::chars($sectiongroup->caption) . '</a>';
    }
}
?>
</div>
</div>