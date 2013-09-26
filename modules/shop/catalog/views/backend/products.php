<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
if ($section === NULL || $section->id === NULL)
{
    $section_id = '0';
}
else
{
    $section_id = (string) $section->id;
}

// ----- Set up urls
$create_url = URL::to('backend/catalog/products', array('action'=>'create', 'cat_section_id' => $section_id), TRUE);
$update_url = URL::to('backend/catalog/products', array('action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/catalog/products', array('action'=>'delete', 'id' => '${id}'), TRUE);

$multi_action_uri = URL::uri_to('backend/catalog/products', array('action'=>'multi'), TRUE);
?>

<div class="buttons"> 
    <a href="<?php echo $create_url; ?>" class="button button_add">Создать</a>

</div>

<?php
// Current section caption
if (isset($section) && $section->id !== NULL)
{
    echo '<h3 class="section_caption">' . $section->full_caption . '</h3>';
}
?>

<?php
// Search form
echo $search_form->render();
?>

<?php
if ( ! count($products))
    // No products
    return;
?>

<?php echo View_Helper_Admin::multi_action_form_open($multi_action_uri); ?>
<table class="products table">
    <tr class="header">
        <th><?php echo View_Helper_Admin::multi_action_select_all(); ?></th>

        <?php
            $columns = array(
                'caption' => 'Название',
                'active'  => 'Акт.',
            );
            echo View_Helper_Admin::table_header($columns, 'cat_porder', 'cat_pdesc');
        ?>

        <th></th>
    </tr>

<?php
foreach ($products as $product)
:
    $_delete_url = str_replace('${id}', $product->id, $delete_url);
    $_update_url = str_replace('${id}', $product->id, $update_url);
    
?>
    <tr>
        <td class="multi_ctl">
            <?php echo View_Helper_Admin::multi_action_checkbox($product->id); ?>
        </td>

    <?php
        foreach (array_keys($columns) as $field)
        {
            switch ($field)
            {
                case 'caption':
                    echo
                        '<td class="capt' .(empty($product->active) ? ' inactive' : '') . '">'
                      . '   <a href="' . $_update_url . '">'
                      .         HTML::chars($product->$field)
                      . '   </a>'
                      . '</td>';
                    break;
                
                case 'active':
                    echo '<td class="c">';

                    if (!$product->visible) {
                        echo View_Helper_Admin::image('controls/invisible.gif', 'Удален пользователем');    
                    } else {                    
                        if ( ! empty($product->$field)) {
                            echo View_Helper_Admin::image('controls/on.gif', 'Да');
                        } else {
                            echo View_Helper_Admin::image('controls/off.gif', 'Нет');
                        }
                    }

                    echo '</td>';
                    break;

                default:
                    echo '<td class="nowrap">';

                    if (isset($product->$field) && trim($product->$field) !== '') {
                        echo HTML::chars($product[$field]);
                    } else {
                        echo '&nbsp';
                    }

                    echo '</td>';
            }
        }
    ?>
        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать событие', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить событие', 'controls/delete.gif', 'Удалить'); ?>
        </td>
    </tr>
<?php
endforeach; //foreach ($products as $product)
?>
</table>

<?php
if (isset($pagination))
{
    echo $pagination;
}
?>

<?php
echo View_Helper_Admin::multi_actions(array(
    array('action' => 'multi_delete', 'label' => 'Удалить', 'class' => 'button_delete'),
    array('action' => 'multi_link', 'label' => 'Привязать', 'class' => 'button_link'),
));
?>

<?php echo View_Helper_Admin::multi_action_form_close(); ?>