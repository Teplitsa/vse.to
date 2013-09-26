<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$view_url = URL::to('frontend/news', array('action'=>'view', 'id' => '{{id}}'), TRUE);
?>

<?php
if (empty($years))
{
    echo '<div class="news"><div class="item">Новостей нет</div></div>';
    return;
}
?>

<!-- Selecting news by year and month -->
<div id="pages">
    <ul class="year">
         <?php
         foreach ($years as $year)
         {
             $url = URL::to('frontend/news', array('year' => $year));

             $selected = ($year == $current_year);

             echo
                '<li>'
              . '   <a href="' . $url . '"' . ($selected ? ' class="current"' : '') . '>' . $year . '</a>'
              . '</li>';
         }
         ?>
    </ul>

    <ul class="month">
        <?php
        for ($month = 1; $month <= 12; $month++)
        {
            if ( ! in_array($month, $months))
            {
                echo '<li><span>' . $month . '</span></li>';
            }
            else
            {
                $url = URL::to('frontend/news', array('year' => $current_year, 'month' => $month));

                $selected = ($month == $current_month);
                 echo
                    '<li>'
                  . '   <a href="' . $url . '"' . ($selected ? ' class="current"' : '') . '>' . $month . '</a>'
                  . '</li>';
            }
        }
        ?>
    </ul>
</div>

<!-- news list -->
<?php
if ( ! count($news))
    // No news
    return;
?>

<div class="news">
<?php
foreach ($news as $newsitem)
:
    $_view_url = str_replace('{{id}}', $newsitem->id, $view_url);
?>
    <div class="item">
        <div class="date"><?php echo $newsitem->date->format(Kohana::config('datetime.date_format')); ?></div>
        <div class="title">
            <a href="<?php echo $_view_url; ?>">
                <?php echo HTML::chars($newsitem->caption); ?>
            </a>
            <div class="text">
                <?php echo $newsitem->short_text; ?>
            </div>
        </div>
    </div>
<?php
endforeach;
?>
    <div class="item last"></div>
</div>

<?php
if (isset($pagination))
{
    echo $pagination;
}
?>