<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
// ----- Set up urls
// Submit results to previous url
$organizers_select_uri = URL::uri_back();

?>

<?php
echo View_Helper_Admin::multi_action_form_open($organizers_select_uri, array('name' => 'organizers_select'));
?>

<table class="organizers_select table">
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
foreach ($organizers as $organizer)
:

?>
    <tr>
        <td class="multi_ctl">
            <?php echo View_Helper_Admin::multi_action_checkbox($organizer->id); ?>
        </td>

    <?php
        foreach (array_keys($columns) as $field)
        {
            switch ($field)
            {
                case 'name':
                    echo
                        '<td>'
                      .             HTML::chars($organizer->$field)
                      . '</td>';
                    break;
                default:
                    echo '<td>';

                    if (isset($organizer->$field) && trim($organizer->$field) !== '') {
                        echo HTML::chars($organizer[$field]);
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
    array('action' => 'organizers_select', 'label' => 'Выбрать', 'class' => 'button_select')
));
?>

<?php echo View_Helper_Admin::multi_action_form_close(); ?>