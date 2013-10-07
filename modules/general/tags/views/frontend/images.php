<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
// Width & height of one image preview
$canvas_height = 110;
$canvas_width  = 110;

$create_url = URL::to('frontend/images', array(
    'action'=>'create',
    'owner_type' => $owner_type,
    'owner_id' => $owner_id,
    'config' => $config
), TRUE);
$update_url = URL::to('frontend/images', array('action'=>'update', 'id' => '${id}', 'config' => $config), TRUE);
$delete_url = URL::to('frontend/images', array('action'=>'delete', 'id' => '${id}', 'config' => $config), TRUE);

$up_url   = URL::to('frontend/images', array('action'=>'up', 'id' => '${id}'), TRUE);
$down_url = URL::to('frontend/images', array('action'=>'down', 'id' => '${id}'), TRUE);
if ( ! $desc)
{
    list($up_url, $down_url) = array($down_url, $up_url);
}
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Добавить изображение</a>
</div>

<div class="images">
<?php
foreach ($images as $image)
:
    $image->config = $config;

    $_update_url = str_replace('${id}', $image->id, $update_url);
    $_delete_url = str_replace('${id}', $image->id, $delete_url);
    $_up_url     = str_replace('${id}', $image->id, $up_url);
    $_down_url   = str_replace('${id}', $image->id, $down_url);

     // Number the thumb to render
    $i = count($image->get_thumb_settings());
    if ($i > 3)
    {
        $i = 3;
    }

    $width  = $image->__get("width$i");
    $height = $image->__get("height$i");

    if ($width > $canvas_width || $height > $canvas_height)
    {
        if ($width > $height)
        {
            $scale = ' style="width: ' . $canvas_width . 'px;"';
            $height = $height * $canvas_width / $width;
            $width = $canvas_width;
        }
        else
        {
            $scale = ' style="height: ' . $canvas_height . 'px;"';
            $width = $width * $canvas_height / $height;
            $height = $canvas_height;
        }
    }
    else
    {
        $scale = '';
    }

    if ($canvas_height > $height)
    {
        $padding = ($canvas_height - $height) >> 1; // ($canvas_height - $height) / 2
        $padding = ' padding-top: ' . $padding . 'px;';
    }
    else
    {
        $padding = '';
    }    
?>
    <div class="image">
        <div class="ctl">
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить изображение', 'controls/delete.gif', 'Удалить'); ?>
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать изображение', 'controls/edit.gif', 'Редактировать'); ?>
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