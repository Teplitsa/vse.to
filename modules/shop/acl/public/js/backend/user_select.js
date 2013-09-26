/**
 * Initialize
 */
$(document).ready(function(){
    if (current_window)
    {
        // ----- Select user buttons
        $('#user_select').click(function(event){
            if ($(event.target).hasClass('user_select'))
            {
                // "Select user" link was pressed

                // Determine user id from link "id" attribute (id is like 'user_15')
                var user_id = $(event.target).attr('id').substr(5);

                // Peform an ajax request to get selected user
                var ajax_url = user_selected_url.replace('{{id}}', user_id);

                $.post(ajax_url, null, function(response) {
                    if (response)
                    {
                        //@FIXME: Security breach!
                        eval('var user = ' + response + ';');

                        // Execute custom actions on user selection
                        // (there should be a corresponding function defined in parent window)
                        if (parent.on_user_select)
                        {
                            parent.on_user_select(user)
                        }

                        // Close the window
                        current_window.close();
                    }
                });

                event.preventDefault();
            }
        });
    }
});