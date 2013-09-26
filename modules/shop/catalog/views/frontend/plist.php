<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php require_once(Kohana::find_file('views', 'frontend/products/_helper')); ?>

<h2 class="title"><?php echo HTML::chars($plist->caption); ?></h2>
<div class="separator"></div>

<?php
if ( ! count($products))
{
    echo '<i>Анонсов нет</i>';
    return;
}
?>

<table class="products" cellpadding="0" cellspacing="0">
<?php
$count = count($products);
$rows = ceil($count / $cols);

$i = 0; $cell_i = 0; $row_i = 0;

$products->rewind();
while ($i < $count)
{
    echo '<tr>';
    
    for ($cell_i = 0; $cell_i < $cols; $cell_i++, $i++)
    {
        if ($i >= $count)
        {
            echo '<td class="last"></td>';
            continue;
        }
        
        $product = $products->current();
        $products->next();

        // build brands, subbrands and categories for product
        list($brands_html, $series_html, $cat_html) = brands_and_categories($product);
        
        $url = URL::site($product->uri_frontend());

        echo
            '<td' . ($cell_i >= $cols - 1 ? ' class="last"' : '') . '>'
          .     '<table class="image"><tr><td>';

        if (isset($product->image))
        {
            echo
                    '<a href="' . $url . '">'
          .             HTML::image('public/data/' . $product->image, array(
                            'width' => $product->image_width,
                            'height' => $product->image_height,
                            'alt' => $product->caption))
          .         '</a>';
        }

        echo
                '</td></tr></table>'
          .     '<a href="' . $url . '">'
          .         '<strong>' . HTML::chars($product->caption) . '</strong>'
          .     '</a>'
          .     '<div class="lh20">'
          .         'Бренд: '  . $brands_html . ' <br />'
          .         'Группа: ' . $series_html . ' <br />'
          .         'Раздел: ' . $cat_html    . ' <br />'
          .         'Цена: <span class="price">' . $product->price . '</span><br />'
          //.         '<span classs="delivery">Доставка: 1-2 дня*</span>'
          .     '</div>';

        //echo Widget::render_widget('products', 'add_to_cart', $product);

        echo
            '</td>';
    }

    echo '</tr>';
    
    if ($row_i < $rows - 1)
    {
        echo '<tr><td colspan="5" class="last" style="width:100%;"><div class="table-separator"> </div></td></tr>';
    }

    $row_i ++;
}
?>
</table>