<?php defined('SYSPATH') or die('No direct access allowed.');

return array (
    'element' =>
'<dt class="element-{{type}} can_be_wide">
    {{label}}
</dt>
<dd class="element-{{type}} can_be_wide">
    <div class="pad_comp"><div>{{input}}{{append}}</div></div>

    <div class="autocomplete_wrapper"><div class="autocomplete_popup" id="{{id}}-autocomplete"></div></div>

    <div class="errors" id="{{id}}-errors">{{errors}}</div>
    <div class="comment">{{comment}}</div>
</dd>
'
);
