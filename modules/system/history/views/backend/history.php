<?php defined('SYSPATH') or die('No direct script access.'); ?>

<div class="history">
<?php
foreach ($entries as $entry)
:
?>
    <div class="entry">
        <div>
            <span class="date">[<?php echo date('Y-m-d H:i:s', $entry->created_at); ?>]</span>
            <?php echo $entry->user_name; ?>
        </div>
        <div>
            <?php echo $entry->html; ?>
        </div>
    </div>
<?php
endforeach;
?>
</div>

<?php
if (isset($pagination))
{
    echo $pagination->render();
}
?>