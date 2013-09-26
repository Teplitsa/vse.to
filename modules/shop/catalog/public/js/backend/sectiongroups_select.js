/**
 * Initialize
 */
$(document).ready(function(){
    if (current_window) // Are we inside iframe window?
    {
        var form = $('form[name="sectiongroups_select"]');
        
        form.submit(function(event){
            var sectiongroup_ids = [];
            
            var data = form.serializeArray();
            for (var i = 0; i < data.length; i++)
            {
                if (data[i].name == 'ids[]')
                {
                    sectiongroup_ids.push(data[i].value);
                }
            }
            
            // Execute callback in parent window
            if (parent.on_sectiongroups_select)
            {
                parent.on_sectiongroups_select(sectiongroup_ids);
            }

            // Close the window
            current_window.close();
            
            event.preventDefault();
        });
    }
});
