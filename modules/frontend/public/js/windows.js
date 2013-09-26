
/**
 * Global variable holding instances of all opened windows
 */
var windows = {};

/**
 * Global uid used to generate unqiue window names
 */
var window_uid = 0;

/**
 * Stores the reference to the current window (for document inside the window)
 */
var current_window;

/**
 * Initialize
 */
$(document).ready(function(){

    // Open window when a link with '.open_window' class is clicked
    $('.open_window').each(function(i, link){
        $(link).click(function(event){

            // Try to determine window dimensions from class n
            var w, h;
            var dimensions = $(this).attr('class').match(/dim(\d+)x(\d+)/);
            if (dimensions)
            {                
                w = parseInt(dimensions[1]);
                h = parseInt(dimensions[2]);
            }
            
            var wnd = new jWindow(this.href, w, h);
            wnd.show();

            event.preventDefault();
        });
    });

    if (parent != window)
    {
        // ----- This is a window (we are inside iframe)
        // Obtain the reference to the jWindow object, representing
        // this window in parent document
        current_window = parent.windows[window.frameElement.id];

        // Init "close window" buttons
        $('.close_window').each(function(i, link){
            $(link).click(function(event){
                current_window.close();

                event.preventDefault();
            });
        });
    }
    else
    {
        // ----- We are inside parent window
        // Resize blocker together with window
        $(window).resize(function(event){
            jWindow.stretchBlocker();
        });
    }
});



/**
 * jWindow
 */
function jWindow(url, width, height)
{
    window_uid++;
    this.id = 'wnd_' + window_uid;
    
    this.url    = url;
    this.width  = width  || 400;
    this.height = height || 500;

    this.header_height = 34;

    this.o = $('<iframe class="window" id="' + this.id + '" frameborder="0" framespacing="0"></iframe>');
    this.o.appendTo(document.body);

    // Store this window in global array
    windows[this.id] = this;
}

/**
 * Show and center the window
 */
jWindow.prototype.show = function()
{
    // Show blocker
    jWindow.showBlocker();

    // Show window
    this.o
        .width(this.width)
        .height(this.height + this.header_height) // [!] Add window header size
        .show()
        .attr('src', this.url + '?window=' + this.width + 'x' + this.height);

    this.center();
}

/**
 * Center window in the viewport
 */
jWindow.prototype.center = function()
{
    var top  = (($(window).height() - this.height) >> 1) + $(document).scrollTop();
    var left = (($(window).width()  - this.width)  >> 1) + $(document).scrollLeft();

    this.o
        .css('top',  top  + 'px')
        .css('left', left + 'px')
}

/**
 * Hide the window
 */
jWindow.prototype.hide = function()
{
    // Hide blocker
    jWindow.hideBlocker();

    // Hide window
    this.o.hide();
}

/**
 * Close (hide and destroy) the window
 */
jWindow.prototype.close = function()
{
    // Hide window
    this.hide();

    // Destroy DOM object
    this.o.remove();

    // Unset window object
    windows[this.id] = null;
}

/**
 * Stretch blocker to the document size
 */
jWindow.stretchBlocker = function()
{
    $('#blocker')
        .width($(document).width())
        .height($(document).height())
}

/**
 * Show blocker
 */
jWindow.showBlocker = function()
{
    jWindow.stretchBlocker();    
    $('#blocker').show();
}

/**
 * Hide blocker
 */
jWindow.hideBlocker = function()
{
    $('#blocker').hide();
}