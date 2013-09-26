<?php defined('SYSPATH') or die('No direct script access.'); ?>


<?php
list($hist_route, $hist_params) = URL::match(URL::uri_back());

$hist_params['user_id'] = '{{user_id}}';
$select_url  = URL::to($hist_route, $hist_params, TRUE);

$hist_params['user_id'] = 0;
$no_user_url = URL::to($hist_route, $hist_params, TRUE);
?>

<div id="user_select">

<?php
if (isset($group))
{
    echo '<h3 class="group_name">' . HTML::chars($group->name) . '</h3>';
}
?>

<table class="very_light_table">
    <tr class="table_header">
        <?php
        $columns = array(
            'image'      => 'Фото',            
            'email' => 'E-mail',
            'name'  => 'Имя',
            'town_name' => 'Город'
        );

        echo View_Helper_Admin::table_header($columns, 'acl_uorder', 'acl_udesc');
        ?>
    </tr>

<?php
foreach ($users as $user)
:
    $image_info = $user->image(4);    
    $_select_url = str_replace('{{user_id}}', $user->id, $select_url);
?>
    <tr>
        <?php
        foreach (array_keys($columns) as $field):
        ?>
            <td>
            <?php
            if ($field === 'image') {
                echo '<a href="' . $_select_url . '" class="user_select" id="user_' . $user->id . '">' . HTML::image('public/data/' . $image_info['image'], array('width' => $image_info['width'],'height' => $image_info['height'])) . '</a></td>';                
            }              
            if (isset($user->$field) && trim($user->$field) !== '') {
                echo '<a href="' . $_select_url . '" class="user_select" id="user_' . $user->id . '">' . HTML::chars($user->$field) . '</a>';
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
endforeach; //foreach ($users as $user)
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