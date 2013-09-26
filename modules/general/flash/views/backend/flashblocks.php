<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$create_url = URL::to('backend/flashblocks', array('action'=>'create'), TRUE);
$update_url = URL::to('backend/flashblocks', array('action'=>'update', 'id' => '{{id}}'), TRUE);
$delete_url = URL::to('backend/flashblocks', array('action'=>'delete', 'id' => '{{id}}'), TRUE);

$up_url   = URL::to('backend/flashblocks', array('action'=>'up', 'id' => '{{id}}'), TRUE);
$down_url = URL::to('backend/flashblocks', array('action'=>'down', 'id' => '{{id}}'), TRUE);
if ( ! $desc)
{
    list($up_url, $down_url) = array($down_url, $up_url);
}
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Создать flash блок</a>
</div>

<?php
if ( ! count($flashblocks))
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

        echo View_Helper_Admin::table_header($columns, 'flashblocks_order', 'flashblocks_desc');
        ?>
    </tr>

<?php
foreach ($flashblocks as $flashblock)
:
    $_delete_url = str_replace('{{id}}', $flashblock->id, $delete_url);
    $_update_url = str_replace('{{id}}', $flashblock->id, $update_url);
    $_up_url     = str_replace('{{id}}', $flashblock->id, $up_url);
    $_down_url   = str_replace('{{id}}', $flashblock->id, $down_url);
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
                echo Kohana::config('flashblocks.names.' . $flashblock->name);
            }
            elseif (isset($flashblock->$field) && trim($flashblock->$field) !== '') {
                echo HTML::chars($flashblock->$field);
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