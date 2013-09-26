/**
 * Initialize
 */
$(document).ready(function(){
    if (current_window) // Are we inside iframe window?
    {
        var form = $('form[name="users_select"]');
        
        form.submit(function(event){
            var user_ids = [];
            
            var data = form.serializeArray();
            for (var i = 0; i < data.length; i++)
            {
                if (data[i].name == 'ids[]')
                {
                    user_ids.push(data[i].value);
                }
            }
            
            // Execute callback in parent window
            if (parent.on_users_select)
            {
                parent.on_users_select(user_ids);
            }

            // Close the window
            current_window.close();
            
            event.preventDefault();
        });
    }
});
