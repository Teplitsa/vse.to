<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
// Set up urls
$create_url = URL::to('backend/catalog/plists', array('action'=>'create'), TRUE);
$update_url = URL::to('backend/catalog/plists', array('action'=>'update', 'id' => '{{id}}'), TRUE);
$delete_url = URL::to('backend/catalog/plists', array('action'=>'delete', 'id' => '{{id}}'), TRUE);
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Создать список товаров</a>
</div>

<?php
if ( ! count($plists))
    // No products
    return;
?>

<table class="table">
    <tr class="header">
        <th>&nbsp;&nbsp;&nbsp;</th>

        <?php
            $columns = array(
                'name'    => 'Имя',
                'caption' => 'Название'
            );
            echo View_Helper_Admin::table_header($columns, 'cat_lorder', 'cat_ldesc');
        ?>
    </tr>

<?php
foreach ($plists as $plist)
:
    $_update_url = str_replace('{{id}}', $plist->id, $update_url);
    $_delete_url = str_replace('{{id}}', $plist->id, $delete_url);

?>
    <tr>        
        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить список товаров', 'controls/delete.gif', 'Удалить'); ?>
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать список товаров', 'controls/edit.gif', 'Редактировать'); ?>
        </td>

        <?php
        foreach (array_keys($columns) as $field)
        {
            echo '<td>';
            if (isset($plist->$field) && trim($plist->$field) !== '')
            {
                echo HTML::chars($plist->$field);
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
endforeach;
?>
</table>