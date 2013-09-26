/**
 * Set form element value
 */
jFormElement.prototype.set_value = function(value)
{
    if (value['name'] && value['id']) {
        $('#' + this.id).val(value['name']);
        $('#' + 'lecturer_id').val(value['id']);
    } else {
        $('#' + this.id).val(value);    
    }
}