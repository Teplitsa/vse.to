/**
 * Set form element value
 */
jFormElement.prototype.set_place_name = function(value)
{
    if (value['name'] && value['id']) {
        $('#' + 'place_name').val(value['name']);
        $('#' + 'place_id').val(value['id']);
    } else {
        $('#' + 'place_name').val(value);    
    }
}