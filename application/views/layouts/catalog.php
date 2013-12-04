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
                <article class="span12" id="event">
                    <?php echo $view->placeholder('vision'); ?>
                    <div class="wrapper product">
                        <?php echo $content; ?>                                
                    </div>
                </article>
            </div>
        </div>
    </div>
    
<?php require('_footer.php'); ?>
    
<?php echo $view->placeholder('modal'); ?>
<script>
    jQuery(function($){
        $('.help-pop').tooltip()
    });
</script>                            
</body>     