/**
 * Sections select callback (called from section select iframe)
 */
function on_organizers_select(organizer_ids)
{
    if ( ! on_organizers_select_url)
        return;
    
    // Peform an ajax request to redraw form "additional sections" elements
    if (organizer_ids.length)
    {
        organizer_ids = organizer_ids.join('_');
    }
    else
    {
        // dummy value when no ids are selected
        organizer_ids = '_';
    }
    var url = on_organizers_select_url
                .replace('{{organizer_ids}}', organizer_ids);

    $('#' + organizers_fieldset_ids).html('Loading...');


    $.get(url, null, function(response){
        // Redraw "additional sections" fieldset
        if (response)
        {
            $('#' + organizers_fieldset_ids).html(response);
        }
    });
}

