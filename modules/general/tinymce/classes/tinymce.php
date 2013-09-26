<?php defined('SYSPATH') or die('No direct script access.');

class TinyMCE
{
    /**
     * @var boolean
     */
    protected static $_scripts_added = FALSE;

    /**
     * Add TinyMCE scripts to the layout
     */
    public static function add_scripts()
    {
        if (self::$_scripts_added)
            return;

        $layout = Layout::instance();

        // TinyMCE configuration script
        $layout->add_script(Modules::uri('tinymce') . '/public/js/tiny_mce_config.js');

        // Set up config options
        $layout->add_script("
            tinyMCE_config.document_base_url = '" . URL::site() . "';
            tinyMCE_config.editor_selector   = /content/;
            tinyMCE_config.content_css       = '" . URL::base() . 'public/css/' . Kohana::config('tinymce.css') . "';
            tinyMCE_config.body_id           = 'Content';
            tinyMCE_config.body_class        = 'content';
        ", TRUE);

        // Set up filemanager for tinyMCE
        if (Modules::registered('filemanager'))
        {
            $layout->add_script(Modules::uri('filemanager') . '/public/js/tiny_mce/filemanager_init.js');
            $layout->add_script("
                tinyMCE_config.filemanager_url = '" . URL::to('backend/filemanager', array('fm_tinymce' => '1', 'fm_style' => 'thumbs')) . "';
            ", TRUE);
        }

        // Load TinyMCE
        $layout->add_script(Modules::uri('tinymce') . '/public/js/tiny_mce/tiny_mce.js');
        $layout->add_script("
            tinyMCE.init(tinyMCE_config);
        ", TRUE);

        self::$_scripts_added = TRUE;
    }
}