<?php defined('SYSPATH') or die('No direct access allowed.');

return array (
    'element' =>
'<dt class="element-select can_be_wide">
    {{label}}
</dt>
<dd class="element-select can_be_wide">
    <div>{{input}}</div>
    <div class="errors" id="{{id}}-errors">{{errors}}</div>
    <div class="comment">{{comment}}</div>
</dd>
'
);