<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
if (empty($messages))
    return;
?>

<div class="flash_messages">
    <?php
    foreach ($messages as $message)
    {
        echo View_Helper::flash_msg($message['text'], $message['type']);
    }
    ?>
</div>