/**
 * Sections select callback (called from section select iframe)
 */
function on_towns_select(town_ids)
{
    if ( ! on_towns_select_url)
        return;
    
    // Peform an ajax request to redraw form "additional sections" elements
    if (town_ids.length)
    {
        town_ids = town_ids.join('_');
    }
    else
    {
        // dummy value when no ids are selected
        town_ids = '_';
    }
    var url = on_towns_select_url
                .replace('{{town_ids}}', town_ids);

    $('#' + towns_fieldset_ids).html('Loading...');


    $.get(url, null, function(response){
        // Redraw "additional sections" fieldset
        if (response)
        {
            $('#' + towns_fieldset_ids).html(response);
        }
    });
}

