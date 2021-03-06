<?php defined('SYSPATH') or die('No direct access allowed.');

return array (
    // Standart layout
    'standart' => array(
        'element' =>
'<tr>
    <td class="input_cell element-{{type}}" colspan="2">
        {{label}}
        <div class="options">{{input}}</div>
        <div class="errors" id="{{id}}-errors">{{errors}}</div>
        <div class="comment">{{comment}}</div>
    </td>
</tr>
',
        'option' =>
'
<span class="option">
    {{checkbox}}
    {{label}}
</span>
'
    ),

    // Wide layout
    'wide' => array(
        'element' =>
'<tr>
    <td class="label_cell">{{label}}</td>

    <td class="input_cell element-{{type}}">
        <div class="options">{{input}}</div>
        <div class="errors" id="{{id}}-errors">{{errors}}</div>
        <div class="comment">{{comment}}</div>
    </td>
</tr>
',
        'option' =>
'
<span class="option">
    <label>{{checkbox}} {{label}}</label>
</span>
'
    )
);