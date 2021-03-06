<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php   $current_user_id= Model_User::current()->id;
        
if ($product->user_id == $current_user_id) { 
        $update_url = URL::to('frontend/catalog/products/control', array('action'=>'update', 'id' => $product->id), TRUE);?>
    <div class="action">
        <a href="<?php echo $update_url ?>" class="link-edit"><i class="icon-pencil icon-white"></i></a>
    </div>
<?php }

if (!$nav_turn_on) {
    
} else {
    
    if ($product->user_id == $current_user_id) {
        $nav = new View('frontend/products/administrator_nav');
        $nav->product = $product;
        $nav->stage = $stage;
        /*if ($stage == Model_Product::START_STAGE) {
            $admin_vision = new View('frontend/products/administrator_vision');
            $admin_vision->product = $product;
            Layout::instance()->set_placeholder('vision',$admin_vision->render());
         }*/
    } else {
        foreach ($telemosts as $tel) {
            if ($tel->user_id == $current_user_id) {
                $nav = new View('frontend/products/user_nav');
                $nav->product = $product;
                $nav->stage = $stage;
                /*if ($stage == Model_Product::START_STAGE && $user_stage == Model_Product::START_STAGE) {
                    $nav = new View('frontend/products/user_nav_after');
                    $nav->product = $product;
                    $user_vision = new View('frontend/products/user_vision');
                    $user_vision->tel = $tel;
                    $user_vision->product = $product;
                    Layout::instance()->set_placeholder('vision',$user_vision->render());
                }*/                 
            }
        }
    }}
        $today_datetime = new DateTime("now");
        $tomorrow_datetime = new DateTime("tomorrow");
        $today_flag= 0;
        $tomorrow_flag= 0;
        $nearest_flag = 0;

        if (($today_flag == 0) && $product->datetime->format('d.m.Y') == $today_datetime->format('d.m.Y')) {
            $today_flag++;
        } elseif (($tomorrow_flag == 0) && $product->datetime->format('d.m.Y') == $tomorrow_datetime->format('d.m.Y')){
            $tomorrow_flag++;            
        } elseif ($nearest_flag == 0) {
            $nearest_flag++;
        }

        $day = $nearest_flag?$product->weekday:($tomorrow_flag?'Завтра':'Сегодня');

        $telemost_flag= FALSE;
        if (Model_Town::current()->alias != Model_Town::ALL_TOWN)
            $telemost_flag = ($product->place->town_name != Model_Town::current()->name);
        
        $user_id = Model_User::current()->id;
        $group_id = Model_User::current()->group_id; 
        $available_num = (int)$product->numviews;
        if (count($telemosts)) { 
            $available_num = ((int)$product->numviews > count($telemosts))?true:false;
        }        
    ?>    

<header>
    <div class="row-fluid">
        <div class="span6" style="white-space: nowrap;">
            <span class="date"><a class="day" href=""><?php echo $day ?></a>, <?php echo $product->get_datetime_front()?></span>
            <?php if ($telemost_flag) { ?><span class="type"><?php echo Model_Product::$_interact_options[$product->interact];?></span><?php } ?>            
        </div>
        <div class="span6 b-link shifted-links">
            <?php if (isset($nav)) {
                echo $nav->render();
            } else {
            ?>            
			
<?php $datenow = new DateTime("now");
if($product->datetime > $datenow): ?>		
<?php if (!$telemost_flag && $group_id != Model_Group::USER_GROUP_ID && $group_id && $product->user_id !=  $user_id) {    
    if ($available_num && !$user_id) { ?>
    <a data-toggle="modal" href="#notifyModal" class="request-link button">Провести телемост</a>    
<?php  } elseif ($available_num && !$already_req) { ?>
<a data-toggle="modal" href="<?php echo "#requestModal_".$product->alias?>" class="request-link button">Провести телемост</a>
<?php } elseif($already_req) {
    $unrequest_url = URL::to('frontend/catalog/product/unrequest', array('alias' => $product->alias));?>
<a href="<?php echo $unrequest_url ?>" class="ajax request-link button">Отменить заявку</a>         
<?php }} ?>
<?php endif ?>
   
            <?php } ?>
        </div>
    </div>
</header>
<div class="row-fluid">

    <div class="span5">
        <?php echo Widget::render_widget('products', 'product_images', $product); ?>

        <hr>
        <div class="event-desc">
            
            <?php if (Model_Town::current()->name == $product->place->town_name) {?> 
                <div class="place-event">
                <div class="place-tv-new">                
                    <p class="title">Событие:</p>
                    <p class="place"><?php echo $product->place->town_name?>, <?php echo $product->place->name ?><?php if ($product->place->address) { ?>,<a href="#"> <?php echo $product->place->address ?></a><?php } ?></p>
                </div>
                <!-- <p class="address"><?php //echo $product->place->town_name?>, <?php //echo $product->place->address?></p> -->
                <p class="organizer">Организатор события: <span><?php echo $product->organizer->name?></span></p>
                </div>
                <hr>

                <?php if (count($telemosts)) { ?>
                <p class="title">Телемосты:</p>
                <?php } ?>

                <?php foreach ($telemosts as $telemost) { ?>
                <div class="place-tv-new">
                        <p><?php echo $telemost->place->town_name?>, <?php echo $telemost->place->name ?><?php if ($telemost->place->address) { ?>,<a href="#"> <?php echo $telemost->place->address ?></a><?php } ?></p>            
                    <!--<div class="address"><?php //echo $telemost->place->town_name?>, <?php //echo $telemost->place->address?> -->
                    </div>
                    <p class="organizer">Организатор телемоста: <span><?php echo $telemost->user->organizer->name?></span></p>
                    <!--<p class="coordinator">Координатор: <span><?php //echo $telemost->user->name?></span></p>-->               
                    <?php if ($telemost->info) { ?><p class="desc">Дополнительно: <span><?php echo $telemost->info?></span></p><?php } ?>                    
                <hr>    
                <?php } ?>
            <?php } else {
                $main_telemosts =array();
                $other_telemosts = array();
                foreach ($telemosts as $telemost) {
                    if ($telemost->place->town_name == Model_Town::current()->name) {
                        $main_telemosts[] = clone $telemost;
                    } else {
                        $other_telemosts[] = clone $telemost;
                    }
                }   
                ?>
              
                <?php if (count($main_telemosts)) { ?>
                <p class="title">Телемост:</p>
                <?php } ?>

                <?php foreach ($main_telemosts as $telemost) {?>
                    
                     <div class="place-tv-new">
                        <p><?php echo $telemost->place->town_name?>, <?php echo $telemost->place->name ?><?php if ($telemost->place->address) { ?>,<a href="#"> <?php echo $telemost->place->address ?></a><?php } ?></p>            
                    <!--<div class="address"><?php //echo $telemost->place->town_name?>, <?php //echo $telemost->place->address?> -->
                    </div>
                    <p class="organizer">Организатор телемоста: <span><?php echo $telemost->user->organizer->name?></span></p>
                    <!--<p class="coordinator">Координатор: <span><?php //echo $telemost->user->name?></span></p>-->               
                    <?php if ($telemost->info) { ?><p class="desc">Дополнительно: <span><?php echo $telemost->info?></span></p><?php } ?>                    
                    <hr>    
                <?php }?>
                
                <div class="place-event">
                <div class="place-tv-new">                
                    <p class="title">Оффлайновое событие:</p>
                    <p class="place"><?php echo $product->place->town_name?>, <?php echo $product->place->name ?><?php if ($product->place->address) { ?>,<a href="#"> <?php echo $product->place->address ?></a><?php } ?></p>
                </div>
                <!-- <p class="address"><?php //echo $product->place->town_name?>, <?php //echo $product->place->address?></p> -->
                <p class="organizer">Организатор события: <span><?php echo $product->organizer->name?></span></p>
                </div>
                <hr>
                    
                <?php if (count($other_telemosts)) { ?>
                <p class="title"><?php echo count($main_telemosts) ? 'Другие телемосты:' : 'Телемосты:' ?></p>
                <?php } ?>

                <?php foreach ($other_telemosts as $telemost) {?>
                    <div class="place-tv-new">
                        <p><?php echo $telemost->place->town_name?>, <?php echo $telemost->place->name ?><?php if ($telemost->place->address) { ?>,<a href="#"> <?php echo $telemost->place->address ?></a><?php } ?></p>            
                    <!--<div class="address"><?php //echo $telemost->place->town_name?>, <?php //echo $telemost->place->address?> -->
                    </div>
                    <p class="organizer">Организатор телемоста: <span><?php echo $telemost->user->organizer->name?></span></p>
                    <!--<p class="coordinator">Координатор: <span><?php //echo $telemost->user->name?></span></p>-->               
                    <?php if ($telemost->info) { ?><p class="desc">Дополнительно: <span><?php echo $telemost->info?></span></p><?php } ?>                    
                    <hr>    
                <?php } ?>
            <?php } ?>    
        </div>
        <div class="b-new-events">
            <?php if ($available_num) { ?>
                <p><strong>Максимальное количество телемостов: <?php echo $product->numviews ?>, осталось: <?php echo ((int)$product->numviews - count($telemosts))?></strong></p>
            <?php } else { ?>
                <p><strong>Вы уже выбрали максимально возможное количество телемостов</strong></p>                
            <?php } ?>
            <?php if (count($app_telemosts)) { ?>    
            <div class="event-desc">                
            <p class="title">Заявки на телемосты:</p>
            
            <?php foreach ($app_telemosts as $app_telemost) { ?>
            <?php if ($available_num && ($user_id == $product->user_id)) { ?>    
            <br>
            <?php } ?>
            <div class="el event-desc">                
                <p class="from-who">От кого: <span><?php echo $app_telemost->user->name?></span>
                &nbsp;&nbsp;Организация: <span><?php echo $app_telemost->user->organizer->name?></span></p>
                <p class="place"><?php echo $app_telemost->place->town_name?>, <?php echo $app_telemost->place->name?><?php if ($app_telemost->place->address) { ?>,<a href="#"> <?php echo $app_telemost->place->address ?></a><?php } ?></p>
                <?php if ($app_telemost->info) { ?><p class="desc">Дополнительно: <span><?php echo $app_telemost->info?></span></p><?php } ?>                    
                <?php if ($available_num && ($user_id == $product->user_id)) { 
                    $select_url = URL::to('frontend/catalog/telemost/select', array('telemost_id' => $app_telemost->id));    
                ?>            
                <div class="action">
                    <a href="<?php echo $select_url?>" class="ajax button button-red">Принять</a>                        
                </div>
                <?php } ?>
            </div>
            <hr>            
            <?php } ?>
            </div>
        <?php }?>            
        </div>
    </div>
    
    <div class="span7 content">
        <h1><?php echo $product->caption?></h1>
        <?php $lecturer_url = URL::to('frontend/acl/lecturers', array('action' => 'show','lecturer_id' => $product->lecturer_id));?>
        <p class="lecturer">Лектор: <a href="<?php echo $lecturer_url ?>"><?php echo $product->lecturer->name; ?></a></p>
        <div class="content">
            <p><?php echo $product->description; ?></p>
        </div>
	<br />
        <div class="dir righted">
        Категория:&nbsp<a  href="<?php echo $product->uri_frontend(); ?>"><?php echo Model_Product::$_theme_options[$product->theme] ?></a>
             <?php if (count($product->tag_items)) { 
                echo "&nbsp&nbsp&nbspТеги:";
             }                
              $i=0; foreach ($product->tag_items as $tag) {
                    $search_url  = URL::to('frontend/catalog/search', array('tag'=>$tag->alias), TRUE);                  
                    $glue =($i)?',':'';$i++;?>
                <a href="<?php echo $search_url ?>"><?php echo $glue.' '.$tag->name ?></a>
                <?php } ?>
        </div>
        <?php echo Cackle::factory()->render();?>        
    </div>
</div>
