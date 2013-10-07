<?php defined('SYSPATH') or die('No direct script access.'); ?>
<?php require('_header_new.php'); ?>

<body>
    <header>
        <div class="container">
            <div class="row">
                <div class="span12">
                    <a href="<?php echo URL::site()?>" class="logo pull-left"></a>
                    <?php echo Widget::render_widget('menus','menu', 'main'); ?> 
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
                <div class="span8">
                        <?php echo $content; ?>                                
                </div>
		<aside class="span4">
		    <div class="right-block">
			<h2>О проекте</h2>
			<div class="left-block-content">
			    Тут будет хороший текст о проекте
			</div>
		    </div>
		    <div class="right-block">
			<h2>События</h2>
			<div class="left-block-content">
			    1 ноября пройдет главное событие года!
			</div>
		    </div>
		    <a href="http://te-st.ru/" class="te-st-link">
			<img src="/modules/frontend/public/css/frontend/img/te-st-banner.png" />
		    </a>
		</aside>
            </div>
	</div>
    </div>
  
    
<?php require('_footer.php'); ?>
    
<?php echo $view->placeholder('modal'); ?>
                            
</body>     