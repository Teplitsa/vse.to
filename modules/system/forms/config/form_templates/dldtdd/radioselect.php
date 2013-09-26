<?php defined('SYSPATH') or die('No direct access allowed.');

return array (
    'element' =>
'<dt class="element-checkselect can_be_wide">
    {{label}}
</dt>
<dd class="element-checkselect can_be_wide">
    <div class="options">{{input}}</div>
    <div class="errors" id="{{id}}-errors">{{errors}}</div>
    <div class="comment">{{comment}}</div>
</dd>
',
    'option' =>
'
<div class="option">
    {{checkbox}}
    {{label}}
</div>
'
);