<?php defined('SYSPATH') or die('No direct script access.'); ?>

<div class="task_log">
    <?php
    foreach ($messages as $message)
    {
        $text = '';

        switch ($message['level'])
        {
            case Log::WARNING:
                $text .= '<span class="warning">Предупреждение:</span>';
                break;

            case Log::ERROR:
                $text .= '<span class="error">Ошибка:</span>';
                break;

            case Log::FATAL:
                $text .= '<span class="fatal">Критическая ошибка:</span>';
                break;
        }

        $text .= $message['message'];

        echo '<div class="log_message">' . nl2br($text). '</div>';
    }
    ?>
</div>