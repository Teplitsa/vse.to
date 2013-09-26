/**
 * Set form element value
 */
jFormElement.prototype.set_lecturer_name = function(value)
{
    if (value['name'] && value['id']) {
        $('#' + 'lecturer_name').val(value['name']);
        $('#' + 'lecturer_id').val(value['id']);
    } else {
        $('#' + 'lecturer_name').val(value);    
    }
}