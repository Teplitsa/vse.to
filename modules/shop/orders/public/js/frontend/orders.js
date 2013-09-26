
$(function(){
    /**
     * Add products to cart via ajax
     */
    $('form.to_cart').submit(function(event){
        
        // Display "loading"
        $(event.target).addClass('adding');

        // Peform an ajax request in current context
        // Use standart callback to render the result
        $.get(event.target.action + '?context=' + widgets_context_uri, null, widgets_callback);
        
        event.stopPropagation();
        event.preventDefault();
    })
})