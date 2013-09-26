<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
require_once(Kohana::find_file('views', 'frontend/sections/menu_branch'));

$toggle_url = URL::to('frontend/catalog/section', array(
    'sectiongroup_name' => $sectiongroup->name,    
    'path' => '{{path}}',
    'toggle' => '{{toggle}}'
    ), TRUE);
?>
<?php if (isset($sectiongroups)) {
    echo '<div id="sectiongroups">';
    
    $tab_url = URL::to('frontend/catalog/search',array('sectiongroup_name'=>'{{name}}'));

    foreach ($sectiongroups as $secgroup) {
        $_tab_url = str_replace('{{name}}', $secgroup->name, $tab_url);
        if ($secgroup->id == $sectiongroup->id)
        {
            echo '<a class="button selected" href="' . $_tab_url . '">' . HTML::chars($secgroup->caption) . '</a>';
        }
        else
        {
            echo '<a class="button" href="' . $_tab_url . '">' . HTML::chars($secgroup->caption) . '</a>';
        }        
    }
    echo '</div>';
 } ?>
<div class="sections_menu tree" id="sections">
<?php

render_sections_menu_branch(
    $sections, NULL,
    $unfolded,
    $toggle_url, $section_id
);
?>

</div>