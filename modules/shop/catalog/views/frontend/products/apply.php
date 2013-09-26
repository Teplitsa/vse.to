<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$url = URL::to('frontend/catalog/products/control', array('action' => 'create', 'product_id' => $product->id,'type_id' => Model_SectionGroup::TYPE_EVENT,), TRUE);
?>

<form action="<?php echo $url; ?>" method="POST" class="ajax">
    <input type="image" src="<?php echo URL::base(FALSE); ?>public/css/img/3cart.gif" class="buy" alt="В корзину" />
</form>