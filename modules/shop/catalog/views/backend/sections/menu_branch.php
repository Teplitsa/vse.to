<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php

function render_sections_menu_branch(
    ModelsTree $sections,
    Model $parent = NULL,
    array $unfolded,
    /*$update_url, $delete_url, $up_url, $down_url, */$toggle_url, $url, $section_id
)
{
    foreach ($sections->children($parent) as $section)
    {
        /*
        $_update_url = str_replace('{{id}}', $section->id, $update_url);
        $_delete_url = str_replace('{{id}}', $section->id, $delete_url);
        $_up_url     = str_replace('{{id}}', $section->id, $up_url);
        $_down_url   = str_replace('{{id}}', $section->id, $down_url);
         */
        $_url        = str_replace('{{id}}', $section->id, $url);

        // Highlight current section
        if ($section->id == $section_id) {
            $selected = ' selected';
        } else {
            $selected = '';
        }

        if ( ! $section->section_active) {
            $active = ' capt_inactive';
        } elseif ( ! $section->active) {
            $active = ' inactive';
        } else {
            $active = '';
        }

        echo '<div class="section">';

        /*
        echo
            '   <div class="ctl">'
          .         View_Helper_Admin::image_control($_delete_url, 'Удалить раздел', 'controls/delete.gif', 'Удалить')
          .         View_Helper_Admin::image_control($_update_url, 'Редактировать раздел', 'controls/edit.gif', 'Редактировать')
          .         View_Helper_Admin::image_control($_up_url, 'Переместить вверх', 'controls/up.gif', 'Вверх')
          .         View_Helper_Admin::image_control($_down_url, 'Переместить вниз', 'controls/down.gif', 'Вниз')
          . '   </div>';
         */

        echo
            '   <div class="caption_wrapper">'
          . '       <div class="level_pad" style="padding-left: ' . ($section->level-1)*15 . 'px">';

        if ($section->has_children)
        {
            // Render toggle on/off bullet
            $toggled_on = in_array($section->id, $unfolded);

            $_toggle_url = str_replace(
                array('{{id}}',     '{{toggle}}'),
                array($section->id, ($toggled_on ? 'off' : 'on')),
                $toggle_url
            );

            echo
                '       <a href="' . $_toggle_url . '"'
              .             ' class="toggle ' . ( $toggled_on ? '' : ' toggled_off') . '"'
              .             ' id="sections_toggle_' . $section->id . '"'
              .          '></a>';
        }
        else
        {
            $toggled_on = FALSE;
        }

        echo
            '           <a'
          . '               href="' . $_url . '"'
          . '               class="capt' . $selected . $active . '"'
          . '               title="Показать все события из раздела \'' . HTML::chars($section->caption) . '\'"'
          . '           >'
          .                 HTML::chars($section->caption)
          . '           </a>'
          . '       </div>'
          . '   </div>';

        echo '</div>';

        if ($section->has_children)
        {
            // Render wrapper for branch (used to redraw branch via ajax if it is folded & to toggle branch on/off)
            echo '<div id="sections_branch_' . $section->id . '" class="' . ($toggled_on ? '' : 'folded') . '">';

            if ($toggled_on)
            {
                render_sections_menu_branch(
                    $sections,
                    $section,
                    $unfolded,
                    /*$update_url, $delete_url, $up_url, $down_url, */$toggle_url, $url, $section_id
                );
            }

            echo '</div>';
        }
    }
}
