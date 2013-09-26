<?php defined('SYSPATH') or die('No direct access allowed.');

return array (
    // Standart layout
    'standart' => array(
        'form' => '
{{form_open}}
    {{messages}}
    {{hidden}}
    <table class="form_table">
        <tr class="empty"><td class="label_cell"></td><td class="input_cell"></td></tr>
        {{elements}}
    </table>
{{form_close}}
'
    ),

    // Wide layout
    'wide' => array(
        'form' => '
{{form_open}}
    {{messages}}
    {{hidden}}
    <table class="form_table">
        <tr class="empty"><td class="label_cell"></td><td class="input_cell"></td></tr>
        {{elements}}
    </table>
{{form_close}}
'
    )
);