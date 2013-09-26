/**
 * Initialize
 */
$(document).ready(function(){
    if (current_window)
    {
        // ----- Select product buttons
        // using "delegate" technique - Instead of attaching an event to the every button,
        // we attach the single event to the whole container itself.
        // The button pressed is determined by 'target' of the event
        $('#product_select').click(function(event){
            if ($(event.target).hasClass('product_select'))
            {
                // "Select product" link was pressed

                // Determine product id from link "id" attribute (id is like 'product_15')
                var product_id = $(event.target).attr('id').substr(8);

                // Peform an ajax request to get selected product
                var ajax_url = product_selected_url.replace('{{id}}', product_id);
                $.post(ajax_url, null, function(response) {
                    if (response)
                    {
                        //@FIXME: Security breach!
                        eval('var product = ' + response + ';');

                        // Execute custom actions on product_selection
                        // (there should be an function defined in parent window)
                        if (parent.on_product_select)
                        {
                            parent.on_product_select(product)
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
