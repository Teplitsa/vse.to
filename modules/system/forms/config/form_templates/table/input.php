<?php defined('SYSPATH') or die('No direct access allowed.');

return array (
    // Standart layout
    'standart' => array(
        'element' =>
'<tr>
    <td class="input_cell element-{{type}}" colspan="2">
        {{label}}
        <div class="pad_comp"><div>{{input}}{{append}}</div></div>
        <div class="autocomplete_wrapper"><div class="autocomplete_popup" id="{{id}}-autocomplete"></div></div>
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
        <div class="autocomplete_wrapper"><div class="autocomplete_popup" id="{{id}}-autocomplete"></div></div>
        <div class="errors" id="{{id}}-errors">{{errors}}</div>
        <div class="comment">{{comment}}</div>
    </td>
</tr>
'
    ),

    // Inline-fieldset layout
    'fieldset_inline' => array(
        'element' =>
'<div class="element-{{type}}">
    {{label}}{{input}}{{append}}
    <div class="autocomplete_wrapper"><div class="autocomplete_popup" id="{{id}}-autocomplete"></div></div>
    <div class="errors" id="{{id}}-errors">{{errors}}</div>
    <div class="comment">{{comment}}</div>
</div>
'
    )
);
