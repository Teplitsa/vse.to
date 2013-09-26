<?php defined('SYSPATH') or die('No direct script access.');?>

<?php $url = URL::to('frontend/catalog/product/fullscreen', array('alias' => $product->alias));

if (isset($_GET['width']) AND isset($_GET['height'])) { ?>
<div class ="look">
<iframe src="<?php echo $aim->event_uri ?>?t=1&export=1&logout=www<?php echo URL::self(array());?>" width="<?php echo $_GET['width'] ?>" height="<?php echo $_GET['height'] ?>" frameborder="0" style="border:none"></iframe>
    
</div>
<?php } else {
  // передаем переменные с размерами
  // (сохраняем оригинальную строку запроса
  //   -- post переменные нужно будет передавать другим способом)

  echo "<script language='javascript'>\n";
  echo "  location.href=\"$url?"
            . "&width=\" + (screen.width-80) + \"&height=\" + (screen.height-80);\n";
  echo "</script>\n";
  exit();
}
?>


