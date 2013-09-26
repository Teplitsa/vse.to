<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
// Width & height of one image preview
$canvas_height = 110;
$canvas_width  = 110;

if ( ! isset($image->id))
{
    $create_url = URL::to('backend/images', array(
        'action'=>'create',
        'owner_type' => $image->owner_type,
        'owner_id' => $image->owner_id,
        'config' => $image->config
    ), TRUE);
}
else
{
    $update_url = URL::to('backend/images', array('action'=>'update', 'id' => $image->id, 'config' => $image->config), TRUE);
    $delete_url = URL::to('backend/images', array('action'=>'delete', 'id' => $image->id, 'config' => $image->config), TRUE);
}
?>

<?php
if ( ! isset($image->id))
:
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Создать изображение</a>
</div>

<?php
else
:
    $i = count($image->get_thumb_settings()); // Number of the last (the smallest) thumb

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

<div class="images">
    <div class="image">
        <div class="ctl">
            <?php echo View_Helper_Admin::image_control($delete_url, 'Удалить изображение', 'controls/delete.gif', 'Удалить'); ?>
            <?php echo View_Helper_Admin::image_control($update_url, 'Редактировать изображение', 'controls/edit.gif', 'Редактировать'); ?>
        </div>

        <div class="img" style="width: <?php echo $canvas_width; ?>px; height: <?php echo $canvas_width; ?>px;">
            <a href="<?php echo $update_url; ?>" style="<?php echo $padding; ?>">
                <img src="<?php
                    // Render the last (assumed to be the smallest) thumbnail
                    echo File::url($image->image($i));
                ?>" <?php echo $scale; ?> alt="" />
            </a>
        </div>
    </div>
</div>

<?php
endif;
?>