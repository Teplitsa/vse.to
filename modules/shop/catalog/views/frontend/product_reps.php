<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php if (count($reps)) {?>
<?php
    $rep_url = URL::to('frontend/acl/users/control', array('action' => 'control','user_id' => '{{user_id}}'));
?>
<div id="product-reps">
<h3>Организаторы:</h3>
    
<table>
    <tr class="table_header">
        <?php
        $columns = array(
            'image'      => 'Фото',            
            'name'  => 'Имя'
        );
        
        ?>
    </tr>

<?php
foreach ($reps as $rep_product_url => $prod)
:
    $rep = $prod->role;
    $image_info = $rep->image(4); 
    $_rep_url = str_replace('{{user_id}}', $rep->id, $rep_url);
?>
    <tr>
        <?php
        foreach (array_keys($columns) as $field):
        ?>
            <td>
            <?php
            if ($field === 'image') {
                echo '<a href="' . $_rep_url . '" class="product_reps" id="rep_' . $rep->id  . '">' . HTML::image('public/data/' . $image_info['image'], array('width' => $image_info['width'],'height' => $image_info['height'])) . '</a></td>';                
            }
            if ($field === 'name') {
                $name = (!empty($rep->organization))?$rep->organization:$rep->name; 
                echo '<a href="' . $_rep_url . '" class="product_reps" id="rep_' . $rep->id . '">' . $name . '</a>';
                continue;
            }            
            if (isset($rep->$field) && trim($lecturer->$field) !== '') {
                echo '<a href="' . $_select_url . '" class="lecturer_select" id="lecturer_' . $lecturer->id . '">' . HTML::chars($lecturer->$field) . '</a>';
            } else {
                echo '&nbsp';
            }
            ?>
            </td>
        <?php
        endforeach; ?>
        <td><?php if ($prod->active) { ?>
            <a href="<?php echo URL::site($rep_product_url); ?>">Просмотр...</a>
            <?php } ?>
        </td>
    </tr>
<?php
endforeach; //foreach ($reps as $rep)
?>
</table>

<?php
if (isset($pagination))
{
    echo $pagination;
}
}
?>

</div>