<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
// Width & height of one image preview
$canvas_height = 110;
$canvas_width  = 110;

$create_url = URL::to('backend/tags', array(
    'action'=>'create',
    'owner_type' => $owner_type,
    'owner_id' => $owner_id,
    'config' => $config
), TRUE);
$update_url = URL::to('backend/tags', array('action'=>'update', 'id' => '${id}', 'config' => $config), TRUE);
$delete_url = URL::to('backend/tags', array('action'=>'delete', 'id' => '${id}', 'config' => $config), TRUE);

$up_url   = URL::to('backend/tags', array('action'=>'up', 'id' => '${id}'), TRUE);
$down_url = URL::to('backend/tags', array('action'=>'down', 'id' => '${id}'), TRUE);
if ( ! $desc)
{
    list($up_url, $down_url) = array($down_url, $up_url);
}
?>

<div class="caption">Теги</div>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Добавить Теги</a>
</div>

<div class="tags">
<?php
foreach ($tags as $tag)
:
    $tag->config = $config;

    $_update_url = str_replace('${id}', $tag->id, $update_url);
    $_delete_url = str_replace('${id}', $tag->id, $delete_url);
    $_up_url     = str_replace('${id}', $tag->id, $up_url);
    $_down_url   = str_replace('${id}', $tag->id, $down_url);
?>
    <div class="tag">
        <div class="ctl">
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить тег', 'controls/delete.gif', 'Удалить'); ?>
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать тег', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_up_url, 'Переместить вверх', 'controls/left.gif', 'Вверх'); ?>
            <?php echo View_Helper_Admin::image_control($_down_url, 'Переместить вниз', 'controls/right.gif', 'Вниз'); ?>
        </div>

        
        <div class="img" style="width: <?php echo $canvas_width; ?>px; height: <?php echo $canvas_width; ?>px;">
            <a href="<?php echo $_update_url; ?>" style="<?php echo $padding; ?>">
                <img src="<?php
                    echo File::url($image->image($i));
                ?>" <?php echo $scale; ?> alt="" />
            </a>
        </div>
    </div>
<?php
endforeach;
?>
</div>