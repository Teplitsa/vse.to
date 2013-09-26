<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$create_url = URL::to('backend/products/goes', array('action'=>'create', 'telemost_id' => $telemost->id), TRUE);
$delete_url = URL::to('backend/products/goes', array('action'=>'delete', 'id' => '${id}'), TRUE);
?>

<div class="caption">Пойдут</div>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Добавить участника</a>
</div>

<div class="images">
<?php
foreach ($goes as $go)
:
    $_delete_url = str_replace('${id}', $go->id, $delete_url);
?>
    <div class="image">
        <div class="ctl">
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить "Я пойду"', 'controls/delete.gif', 'Удалить'); ?>
        </div>
        <div class="img">
            Участник: <?php echo $go->user->name?>
        </div>
    </div>
<?php
endforeach;
?>
</div>
