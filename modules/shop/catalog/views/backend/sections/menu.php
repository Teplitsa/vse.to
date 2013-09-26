<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
require_once(Kohana::find_file('views', 'backend/sections/menu_branch'));

/*
// Set up urls
$create_url = URL::to('backend/catalog/sections', array(
    'action' => 'create',
    'cat_section_id' => $section_id,
    'cat_sectiongroup_id' => $sectiongroup_id),
TRUE);
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
    $all_url = URL::to('backend/catalog/products', array(
                            'cat_section_id' => '0',
                            'cat_sectiongroup_id' => $sectiongroup_id,
                            'history' => $history), TRUE);
    //$url = URL::to('backend/catalog/products', array('cat_section_id' => '{{id}}', 'history' => $history), TRUE);
}
else
{
    $all_url = URL::to('backend/catalog/products', array(
                            'cat_section_id' => '0',
                            'cat_sectiongroup_id' => $sectiongroup_id));
    //$url = URL::to('backend/catalog/products', array('cat_section_id' => '{{id}}'));
}
$url = URL::self(array(
    'cat_section_id' => '{{id}}',
    'search_text' => '',
    'active' => '-1'
));
?>

<?php
// Tabs to select current section group
if (isset($sectiongroups))
{
    //$tab_url = URL::to('backend/catalog/products', array('cat_sectiongroup_id'=>'{{id}}'));
    $tab_url = URL::self(array('cat_sectiongroup_id' => '{{id}}', 'cat_section_id' => NULL));

    echo '<div class="sectiongroup_tabs"><div class="tabs">';

    foreach ($sectiongroups as $sectiongroup)
    {
        $_tab_url = str_replace('{{id}}', $sectiongroup->id, $tab_url);

        if ($sectiongroup->id == $sectiongroup_id)
        {
            echo '<a href="' . $_tab_url . '" class="selected">' . HTML::chars($sectiongroup->caption) . '</a>';
        }
        else
        {
            echo '<a href="' . $_tab_url . '">' . HTML::chars($sectiongroup->caption) . '</a>';
        }
    }

    echo '</div></div>';
}
?>

<!--
<div class="buttons">
    <a href="<?php //echo $create_url; ?>" class="button button_section_add">Создать раздел</a>
</div>
-->


<div class="sections_menu tree" id="sections">
    <div>
        <?php
        // Highlight current section
        if ($section_id == 0) {
            $selected = ' selected';
        } else {
            $selected = '';
        }
        ?>
        <a
            href="<?php echo $all_url; ?>"
            class="<?php echo $selected; ?>"
            title="Показать все события"
        >
            <strong>Все события</strong>
        </a>
    </div>

<?php

render_sections_menu_branch(
    $sections, NULL,
    $unfolded,
    /*$update_url, $delete_url, $up_url, $down_url, */$toggle_url, $url, $section_id
);
?>

</div>