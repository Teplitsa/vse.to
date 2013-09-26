<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
require_once(Kohana::find_file('views', 'backend/sections/menu_branch'));

// Set up urls
/*
$update_url = URL::to('backend/catalog/sections', array('action'=>'update', 'id' => '{{id}}'), TRUE);
$delete_url = URL::to('backend/catalog/sections', array('action'=>'delete', 'id' => '{{id}}'), TRUE);

$up_url   = URL::to('backend/catalog/sections', array('action'=>'up', 'id' => '{{id}}'), TRUE);
$down_url = URL::to('backend/catalog/sections', array('action'=>'down', 'id' => '{{id}}'), TRUE);
if ( ! $desc)
{
    list($up_url, $down_url) = array($down_url, $up_url);
}
 * 
 */

$toggle_url = URL::to('backend/catalog/sections', array('action' => 'toggle', 'id' => '{{id}}', 'toggle' => '{{toggle}}'), TRUE);

$history = Request::current()->param('history');
if (isset($history))
{
    //$url = URL::to('backend/catalog/products', array('cat_section_id' => '{{id}}', 'history' => $history), TRUE);
}
else
{
    //$url = URL::to('backend/catalog/products', array('cat_section_id' => '{{id}}'));
}
$url = URL::self(array('cat_section_id' => '{{id}}'));
?>

<?php
render_sections_menu_branch(
    $sections, $parent,
    $unfolded,
    /*$update_url, $delete_url, $up_url, $down_url, */$toggle_url, $url, NULL
);
?>