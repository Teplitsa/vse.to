<?php defined('SYSPATH') or die('No direct script access.'); ?>
<?php require('_header_new.php'); ?>

<body>
    <header>
        <div class="container">
            <div class="row">
                <div class="span12">
                    <a href="<?php echo URL::site()?>" class="logo pull-left"></a>
                    <?php echo Widget::render_widget('menus','menu', 'main'); ?> 
                    <ul class="second-menu">
                        <?php echo Widget::render_widget('towns','select'); ?>
                        <?php echo Widget::render_widget('products','format_select'); ?>                         
                        <?php echo Widget::render_widget('products','theme_select'); ?>                         
                        <?php echo Widget::render_widget('products','calendar_select'); ?> 
                    </ul>
                    <div class="b-auth-search">
                        <?php echo Widget::render_widget('acl', 'login'); ?>
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
                <div class="span12" id="page">
                    <!-- <div class="wrapper"> -->
                        <div class="row-fluid">
                            <div class="span3">
                                <?php echo HTML::image('public/data/img/logo.svg',array('class' =>'big-logo'))?>
                                <div class="soc-link">
                                    <a href="#" class="button fb">f</a>
                                    <a href="#" class="tw button ">t</a>
                                    <a href="#" class="button vk">v</a>
                                </div>
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
                            
</body>     