<?php defined('SYSPATH') or die('No direct script access.'); ?>
    <footer>
        <div class="container">
            <div class="row">
                <div class="span2 footer-logo-block">
                    <a href="<?php echo URL::site();?>" class="logo"></a>
                    <span>первая социальная сеть телемостов и семинаров</span>
                </div>
                <div class="span5">
                    <p>
                    Проект <a href="http://www.newmediacenter.ru/">ЦИИО РЭШ</a>. <br />
                    Создан при поддержке <a href="http://te-st.ru/">Теплицы социальных технологий</a>
                    </p>
                    <p>
                    Все права на информацию, размещенную на данном ресурсе, принадлежат ЦИИО РЭШ. <br />
                    При копирование материалов ссылка на сайт обязательна. <br />
                    &copy; vse.to 2013
                    </p>
                </div>
                <div class="span5">
                    <div class="address">
                        <p>
                            Контакты:<br />
                            Центр изучения интернета и общества <br />
                            117418, Москва, Нахимовский пр. 47, оф. 1918<br />
                            <a href="mailto:info@vse.to">info@vse.to</a>
						</p>
                    </div>
					<div>
						<a href="<?php echo URL::uri_to('frontend/catalog/products/control', array('action' => 'archive'), TRUE) ?>">Архив событий</a>
					</div>
                    <div class="soc-link">
                        <a href="https://www.facebook.com/vsetonetwork" class="button fb">f</a>
                        <a href="https://twitter.com/vse_to" class="tw button ">t</a>
                        <a href="https://vk.com/vseto" class="button vk">v</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
<?php echo $view->placeholder('scripts'); ?>
