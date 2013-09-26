
/**
 * Change place in product form
 */
function on_place_select(place)
{
    var f = jforms['telemost1'];

    if (place['id'])
    {
        f.get_element('place_id').set_value(place['id']);
        f.get_element('place_name').set_value(place['town']+": "+place['name']);
    }
    else
    {
        f.get_element('place_id').set_value(0);
        f.get_element('place_name').set_value('');
    }
}