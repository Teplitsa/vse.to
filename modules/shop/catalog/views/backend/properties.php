<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
// Set up urls
$create_url = URL::to('backend/catalog/properties', array('action'=>'create'), TRUE);
$update_url = URL::to('backend/catalog/properties', array('action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/catalog/properties', array('action'=>'delete', 'id' => '${id}'), TRUE);

$up_url   = URL::to('backend/catalog/properties', array('action'=>'up', 'id' => '${id}'), TRUE);
$down_url = URL::to('backend/catalog/properties', array('action'=>'down', 'id' => '${id}'), TRUE);
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
            echo View_Helper_Admin::table_header($columns, 'cat_prorder', 'cat_prdesc');
        ?>
    </tr>

<?php
foreach ($properties as $property)
:
    $_update_url = str_replace('${id}', $property->id, $update_url);
    $_delete_url = str_replace('${id}', $property->id, $delete_url);
    $_up_url     = str_replace('${id}', $property->id, $up_url);
    $_down_url   = str_replace('${id}', $property->id, $down_url);

?>
    <tr>        
        <td class="ctl">
            <?php
            if ( ! $property->system)
            {
                echo View_Helper_Admin::image_control($_delete_url, 'Удалить свойство', 'controls/delete.gif', 'Удалить');
            }
            else
            {
                echo View_Helper_Admin::image('controls/delete_disabled.gif');
            }
            ?>
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать свойство', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_up_url, 'Переместить вверх', 'controls/up.gif', 'Вверх'); ?>
            <?php echo View_Helper_Admin::image_control($_down_url, 'Переместить вниз', 'controls/down.gif', 'Вниз'); ?>
        </td>

        <?php
        foreach (array_keys($columns) as $field)
        {
            echo '<td>';
            if (isset($property->$field) && trim($property->$field) !== '')
            {
                echo HTML::chars($property->$field);
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