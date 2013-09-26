<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php require_once(Kohana::find_file('views', 'frontend/products/_helper')); ?>

<h1>Корзина</h1>
<?php
if ( ! count($cartproducts))
{
    echo '<i>В корзине нет товаров</i>';
    return;
}
?>

<?php
$remove_url = URL::to('frontend/cart', array('action' => 'remove_product', 'product_id' => '{{id}}'), TRUE);
?>

<?php echo $form->render_form_open(); ?>
<?php echo $form->render_messages(); ?>
<table cellspacing="0" cellpadding="0" class="cart">
    <tr>
        <th></th>        
        <th>Артикул</th>
        <th>Название</th>
        <th>Категория</th>
        <th>Цена, руб.</th>
        <th>Количество</th>        
        <th>&nbsp;</th>
    </tr>
    <?php
    foreach ($cartproducts as $cartproduct)
    {
        // Corresponding product in catalog
        $product = $cartproduct->product;
        $image_info = $product->image(4);
        list($brands_html,,) = brands_and_categories($product);

        $_product_url = URL::site($product->uri_frontend());
        $_remove_url = str_replace('{{id}}', $cartproduct->product_id, $remove_url);
        
        echo
            '<tr>'
          .     '<td><a href="' . $_product_url . '">' . HTML::image('public/data/' . $image_info['image'], array('width' => $image_info['width'],'height' => $image_info['height'])) . '</a></td>'
          .     '<td>' . $product->marking . '</td>'
          .     '<td><a href="' . $_product_url . '">' . $product->caption . '</a></td>'
          .     '<td>' . $brands_html . '</td>'
          .     '<td>' . $cartproduct->price . '</td>'
          .     '<td>' 
          .         $form->get_component('quantities[' . $cartproduct->product_id . ']')->render_input()
          .         '<div class="errors">' . $form->get_component('quantities[' . $cartproduct->product_id . ']')->render_errors() . '</div>'
          . '</td>'                
          .     '<td><a href="' . $_remove_url . '" class="delete ajax">Удалить&nbsp;этот&nbsp;товар</a></td>'
          . '</tr>';

    }
    ?>
    <tr class="bottom">
        <td colspan="5"><strong></strong></td>
        <td colspan="2">
            <strong>ИТОГО: <?php echo $cart->total_sum ?></strong><br clear="all" />
            <input type="image" name="submit" src="<?php echo URL::base(FALSE) . 'public/css/img/order.gif'; ?>" alt="оформить заказ" class="OrderFinal" />
        </td>
    </tr>
</table>
<?php echo $form->render_form_close(); ?>
