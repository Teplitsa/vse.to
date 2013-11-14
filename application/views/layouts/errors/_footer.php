<?php defined('SYSPATH') or die('No direct script access.'); ?>
    <footer>
        <div class="container">
            <div class="row">
                <div class="span3">
                    <a href="<?php echo URL::site();?>" class="logo"></a>
                    <?php echo Widget::render_widget('menus','menu', 'footer'); ?> 
                </div>
                <div class="span5">
                    <div class="soc-link">
                        <a href="#" class="button fb">f</a>
                        <a href="#" class="tw button ">t</a>
                        <a href="#" class="button vk">v</a>
                    </div>
                    <div class="address">
                        <p><strong>Контакты:</strong><br>
                            123123 Москва<br>
                            ул. Тверская-Ямская, 99<br>
                            большое здание
                        </p>
                    </div>
                </div>
                <div class="span4">
                    <!-- 
					<img src="/modules/frontend/public/css/frontend/img/banner.png" />
					<img src="/modules/frontend/public/css/frontend/img/banner.png" />
					<img src="/modules/frontend/public/css/frontend/img/banner.png" />
					<img src="/modules/frontend/public/css/frontend/img/banner.png" />
					<img src="/modules/frontend/public/css/frontend/img/banner.png" />
					<img src="/modules/frontend/public/css/frontend/img/banner.png" />
                    -->
				</div>
            </div>
            <div class="row">
                <div class="span12">
                    <p class="copy">
						&copy; vse.to 2013.
						&nbsp;
						Проект <a href="http://www.newmediacenter.ru/">ЦИИО РЭШ</a>. Создано при поддержке <a href="http://te-st.ru/">Теплицы социальных технологий</a>
					</p>
				</div>
            </div>
        </div>
    </footer>
<?php echo $view->placeholder('scripts'); ?>
