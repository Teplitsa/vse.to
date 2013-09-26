<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
require_once(Kohana::find_file('views', 'frontend/sections/menu_branch'));


$toggle_url = URL::to('frontend/catalog/section', array(
    'sectiongroup_name' => $sectiongroup_name,    
    'path' => '{{path}}',
    'toggle' => '{{toggle}}'
    ), TRUE);

render_sections_menu_branch(
    $sections, $parent,
    $unfolded,
    $toggle_url, NULL
);
?>