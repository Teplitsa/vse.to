<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Build brands, subbrands and categories html for given product
 *
 * @param Model_Product $product
 * @return array(brands_html, subbrands_html, cat_html)
 */
function categories(Model_Product $product)
{
    $sectiongroups = Model::fly('Model_SectionGroup')->find_all_cached();
    foreach ($sectiongroups as $sectiongroup) {
        $sections[$sectiongroup->id] = Model::fly('Model_Section')->find_all_active_cached($sectiongroup->id);
    }

    $section_ids = $product->sections;

    $series_html = array();
    $cat_html = array();
    
    foreach ($sectiongroups as $sectiongroup) {
        if (isset($section_ids[$sectiongroup->id]))
        {
            foreach (array_keys($section_ids[$sectiongroup->id]) as $id)
            {
                $sec = $sections[$sectiongroup->id][$id];

                if ($sec->level <= 2)
                {
                    $cat_html[$sectiongroup->id][$sec->id] = '<a href="' . URL::site($sec->uri_frontend()) . '">' . $sec->caption . '</a>';
                }
                else
                {
                    foreach ($sections[$sectiongroup->id]->parents($sec) as $par)
                    {
                        if ($par->level == 2) {
                            $cat_html[$sectiongroup->id][$par->id] = '<a href="' . URL::site($par->uri_frontend()) . '">' . $par->caption . '</a>';
                            break;
                        }
                    }
                }
            }
        }
    }
    
    foreach ($cat_html as $sectiongroup_id => $cat_html_sectiongroup) {
        $cat_html_sectiongroup = ( ! empty($cat_html_sectiongroup))    ? implode(' , ', $cat_html_sectiongroup)    : '---';
        $cat_html[$sectiongroup_id] = $cat_html_sectiongroup;
    }
    
    return $cat_html;
}

