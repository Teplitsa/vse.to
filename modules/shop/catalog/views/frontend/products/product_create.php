<?php defined('SYSPATH') or die('No direct script access.'); ?>
<?php

$create_url = URL::to('frontend/catalog/products', array('action'=>'create', 'sectiongroup_name' => $sectiongroup->name), TRUE);
?>
<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Создать событие</a>
</div>
