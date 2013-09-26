
var eresusFileManagerDialogue = {
    init : function () {
        // Add onclick event to all file links in current document, so dialog is submitted whenever user clicks on file
        var file_links = tinymce.DOM.select(".file_link", document);

        for (var i = 0; i < file_links.length; i++) {
            tinymce.DOM.bind(file_links[i], 'click', this.submit);
        }
    }

    /**
     * Submit is called when user clicks on selected file
     */
   ,submit : function (e) {
        e.preventDefault();

        // URL to file
        var URL = e.target.href;

        // image popup window
        var win = tinyMCEPopup.getWindowArg("window");

        // insert url into input field
        win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = URL;

        // are we an image browser?
        if (typeof(win.ImageDialog) != "undefined") {
            // Update image dimensions
            if (win.ImageDialog.getImageData)
                win.ImageDialog.getImageData();

            // Update preview
            if (win.ImageDialog.showPreviewImage)
                win.ImageDialog.showPreviewImage(URL);

            // Update image description
            if (win.document.forms[0].elements.alt)
            {
                var alt = URL.match(/[^\/]+$/);
                win.document.forms[0].elements.alt.value = alt;
            }
        }

        // close popup window
        tinyMCEPopup.close();
    }
}

tinyMCEPopup.onInit.add(eresusFileManagerDialogue.init, eresusFileManagerDialogue);