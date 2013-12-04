<?php defined('SYSPATH') or die('No direct script access.'); ?>
<?php require('_header_new.php'); ?>

<body>
    <header>
        <div class="mainheader">
        <div class="container">
            <div class="row">
                <div class="span2">
                    <a href="<?php echo URL::site()?>" class="logo pull-left"></a>
                </div>
                <div class="span6">
                <?php echo Widget::render_widget('menus','menu', 'main'); ?> 
                </div>
                <div class="span4">
                    <div class="b-auth-search">
                        <?php echo Widget::render_widget('products', 'search');?>
                    </div>
                </div>
            </div>
         </div>
        </div>
        <div class="subheader">
            <div class="container">
                <div class="row">
                    <div class="span2">
                    </div>
                    <div class="span6">
                        <ul class="second-menu">
                            <?php echo Widget::render_widget('towns','select'); ?>
                            <?php echo Widget::render_widget('products','format_select'); ?>                         
                            <?php echo Widget::render_widget('products','theme_select'); ?>                         
                            <?php echo Widget::render_widget('products','calendar_select'); ?> 
                        </ul>                    
                    </div>
                    <div class="span4">
                        <div class="login-form">
                            <?php echo Widget::render_widget('acl', 'login'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </header>

    <div id="main">
        <div class="container">
            <div class="row">
                <div class="span12" id="page">
                    <!-- <div class="wrapper"> -->
                        <div class="row-fluid">
                            <div class="span3">
                                <?php echo HTML::image('public/data/img/logo.svg',array('class' =>'big-logo'))?>
<!--                                <div class="soc-link">
                                    <a href="#" class="button fb">f</a>
                                    <a href="#" class="tw button ">t</a>
                                    <a href="#" class="button vk">v</a>
                                </div>-->
                            </div>
                            <div class="span1">&nbsp;</div>
                            <div class="span7 content">
                            <h1>404 - ошибка</h1>
                            <p>Такой страницы не существует. Но у нас есть много других страниц,</p>
                            <p>например, <a href="<?php echo URL::site()?>">список</a> оффлайновых событий и телемостов в разных городах</p>                            
                            <div class="" style="height: 400px;"></div>
                            </div>
                            <div class="span1">&nbsp;</div>
                        </div>
                    <!-- </div> -->
                </div>
            </div>
        </div>
    </div>
    <!-- /#main -->
    
<?php require('_footer.php'); ?>
    
<?php echo $view->placeholder('modal'); ?>
          
<!-- Reformal -->
<script type="text/javascript">
    var reformalOptions = {
        project_id: 184340,
        project_host: "vseto.reformal.ru",
        tab_orientation: "left",
        tab_indent: "50%",
        tab_bg_color: "#a93393",
        tab_border_color: "#FFFFFF",
        tab_image_url: "http://tab.reformal.ru/T9GC0LfRi9Cy0Ysg0Lgg0L%252FRgNC10LTQu9C%252B0LbQtdC90LjRjw==/FFFFFF/2a94cfe6511106e7a48d0af3904e3090/left/1/tab.png",
        tab_border_width: 0
    };
    
    (function() {
        var script = document.createElement('script');
        script.type = 'text/javascript'; script.async = true;
        script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'media.reformal.ru/widgets/v3/reformal.js';
        document.getElementsByTagName('head')[0].appendChild(script);
    })();
</script><noscript><a href="http://reformal.ru"><img src="http://media.reformal.ru/reformal.png" /></a><a href="http://vseto.reformal.ru">Oтзывы и предложения для vse.to</a></noscript>
    
    
    
    
</body>     