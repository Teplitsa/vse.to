
/**
 * Change user in product form
 */
function on_lecturer_select(lecturer)
{
    var f = jforms['product1'];

    if (lecturer['id'])
    {
        f.get_element('lecturer_id').set_value(lecturer['id']);
        f.get_element('lecturer_name').set_value(lecturer['lecturer_name']);
    }
    else
    {
        f.get_element('lecturer_id').set_value(0);
        f.get_element('lecturer_name').set_value('--- лектор не указан ---');
    }
}