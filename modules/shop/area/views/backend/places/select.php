<?php defined('SYSPATH') or die('No direct script access.'); ?>


<?php
list($hist_route, $hist_params) = URL::match(URL::uri_back());

$hist_params['place_id'] = '{{place_id}}';
$select_url  = URL::to($hist_route, $hist_params, TRUE);

$hist_params['place_id'] = 0;
$no_place_url = URL::to($hist_route, $hist_params, TRUE);

?>

<div id="place_select">

<?php
if (isset($group))
{
    echo '<h3 class="town_name">' . HTML::chars($town->name) . '</h3>';
}
?>

<table class="very_light_table">
    <tr class="table_header">
        <?php
        $columns = array(
            'image'      => 'Фото',            
            'name'  => 'Имя'
        );

        echo View_Helper_Admin::table_header($columns, 'are_porder', 'are_pdesc');
        ?>
    </tr>

<?php
foreach ($places as $place)
:
    $image_info = $place->image(4);    
    $_select_url = str_replace('{{place_id}}', $place->id, $select_url);
?>
    <tr>
        <?php
        foreach (array_keys($columns) as $field):
        ?>
            <td>
            <?php
            if ($field === 'image') {
                echo '<a href="' . $_select_url . '" class="place_select" id="place_' . $place->id . '">' . HTML::image('public/data/' . $image_info['image'], array('width' => $image_info['width'],'height' => $image_info['height'])) . '</a></td>';                
            }              
            if (isset($place->$field) && trim($place->$field) !== '') {
                echo '<a href="' . $_select_url . '" class="place_select" id="place_' . $place->id . '">' . HTML::chars($place->$field) . '</a>';
            } else {
                echo '&nbsp';
            }
            ?>
            </td>

        <?php
        endforeach;
        ?>
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
if (empty($_GET['window']))
:
?>
<div class="back">
    <div class="buttons">
        <a href="<?php echo URL::back(); ?>" class="button_adv button_cancel"><span class="icon">Назад</span></a>
    </div>
</div>
<?php
endif;
?>

</div>