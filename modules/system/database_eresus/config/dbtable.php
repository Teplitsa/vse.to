<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'auto_create' => (Kohana::$environment !== Kohana::PRODUCTION),
    'auto_update' => (Kohana::$environment !== Kohana::PRODUCTION)
);