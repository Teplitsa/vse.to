<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	// на какой адрес придёт POST-запрос от uLogin
	'redirect_uri'  => Url::base(Request::$current, true).'acl/login_social',
        'optional'      => array('phone'),
        'fields'        => array('city'),
);
