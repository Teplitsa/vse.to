<?php defined('SYSPATH') or die('No direct script access.'); ?>

<!--bof-navigation display -->
<div id="navSuppWrapper">
<div id="navSupp">
<?php echo Widget::render_widget('menus','menu', 'main'); ?>    
</div>
</div>
<!--eof-navigation display -->
<?php
if (Kohana::$profiling)
{
    //echo View::factory('profiler/stats')->render();
}
?>

</body>
</html>