<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$create_url = URL::to('backend/coupons', array('action'=>'create'), TRUE);
$update_url = URL::to('backend/coupons', array('action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/coupons', array('action'=>'delete', 'id' => '${id}'), TRUE);
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Создать купон</a>
</div>

<?php
if ( ! count($coupons))
    return;
?>

<table class="table">
    <tr class="header">
        <?php
        $columns = array(
            'code'                => 'Код',
            'type'                => array('label' => 'Тип', 'sort_field' => 'multiple'),
            'discount_formatted'  => 'Скидка',
            'description_short'   => array('label' => 'Описание', 'sortable' => FALSE),
            'date_from_formatted' => array('label' => 'Действ. с', 'sort_field' => 'valid_after'),
            'date_to_formatted'   => array('label' => 'Действ. по', 'sort_field' => 'valid_before'),
            'user_name'           => 'Пользователь',
            'site_captions'       => array('label' => 'Магазины', 'sortable' => FALSE),
        );

        echo View_Helper_Admin::table_header($columns, 'coupons_order', 'coupons_desc');
        ?>
        
        <th></th>
    </tr>

<?php
foreach ($coupons as $coupon)
:
    $_update_url = str_replace('${id}', $coupon->id, $update_url);
    $_delete_url = str_replace('${id}', $coupon->id, $delete_url);
?>
    <tr>
        <?php
        foreach (array_keys($columns) as $field):
        ?>
            <td>
            <?php
            if (isset($coupon->$field) && trim($coupon->$field) !== '') {
                echo HTML::chars($coupon->$field);
            } else {
                echo '&nbsp';
            }
            ?>
            </td>

        <?php
        endforeach;
        ?>

        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать купон', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить купон', 'controls/delete.gif', 'Удалить'); ?>
        </td>
    </tr>
<?php
endforeach;
?>
</table>
