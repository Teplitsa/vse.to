<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
// ----- Set up urls
// Submit results to previous url
$towns_select_uri = URL::uri_back();

?>

<?php
echo View_Helper_Admin::multi_action_form_open($towns_select_uri, array('name' => 'towns_select'));
?>

<table class="towns_select table">
    <tr class="header">
        <th><?php echo View_Helper_Admin::multi_action_select_all(); ?></th>

        <?php
            $columns = array(
                'name' => 'Название'
            );
            echo View_Helper_Admin::table_header($columns, 'are_torder', 'are_tdesc');
        ?>
    </tr>

<?php
foreach ($towns as $town)
:

?>
    <tr>
        <td class="multi_ctl">
            <?php echo View_Helper_Admin::multi_action_checkbox($town->id); ?>
        </td>

    <?php
        foreach (array_keys($columns) as $field)
        {
            switch ($field)
            {
                case 'name':
                    echo
                        '<td>'
                      .             HTML::chars($town->$field)
                      . '</td>';
                    break;
                default:
                    echo '<td>';

                    if (isset($town->$field) && trim($town->$field) !== '') {
                        echo HTML::chars($town[$field]);
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
echo View_Helper_Admin::multi_actions(array(
    array('action' => 'towns_select', 'label' => 'Выбрать', 'class' => 'button_select')
));
?>

<?php echo View_Helper_Admin::multi_action_form_close(); ?>