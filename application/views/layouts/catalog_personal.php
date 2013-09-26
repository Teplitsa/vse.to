<?php defined('SYSPATH') or die('No direct script access.'); ?>
<?php require('_header_new.php'); ?>

<body>
    <header>
        <div class="container">
            <div class="row">
                <div class="span12">
                    <a href="home.html" class="logo pull-left"></a>
                    <?php echo Widget::render_widget('menus','menu', 'main'); ?> 
                    <ul class="second-menu">
                        <li><a href="">Петропавловск-Камчатский</a></li>
                        <li><a href="">формат события</a></li>
                        <li><a href="">следующая неделя</a></li>
                        <li><a href="">архитектура</a></li>
                        <li><a href="">лайки</a></li>
                    </ul>
                    <div class="b-auth-search">
                        <a data-toggle="modal" href="#enterModal">Войти</a><span class="dash">|</span><a data-toggle="modal" href="#regModal">Регистрация</a>
                        <?php echo Widget::render_widget('products', 'search');?>
                        <div class="b-lang">
                            <a class="current" href="">RU</a><span class="dash">|</span><a href="">EN</a>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </header>


    <div id="main">
        <div class="container">
            <div class="row">
                <div class="span8">
                    <div class="wrapper main-list">
                        <?php echo $content; ?>                                
                    </div>
                </div>
                <aside class="span4">
                    <div class="near-tv">
                    </div>
                </aside>
            </div>
        </div>
    </div>   
    
<?php require('_footer.php'); ?>
</body>               
    



        