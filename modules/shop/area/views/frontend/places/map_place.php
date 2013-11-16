<?php 
$url = URL::to('frontend/area/places', array('alias' => $place->alias));

$image = $place->image(4);

if (isset($image['image']))
{
    $image =  
            '<a href="#">'
  .             HTML::image('public/data/' . $image['image'], array('alt' => $place->name))
  .         '</a>';
    
} else {
    $image ='';
} 
?>   
    <div class="row-fluid map_info"><div class="span3 bio"><?php echo $image ?><?php if (is_array($place->links)) {
                  foreach ($place->links as $webpage) { ?><a class="website" href="<?php echo $webpage?>"><?php echo $webpage?></a>
<?php }} ?></div><div class="span6 content"><a href="#"><?php echo $place->name?></a><p class="place"><?php echo $place->town_name?>, <?php echo $place->address ?></p><?php echo $place->description ?></div></div>
