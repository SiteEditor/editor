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

            this.previewMode = "off";

            this.changePreviewer = false;

            this.ready();
        },

        ready : function(){
            var self = this;

            //when refresh previewer
            api.addFilter( "sedPreviewerQueryFilter" , function( query ){

                if( self.previewMode == "on" ) {
                    self.changePreviewer = true;
                }

                return query;
            });

            //TODO : fix loaded pages inlclude video && Embeded(exist delay for enable preview mode)
            api.previewer.bind( 'previewerActive', function( ) {

                if( self.previewMode == "on" ){

                    api.previewer.send("previewMode" , self.previewMode );

                }

            });

            $("#app-preview-mode-btn").on("click" , function(){

                self.previewMode = "on";

                $("body").addClass("sed-app-preview");
                api.previewer.send("previewMode" , self.previewMode );
                self.dialogsClosed();
            });

            $("#back-to-editor-btn").on("click" , function(){

                self.previewMode = "off";

                $("body").removeClass("sed-app-preview");
                api.previewer.send("previewMode" , self.previewMode );
                $("#website").css("width" , "100%" );

                if( self.changePreviewer === false ) {
                    self.dialogsOpened();
                }else{
                    self.changePreviewer = false;
                }

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
        }


	});

    $( function() {

        api.appPreview = new api.AppPreview();

    });


})( sedApp, jQuery );