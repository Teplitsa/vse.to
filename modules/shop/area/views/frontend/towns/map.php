<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php echo Gmaps3::instance()->get_apilink();?> 
<!-- <script>
          jQuery(function($){
            var gmap = document.getElementById('gmap'),
                list = document.getElementById('main'),
                shm = document.getElementsByClassName('show-map')[0],
                shl = document.getElementsByClassName('show-list')[0],
                ftr = document.getElementsByTagName('footer')[0];
            $('.show-list').click(function(){
                gmap.hidden = true;
                list.hidden = false;
                this.hidden = true;
                shm.hidden = false;
                $(ftr).show();
            })
            $('.show-map').click(function(){
                gmap.hidden = false;
                list.hidden = true;
                this.hidden = true;
                shl.hidden = false;
                $(ftr).hide();
            })
          })
          </script>
-->          
<script type="text/javascript">
    window.onload = function() {
        // Reference to autogenerated code  
        <?php echo Gmaps3::instance()->get_map('gmap',$lat,$lon,$zoom);?>
    };
</script>
<!--<p class="change-view">
    <a href="#" class="button show-map" hidden>Показaть карту</a>
    <a href="#" class="button show-list">Показать список</a>
</p>-->
<div id="gmap"> </div> 
<div id="main" class="map" hidden>
        <div id="listPoint">
            <div class="container">
                <div class="wrapper">
                    <h1>Представители</h1>
                    <div class="row-fluid">
                        <div class="span4">
                            <h2>ПЕТРОПАВЛОВСК-КАМЧАТСКИЙ</h2>

                            <div class="el"><img src="img/img.png"  class="pull-left" alt=""><p class="title">Иванов Иван</span></p>
                            <p class="desc">
                                Интересы: <a href="#">интерес</a>, <a href="#">другой интерес</a>, <a href="#"> третий интерес</a>, <a href="#"> просто интерес</a>
                            </p>
                            </div>
                            
                            <div class="el"><img src="img/img.png"  class="pull-left" alt=""><p class="title">Библиотека имени Ленина</span><span class="dash">|</span><a href="#">как добраться</a></p>
                            <p class="desc">Петропавловск-Камчатский, ул. Краснопролетарская, 
                                д. 999, библиотечный зал.</p></div>
                            
                            <div class="el"><img src="img/img.png"  class="pull-left" alt=""><p class="title">Библиотека имени Ленина</span><span class="dash">|</span><a href="#">как добраться</a></p>
                            <p class="desc">Петропавловск-Камчатский, ул. Краснопролетарская, 
                                д. 999, библиотечный зал.</p></div>
                            
                            <div class="el"><img src="img/img.png"  class="pull-left" alt=""><p class="title">Библиотека имени Ленина</span><span class="dash">|</span><a href="#">как добраться</a></p>
                            <p class="desc">Петропавловск-Камчатский, ул. Краснопролетарская, 
                                д. 999, библиотечный зал.</p></div>
                        </div>
                        <div class="span4">
                            <h2>ПЕТРОПАВЛОВСК-КАМЧАТСКИЙ</h2>

                            <div class="el"><p class="title">Библиотека имени Ленина</span><span class="dash">|</span><a href="#">как добраться</a></p>
                            Петропавловск-Камчатский, ул. Краснопролетарская, 
                            д. 999, библиотечный зал.</div>
                            
                            <div class="el"><p class="title">Библиотека имени Ленина</span><span class="dash">|</span><a href="#">как добраться</a></p>
                            Петропавловск-Камчатский, ул. Краснопролетарская, 
                            д. 999, библиотечный зал.</div>
                            
                            <div class="el"><p class="title">Библиотека имени Ленина</span><span class="dash">|</span><a href="#">как добраться</a></p>
                            Петропавловск-Камчатский, ул. Краснопролетарская, 
                            д. 999, библиотечный зал.</div>
                            
                            <div class="el"><p class="title">Библиотека имени Ленина</span><span class="dash">|</span><a href="#">как добраться</a></p>
                            Петропавловск-Камчатский, ул. Краснопролетарская, 
                            д. 999, библиотечный зал.</div>
                        </div>
                        <div class="span4">
                            <h2>ПЕТРОПАВЛОВСК-КАМЧАТСКИЙ</h2>

                            <div class="el"><p class="title">Библиотека имени Ленина</span><span class="dash">|</span><a href="#">как добраться</a></p>
                            Петропавловск-Камчатский, ул. Краснопролетарская, 
                            д. 999, библиотечный зал.</div>
                            
                            <div class="el"><p class="title">Библиотека имени Ленина</span><span class="dash">|</span><a href="#">как добраться</a></p>
                            Петропавловск-Камчатский, ул. Краснопролетарская, 
                            д. 999, библиотечный зал.</div>
                            
                            <div class="el"><p class="title">Библиотека имени Ленина</span><span class="dash">|</span><a href="#">как добраться</a></p>
                            Петропавловск-Камчатский, ул. Краснопролетарская, 
                            д. 999, библиотечный зал.</div>
                            
                            <div class="el"><p class="title">Библиотека имени Ленина</span><span class="dash">|</span><a href="#">как добраться</a></p>
                            Петропавловск-Камчатский, ул. Краснопролетарская, 
                            д. 999, библиотечный зал.</div>
                        </div>
                    </div>
                    <h1>Площадки</h1>
                    <div class="row-fluid">
                        <div class="span4">
                            <h2>ПЕТРОПАВЛОВСК-КАМЧАТСКИЙ</h2>

                            <div class="el"><p class="title">Библиотека имени Ленина</span><span class="dash">|</span><a href="#">как добраться</a></p>
                            Петропавловск-Камчатский, ул. Краснопролетарская, 
                            д. 999, библиотечный зал.</div>
                            
                            <div class="el"><p class="title">Библиотека имени Ленина</span><span class="dash">|</span><a href="#">как добраться</a></p>
                            Петропавловск-Камчатский, ул. Краснопролетарская, 
                            д. 999, библиотечный зал.</div>
                            
                            <div class="el"><p class="title">Библиотека имени Ленина</span><span class="dash">|</span><a href="#">как добраться</a></p>
                            Петропавловск-Камчатский, ул. Краснопролетарская, 
                            д. 999, библиотечный зал.</div>
                            
                            <div class="el"><p class="title">Библиотека имени Ленина</span><span class="dash">|</span><a href="#">как добраться</a></p>
                            Петропавловск-Камчатский, ул. Краснопролетарская, 
                            д. 999, библиотечный зал.</div>
                        </div>
                        <div class="span4">
                            <h2>ПЕТРОПАВЛОВСК-КАМЧАТСКИЙ</h2>

                            <div class="el"><p class="title">Библиотека имени Ленина</span><span class="dash">|</span><a href="#">как добраться</a></p>
                            Петропавловск-Камчатский, ул. Краснопролетарская, 
                            д. 999, библиотечный зал.</div>
                            
                            <div class="el"><p class="title">Библиотека имени Ленина</span><span class="dash">|</span><a href="#">как добраться</a></p>
                            Петропавловск-Камчатский, ул. Краснопролетарская, 
                            д. 999, библиотечный зал.</div>
                            
                            <div class="el"><p class="title">Библиотека имени Ленина</span><span class="dash">|</span><a href="#">как добраться</a></p>
                            Петропавловск-Камчатский, ул. Краснопролетарская, 
                            д. 999, библиотечный зал.</div>
                            
                            <div class="el"><p class="title">Библиотека имени Ленина</span><span class="dash">|</span><a href="#">как добраться</a></p>
                            Петропавловск-Камчатский, ул. Краснопролетарская, 
                            д. 999, библиотечный зал.</div>
                        </div>
                        <div class="span4">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>