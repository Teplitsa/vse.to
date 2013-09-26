<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
// Set up urls
$create_url = URL::to('backend/acl/userprops', array('action'=>'create'), TRUE);
$update_url = URL::to('backend/acl/userprops', array('action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/acl/userprops', array('action'=>'delete', 'id' => '${id}'), TRUE);

$up_url   = URL::to('backend/acl/userprops', array('action'=>'up', 'id' => '${id}'), TRUE);
$down_url = URL::to('backend/acl/userprops', array('action'=>'down', 'id' => '${id}'), TRUE);
if ( ! $desc)
{
    list($up_url, $down_url) = array($down_url, $up_url);
}
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Создать характеристику</a>
</div>

<table class="table">
    <tr class="header">
        <th>&nbsp;&nbsp;&nbsp;</th>

        <?php
            $columns = array(
                'caption'   => 'Название',
                'type_text' => 'Тип',
            );
            echo View_Helper_Admin::table_header($columns, 'acl_uprorder', 'acl_uprdesc');
        ?>
    </tr>

<?php
foreach ($userprops as $userprop)
:
    $_update_url = str_replace('${id}', $userprop->id, $update_url);
    $_delete_url = str_replace('${id}', $userprop->id, $delete_url);
    $_up_url     = str_replace('${id}', $userprop->id, $up_url);
    $_down_url   = str_replace('${id}', $userprop->id, $down_url);

?>
    <tr>        
        <td class="ctl">
            <?php
            if ( ! $userprop->system)
            {
                echo View_Helper_Admin::image_control($_delete_url, 'Удалить  характеристику', 'controls/delete.gif', 'Удалить');
            }
            else
            {
                echo View_Helper_Admin::image('controls/delete_disabled.gif');
            }
            ?>
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать характеристику', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_up_url, 'Переместить вверх', 'controls/up.gif', 'Вверх'); ?>
            <?php echo View_Helper_Admin::image_control($_down_url, 'Переместить вниз', 'controls/down.gif', 'Вниз'); ?>
        </td>

        <?php
        foreach (array_keys($columns) as $field)
        {
            echo '<td>';
            if (isset($userprop->$field) && trim($userprop->$field) !== '')
            {
                echo HTML::chars($userprop->$field);
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
endforeach; //foreach ($userprops as $userprop)
?>
</table>