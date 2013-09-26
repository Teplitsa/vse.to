/**
 * Sections select callback (called from section select iframe)
 */
function on_users_select(user_ids)
{
    if ( ! on_users_select_url)
        return;
    
    // Peform an ajax request to redraw form "additional sections" elements
    if (user_ids.length)
    {
        user_ids = user_ids.join('_');
    }
    else
    {
        // dummy value when no ids are selected
        user_ids = '_';
    }
    var url = on_users_select_url
                .replace('{{user_ids}}', user_ids);

    $('#' + users_fieldset_ids).html('Loading...');


    $.get(url, null, function(response){
        // Redraw "additional sections" fieldset
        if (response)
        {
            $('#' + users_fieldset_ids).html(response);
        }
    });
}

