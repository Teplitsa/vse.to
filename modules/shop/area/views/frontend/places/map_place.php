<?php 
$url = URL::to('frontend/area/places', array('alias' => $place->alias));
?>
<div class="map_info"><h3><?php echo $place->name?></h3><p class="place"><?php echo $place->town_name?>, <?php echo $place->address ?></p></div>
