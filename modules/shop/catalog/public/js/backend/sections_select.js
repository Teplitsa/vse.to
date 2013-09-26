/**
 * Initialize
 */
$(document).ready(function(){
    if (current_window) // Are we inside iframe window?
    {
        var form = $('form[name="sections_select"]');
        
        form.submit(function(event){
            var section_ids = [];
            var sectiongroup_id = 0;
            
            var data = form.serializeArray();
            for (var i = 0; i < data.length; i++)
            {
                if (data[i].name == 'ids[]')
                {
                    section_ids.push(data[i].value);
                }

                if (data[i].name == 'sectiongroup_id')
                {
                    sectiongroup_id = data[i].value;
                }
            }
            
            // Execute callback in parent window
            if (parent.on_sections_select)
            {
                parent.on_sections_select(section_ids, sectiongroup_id);
            }

            // Close the window
            current_window.close();
            
            event.preventDefault();
        });
    }
});
