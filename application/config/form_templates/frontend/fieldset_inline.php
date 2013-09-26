<?php defined('SYSPATH') or die('No direct access allowed.');

return array (
    // Standart layout
    'standart' => array(
        'fieldset' =>
'<tr>
    <td class="fieldset_inline_cell" colspan="2">
        <div>{{label}}</div>
        
        <div id="{{id}}">
            <table><tr>
                {{elements}}
            </tr></table>
        </div>
    </td>
</tr>
',
        'fieldset_ajax' =>
'<table><tr>
    {{elements}}
</tr></table>
',
        'element' => '<td class="fieldset_inline_element_cell">{{element}}</td>'
    ),

    // Wide layout
    'wide' => array(
        'fieldset' =>
'<tr>
    <td class="label_cell">{{label}}</td>

    <td class="fieldset_inline_cell">
        <div id="{{id}}">
            <table class="fieldset_inline_elements"><tr>
                {{elements}}
            </tr></table>
        </div>
    </td>
</tr>
',
        'fieldset_ajax' =>
'<table><tr>
    {{elements}}
</tr></table>
',
        'element' => '<td class="fieldset_inline_element_cell">{{element}}</td>'
    )
);
