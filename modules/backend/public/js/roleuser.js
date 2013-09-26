
/**
 * Change user in product form
 */
function on_user_select(user)
{
    var f = jforms['product1'];

    if (user['id'])
    {
        f.get_element('role_id').set_value(user['id']);
        f.get_element('role_name').set_value(user['user_name']);
    }
    else
    {
        f.get_element('role_id').set_value(0);
        f.get_element('role_name').set_value('--- пользователь не указан ---');
    }
}