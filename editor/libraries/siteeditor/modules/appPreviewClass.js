/**
 * siteEditorCss.js
 *
 *
 * License: http://www.siteeditor.org/license
 * Contributing: http://www.siteeditor.org/contributing
 */

/*global diagram:true */
(function( exports, $ ){
	var api = sedApp.editor;

	api.AppPreview = api.Class.extend({
        initialize: function( options ){

            $.extend( this, options || {} );

            this.ready();
        },

        ready : function(){
            var self = this;
            $("#app-preview-mode-btn").on("click" , function(){
                $("body").addClass("sed-app-preview");
                api.previewer.send("previewMode" , "on");
                self.dialogsClosed();
            });

            $("#back-to-editor-btn").on("click" , function(){
                $("body").removeClass("sed-app-preview");
                api.previewer.send("previewMode" , "off");
                $("#website").css("width" , "100%" ); 
                self.dialogsOpened();
            });

            $(".preview-mode-toolbar .preview-mode").on("click" , function(){
                var previewMode = $(this).data("previewMode") ,
                    IframeW;

                switch ( previewMode ) {
                  case "tablets-landscape-mode":
                      IframeW = 1023;
                  break;
                  case "tablets-portrait-mode":
                      IframeW = 767;
                  break;
                  case "smartphones-landscape-mode":
                      IframeW = 479;
                  break;
                  case "smartphones-portrait-mode":
                      IframeW = 319;
                  break;
                  /*case "desktop-mode":

                  break;*/
                }

                if(previewMode == "desktop-mode")
                    IframeW = "100%";
                else
                    IframeW = IframeW + "px";

                $("#website").css("width" , IframeW );
                $("#website").addClass(previewMode + "-iframe");

            });

        },

        dialogsClosed : function( ){
            var self = this;
            this.openDialogs = [];
            $(".ui-dialog-content").each(function(){
                if(  $(this).dialog( "isOpen" )){
                    $(this).dialog("close");
                    self.openDialogs.push( $(this) );
                }
            });
        },

        dialogsOpened : function( ){
            _.each( this.openDialogs , function( dialog ){
                dialog.dialog("open");
            });
        },


	});

    $( function() {

        api.appPreview = new api.AppPreview();

    });


})( sedApp, jQuery );