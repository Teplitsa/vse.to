$(function(){

    // ----- "Toggle all" support
    $('form').each(function(i, form){
        $('.toggle_all', form).each(function(i, toggle){

            // Toggle all form checkboxes by pattern,
            // where pattern is obtained form the "toggle" name
            // by stripping of 'toggle_all-' from the beginning
            var pattern = toggle.name.substr(11);

            // Create regexp from pattern (escape special chars)
            var re = pattern.replace('*', "\\w+");
            re = re.replace(/([\[\]])/g, "\\$1");
            re = new RegExp(re);

            $(toggle).click(function(){

                var value = this.checked;

                // Toggle all checkboxes with names matching the pattern
                $('input[type=checkbox]', form).each(function(i, chk){                    
                    if (re.test(chk.name)) {
                        chk.checked = value;
                    }
                });
            });
        });
    });

    // ----- Custom select design
    restyle_selects();
});

/******************************************************************************
 * Custom selects
 ******************************************************************************/
function restyle_selects(selects_ids)
{
    // Store all select options popups here (ie6)
    restyle_selects.select_popups = [];
    
    if (!selects_ids) {
        selects_arr = $('select');
    } else {
        selects_arr = new Array();
        for (i = 0; i < selects_ids.length; i++)
        {
            selects_arr[i] = document.getElementById(selects_ids[i]);
        }        
    }
    
    $(selects_arr).each(function(j, select){
        // Wrap select element with decorators

        wrap_class = select.parentElement.className;            

        if (wrap_class == "select_disabled" || wrap_class == "select") {
            $(select).unwrap();
            select.parentElement.removeChild(document.getElementById(select.id+'_value'));

        }
        
        if (select.className =="disabled") {
            $(select).wrap('<div class="select_disabled" />');        
        } else {            
            $(select).wrap('<div class="select" />');                    
        }       
        
        var value = $('<div id="'+select.id+'_value" class="value" />');
        value.insertBefore(select);            
        // Add value container

        // Update value on select
        var i = select.selectedIndex;
        if (select.options.length && i >= 0)
        {
            value.html(select.options[i].innerHTML);
        }

        $(select).change(function(){
            var i = select.selectedIndex;
            if (select.options.length && i >= 0)
            {
                value.html(select.options[i].innerHTML);
            }
        });

        // Apply dimensions
        var wrapper = $(select).parent();
            
        var w = $(select).width();

        // Increase select width
        //$(select).width(w - parseInt(wrapper.css('padding-right')));

        // Adjust wrapper width
        wrapper.width(w-12);

        // Adjust value width
        value.width(w - parseInt(value.css('padding-left')) - parseInt(value.css('padding-right')));
        

        if ($.browser.msie && $.browser.version <= 6)
        {            
            // ----- IE6 only
            // Hide the select element
            $(select).hide();

            // Create options popup
            var options = $('<div class="select_options" />');
            $(document.body).prepend(options);

            restyle_selects.select_popups.push(options);

            // Stretch options to the width of wrapper
            //options.width(wrapper.width());

            // Append options from select
            for (i = 0; i < select.options.length; i++)
            {
                var option = '<a href="#' + i + '">' + select.options[i].text + '</a>';
                options.append(option);
            }

            // Select the option from list when clicked
            options.click(function(event){
                // Determine the index of option selected
                if (event.target.href)
                {
                    var ind = event.target.href.match(/#(\d+)/);
                    if (ind.length)
                    {
                        ind = parseInt(ind[1]);

                        select.selectedIndex = ind;
                        $(select).trigger('change');

                    }

                    options.hide();
                    event.preventDefault();
                }
            });

            // Show options popup for this select
            wrapper.click(function(event){
                // Close all other popups
                restyle_selects.hide_popups();

                // Show this popup
                options.show();
                
                // Move to the bottom of wrapper
                options.offset({
                    top  : wrapper.position().top + wrapper.height(),
                    left : wrapper.position().left
                });

                event.stopPropagation();
            });
        }
    });

    if ($.browser.msie && $.browser.version <= 6)
    {
        // Close all options when document is clicked
        $(document).click(function(){
            restyle_selects.hide_popups();
        })
    }
}

/**
 * Hide all select options popups (ie6)
 */
restyle_selects.hide_popups = function()
{
    for (i = 0; i < restyle_selects.select_popups.length; i++)
    {
        restyle_selects.select_popups[i].hide();
    }
}

/******************************************************************************
 * Tabs
 ******************************************************************************/
/**
 * Initialize tabs
 */
function Tabs(tabs_id)
{
    var self = this;
    
    // Find the container that holds the id of currently selected tab
    this.current_tab_cont = $("#" + tabs_id + "-current_tab");
    this.current_tab_id = null;

    // Tabs {id : {tab : jQuery, content : jQuery}}
    this.tabs = {};
    
    // Find all tabs and corresponding contents
    $("a[href^='#" + tabs_id +"']").each(function(i, tab) {
        // Extract tab id
        var matches = tab.href.match(/#(.*)/);
        if (matches)
        {
            // Find content with the same id
            var tab_id = matches[1];
            var content = $('#' + tab_id);

            if (content.length)
            {
                // Corresponding content was found
                self.tabs[tab_id] = {
                    'tab'     : $(tab),
                    'content' : content
                }

                // Select the tab when it is clicked
                $(tab).click(function(event) {
                    self.select(tab_id);
                    event.preventDefault();
                });
            }
        }
    });


    // Select the current tab and deselect the other tabs
    var current_tab_id = this.get_current_tab_id();

    for (var tab_id in this.tabs)
    {
        if ( ! current_tab_id || current_tab_id == tab_id)
        {
            // Highlight the current tab
            this.select(tab_id, true);
            current_tab_id = tab_id;
        }
        else
        {
            // Hide all other tabs
            this.tabs[tab_id].content.hide();
        }
    }
}

/**
 * Select tab with specified tab id
 */
Tabs.prototype.select = function(tab_id, force)
{
    if ( ! this.tabs[tab_id])
    {
        // Invalid tab id was specified
        return;
    }

    var current_tab_id = this.get_current_tab_id();

    if (tab_id == current_tab_id && ! force)
    {
        // Tab is already selected
        return;
    }

    // Deselect current tab
    if (current_tab_id && this.tabs[current_tab_id])
    {
        // Hide content
        this.tabs[current_tab_id].content.hide();

        // Unhighlight
        this.tabs[current_tab_id].tab.removeClass('selected');
    }

    // Show the desired tab
    this.tabs[tab_id].content.show();
    this.tabs[tab_id].tab.addClass('selected');

    // Update current tab id
    this.set_current_tab_id(tab_id);
}

/**
 * Get the id of current tab
 */
Tabs.prototype.get_current_tab_id = function()
{
    if (this.current_tab_cont.length)
    {
        // Container is assumed to be an "input" element
        //@TODO: Can be another element
        return this.current_tab_cont.val();
    }
    else
    {
        return this.current_tab_id;
    }
}

/**
 * Set the id of current tab
 */
Tabs.prototype.set_current_tab_id = function(tab_id)
{
    if (this.current_tab_cont.length)
    {
        // Container is assumed to be an "input" element
        //@TODO: Can be another element
        this.current_tab_cont.val(tab_id);
    }
    else
    {
        this.current_tab_id = tab_id;
    }
}
