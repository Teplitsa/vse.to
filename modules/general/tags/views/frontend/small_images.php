<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
// Width & height of one image preview
$canvas_height = 110;
$canvas_width  = 110;

$create_url = URL::to('frontend/images',array('action' => 'ajax_create','owner_type' => $owner_type,'owner_id' => $owner_id,'config' => $config));

?>


<div class="buttons">
    <a data-toggle="modal" href="#ImgModal" class="request-link button">Загрузить фото</a>         
</div>

<div class="images">
<?php
foreach ($images as $image)
:
    $image->config = $config;

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

        <div class="img" style="width: <?php echo $canvas_width; ?>px; height: <?php echo $canvas_width; ?>px;">
            <img src="<?php
                echo File::url($image->image($i));
            ?>" <?php echo $scale; ?> alt="" />
        </div>
    </div>
<?php
endforeach;
?>
</div>