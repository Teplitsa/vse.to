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

// Presume that the results should be submitted to the previous url
$products_select_uri = URL::uri_back();
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

<?php echo View_Helper_Admin::multi_action_form_open($products_select_uri); ?>
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
    </tr>

<?php
foreach ($products as $product)
:
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
                      .     HTML::chars($product->$field)
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
echo View_Helper_Admin::multi_actions(array(
    array('action' => 'multi_select', 'label' => 'Выбрать', 'class' => 'button_select')
));
?>

<?php echo View_Helper_Admin::multi_action_form_close(); ?>