<?php defined('SYSPATH') or die('No direct script access.');

return array(
    'files_root_path'     => DOCROOT . 'public/user_data',
    'css_root_path'       => DOCROOT . 'public/css',
    'js_root_path'        => DOCROOT . 'public/js',
    'templates_root_path' => DOCROOT . 'application/views/layouts',

    // Content of file with this extensions can be edited 
    'ext_editable' => array('php', 'css', 'js', 'html'),

    'thumbs' => array(
        'preview' => array(
            'enable' => 1,
            'dir_base_name' => '.thumbs',
            'width'  => 80,
            'height' => 80
        ),
        'popups' => array(
            'enable' => 1,
            'dir_base_name' => '.popups',
            'width'  => 0,
            'height' => 0
        )
    ),

    'image_resize' => array(
        'enable' => 1,
        'width' => 300,
        'height' => 300,
    )
);
