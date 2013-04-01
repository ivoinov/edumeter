jQuery(function() {
	tinyMCE.init({
        // General options
        theme : "advanced",
        plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

        // Theme options
        theme_advanced_buttons1 : "fullscreen,|,undo,redo,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,sub,sup,|,styleselect,formatselect,link,unlink,anchor,image,media,|,insertdate,inserttime,charmap",//,|,cut,copy,paste,pastetext,pasteword,|,search,replace",
        theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,blockquote,|,tablecontrols,|,hr,removeformat,visualaid,|,visualchars,nonbreaking,blockquote,|,preview,cleanup,print,spellchecker,code,help",
        theme_advanced_buttons3 : "",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,

        // Example content CSS
        content_css : app.url.getBaseUrl() + "/public/skin/frontend/default/content.css",

        // Drop lists for link/image/media/template dialogs
        /*external_link_list_url  : "js/link_list.js",
        external_image_list_url : "js/image_list.js",
        media_external_list_url : [
               	[__('Highlight'), 'highlight'], 
              	[__('Warning'), 'warning']
        ],*/

		// Selector
        mode : "specific_textareas",
        relative_urls : false,
        editor_selector : "widget-wysiwyg"
	});

});
