/**
 * Initialize
 */
$(document).ready(function(){
    if (current_window)
    {
        // ----- Choose product buttons
        $('#events_show').click(function(event){
            if ($(event.target).hasClass('product_choose'))
            {
                // "Choose Product" link was pressed

                // Determine product alias from link "alias" attribute (alias is like 'product_somealias')
                var product_alias = $(event.target).attr('id').substr(8);

                // Peform an ajax request to get selected user
                var ajax_url = product_selected_url.replace('{{alias}}', product_alias);

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