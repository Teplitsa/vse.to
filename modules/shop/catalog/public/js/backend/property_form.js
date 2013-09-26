/**
 * Sections select callback (called from section select iframe)
 */
function on_sectiongroups_select(sectiongroup_ids)
{    
    if ( ! propsections_fieldset || ! on_sectiongroups_select_url)
        return;
       
    // Peform an ajax request to redraw form "additional sections" elements
    if (sectiongroup_ids.length)
    {
        sectiongroup_ids = sectiongroup_ids.join('_');
    }
    else
    {
        // dummy value when no ids are selected
        sectiongroup_ids = '_';
    }
    var url = on_sectiongroups_select_url
                .replace('{{sectiongroup_ids}}', sectiongroup_ids);

    $('#' + propsections_fieldset).html('Loading...');

    $.get(url, null, function(response){
        // Redraw "additional sections" fieldset
        if (response)
        {
            $('#' + propsections_fieldset).html(response);
        }
    });
}
