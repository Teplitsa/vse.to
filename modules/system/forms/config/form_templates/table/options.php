<?php defined('SYSPATH') or die('No direct access allowed.');

return array (
    array(
        'element' =>
'<tr>
    <td colspan="2" class="fieldset">
        <fieldset>
            <legend>{{label_text}}</legend>
            <div id="{{id}}">
                <table class="form_table">
                    <tr class="empty"><td class="label_cell"></td><td class="input_cell"></td></tr>
                    {{input}}
                    <tr>
                        <td colspan="2">
                            <a href="{{inc_options_count_url}}"> + {{option_caption}}</a>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
    </td>
</tr>
'
    )
);