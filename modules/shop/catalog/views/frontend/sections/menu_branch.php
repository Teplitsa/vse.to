<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php

function render_sections_menu_branch(
    ModelsTree $sections,
    Model $parent = NULL,
    array $unfolded,
    $toggle_url, $section_id
)
{
    foreach ($sections->children($parent) as $section)
    {   
        $_url = $section->uri_frontend(TRUE);

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

        echo
            '   <div class="caption_wrapper">'
          . '       <div class="level_pad" style="padding-left: ' . ($section->level-1)*15 . 'px">';

        if ($section->has_children)
        {
            // Render toggle on/off bullet
            $toggled_on = in_array($section->id, $unfolded);

            $_toggle_url = str_replace(
                array('{{path}}',     '{{toggle}}'),
                array($section->full_alias, ($toggled_on ? 'off' : 'on')),
                $toggle_url
            );
            
            echo
                '       <a href="' . $_toggle_url . '"'
              .             ' class="toggle ' . ( $toggled_on ? '' : ' toggled_off') . '"'
              .             ' id="sections_toggle_' . str_replace('/', '__', $section->full_alias) . '"'
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
            echo '<div id="sections_branch_' . str_replace('/', '__', $section->full_alias) . '" class="' . ($toggled_on ? '' : 'folded') . '">';

            if ($toggled_on)
            {
                render_sections_menu_branch(
                    $sections,
                    $section,
                    $unfolded,
                    $toggle_url, $section_id
                );
            }

            echo '</div>';
        }
    }
}
