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

                    <div class="wrapper">

                        <div class="row-fluid">

                            <div class="span3">

                                <?php echo HTML::image('public/data/img/logo.svg',array('class' =>'big-logo', 'style'=>'margin-top: 10px'))?>

                                <div class="soc-link">
                                    <a href="https://www.facebook.com/vsetonetwork" class="button fb">f</a>
                                    <a href="https://twitter.com/vse_to" class="tw button ">t</a>
                                    <a href="https://vk.com/vseto" class="button vk">v</a>
                                </div>

                            </div>
                        
                            <div class="span7 content">
                                <?php echo Widget::render_widget('blocks', 'block', 'about'); ?>
                                <h2 id="faq">FAQ</h2>
                                <div class="accordion" id="accordion2">
                                    <div class="accordion-group">
                                      <div class="accordion-heading">
                                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse1">
                                          Что такое vse.to?
                                        </a>
                                      </div>
                                      <div id="collapse1" class="accordion-body collapse">
                                        <div class="accordion-inner">
                                          Первая в мире сеть обмена культурными событиями. В отличие от обычной социальной сети, vse.to соединяет не только людей, но и пространства: площадки, оборудованные для проведения телемостов и вебинаров и стриминговых трансляций.
                                        </div>
                                      </div>
                                    </div>
                                    <div class="accordion-group">
                                      <div class="accordion-heading">
                                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse2">
                                          Если у вас есть событие, и вы хотите транслировать его в регионы
                                        </a>
                                      </div>
                                      <div id="collapse2" class="accordion-body collapse">
                                        <div class="accordion-inner">
                                            <ul>
                                            <li><a herf="http://vse.to/register">Станьте представителем vse.to</a> </li>
                                            <li>Разместите анонс Вашего события </li>
                                            <li>Региональные представители vse.to подадут заявки на трансляцию Вашего события </li>
                                            <li>Выберите заявки из тех городов, в которых Вы хотите провести трансляцию (города могут быть также отобраны автоматически) </li>
                                            <li>Проведите телемост</li>
                                            </ul>                                            
                                        </div>
                                      </div>
                                    </div>
                                    <div class="accordion-group">
                                      <div class="accordion-heading">
                                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse3">
                                          Если вам нравится удалённое событие, и вы хчттите показать его в своём городе
                                        </a>
                                      </div>
                                      <div id="collapse3" class="accordion-body collapse">
                                        <div class="accordion-inner">
                                            <ul>
                                            <li><a herf="http://vse.to/register">Станьте представителем vse.to</a> </li>
                                            <li>Добавьте заявку на трансляцию, нажав на кнопку &ldquo;Подать заявку&rdquo; на странице события </li>
                                            <li>Если Ваша заявка будет одобрена, создайте ивент в социальных сетях и пригласите всех на телемост </li>
                                            <li>Проверьте, что у площадки, которую Вы выбрали, есть всё необходимое оборудование </li>
                                            <li>Проведите телемост</li>
                                            </ul>                                            
                                        </div>
                                      </div>
                                    </div>
                                    <div class="accordion-group">
                                      <div class="accordion-heading">
                                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse4">
                                          Если вы хотите посмотреть телемост удалённого события в своём городе
                                        </a>
                                      </div>
                                      <div id="collapse4" class="accordion-body collapse">
                                        <div class="accordion-inner">
                                            <ul>
                                            <li>Зайдите на страницу удалённого события, которое Вам интересно, и нажмите на кнопку &ldquo;Хочу телемост&rdquo; </li>
                                            <li>Представитель в Вашем городе узнает о Вашем интересе и, возможно, организует трансляцию </li>
                                            <li>Если Вы хотите организовать телемост сами, Вы можете стать представителем vse.to.         
                                            <ul>                                            
                                        </div>
                                      </div>
                                    </div>
                                    <div class="accordion-group">
                                      <div class="accordion-heading">
                                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse5">
                                          Кто такие представители vse.to?
                                        </a>
                                      </div>
                                      <div id="collapse5" class="accordion-body collapse">
                                        <div class="accordion-inner">
                                            <p>Люди или организации участвующие в обмене культурными событиями</p>
                                            <p>Представитель vse.to может:</p>
                                            <ul>
                                            <li>Организовать событие, которое увидят в других городах</li>
                                            <li>Организовать телемост - то есть трансляцию удалённого события в своём городе.</li>
                                            </ul>                                            
                                        </div>
                                      </div>
                                    </div>
                                    <div class="accordion-group">
                                      <div class="accordion-heading">
                                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse6">
                                          Как стать представителем?
                                        </a>
                                      </div>
                                      <div id="collapse6" class="accordion-body collapse">
                                        <div class="accordion-inner">
                                            <p>Представителями могут стать человек или организация, у которых есть возможность и желание провести телемост, то есть</p>
                                            <ul>
                                            <li>помещение</li>
                                            <li>доступ к Интернету</li>
                                            <li>оборудование</li>
                                            <li>умение собирать зрителей.</li>
                                            <li><a herf="http://vse.to/register">Станьте представителем vse.to</a> </li>
                                            </ul>                                            
                                        </div>
                                      </div>
                                    </div>
                                    <div class="accordion-group">
                                      <div class="accordion-heading">
                                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse7">
                                          - оборудование для телемоста
                                        </a>
                                      </div>
                                      <div id="collapse7" class="accordion-body collapse">
                                        <div class="accordion-inner">
                                            <p>Для участия в телемосте Вам нужно иметь</p>
                                            <p>ПК: Intel Core 2 Duo 2.13 ГГц или AMD Athlon II 215 и выше, Оперативная память: от 2 Гб и выше для всех ОС</p>
                                            <p>Интернет со скоростью доступа не меньше, чем 1 Мбит/с.</p>
                                            <p>Вы можете проверить Вашу систему <a href="http://www.comdi.com/support/">тут</a> .</p>                                            
                                        </div>
                                      </div>
                                    </div>
                                    <div class="accordion-group">
                                      <div class="accordion-heading">
                                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse8">
                                          - инструкция к вебинарному сервису
                                        </a>
                                      </div>
                                      <div id="collapse8" class="accordion-body collapse">
                                        <div class="accordion-inner">
                                            <p>Большая инструкция есть <a href="http://help.comdi.com/">тут</a>.</p>                                           
                                        </div>
                                      </div>
                                    </div>
                                    <div class="accordion-group">
                                      <div class="accordion-heading">
                                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse9">
                                          - виды трансляций на vse.to
                                        </a>
                                      </div>
                                      <div id="collapse9" class="accordion-body collapse">
                                        <div class="accordion-inner">
                                            <ul>
                                            <li>Телемост (вебинар, видеоконференция) - это прямая трансляция события c возможностью обратной видеосвязи.</li>
                                            <li>Стриминг - прямая трансляция без обратной связи или с ограниченной обратной связью через чат.</li>
                                            </ul>                                         
                                        </div>
                                      </div>
                                    </div>
                                     <div class="accordion-group">
                                      <div class="accordion-heading">
                                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse10">
                                          - какие события может транслировать vse.to
                                        </a>
                                      </div>
                                      <div id="collapse10" class="accordion-body collapse">
                                        <div class="accordion-inner">
                                            <p>Любые, например: лекции, семинары, дискуссии, встречи с писателями, журналистами или режиссерами, фильмы, концерты, спектакли, перформансы.</p>                                            
                                        </div>
                                      </div>
                                    </div>                                   
                                     <div class="accordion-group">
                                      <div class="accordion-heading">
                                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse11">
                                          - как проходит отбор заявок
                                        </a>
                                      </div>
                                      <div id="collaps11" class="accordion-body collapse">
                                        <div class="accordion-inner">
                                            <p>Добавляя анонс события на vse.to, организатор указывает максимальное количество телемостов и определяет, какие представители могут подать заявку на проведение телемоста:</p>
                                            <ul>
                                            <li>все представители</li>
                                            <li>представители из определённых городов</li>
                                            <li>определённые типы организаций (например, библиотеки или ВУЗы)</li>
                                            <li>определённые региональные сети организаций (например, филиалы ГЦСИ)</li>
                                            <li>конкретные представители, с которыми есть предварительная договорённость</li>
                                            </ul>
                                            <p>Кроме того, организатор события выбирает способ отбора заявок: автоматический или ручной. При автоматическом отборе алгоритм выбирает случайные заявки, при ручном - заявки одобряет организатор.</p>                                            
                                        </div>
                                      </div>
                                    </div> 
                                     <div class="accordion-group">
                                      <div class="accordion-heading">
                                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse12">
                                          - какие бывают типы организаций
                                        </a>
                                      </div>
                                      <div id="collapse12" class="accordion-body collapse">
                                        <div class="accordion-inner">
                                            <p>Библиотека, книжный магазин, клуб, ресторан, институт, культурный центр, творческая группа.</p>
                                        </div>
                                      </div>
                                    </div>                                      
                                </div>
                                
                            </div>


                            <div class="span1">&nbsp;</div>

                        </div>

                    </div>

                </div>

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
