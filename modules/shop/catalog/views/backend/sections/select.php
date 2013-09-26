<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
// ----- Set up urls
// Submit results to previous url
$sections_select_uri = URL::uri_back();
?>

<?php
echo View_Helper_Admin::multi_action_form_open($sections_select_uri, array('name' => 'sections_select'));
echo Form_Helper::hidden('sectiongroup_id', $sectiongroup_id);
?>

<table class="sections_select table">
    <tr class="header">
        <th><?php echo View_Helper_Admin::multi_action_select_all(); ?></th>

        <?php
            $columns = array(
                'caption' => 'Название',
                'section_active'  => 'Акт.',
            );
            echo View_Helper_Admin::table_header($columns, 'cat_sorder', 'cat_sdesc');
        ?>
    </tr>

<?php
foreach ($sections as $section)
:
    if ( ! $section->section_active) {
        $active = 'capt_inactive';
    } elseif ( ! $section->active) {
        $active = 'inactive';
    } else {
        $active = '';
    }
?>
    <tr>
        <td class="multi_ctl">
            <?php echo View_Helper_Admin::multi_action_checkbox($section->id); ?>
        </td>

    <?php
        foreach (array_keys($columns) as $field)
        {
            switch ($field)
            {
                case 'caption':
                    echo
                        '<td>'
                      . '   <div style="padding-left: ' . ($section->level-1)*15 . 'px">'
                      . '       <div class="capt ' . $active . '">'
                      .             HTML::chars($section->$field)
                      . '       </div>'
                      . '   </div>'
                      . '</td>';
                    break;

                case 'section_active':
                    echo '<td class="c">';

                    if ( ! empty($section->$field)) {
                        echo View_Helper_Admin::image('controls/on.gif', 'Да');
                    } else {
                        echo View_Helper_Admin::image('controls/off.gif', 'Нет');
                    }

                    echo '</td>';
                    break;

                default:
                    echo '<td>';

                    if (isset($section->$field) && trim($section->$field) !== '') {
                        echo HTML::chars($section[$field]);
                    } else {
                        echo '&nbsp';
                    }

                    echo '</td>';
            }
        }
    ?>
    </tr>
<?php
endforeach;
?>
</table>

<?php
echo View_Helper_Admin::multi_actions(array(
    array('action' => 'sections_select', 'label' => 'Выбрать', 'class' => 'button_select')
));
?>

<?php echo View_Helper_Admin::multi_action_form_close(); ?>