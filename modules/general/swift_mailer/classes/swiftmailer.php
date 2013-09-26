<?php defined('SYSPATH') or die('No direct script access.');

class SwiftMailer {
    /**
     * Setup swift mailer
     */
    public static function init()
    {
        require_once Modules::path('swift_mailer') . '/lib/swift_required.php';
    }

    protected function  __construct()
    {
        // This is a static class
    }
}