<?php defined('SYSPATH') or die('No direct script access.');

$provider = $product->get_telemost_provider();
$isButton = false;

if ($provider == Model_Product::COMDI) {
    $connectUrl = URL::to('frontend/catalog/product/fullscreen', array('alias' => $product->alias));
    $endUrl = URL::site($product->uri_frontend(NULL,Model_Product::STOP_STAGE));
    
} else if ($provider == Model_Product::HANGOTS) {
    
    if (!empty($product->hangouts_url)) {
        $connectUrl = base64_decode($product->hangouts_url);
    } else {
        $isButton = true;
        $connectUrl = '';
    }
    
    $endUrl = URL::site($product->uri_frontend(NULL,Model_Product::STOP_STAGE));
}
?>

    

<?php     
    switch ($stage) {
        case Model_Product::ACTIVE_STAGE:
?>
                    <a href="<?php echo $connectUrl; ?>" class="go-link button" target="_blank">Подключиться</a>
<?php
            break;                        
        case Model_Product::START_STAGE:
?>
    <?php if($isButton): ?>
        <div id="hangouts-button"></div>
        <script src="https://apis.google.com/js/platform.js"></script>
        <script>
            
(function() {
    gapi.hangout.render('hangouts-button', {
            'render': 'createhangout',
            'initial_apps': [{'app_id' : '1085528649580', 'start_data' : '<?php echo $product->hangouts_secret_key;  ?>', 'app_type' : 'ROOM_APP' }],
            'hangout_type': 'onair',
            'widget_size': 175 // 136, 72
    });
})();
        </script>    
    <?php else: ?>
        <a href="<?php echo $connectUrl; ?>" class="go-link button" target="_blank">Подключиться</a>
    <?php endif; ?>
                    <a href="<?php echo $endUrl ?>" style="vertical-align: top" class="request-link button">Закончить</a>
<?php
            break;
    }
?>


