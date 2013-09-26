
/**
 * Change user in product form
 */
function on_user_select(user)
{
    var f = jforms['message1'];

    if (user['id'])
    {
        f.get_element('receiver_id').set_value(user['id']);
        f.get_element('user_name').set_value(user['user_name']);
    }
    else
    {
        f.get_element('receiver_id').set_value(0);
        f.get_element('user_name').set_value('--- получатель не указан ---');
    }
}