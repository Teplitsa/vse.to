<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
// Width & height of one image preview
$canvas_height = 110;
$canvas_width  = 110;
if (!$image->id) {
    return;
}
$delete_url = URL::to('frontend/images', array('action'=>'delete', 'id' => $image->id, 'config' => $image->config), TRUE);

?>



<?php

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

<div class="images"style="width: <?php echo $canvas_width+10; ?>px; float: left; margin-left:130px;">
    <div class="image">
        <div class="ctl">
            <?php echo View_Helper_Admin::image_control($delete_url, 'Удалить изображение', 'controls/delete.gif', 'Удалить'); ?>
        </div>

        <div class="img" style="width: <?php echo $canvas_width; ?>px; height: <?php echo $canvas_width; ?>px;">
            <a href="" style="<?php echo $padding; ?>">
                <img src="<?php
                    // Render the last (assumed to be the smallest) thumbnail
                    echo File::url($image->image($i));
                ?>" <?php echo $scale; ?> alt="" />
            </a>
        </div>
    </div>
</div>
