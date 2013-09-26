/**
 * Change user in order form
 */
function on_user_select(user)
{
    var f = jforms['order1'];

    // change "user_id" element value
    var e = f.get_element('user_id');
    if (user['id'])
    {
        e.set_value(user['id']);
        
        // Change personal data
        for (var name in {'user_name' : '', 'first_name' : '', 'last_name' : '', 'middle_name' : ''})
        {
            e = f.get_element(name);
            e.set_value(user[name]);
        }
    }
    else
    {
        e.set_value(0);
        f.get_element('user_name').set_value('---');
    }
}

/**
 * Apply product values in order product form
 */
function on_product_select(product)
{
    var f = jforms['orderproduct1'];
    
    for (var name in f.get_elements())
    {        
        if (product[name] !== undefined)
        {
            f.get_element(name).set_value(product[name]);
        }
    }
}