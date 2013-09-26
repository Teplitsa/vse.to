/**
 * Initialize
 */
$(document).ready(function(){
    if (current_window)
    {
        // ----- Select lecturer buttons
        $('#lecturer_select').click(function(event){
            if ($(event.target).hasClass('lecturer_select'))
            {
                // "Select lecturer" link was pressed

                // Determine lecturer id from link "id" attribute (id is like 'lecturer_15')
                var lecturer_id = $(event.target).attr('id').substr(9);
                // Peform an ajax request to get selected lecturer
                var ajax_url = lecturer_selected_url.replace('{{id}}', lecturer_id);

                $.post(ajax_url, null, function(response) {
                    if (response)
                    {
                        //@FIXME: Security breach!
                        eval('var lecturer = ' + response + ';');

                        // Execute custom actions on lecturer selection
                        // (there should be a corresponding function defined in parent window)
                        if (parent.on_lecturer_select)
                        {
                            parent.on_lecturer_select(lecturer)
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