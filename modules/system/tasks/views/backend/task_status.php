<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$log_url = URL::to('backend/tasks', array('action' => 'log', 'task' => $task));
?>

<div class="task_status">

<div class="progress">
    <div class="bar" style="width: <?php echo $progress; ?>%"></div>
    <div class="status">
        <?php
        echo HTML::chars($status);
        if ($progress > 0)
        {
            echo " ($progress %)";
        }
        ?>
    </div>
</div>
<?php
if (isset($status_info))
{
    echo '<div class="status_info">' . nl2br(HTML::chars($status_info)) . '</div>';
}
?>

<?php
if ($log_stats[0] > 0)
{

    $errors_num   = $log_stats[Log::ERROR] + $log_stats[Log::FATAL];
    $warnings_num = $log_stats[Log::WARNING];

    $stats = '';

    if ($errors_num || $warnings_num)
    {
        $stats .= '( ';
    }

    if ($errors_num > 0)
    {
        $stats .=
            '<span class="errors_num">'
          .     $errors_num . ' ' . l10n::plural($errors_num, 'ошибка', 'ошибок', 'ошибки')
          . '</span>, ';
    }

    if ($warnings_num > 0)
    {
        $stats .=
            '<span class="warnings_num">'
          .     $warnings_num . ' ' . l10n::plural($warnings_num, 'предупреждение', 'предупреждений', 'предупреждения')
          . '</span>, ';
    }

    if ($errors_num || $warnings_num)
    {
        $stats = trim($stats, ' ,') . ' )';
    }

    echo
        '<div><a href="' . $log_url . '" target="_blank" class="log_link">'
      .     'Лог' . ($stats ? ' ' . $stats : '') .' &raquo;'
      . '</a></div>';
}
?>

</div>