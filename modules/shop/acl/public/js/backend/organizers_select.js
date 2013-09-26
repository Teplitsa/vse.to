/**
 * Initialize
 */
$(document).ready(function(){
    if (current_window) // Are we inside iframe window?
    {
        var form = $('form[name="organizers_select"]');
        
        form.submit(function(event){
            var organizer_ids = [];
            
            var data = form.serializeArray();
            for (var i = 0; i < data.length; i++)
            {
                if (data[i].name == 'ids[]')
                {
                    organizer_ids.push(data[i].value);
                }
            }
            
            // Execute callback in parent window
            if (parent.on_organizers_select)
            {
                parent.on_organizers_select(organizer_ids);
            }

            // Close the window
            current_window.close();
            
            event.preventDefault();
        });
    }
});
