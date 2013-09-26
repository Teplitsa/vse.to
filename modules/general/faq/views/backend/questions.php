<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$create_url = URL::to('backend/faq', array('action'=>'create'), TRUE);
$update_url = URL::to('backend/faq', array('action'=>'update', 'id' => '{{id}}'), TRUE);
$delete_url = URL::to('backend/faq', array('action'=>'delete', 'id' => '{{id}}'), TRUE);

$up_url   = URL::to('backend/faq', array('action'=>'up', 'id' => '{{id}}'), TRUE);
$down_url = URL::to('backend/faq', array('action'=>'down', 'id' => '{{id}}'), TRUE);
if ( ! $desc)
{
    list($up_url, $down_url) = array($down_url, $up_url);
}

$multi_action_uri = URL::uri_to('backend/faq', array('action'=>'multi'), TRUE);
?>

<div class="buttons">
    <a href="<?php echo $create_url; ?>" class="button button_add">Новый вопрос</a>
</div>

<?php
if ( ! count($questions))
    // No menus
    return;
?>

<?php echo View_Helper_Admin::multi_action_form_open($multi_action_uri); ?>
<table class="questions table">
    <tr class="header">
        <th><?php echo View_Helper_Admin::multi_action_select_all(); ?></th>
        
        <?php
        $columns = array(
            'date' => 'Дата',
            'question_preview'   => array(
                'label'  => 'Вопрос',
                'sortable' => FALSE
            ),
            'active' => 'Акт'
        );

        echo View_Helper_Admin::table_header($columns, 'questions_order', 'questions_desc');
        ?>
        
        <th>&nbsp;&nbsp;&nbsp;</th>
    </tr>

<?php
foreach ($questions as $question)
:
    $_delete_url = str_replace('{{id}}', $question->id, $delete_url);
    $_update_url = str_replace('{{id}}', $question->id, $update_url);
    $_up_url     = str_replace('{{id}}', $question->id, $up_url);
    $_down_url   = str_replace('{{id}}', $question->id, $down_url);

    $class =
              ($question->active   ? 'active'   : 'inactive')
      . ' ' . ($question->answered ? 'answered' : 'unanswered');
?>
    <tr class="<?php echo $class; ?>">
        <td class="multi_ctl">
            <?php echo View_Helper_Admin::multi_action_checkbox($question->id); ?>
        </td>
        
        <?php
        foreach (array_keys($columns) as $field)
        {
            switch ($field)
            {
                case 'question_preview':
                    echo
                        '<td>'
                      . '   <a href="' . $_update_url . '">'
                      .         HTML::chars($question->$field)
                      . '   </a>'
                      . '</td>';
                    break;

                case 'active':
                    echo '<td class="c">';

                    if ( ! empty($question->$field)) {
                        echo View_Helper_Admin::image('controls/on.gif', 'Да');
                    } else {
                        echo View_Helper_Admin::image('controls/off.gif', 'Нет');
                    }

                    echo '</td>';
                    break;

                default:
                    echo '<td>';

                    if (isset($question->$field) && trim($question->$field) !== '') {
                        echo HTML::chars($question[$field]);
                    } else {
                        echo '&nbsp';
                    }

                    echo '</td>';
            }
        }
        ?>

        <td class="ctl">
            <?php echo View_Helper_Admin::image_control($_update_url, 'Редактировать блок', 'controls/edit.gif', 'Редактировать'); ?>
            <?php echo View_Helper_Admin::image_control($_delete_url, 'Удалить блок', 'controls/delete.gif', 'Удалить'); ?>
            <?php //echo View_Helper_Admin::image_control($_up_url, 'Переместить вверх', 'controls/up.gif', 'Вверх'); ?>
            <?php //echo View_Helper_Admin::image_control($_down_url, 'Переместить вниз', 'controls/down.gif', 'Вниз'); ?>
        </td>
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

<?php
echo View_Helper_Admin::multi_actions(array(
    array('action' => 'multi_delete', 'label' => 'Удалить', 'class' => 'button_delete')
));
?>

<?php echo View_Helper_Admin::multi_action_form_close(); ?>