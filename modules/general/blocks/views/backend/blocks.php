<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$create_url = URL::to('backend/blocks', array('action'=>'create'), TRUE);
$update_url = URL::to('backend/blocks', array('action'=>'update', 'id' => '{{id}}'), TRUE);
$delete_url = URL::to('backend/blocks', array('action'=>'delete', 'id' => '{{id}}'), TRUE);

$up_url   = URL::to('backend/blocks', array('action'=>'up', 'id' => '{{id}}'), TRUE);
$down_url = URL::to('backend/blocks', array('action'=>'down', 'id' => '{{id}}'), TRUE);
if ( ! $desc)
{
    list($up_url, $down_url) = array($down_url, $up_url);
}
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Создать блок</a>
</div>

<?php
if ( ! count($blocks))
    // No menus
    return;
?>

<table class="table">
    <tr class="header">
        <th>&nbsp;&nbsp;&nbsp;</th>

        <?php
        $columns = array(
            'name'    => 'Положение',
            'caption' => 'Название'
        );

        echo View_Helper_Admin::table_header($columns, 'blocks_order', 'blocks_desc');
        ?>
    </tr>

<?php
foreach ($blocks as $block)
:
    $_delete_url = str_replace('{{id}}', $block->id, $delete_url);
    $_update_url = str_replace('{{id}}', $block->id, $update_url);
    $_up_url     = str_replace('{{id}}', $block->id, $up_url);
    $_down_url   = str_replace('{{id}}', $block->id, $down_url);
?>
    <tr>
        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить блок', 'controls/delete.gif', 'Удалить'); ?>
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать блок', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_up_url, 'Переместить вверх', 'controls/up.gif', 'Вверх'); ?>
            <?php echo View_Helper_Admin::image_control($_down_url, 'Переместить вниз', 'controls/down.gif', 'Вниз'); ?>
        </td>

        <?php
        foreach (array_keys($columns) as $field):
        ?>
            <td>
            <?php
            if ($field == 'name')
            {
                echo Kohana::config('blocks.names.' . $block->name);
            }
            elseif (isset($block->$field) && trim($block->$field) !== '') {
                echo HTML::chars($block->$field);
            } else {
                echo '&nbsp';
            }
            ?>
            </td>

        <?php
        endforeach;
        ?>
    </tr>
<?php
endforeach;
?>
</table>