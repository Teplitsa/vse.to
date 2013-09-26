/**
 * Eresus filemanager integration with TinyMCE
 *
 * Url to PHP backend is determined by 'filemanager_url' TinyMCE config parameter:
 * tinyMCE.init({
 *     ...
 *     filemanager_url : 'http://host.tld/admin/filemanager/tinymce-1/'
 *     ...
 * });
 */

/**
 * TinMCE file browser callback.
 * Opens file manager popup window.
 */
function eresusFileManagerCallback (field_name, url, type, win)
{
    // URL to filemanager backend should be specified in 'filemanager_url' config parameter
    var filemanager_url = tinyMCE.activeEditor.getParam('filemanager_url');

    tinyMCE.activeEditor.windowManager.open({
        file : filemanager_url
       ,width : 600
       ,height : 600
       ,resizable : "yes"
       ,inline : "yes"
       ,close_previous : "no"
       ,popup_css : false // Disable tinyMCE's default popup css
    }, {
        window : win,
        input : field_name
    });

    return false;
}

// Register callback
tinyMCE_config.file_browser_callback = 'eresusFileManagerCallback';
