<?php defined('SYSPATH') or die('No direct access allowed.');

return array (
    array(
        'element' =>
'<tr>
    <td class="input_cell element-{{type}}" colspan="2">
        <fieldset class="element-checkselect">
            <legend>{{label_text}}</legend>
            <div class="options">{{input}}</div>
            <div class="errors" id="{{id}}-errors">{{errors}}</div>
            <div class="comment">{{comment}}</div>
        </fieldset>
    </td>
</tr>
'
    )
);