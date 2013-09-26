<?php defined('SYSPATH') or die('No direct access allowed.');

return array (
    'element' =>
'
<dt class="element-{{type}} can_be_wide"></dt>
<dd class="element-{{type}} can_be_wide">
    <div>{{input}}{{label}}</div>
    <div class="errors">{{errors}}</div>
    <div class="comment">{{comment}}</div>
</dd>
'
);