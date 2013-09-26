/**
 * Initialize
 */
$(document).ready(function(){
    if (current_window) // Are we inside iframe window?
    {
        var form = $('form[name="towns_select"]');
        
        form.submit(function(event){
            var town_ids = [];
            
            var data = form.serializeArray();
            for (var i = 0; i < data.length; i++)
            {
                if (data[i].name == 'ids[]')
                {
                    town_ids.push(data[i].value);
                }
            }
            
            // Execute callback in parent window
            if (parent.on_towns_select)
            {
                parent.on_towns_select(town_ids);
            }

            // Close the window
            current_window.close();
            
            event.preventDefault();
        });
    }
});
