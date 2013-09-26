<?php defined('SYSPATH') or die('No direct access allowed.');

return array (
    'fieldset' =>
'<dt></dt>
<dd>
    <fieldset {{attributes}}>
        <legend>{{label}}</legend>
        <dl>
            {{elements}}
        </dl>
    </fieldset>
</dd>
'
);