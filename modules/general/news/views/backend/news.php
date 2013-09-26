<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$create_url = URL::to('backend/news', array('action'=>'create'), TRUE);
$update_url = URL::to('backend/news', array('action'=>'update', 'id' => '${id}'), TRUE);
$delete_url = URL::to('backend/news', array('action'=>'delete', 'id' => '${id}'), TRUE);
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Добавить новость</a>
</div>

<?php
if ( ! count($news))
    // No news
    return;
?>

<table class="table">
    <tr class="header">
        <th>&nbsp;&nbsp;&nbsp;</th>

        <?php
        $columns = array(
            'date'    => 'Дата',
            'caption' => 'Заголовок'
        );

        echo View_Helper_Admin::table_header($columns, 'news_order', 'news_desc');
        ?>
    </tr>

<?php
foreach ($news as $newsitem)
:
    $_delete_url = str_replace('${id}', $newsitem->id, $delete_url);
    $_update_url = str_replace('${id}', $newsitem->id, $update_url);
?>
    <tr>
        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить новость', 'controls/delete.gif', 'Удалить'); ?>
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать новость', 'controls/edit.gif', 'Редактировать'); ?>
        </td>

        <?php
        foreach (array_keys($columns) as $field):
        ?>
            <td>
            <?php
            if (isset($newsitem->$field)) {
                if ($newsitem->$field instanceof DateTime) echo $newsitem->$field->format(Kohana::config('datetime.date_format'));
                else echo HTML::chars ($newsitem->$field);
            } else {
                echo '&nbsp';
            }
            ?>
            </td>

        <?php
        endforeach;
        ?>
    </tr>
<?php
endforeach;
?>
</table>

<?php
if (isset($pagination))
{
    echo $pagination;
}
?>