<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
// ----- Set up urls
$update_url = URL::to('frontend/catalog/products/control', array('action'=>'update','type_id' => Model_SectionGroup::TYPE_EVENT, 'id' => '${id}'), TRUE);
$delete_url = URL::to('frontend/catalog/products/control', array('action'=>'delete', 'id' => '${id}'), TRUE);

$multi_action_uri = URL::uri_to('frontend/catalog/products/control', array('action'=>'multi'), TRUE);
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
                'datetime' => 'Дата',                
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
                      . '   <a href="' . URL::site($product->uri_frontend()) . '">'
                      .         HTML::chars($product->$field)
                      . '   </a>'
                      . '</td>';
                    break;
                case 'datetime':
                    echo
                        '<td class="capt' .(empty($product->active) ? ' inactive' : '') . '">'
                      .         HTML::chars($product->datetime_rus)
                      . '</td>';
                    break;
                
                case 'active':
                    echo '<td class="c">';

                    if ( ! empty($product->$field)) {
                        echo View_Helper_Admin::image('controls/on.gif', 'Да');
                    } else {
                        echo View_Helper_Admin::image('controls/off.gif', 'Нет');
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
));
?>

<?php echo View_Helper_Admin::multi_action_form_close(); ?>