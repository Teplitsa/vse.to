<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
// Set up urls
$create_url = URL::to('backend/acl/privileges', array('action'=>'create'), TRUE);
$update_url = URL::to('backend/acl/privileges', array('action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/acl/privileges', array('action'=>'delete', 'id' => '${id}'), TRUE);

$up_url   = URL::to('backend/acl/privileges', array('action'=>'up', 'id' => '${id}'), TRUE);
$down_url = URL::to('backend/acl/privileges', array('action'=>'down', 'id' => '${id}'), TRUE);
if ( ! $desc)
{
    list($up_url, $down_url) = array($down_url, $up_url);
}
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Создать привилегию</a>
</div>

<table class="table">
    <tr class="header">
        <th>&nbsp;&nbsp;&nbsp;</th>

        <?php
            $columns = array(
                'caption'   => 'Название',
            );
            echo View_Helper_Admin::table_header($columns, 'acl_prorder', 'acl_prdesc');
        ?>
    </tr>

<?php
foreach ($privileges as $privilege)
:
    $_update_url = str_replace('${id}', $privilege->id, $update_url);
    $_delete_url = str_replace('${id}', $privilege->id, $delete_url);
    $_up_url     = str_replace('${id}', $privilege->id, $up_url);
    $_down_url   = str_replace('${id}', $privilege->id, $down_url);

?>
    <tr>        
        <td class="ctl">
            <?php
            if ( ! $privilege->system)
            {
                echo View_Helper_Admin::image_control($_delete_url, 'Удалить  привилегию', 'controls/delete.gif', 'Удалить');
            }
            else
            {
                echo View_Helper_Admin::image('controls/delete_disabled.gif');
            }
            ?>
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать привилегию', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_up_url, 'Переместить вверх', 'controls/up.gif', 'Вверх'); ?>
            <?php echo View_Helper_Admin::image_control($_down_url, 'Переместить вниз', 'controls/down.gif', 'Вниз'); ?>
        </td>

        <?php
        foreach (array_keys($columns) as $field)
        {
            echo '<td>';
            if (isset($privilege->$field) && trim($privilege->$field) !== '')
            {
                echo HTML::chars($privilege->$field);
            } 
            else
            {
                echo '&nbsp';
            }
            echo '</td>';
        }
        ?>
    </tr>
<?php
endforeach; //foreach ($products as $product)
?>
</table>