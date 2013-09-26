<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$total_count = $cart->total_count;

echo
    '<a href="' . URL::to('frontend/cart') . '">'
  .     HTML::image('public/css/img/cart.gif', array('style' => 'display:inline-block; vertical-align:middle; margin:0 10px 0 10px;', 'alt' => 'Ваша корзина'))
  . '</a>'
  . '<span class="big">';

if ($total_count == 0)
{
    echo
        'пуста';
}
else
{
    
    echo
        '<a href="' . URL::to('frontend/cart') . '">'
        .'<strong>'.$total_count . ' ' . l10n::plural($total_count, 'товар', 'товаров', 'товара').'</strong>'
        . '</a>';    
}

echo
    '</span>';
?>