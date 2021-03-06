
function copyPrototype(descendant, parent)
{
    var sConstructor = parent.toString();
    var aMatch = sConstructor.match( /\s*function (.*)\(/ );

    if ( aMatch != null )
    {
        descendant.prototype[aMatch[1]] = parent;
    }

    for (var m in parent.prototype)
    {
        descendant.prototype[m] = parent.prototype[m];
    }
}

// Global forms contatiner
var jforms = {};

/*******************************************************************************
 * jForm
 ******************************************************************************/
function jForm(name, id)
{
    // Form name
    this.name = name;
    // Form id
    this.id = id;

    // Form elements {name => element}
    this.elements = {};

    // Attach to container
    jforms[name] = this;    
}

/**
 * Initialize form and elements
 */
jForm.prototype.init = function()
{
    // Init elements
    for (var name in this.elements)
    {
        if (this.elements[name].init)
        {
            this.elements[name].init();
        }
    }    
}

/**
 * Add a new element to the form
 */
jForm.prototype.add_element = function(e)
{
    e.form = this;
    this.elements[e.name] = e;
}

/**
 * Get form element by name
 */
jForm.prototype.get_element = function(name)
{
    return this.elements[name];
}

/**
 * Return all form elements
 */
jForm.prototype.get_elements = function()
{
    return this.elements;
}

/**
 * Get all values from form
 */
jForm.prototype.get_values = function()
{
    var values = {};

    for (var name in this.elements)
    {
        e = this.elements[name];
        values[name] = e.get_value();
    }

    return values;
}

/**
 * Validate the form
 */
jForm.prototype.validate = function()
{
    // No errors so far
    var result = true;

    // Validate all elements in form
    for (var name in this.elements)
    {
        e = this.elements[name];
        if ( ! e.validate())
        {
            result = false;

            // Display element errors
            e.display_errors();
        }
    }

    return result;
}


/*******************************************************************************
 * jFormElement
 ******************************************************************************/
function jFormElement(name, id)
{
    // Element name
    this.name = name;
    // Element id
    this.id = id;

    this.disabled = false;

    // Element value has been changed by user?
    this.tampered = false;

    // Form to which this element relates
    this.form = null;

    // Element validators
    this.validators = [];

    // Names of the elements to validate together with this one (i.e. password & confirmation)
    this.validate_also = [];

    // Validation errors
    this.errors = [];
}

/**
 * Initialize form element.
 * 
 * Set up all necessary event handlers
 */
jFormElement.prototype.init = function()
{
    var self = this;

    // Init validators
    for (var i = 0; i < this.validators.length; i++)
    {
        if (this.validators[i].init)
        {
            this.validators[i].init();
        }
    }

    // Validate element when it looses focus
    // and highlight as active when it gets focus

    $('#' + this.id)
        .blur(function(){
            // Mark element as changed
            self.tampered = true;

            // Validate it (and display/hide errors accordingly)
            self.validate();
        })
        .focus(function(){
            self.set_active();
        });

    // Disable element
    if (this.disabled)
    {
        this.disable();
    }

    if (this.autocomplete_url)
    {
        // Enable autocomplete for this field
        this.autocomplete();
    }

}

/**
 * Add validator to element
 */
jFormElement.prototype.add_validator = function(v)
{
    v.element = this;
    this.validators.push(v);
}

/**
 * Add the name of the dependent form element
 *
 * It will be validated together with this element
 */
jFormElement.prototype.add_validate_also = function(name)
{
    this.validate_also.push(name);
}

/**
 * Get form element value
 */
jFormElement.prototype.get_value = function()
{
    return $('#' + this.id).val();
}

/**
 * Set form element value
 */
jFormElement.prototype.set_value = function(value)
{
    $('#' + this.id).val(value);
}

/**
 * Validate this element
 */
jFormElement.prototype.validate = function()
{       
    if ( ! this.tampered)
    {
        // Element has not been touched by user yet - don't validate
        return;
    }

    // Start validation chain
    this.start_validation();

    // Validate other elements that depend on this one
    for (i = 0; i < this.validate_also.length; i++)
    {
        var name = this.validate_also[i];

        var e = this.form.get_element(name);
        e.validate();
    }
}

/**
 * Start validation chain
 */
jFormElement.prototype.start_validation = function()
{
    // No errors so far
    this.errors = [];
    this.v_result = true;

    this.v_i = -1; // Index of the current validator
    this.v_value = this.get_value(); // Value to validate
    this.v_context = this.form.get_values(); // "Context"

    if (this.validators.length > 0)
    {
        this.next_validator(true);
    }
    else
    {
        this.set_valid();
    }
}

/**
 * Process result of the current validator and move to the next one
 */
jFormElement.prototype.next_validator = function(result)
{
    if (result == false && this.v_i >= 0)
    {
        var current_v = this.validators[this.v_i];
        
        // This validator failed and so does the whole element validation
        this.v_result = false

        if (current_v.error_msg)
        {
            this.errors.push(current_v.error_msg);
        }
        else if (current_v.get_error_msg)
        {
            this.errors.push(current_v.get_error_msg());
        }

        if (current_v.breaks_chain)
        {
            this.finish_validation();
            return;
        }
    }

    // Move to the next validator
    this.v_i++;

    if (this.v_i < this.validators.length)
    {
        this.validators[this.v_i].validate(this.v_value, this.v_context);
    }
    else
    {
        this.finish_validation();
    }
}

/**
 * Finish element validation, update the status
 */
jFormElement.prototype.finish_validation = function()
{
    // Lets see what we've got here...
    if (this.v_result == true)
    {
        // Validation succeded
        this.set_valid();
    }
    else
    {
        // Validation failed.
        this.set_invalid();
    }
}




/**
 * Highlight element as invalid and
 * display errors for this element
 */
jFormElement.prototype.set_invalid = function()
{
    // Highlight input as invalid
    $('#' + this.id + ',#' + this.id +'-status')
        .removeClass('valid active')
        .addClass('invalid');

    // Display error messages
    $('#' + this.id + '-errors')
        .html('<div>' + this.errors.join('</div><div>') + '</div>')
        .show();
}

/**
 * Highlight element as valid and
 * hide possible errors for this element
 */
jFormElement.prototype.set_valid = function()
{
    // Highlight input as valid
    $('#' + this.id + ',#' + this.id +'-status')
        .removeClass('invalid active')
        .addClass('valid');

    // Remove error messages
    $('#' + this.id + '-errors').empty().hide();
}

/**
 * Set active
 */
jFormElement.prototype.set_active = function()
{
    // Highlight input as active
    $('#' + this.id + ',#' + this.id +'-status')
        .removeClass('valid invalid')
        .addClass('active');

    // Remove error messages
    $('#' + this.id + '-errors').empty().hide();
}

/**
 * Reset element status
 */
jFormElement.prototype.reset_status = function()
{
    // Remove all status classes
    $('#' + this.id + ',#' + this.id +'-status')
        .removeClass('valid invalid active');

    // Remove error messages
    $('#' + this.id + '-errors').empty().hide();
}

/**
 * Enable element
 */
jFormElement.prototype.enable = function()
{
    this.enabled = true;

    $('#' + this.id)
        .attr('disabled', false);
        
    $('#' + this.id + ',#' + this.id +'-status')
        .removeClass('disabled');
        
    if ($('#' + this.id).is('select')) {
        restyle_selects(new Array(this.id));
    }    
}

/**
 * Disable element
 */
jFormElement.prototype.disable = function()
{
    this.enabled = false;

    this.reset_status();

    $('#' + this.id)
        .attr('disabled', true);
        
    $('#' + this.id + ',#' + this.id +'-status')
        .addClass('disabled');

    if ($('#' + this.id).is('select')) {
        restyle_selects(new Array(this.id));
    }
        
}


/**
 * Init autocomplete for this element
 */
jFormElement.prototype.autocomplete = function()
{
    // Initialize autocomplete
    this.ac_popup = new Autocomplete(this);
}


/*******************************************************************************
 * Autocomplete popup
 ******************************************************************************/
 /**
  * Initialize autocomplete popup for the specified form element
  */
function Autocomplete(form_element)
{
    var self = this;

    this.form_element = form_element;

    // Find popup DOM element by id (constructed from form element id)ы
    this.popup = $('#' + form_element.id + '-autocomplete');
    
    // Popup is not visible
    this.visible = false;

    // Form element event handlers
    $('#' + form_element.id).keydown(function(event){

        if (event.keyCode == '38')
        {
            // An "up" key was pressed
            self.up(form_element);
        }
        else if (event.keyCode == '40')
        {
            // A "down" key was pressed
            self.down(form_element)
        }
    }).keypress(function(event){
        
        if (event.keyCode == '13' && self.visible)
        {
            // An "enter" key was pressed
            event.preventDefault();
            self.hide();
        }
        else if (event.keyCode != '9' && event.keyCode != '38' && event.keyCode != '40')
        {
            // Peform an ajax request when key is pressed in the input (except up, down, enter and tab)
            // with some delay, because we need the value in form input to be updated after the keypress
            var text_val;
            text_val = form_element.get_value();
            if (form_element.autocomplete_chunk) 
            {
                var text_vals;
                text_vals = text_val.split(form_element.autocomplete_chunk);
                text_val  = text_vals[text_vals.length -1];
            }
            setTimeout(function(){
                $.post(form_element.autocomplete_url, 'value=' + encodeURIComponent(text_val), function(response) {
                    if (response)
                    {
                        //@FIXME: Security breach!
                        eval('var items = ' + response + ';');
                        self.show(items);
                    }
                    else
                    {
                        self.hide();
                    }
                })
            }, 30);
        }
    }).blur(function(){
        // Hide autocomplete popup when element looses focus
        self.hide();
    });

    // Selecting items with mouse
    this.popup.mouseover(function(event){
        var i = self.get_i_by_target(event.target);

        if (i > -1)
        {
            self.highlight(i);
        }
    }).mousedown(function(event){
        
        var i = self.get_i_by_target(event.target);

        if (i > -1)
        {
            self.highlight(i, true,form_element);
        } else {
            target = $(event.target);
            if (target.hasClass('active'))
            {
                target.click();
            }            
        }
    });
}

/**
 * Highlight currently selected item
 */
Autocomplete.prototype.highlight = function(i, set_value,form_element)
{
    this.i = i;

    // Unhighlight all items
    $('.item', this.popup).removeClass('highlighted');

    // Highlight the current one
    $('.i_' + i, this.popup).addClass('highlighted');

    if (set_value)
    {
        var text_val = '';
        if (form_element.autocomplete_chunk)
        {
            var text_vals;
            text_vals = form_element.get_value().split(form_element.autocomplete_chunk);
            for (var j = 0; j < text_vals.length-1; j++)
            {
                text_val =text_val + text_vals[j] + form_element.autocomplete_chunk; 
            }
        }
        this.items[i].value.name = text_val + this.items[i].value.name;
        
        var method = 'set_' + this.form_element.id;
        // Copy selected item value to form input
        if (method_exists(this.form_element,method)) {
            eval('this.form_element.'+ method)(this.items[i].value);
        } else {
            this.form_element.set_value(this.items[i].value);
        }
    }
}

/**
 * Move to the previous item (when user presses "up" key)
 */
Autocomplete.prototype.up = function(form_element)
{
    if (this.i > 0)
    {
        this.highlight(this.i - 1, true,form_element);
    }
}

/**
 * Move to the next item (when user presses "down" key)
 */
Autocomplete.prototype.down = function(form_element)
{    
    if (this.i < this.items.length - 1)
    {
        this.highlight(this.i + 1, true,form_element);
    }
}

/**
 * Show autocomplete popup and fill it with the specified items
 */
Autocomplete.prototype.show = function(items)
{
    this.items = items;

    // Reset index of currently selected item
    this.i = -1;
    
    // Clean-up popup contents
    this.popup.empty();

    // Create a div wrapper
    var items_wrapper = $('<div class="autocomplete"></div>').appendTo(this.popup);

    // Add items
    for (var i = 0; i < items.length; i++)
    {
        $('<div class="item i_' + i + '">' + items[i].caption + '</div>')
            .appendTo(items_wrapper)
    }

    this.popup.show();
    this.visible = true;
}

/**
 * Hide autocomplete popup
 */
Autocomplete.prototype.hide = function()
{
    this.popup.hide();
    this.visible = false;
}

/**
 * Determine index of selected item by target of the event
 */
Autocomplete.prototype.get_i_by_target = function(target)
{
    target = $(target);
    if (target.hasClass('item'))
    {
        // Determine the index of the clicked item by class name (like i_2)
        var matches = target.attr('class').match(/i_(\d+)/);
        if (matches[1])
        {
            return parseInt(matches[1]);
        }
    }
    return -1;
}

/*******************************************************************************
 * jFormElementCheckboxEnable
 ******************************************************************************/
function jFormElementCheckboxEnable(name, id)
{
    // Call parent constructor
    this.jFormElement(name, id);

    this.dep_elements = [];
    
}

// Inherit from jFormElement
copyPrototype(jFormElementCheckboxEnable, jFormElement);

/**
 * Set dependent elements
 */
jFormElementCheckboxEnable.prototype.set_dep_elements = function(elements)
{
    this.dep_elements = elements;
}

/**
 * Init element
 */
jFormElementCheckboxEnable.prototype.init = function()
{
    var self = this;

    // Toggle dependent elements on value change
    $('#' + this.id)
        .click(function(){
            self.toggle_dependent();
        });
}

/**
 * Get checkbox value
 */
jFormElementCheckboxEnable.prototype.get_value = function()
{
    //@TODO: not 'true', 'false', but actual values....
    return $('#' + this.id).attr('checked') ? true : false;
}

/**
 * Enable/disable dependent elements
 */
jFormElementCheckboxEnable.prototype.toggle_dependent = function()
{
    var value = this.get_value();
    var i=0;
    var name;
    var e;
    for (i = 0; i < this.dep_elements.length; i++)
    {
        name = this.dep_elements[i];
        e = this.form.get_element(name);
        if ( ! e)
            continue;

        if (value)
            e.enable();
        else
            e.disable();
    }
    
    for (i = 0; i < this.depvis_elements.length; i++)
    {

        name = this.depvis_elements[i];
        e = this.form.get_element(name);
        if ( ! e)
            continue;

        if (value)
            e.visible = true;
        else
            e.visible = false;
    }    
}


/*******************************************************************************
 * jFormValidatorRegexp
 ******************************************************************************/
function jFormValidatorRegexp(regexp, error_msg, allow_empty, breaks_chain)
{
    this.regexp = regexp;
    this.error_msg = error_msg;

    this.allow_empty = allow_empty || false;
    this.breaks_chain = breaks_chain || true;
}

/**
 * Validate a value via validator
 */
jFormValidatorRegexp.prototype.validate = function(value, context)
{
    var result = false;
    

    if (
        value != null &&
        (
            value.match(this.regexp) || // value matches regexp
            (this.allow_empty && value.match(/^\s*$/)) // or value is empty and empty values are allowed
        )
    )
    {
        result = true;
    }
    this.element.next_validator(result);
}

/*******************************************************************************
 * jFormValidatorStringLength
 ******************************************************************************/
function jFormValidatorStringLength(min_length, max_length, config)
{
    this.min_length = min_length;
    this.max_length = max_length;
    this.config = config;

    this.breaks_chain = true;
}

/**
 * Validate a value via validator
 */
jFormValidatorStringLength.prototype.validate = function(value, context)
{
    var result = true;

    if (value.length < this.min_length)
    {
        this.error_msg = this.config.messages['TOO_SHORT'];
        result = false;
    }
    else if (value.length > this.max_length)
    {
        this.error_msg = this.config.messages['TOO_LONG'];
        result = false;
    }

    this.element.next_validator(result);
}
/*******************************************************************************
 * jFormValidatorEqualTo
 ******************************************************************************/
function jFormValidatorEqualTo(target, error_msg, breaks_chain)
{
    this.target = target;
    this.error_msg = error_msg;

    this.breaks_chain = breaks_chain || true;
}

/**
 * Init validator
 */
jFormValidatorEqualTo.prototype.init = function()
{
    // Target element should be validated together with this element
    var e = this.element.form.get_element(this.target);
    e.add_validate_also(this.element.name);
}

/**
 * Validate a value via validator
 */
jFormValidatorEqualTo.prototype.validate = function(value, context)
{
    var result = false;
    if (context[this.target] && value == context[this.target])
    {
        result = true;
    }
    this.element.next_validator(result);
}

/*******************************************************************************
 * jFormValidatorAjax
 ******************************************************************************/
function jFormValidatorAjax(url)
{
    this.url = url;
}

/**
 * Validate a value via validator
 */
jFormValidatorAjax.prototype.validate = function(value, context)
{
    // Serialize all form values
    var values = $('#' + this.element.form.id).serialize();

    var self = this;

    $.post(this.url + '?name=' + this.element.name, values, function(data) {
        //@FIXME: Security breach!
        eval('var data = ' + data + ';');

        // Find an error for this element
        var valid = true;

        for (var i = 0; i < data.length; i++)
        {
            //@FIXME: display all messages, not just the last one
            if (data[i]['field'] && data[i]['field'] == self.element.name)
            {
                // There is an error message for this element - validation failed
                self.error_msg = data[i]['text'];
                valid = false;
            }
        }

        // Next validator in chain
        self.element.next_validator(valid);
        
    });

    return 0;
}

function method_exists (obj, method) {
  // http://kevin.vanzonneveld.net
  // +   original by: Brett Zamir (http://brett-zamir.me)
  // *     example 1: function class_a() {this.meth1 = function () {return true;}};
  // *     example 1: var instance_a = new class_a();
  // *     example 1: method_exists(instance_a, 'meth1');
  // *     returns 1: true
  // *     example 2: function class_a() {this.meth1 = function () {return true;}};
  // *     example 2: var instance_a = new class_a();
  // *     example 2: method_exists(instance_a, 'meth2');
  // *     returns 2: false
  if (typeof obj === 'string') {
    return this.window[obj] && typeof this.window[obj][method] === 'function';
  }

  return typeof obj[method] === 'function';
}