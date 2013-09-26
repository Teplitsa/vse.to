<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
require_once(Kohana::find_file('views', 'backend/sections/list_branch'));

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

$multi_action_uri = URL::uri_to('backend/catalog/sections', array('action'=>'multi'), TRUE);

$toggle_url = URL::to('backend/catalog/sections', array('action' => 'toggle', 'id' => '{{id}}', 'toggle' => '{{toggle}}'), TRUE);
?>

<?php
// Tabs to select current section group
if (isset($sectiongroups))
{
    $tab_url = URL::to('backend/catalog/sections', array('cat_sectiongroup_id'=>'{{id}}'));

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

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_section_add">Создать раздел</a>
</div>

<?php
if ( ! $sections->has_children())
    return;
?>


<div class="sections tree" id="sections">

<?php echo View_Helper_Admin::multi_action_form_open($multi_action_uri); ?>

<table class="very_light_table">
    <tr class="table_header">
        <th><?php echo View_Helper_Admin::multi_action_select_all(); ?></th>

        <?php
            $columns = array(
                'caption' => 'Название',
                'active'  => 'Акт.'
            );
            echo View_Helper_Admin::table_header($columns, 'cat_sorder', 'cat_sdesc');
        ?>

        <th></th>
    </tr>

<?php
render_sections_branch(
    $sections, NULL,
    $unfolded,
    $update_url, $delete_url, $up_url, $down_url, $toggle_url, $section_id
);
?>

</table>

<?php
echo View_Helper_Admin::multi_actions(array(
    array('action' => 'multi_delete', 'label' => 'Удалить', 'class' => 'button_delete')
));
?>
<?php echo View_Helper_Admin::multi_action_form_close(); ?>

</div>