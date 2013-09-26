/**
 * Sections select callback (called from section select iframe)
 */
function on_sections_select(section_ids, sectiongroup_id)
{
    if ( ! sections_fieldset_ids || ! sections_fieldset_ids[sectiongroup_id] || ! on_sections_select_url)
        return;
    
    // Peform an ajax request to redraw form "additional sections" elements
    if (section_ids.length)
    {
        section_ids = section_ids.join('_');
    }
    else
    {
        // dummy value when no ids are selected
        section_ids = '_';
    }
    var url = on_sections_select_url
                .replace('{{section_ids}}', section_ids)
                .replace('{{sectiongroup_id}}', sectiongroup_id);

    $('#' + sections_fieldset_ids[sectiongroup_id]).html('Loading...');

    $.get(url, null, function(response){
        // Redraw "additional sections" fieldset
        if (response)
        {
            $('#' + sections_fieldset_ids[sectiongroup_id]).html(response);
        }
    });
}

/**
 * Redraw product properties when product main section is changed
 */
$(document).ready(function(){
    if ( ! product_form_name || ! properties_fieldset_id || ! properties_url)
        return;
    
    var f = jforms[product_form_name];
    var e = f.get_element('section_id');

    $('#' + e.id).change(function(){
        var section_id = e.get_value();

        $('#' + properties_fieldset_id).html('Loading...');

        $.get(properties_url + '?section_id=' + section_id, null, function(response){
            $('#' + properties_fieldset_id).html(response);

            f.get_element('section_id_original').set_value(section_id);
        });
    });
});
