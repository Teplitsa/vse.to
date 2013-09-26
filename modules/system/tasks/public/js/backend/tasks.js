/**
 * Initialize
 */
$(function(){
    
    var widget_ids = [];

    $('.widget_task').each(function(i, widget){

        widget_ids.push(widget.id);
    });

    setInterval(function(){
        for (var i in widget_ids)
        {
            if (jWidget.widgets[widget_ids[i]])
            {
                jWidget.widgets[widget_ids[i]].ajax();
            }
        }
    }, 2000)
});

