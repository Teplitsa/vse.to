<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
if (isset($image_id)) {
$image_url = URL::self(array('image_id' => '{{image_id}}'));

// First image - big
$image = $images[$image_id];
echo
    '<a href="' . $image->url(1) . '" target="_blank">'
  .     HTML::image($image->uri(2), array(
            'alt' => $user->name,
            'width' => $image->width2,
            'height' => $image->height2
        ))
  . '</a>'
  . '<br clear="all">';

if (count($images) > 1)
{
    foreach ($images as $image)
    {
        $_url = str_replace('{{image_id}}', $image->id, $image_url);
        // Small thumbnails
        echo
            '<a href="' . $_url . '" class="ajax">'
          .     HTML::image($image->uri(3), array('class' => ($image->id == $image_id ? 'active' : NULL), 'alt' => $user->name))
          . '</a>';
    }
}
}
?>
