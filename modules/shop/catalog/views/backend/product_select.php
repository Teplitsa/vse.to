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

// ----- Set up url
list($hist_route, $hist_params) = URL::match(URL::uri_back());

$hist_params['product_id'] = '{{product_id}}';

$select_url = URL::to($hist_route, $hist_params, TRUE);
?>

<?php
// Current section caption
if (isset($section) && $section->id !== NULL)
{
    echo '<h3 class="section_caption">' . HTML::chars($section->caption) . '</h3>';
}
?>

<?php
// Search form
echo $search_form->render();
?>

<table class="products table" id="product_select">
    <tr class="table_header">
        <?php
            $columns = array(
                'caption' => 'Название',
                'active'  => 'Акт.',
            );
            echo View_Helper_Admin::table_header($columns, 'cat_porder', 'cat_pdesc');
        ?>
    </tr>

<?php
foreach ($products as $product)
:
    $_select_url = str_replace('{{product_id}}', $product->id, $select_url);
?>
    <tr>
    <?php
        foreach (array_keys($columns) as $field)
        {
            if ($field == 'caption') {
                echo
                    '<td class="capt' .(empty($product->active) ? ' inactive' : '') . '">'
                  . '   <a href="' . $_select_url . '" class="product_select" id="product_' . $product->id . '">'
                  .         HTML::chars($product->$field)
                  . '   </a>'
                  . '</td>';
            }
            elseif ($field == 'active')
            {
                echo '<td class="c">';

                if ( ! empty($product->$field)) {
                    echo View_Helper_Admin::image('controls/on.gif', 'Да');
                } else {
                    echo View_Helper_Admin::image('controls/off.gif', 'Нет');
                }

                echo '</td>';
            }
            else
            {
                echo '<td>';

                if (isset($product->$field) && trim($product->$field) !== '') {
                    echo HTML::chars($product[$field]);
                } else {
                    echo '&nbsp';
                }

                echo '</td>';
            }
        }
    ?>
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
if ( !Request::current()->in_window())
:
?>
<div style="padding-top: 20px;">
    <div class="buttons">
        <a href="<?php echo URL::back(); ?>" class="button_adv button_cancel"><span class="icon">Назад</span></a>
    </div>
</div>
<?php
endif;
?>
