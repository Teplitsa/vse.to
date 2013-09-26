
/**
 * Initialize widgets
 */
$(function(){
    // standart widgets    
    $('.widget').each(function(i,container){
        var widget = new jWidget();
        widget.init(container.id);
    });

    // popup widgets
    $('.popup_widget').each(function(i, container){
        var widget = new jWidgetPopup();
        widget.init(container.id);
        widget.init_popup(container.id);
    });
})

// -----------------------------------------------------------------------------
// jWidget
// -----------------------------------------------------------------------------
/**
 * Initialize new widget from dom element
 */
function jWidget()
{}

/**
 * Extend the object by copying its prototype
 * Constructor of the ancestor is available via this.ancestor_class
 */
jWidget.extend = function(descendant)
{
    // make the jWidget constructor available in descendant via this.jWidget
    descendant.prototype['jWidget'] = jWidget;

    for (var m in jWidget.prototype)
    {
        descendant.prototype[m] = jWidget.prototype[m];
    }
}

/**
 * Collection of all registered widgets {widget_id : widget_object}
 */
jWidget.widgets = {};

/**
 * Callback function for ajax requests
 */
jWidget.ajax_callback = function(data)
{
    if ( ! data)
        return;

    //@FIXME: security breach
    eval('var response = ' + data + ';');

    // Redraw widgets
    if (response.widgets)
    {
        for (var widget_id in response.widgets)
        {
            if (jWidget.widgets[widget_id])
            {
                jWidget.widgets[widget_id].redraw(response.widgets[widget_id]);
            }
        }
    }

    // Display messages as alerts
    if (response.messages)
    {
        for (var i = 0; i < response.messages.length; i++)
        {
            alert(response.messages[i].text);
        }
    }
}

/**
 * Initialize the specified widget
 *
 * @param id widget id (id of the dom element that serves as a container for the widget)
 */
jWidget.prototype.init = function(id)
{
    var self = this;
    
    // ----- save widget id and register widget
    this.id = id;
    jWidget.widgets[id] = this;
    
    // ----- setup event handlers
    $('#' + id).click(function(event) {
        // Listen to the clicks on links with 'ajax' class inside the widget
        
        var target;
        
        if (event.target.tagName != 'A')
        {
            // Check parents of the clicked element - may be this is an element inside the link
            target = $(event.target).parent('a');
        }
        else
        {
            target = $(event.target);
        }

        if (target.hasClass('ajax'))
        {
            // links, that are supposed to redraw the widget, must have an 'ajax' class
            
            // Execute the handler
            self.click_handler(target);

            event.stopPropagation();
            event.preventDefault();
        }        
    });
        $('#' + id).submit(function(event){
            // Listen to the submits of forms with 'ajax' class inside the widget

            var target = $(event.target);

            if (target.hasClass('ajax'))
            {
                // forms, that are supposed to be submitted via ajax, must have an 'ajax' class

                // Execute the handler
                self.submit_handler(target);

                event.stopPropagation();
                event.preventDefault();
            }
        });
    
}

/**
 * This function is called when a link with 'ajax' class inside the widget is clicked
 *
 * @param link jQuery object, created from the clicked link
 */
jWidget.prototype.click_handler = function(link)
{
    this.ajax(link.attr('href'));
}

/**
 * This function is called when a form with 'ajax' class is submitted inside the widget
 *
 * @param form jQuery object, created from the form being submitted
 */
jWidget.prototype.submit_handler = function(form)
{
    this.ajax(form.attr('action'), form.serializeArray());
}

/**
 * Some action to peform before an ajax request (display "loading...", for example)
 */
jWidget.prototype.before_ajax = function(url, data)
{
    
}

/**
 * Peform an ajax request for the specified widget.
 * if data is specified, than a POST request will be made, a GET request
 * wil be made otherwise.
 *
 * @param url Url to send the request to
 * @param data Request data
 */
jWidget.prototype.ajax = function(url, data)
{
    // url that will be used for ajax request
    var ajax_url = $('#' + this.id + '_url').html();
    if ( ! ajax_url)
    {
        // send request to the clicked url/form action itself
        ajax_url = url;
    }

    if ( ! ajax_url)
        return; // Failed to retrieve url and it was not supplied to function

    // context uri / url
    var context_uri = $('#' + this.id + '_context_uri').html();
    if (context_uri)
    {
        ajax_url += '?context=' + context_uri;
    }
    else
    {
        // used clicked url/form action as context
        ajax_url += '?context=' + url;
    }

    this.before_ajax(url, data);

    if (data)
    {
        $.post(ajax_url, data, jWidget.ajax_callback);
    }
    else
    {
        $.get(ajax_url, null, jWidget.ajax_callback);
    }
}


/**
 * Redraw widget contents
 */
jWidget.prototype.redraw = function(html)
{
    $('#' + this.id).html(html);
}


// -----------------------------------------------------------------------------
// jWidgetPopup
// -----------------------------------------------------------------------------
/**
 * Initialize new widget from dom element
 */
function jWidgetPopup()
{}

jWidget.extend(jWidgetPopup);

/**
 * Initialize popup widget
 */
jWidgetPopup.prototype.init_popup = function(id)
{
    this.layer   = $('#' + id + '_layer');
    this.blocker = $('#' + id + '_blocker');
    this.wrapper = $('#' + id + '_wrapper');

    this.config = {
        'alignment'       : 'center',
        'stretch_blocker' : true
    }

    var self = this;
    // Bind events to links that should open the popup
    // This class of those links must be equal to the id of the popup widget
    $('a.' + id).each(function(i, link){
        $(link).click(function(event){
            
            self.show();
            self.ajax(link.href);

            event.stopPropagation();
            event.preventDefault();
        });
    });

    // Bind event to the close popup button
    $('#' + id).click(function(event) {
        if (event.target.id == self.id + '_close')
        {
            self.hide();

            event.stopPropagation();
            event.preventDefault();
        }
    });
}

/**
 * Show popup
 */
jWidgetPopup.prototype.show = function()
{
    // Show popup layer
    this.layer.show();

    // Stretch blocker
    this.stretch_blocker();

    // Refresh popup position popup
    this.reposition();
}

/**
 * Stretch blocker vertically to 100% of document
 */
jWidgetPopup.prototype.stretch_blocker = function()
{
    if (this.config['stretch_blocker'])
    {
        this.layer.height($(document).height());
    }
}

/**
 * Hide popup
 */
jWidgetPopup.prototype.hide = function()
{
    this.layer.hide();
}

/**
 * Reposition popup depending on the alignment
 */
jWidgetPopup.prototype.reposition = function()
{
    if (this.config['alignment'] == 'center')
    {
        // Center popup in browser window
        var top = ($(window).height() - this.wrapper.height()) / 2;
        if (top < 0) {
            top = 0;
        }

        var left = ($(window).width() - this.wrapper.width()) / 2;
        if (left < 0) {
            left = 0;
        }
        
        this.wrapper
            .css('top',  $(window).scrollTop()  + top)
            .css('left', $(window).scrollLeft() + left)
    }
}

/**
 * Display "loading..." message while ajax request is peformed
 */
jWidgetPopup.prototype.before_ajax = function()
{
    this.redraw('<div class="loading">Загрузка...</div>');
}

/**
 * Redraw widget contents
 */
jWidgetPopup.prototype.redraw = function(html)
{
    $('#' + this.id).html(html);
    this.reposition();
}

/**
 * Redraw popup content. Content is obtained via ajax request to url
 */
/*
jWidgetPopup.prototype.redraw = function(url, data)
{
    // Reset popup contents
    this.popup_content.html('<div class="loading">Загрузка</div>');
    this.reposition();

    // Load content via ajax request and reposition popup on complete
    var self = this;
    this.popup_content.load(url, data, function() {
        // Refresh popup position
        self.reposition();
        // Stretch blocker (new content can increase document height)
        self.stretch_blocker();

        // Re-init forms in popup content for IE 6-7
        if ($.browser.msie)
        {
            self.init_forms();
        }
    });
}
*/