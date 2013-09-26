<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$id = $view->id;

if ( ! Request::$is_ajax)
{
    echo
        '<div id="' . $id . '_layer">'
      .     '<div id="' . $id . '_blocker" class="popup_blocker"></div>'
      .     '<div id="' . $id . '_wrapper" class="popup_wrapper">'
      .         '<div id="' . $id .'" class="popup_widget">';
}
echo $props;

if (Request::$is_ajax)
{
    echo
        '<div class="popup_content">'
      .     '<a href="#" id="' . $id . '_close" class="popup_close">закрыть</a>';
}

echo $content;

if (Request::$is_ajax)
{
    echo
        '</div>';
}

if ( ! Request::$is_ajax)
{
    echo
                '</div>'
     .      '</div>'
     .  '</div>';
}