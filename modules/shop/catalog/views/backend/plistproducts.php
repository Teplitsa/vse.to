<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
// ----- Set up urls
$add_uri = URL::uri_to(
    'backend/catalog/plistproducts',
    array('action' => 'add', 'plist_id' => $plist->id),
    TRUE
);

$products_select_url = URL::to('backend/catalog/products', array('site_id' => $plist->site_id, 'action' => 'products_select', 'history' => $add_uri), TRUE);

$update_url = URL::to('backend/catalog/products', array('action' => 'update', 'id' => '{{id}}'), TRUE);
$delete_url = URL::to('backend/catalog/plistproducts', array('action'=>'delete', 'id' => '{{id}}'), TRUE);

$up_url   = URL::to('backend/catalog/plistproducts', array('action'=>'up', 'id' => '{{id}}'), TRUE);
$down_url = URL::to('backend/catalog/plistproducts', array('action'=>'down', 'id' => '{{id}}'), TRUE);
if ( ! $desc)
{
    list($up_url, $down_url) = array($down_url, $up_url);
}

$multi_action_uri = URL::uri_to('backend/catalog/plistproducts', array('action'=>'multi'), TRUE);
?>

<div class="buttons">
    <a href="<?php echo $products_select_url; ?>" class="button button_add">Добавить товары в список</a>
</div>

<?php
if ( ! count($plistproducts))
    // No products
    return;
?>

<?php echo View_Helper_Admin::multi_action_form_open($multi_action_uri); ?>
<table class="products table">
    <tr class="header">
        <th><?php echo View_Helper_Admin::multi_action_select_all(); ?></th>

        <?php
            $columns = array(
                'caption' => 'Название'
            );
            echo View_Helper_Admin::table_header($columns, 'cat_lporder', 'cat_lpdesc');
        ?>

        <th></th>
    </tr>

<?php
foreach ($plistproducts as $plistproduct)
:
    $_delete_url = str_replace('{{id}}', $plistproduct->id, $delete_url);
    $_update_url = str_replace('{{id}}', $plistproduct->product_id, $update_url);
?>
    <tr>
        <td class="multi_ctl">
            <?php echo View_Helper_Admin::multi_action_checkbox($plistproduct->id); ?>
        </td>

    <?php
        foreach (array_keys($columns) as $field)
        {
            switch ($field)
            {
                case 'caption':
                    echo
                        '<td class="capt' .(empty($plistproduct->active) ? ' inactive' : '') . '">'
                      . '   <a href="' . $_update_url . '">'
                      .         HTML::chars($plistproduct->$field)
                      . '   </a>'
                      . '</td>';
                    break;
                
                case 'active':
                    echo '<td class="c">';

                    if ( ! empty($plistproduct->$field)) {
                        echo View_Helper_Admin::image('controls/on.gif', 'Да');
                    } else {
                        echo View_Helper_Admin::image('controls/off.gif', 'Нет');
                    }

                    echo '</td>';
                    break;

                default:
                    echo '<td>';

                    if (isset($plistproduct->$field) && trim($plistproduct->$field) !== '') {
                        echo HTML::chars($plistproduct[$field]);
                    } else {
                        echo '&nbsp';
                    }

                    echo '</td>';
            }
        }
    ?>
        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить товар из списка', 'controls/delete.gif', 'Удалить'); ?>
        </td>
    </tr>
<?php
endforeach;
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
    array('action' => 'multi_delete', 'label' => 'Удалить из списка', 'class' => 'button_delete')
));
?>

<?php echo View_Helper_Admin::multi_action_form_close(); ?>