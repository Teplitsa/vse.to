<?php defined('SYSPATH') or die('No direct access allowed.');

return array (
    // Standart layout
    'standart' => array(
        'element' =>
'<tr>
    <td class="input_cell element-{{type}}" colspan="2">
        {{label}}
        <div class="pad_comp"><div>{{input}}{{append}}</div></div>
        <div class="errors" id="{{id}}-errors">{{errors}}</div>
        <div class="comment">{{comment}}</div>
    </td>
</tr>
'
    ),

    // Wide layout
    'wide' => array(
        'element' =>
'<tr>
    <td class="label_cell label_pad">{{label}}</td>

    <td class="input_cell element-{{type}}">
        <div class="pad_comp"><div>{{input}}{{append}}</div></div>
        <div class="errors" id="{{id}}-errors">{{errors}}</div>
        <div class="comment">{{comment}}</div>
    </td>
</tr>
'
    )
);
