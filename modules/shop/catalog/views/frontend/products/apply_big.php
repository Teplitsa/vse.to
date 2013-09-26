<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$url = URL::to('frontend/catalog/products/control', array('action' => 'create', 'product_id' => $product->id,'type_id' => Model_SectionGroup::TYPE_EVENT,), TRUE);

// Button to select user
/*$button = new Form_Element_LinkButton('apply_button',
        array('label' => 'Подать заявку', 'render' => FALSE),
        array('class' => 'apply_button open_window')
);
$button->url  = $url;

echo $button->render(); */
?>

<form action="<?php echo $url; ?>" method="POST">
    <input type="image" src="<?php echo URL::base(FALSE); ?>public/css/img/2cart-bigd.gif" class="buy" alt="Регистрация" />
</form>
