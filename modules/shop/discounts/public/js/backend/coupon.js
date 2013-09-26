
/**
 * Change user in coupon form
 */
function on_user_select(user)
{
    var f = jforms['coupon1'];

    if (user['id'])
    {
        f.get_element('user_id').set_value(user['id']);
        f.get_element('user_name').set_value(user['user_name']);
    }
    else
    {
        f.get_element('user_id').set_value(0);
        f.get_element('user_name').set_value('--- для всех пользователей ---');
    }
}