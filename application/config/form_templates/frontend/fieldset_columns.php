<?php defined('SYSPATH') or die('No direct access allowed.');

return array (
    array(
        'fieldset' =>
'<tr>
    <td colspan="2">
        <table class="form_table"><tr>
            {{elements}}
        </tr></table>
    </td>
</tr>
',
        'column' =>
'<td class="fieldset_column {{class}} {{last}}">
    <table class="form_table">
        <tr class="empty"><td class="label_cell"></td><td class="input_cell"></td></tr>
        {{elements}}
    </table>
</td>    
',
        'element' => '{{element}}'
    )
);