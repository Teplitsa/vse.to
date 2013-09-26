<?php defined('SYSPATH') or die('No direct access allowed.');

return array (
    array(
        'fieldset' =>
'<tr>
    <td colspan="2">
        <div id="{{id}}">
            <table class="form_table">
                <tr class="empty"><td class="label_cell"></td><td class="input_cell"></td></tr>
                {{elements}}
            </table>
        </div>
    </td>
</tr>
',
        'element' => '{{element}}'
    )
);