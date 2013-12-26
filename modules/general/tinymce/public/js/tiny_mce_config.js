var tinyMCE_config = {
    mode     :  "specific_textareas"
   ,language :  "ru"

   ,convert_urls       :	false
   ,relative_urls      :	false
   ,remove_script_host :    false

   ,theme 							: 	"advanced"
   ,theme_advanced_toolbar_location	:	"top"
   ,theme_advanced_toolbar_align    :   "left"
   ,theme_advanced_buttons1			:	"formatselect,fontselect,fontsizeselect,separator,"+
										"forecolor,backcolor,bold,italic,separator," + 
                                                                                "numlist,bullistlink,unlink,image,separator," + 
                                                                                "pasteword,removeformat,cleanup,code"
   ,theme_advanced_buttons2			:	""
   ,theme_advanced_buttons3			:	""

   ,plugins :   "inlinepopups,advimage,advlink,table,paste"

	//,document_base_url :	--- Is set in layout
	//,editor_selector	:	--- Is set in layout
	//,content_css 	    :	--- Is set in layout

    ,height: 600
};