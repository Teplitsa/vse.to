<?php defined('SYSPATH') or die('No direct script access.'); ?>
<?php if (isset($url)) {?>
    <p>Бронь на сертификат будет действовать в течении 12 часов.</p>
    <p>Вы можете произвести оплату сейчас же на портале <a href="http://vse.to">VSE.TO</a> либо воспользоваться личным кабинетом <a href="https://visa.qiwi.com/features/list.action">VISA QIWI WALLET</a> в случае если оплата будет произведена позже.</p>    
    <iframe name="iframeName" src="<?php echo $url?>" frameborder="0" width="100%" height="450" scrolling="no"></iframe>    
<?php }  ?> 