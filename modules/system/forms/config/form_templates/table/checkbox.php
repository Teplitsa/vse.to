<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
    // Standart layout
    array(
        'element' =>
'<tr>
    <td class="input_cell element-{{type}}" colspan="2">
        <div>{{input}}{{label}}</div>
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
        <div class="pad_comp"><div>{{prepend}}{{input}}{{append}}</div></div>
        <div class="errors" id="{{id}}-errors">{{errors}}</div>
        <div class="comment">{{comment}}</div>
    </td>
</tr>
'
    ),
    // prepend layout
    'prepend' => array(
        'element' =>
'
        {{input}}
'
    ),    
);