<?php defined('SYSPATH') or die('No direct script access.');

require_once(Kohana::find_file('views', 'frontend/products/_helper'));

function product_adv(Model_Product $product,$url,$image,$today_flag,$tomorrow_flag,$nearest_flag)
{
    $lecturer_url = URL::to('frontend/acl/lecturers', array('action' => 'show','lecturer_id' => $product->lecturer_id));
    $day = $nearest_flag?$product->weekday:($tomorrow_flag?'Завтра':'Сегодня');
    
    $html = '<section><header>';
    $html .= '<div class="row-fluid">';
    $html .= '<div class="span6" style="white-space: nowrap;">';
    $html .= '<span class="date"><a class="day" href="">'.$day.'</a>, '.$product->get_datetime_front().' </span>';
    $html .= '<span class="type">'.Model_Product::$_interact_options[$product->interact].'</span>';
    $html .= '</div>';
    $html .= '<div class="span6 b-link">';
    $html .= '<a data-toggle="modal" href="#requestModal" class="request-link button">Подать заявку</a>';
    if (Model_User::current()->id == NULL) {
        $html .= '<a data-toggle="modal" href="#notifyModal" class="go-link button">Я пойду</a>';
    } else {
        $choose_url = URL::to('frontend/catalog/product/choose', array('alias' => $product->alias));
        $html .= '<a href="'.$choose_url.'" class="ajax go-link button">Я пойду</a>';    
    }
    $html .= '</div></div></header>';
    $html .= '<div class="body-section">';
    $html .= '<div class="row-fluid">';
    $html .= '<div class="span6 face">';
    $html .= $image;
    $html .=  Widget::render_widget('products', 'product_stats', $product);
    //$html .= '<p class="counter"><span title="хочу телемост" id-"" class="hand">999</span></p>';
    $html .= '</div>';
    $html .= '<div class="span6">';
    $html .= '<a class="dir" href="#">'.Model_Product::$_theme_options[$product->theme].'</a>';
    $html .= '<h2><a href="'.$url.'">'.$product->caption.'</a></h2>';
    $html .= '<p class="lecturer">Лектор: <a href="#">'.$product->lecturer_name.'</a></p>';
    $html .= '<div class="desc"><p>'.$product->short_desc.'</p></div>';
    $html .= '<p class="link-more"><a href="'.$url.'">Подробнее</a></p>';
    $html .= '</div></div></div></section><hr>';
    return $html;
}

