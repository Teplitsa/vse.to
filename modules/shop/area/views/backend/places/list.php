<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
if ($town === NULL || $town->id === NULL)
{
    $town_alias = '';
}
else
{
    $town_alias = (string) $town->alias;
}

// ----- Set up urls
$create_url = URL::to('backend/area/places', array('action'=>'create', 'are_town_alias' => $town_alias), TRUE);
$update_url = URL::to('backend/area/places', array('action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/area/places', array('action'=>'delete', 'id' => '${id}'), TRUE);

$multi_action_uri = URL::uri_to('backend/area/places', array('action'=>'multi'), TRUE);
?>

<div class="buttons"> 
    <a href="<?php echo $create_url; ?>" class="button button_add">Создать</a>

</div>

<?php
// Current section caption
if (isset($town) && $town->id !== NULL)
{
    echo '<h3 class="town_caption">' . $town->name . '</h3>';
}
?>

<?php
if ( ! count($places))
    // No places
    return;
?>

<?php echo View_Helper_Admin::multi_action_form_open($multi_action_uri); ?>
<table class="places table">
    <tr class="header">
        <th><?php echo View_Helper_Admin::multi_action_select_all(); ?></th>

        <?php
            $columns = array(
                'name' => 'Название',
                'town_name' => 'Город',
                'ispeed'  => 'Интернет',
            );
            echo View_Helper_Admin::table_header($columns, 'are_porder', 'are_pdesc');
        ?>

        <th></th>
    </tr>

<?php
foreach ($places as $place)
:
    $_delete_url = str_replace('${id}', $place->id, $delete_url);
    $_update_url = str_replace('${id}', $place->id, $update_url);
    
?>
    <tr>
        <td class="multi_ctl">
            <?php echo View_Helper_Admin::multi_action_checkbox($place->id); ?>
        </td>

    <?php
        foreach (array_keys($columns) as $field)
        {
            switch ($field)
            {                
                case 'ispeed':
                    echo '<td class="nowrap">';

                    echo HTML::chars(Model_Place::$_ispeed_options[$place->ispeed[$field]]);

                    echo '</td>';
                    break;

                default:
                    echo '<td class="nowrap">';

                    if (isset($place->$field) && trim($place->$field) !== '') {
                        echo HTML::chars($place[$field]);
                    } else {
                        echo '&nbsp';
                    }

                    echo '</td>';
            }
        }    
    ?>
        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать площадку', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить площадку', 'controls/delete.gif', 'Удалить'); ?>
        </td>
    </tr>
<?php
endforeach; //foreach ($places as $place)
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