<?php defined('SYSPATH') or die('No direct access allowed.');

return array (
    array(
        'element' =>
'<tr>
    <td class="input_cell element-{{type}}" colspan="2">
        {{label}}
        <div class="errors" id="{{id}}-errors">{{errors}}</div>
        <div class="pad_comp_textarea"><div>{{input}}</div></div>
        <div class="comment">{{comment}}</div>
    </td>
</tr>
'
    )    
);
