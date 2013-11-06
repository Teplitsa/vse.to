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
                        <?php echo Widget::render_widget('towns','select','map'); ?>
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

    <?php echo $content; ?>                                

<?php require('_footer.php'); ?>
    
<?php echo $view->placeholder('modal'); ?>
<script>
    jQuery(function($){
        $('.help-pop').tooltip()
    });
</script>                            
</body>     