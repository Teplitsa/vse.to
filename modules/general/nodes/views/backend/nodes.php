<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
// Set up urls
$create_url = URL::to('backend/nodes', array('action'=>'create', 'nodes_node_id'=>$node_id), TRUE);
$update_url = URL::to('backend/nodes', array('action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/nodes', array('action'=>'delete', 'id' => '${id}'), TRUE);

$up_url   = URL::to('backend/nodes', array('action'=>'up', 'id' => '${id}'), TRUE);
$down_url = URL::to('backend/nodes', array('action'=>'down', 'id' => '${id}'), TRUE);
if ( ! $desc)
{
    list($up_url, $down_url) = array($down_url, $up_url);
}
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Создать страницу</a>
</div>

<?php
if ( ! count($nodes))
    // No users
    return;
?>

<table class="table nodes">
    <tr class="header">
        <th>&nbsp;&nbsp;&nbsp;</th>

        <?php
        $columns = array(
            'caption' => 'Название'
        );

        echo View_Helper_Admin::table_header($columns, 'nodes_order', 'nodes_desc');
        ?>
    </tr>

<?php
    foreach ($nodes as $node)
    :
        $_update_url = str_replace('${id}', $node->id, $update_url);
        $_delete_url = str_replace('${id}', $node->id, $delete_url);
        $_up_url     = str_replace('${id}', $node->id, $up_url);
        $_down_url   = str_replace('${id}', $node->id, $down_url);

        // Highlight current node
        if ($node->id == $node_id) {
            $selected = 'selected';
        } else {
            $selected = '';
        }

        // Highlight inactive nodes
        if ( ! $node->node_active) {
            $active = 'node_inactive';
        } elseif ( ! $node->active) {
            $active = 'inactive';
        } else {
            $active = '';
        }
?>
    <tr class="<?php echo Text::alternate('odd', 'even'); ?>">
        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить раздел', 'controls/delete.gif', 'Удалить'); ?>
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать раздел', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_up_url, 'Переместить вверх', 'controls/up.gif', 'Вверх'); ?>
            <?php echo View_Helper_Admin::image_control($_down_url, 'Переместить вниз', 'controls/down.gif', 'Вниз'); ?>
        </td>
        
        <td class="node <?php echo "$selected $active";?>">
            <a class="node_caption" href="<?php echo $node->get_backend_url(TRUE);//$_update_url;?>" style="margin-left: <?php echo ($node->level-1)*15; ?>px">
                <?php echo HTML::chars($node->caption); ?>
            </a>
        </td>
    </tr>
<?php
    endforeach;
?>
</table>
