<?php defined('SYSPATH') or die('No direct script access.'); ?>


<?php
list($hist_route, $hist_params) = URL::match(URL::uri_back());

$hist_params['lecturer_id'] = '{{lecturer_id}}';
$select_url  = URL::to($hist_route, $hist_params, TRUE);

$hist_params['lecturer_id'] = 0;
$no_lecturer_url = URL::to($hist_route, $hist_params, TRUE);
?>

<div id="lecturer_select">
    
<div class="no_lecturer">
    <em><a href="<?php echo $no_lecturer_url; ?>" class="lecturer_select" id="lecturer_0">&raquo; Без лектора</a></em>
</div>

<table class="very_light_table">
    <tr class="table_header">
        <?php
        $columns = array(
            'image'      => 'Фото',            
            'name'  => 'Имя'
        );

        echo View_Helper_Admin::table_header($columns, 'acl_lorder', 'acl_ldesc');
        ?>
    </tr>

<?php
foreach ($lecturers as $lecturer)
:
    $image_info = $lecturer->image(4); 
    $_select_url = str_replace('{{lecturer_id}}', $lecturer->id, $select_url);
?>
    <tr>
        <?php
        foreach (array_keys($columns) as $field):
        ?>
            <td>
            <?php
            if ($field === 'image') {
                echo '<a href="' . $_select_url . '" class="lecturer_select" id="lecturer_' . $lecturer->id . '">' . HTML::image('public/data/' . $image_info['image'], array('width' => $image_info['width'],'height' => $image_info['height'])) . '</a></td>';                
            }            
            if (isset($lecturer->$field) && trim($lecturer->$field) !== '') {
                echo '<a href="' . $_select_url . '" class="lecturer_select" id="lecturer_' . $lecturer->id . '">' . HTML::chars($lecturer->$field) . '</a>';
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
endforeach; //foreach ($lecturers as $lecturer)
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