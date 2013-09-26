/**
 * Set form element value
 */
jFormElement.prototype.set_organizer_name = function(value)
{
    if (value['name'] && value['id']) {
        $('#' + 'organizer_name').val(value['name']);
        $('#' + 'organizer_id').val(value['id']);
    } else {
        $('#' + 'organizer_name').val(value);    
    }
}