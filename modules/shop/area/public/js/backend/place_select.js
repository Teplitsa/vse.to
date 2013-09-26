/**
 * Initialize
 */
$(document).ready(function(){
    if (current_window)
    {
        // ----- Select user buttons
        $('#place_select').click(function(event){
            if ($(event.target).hasClass('place_select'))
            {
                // "Select place" link was pressed

                // Determine place id from link "id" attribute (id is like 'place_15')
                var place_id = $(event.target).attr('id').substr(6);

                // Peform an ajax request to get selected user
                var ajax_url = place_selected_url.replace('{{id}}', place_id);

                $.post(ajax_url, null, function(response) {
                    if (response)
                    {
                        //@FIXME: Security breach!
                        eval('var place = ' + response + ';');

                        // Execute custom actions on place selection
                        // (there should be a corresponding function defined in parent window)
                        if (parent.on_place_select)
                        {
                            parent.on_place_select(place)
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