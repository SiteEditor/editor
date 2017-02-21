(function( exports, $ ) {

    var api = sedApp.editor;

    api.SiteEditorIframe = api.Class.extend({
        initialize: function( options , params ) {
            var self = this;

            $.extend(this, params || {});

            self.options = $.extend({}, options);

        },

        getMceToolBar : function( type , row ){
            switch ( type ) {
                case "title":
                  if( row == 1 )
                    return "formatselect | bold italic underline strikethrough | fontselect fontsizeselect";  //| closeeditor

                  if( row == 2 )
                    return "charmap removeformat | undo redo | link unlink | forecolor backcolor | alignleft aligncenter alignright alignjustify";
                break;
                case "paragraph":
                  if( row == 1 )
                    return "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | fontselect fontsizeselect";  // | closeeditor

                  if( row == 2 )
                    return "pastetext removeformat charmap | bullist numlist outdent indent  | undo redo | link unlink | forecolor backcolor";
                break;
                case "normal-paragraph":
                  if( row == 1 )
                    return "pastetext removeformat charmap | alignleft aligncenter alignright alignjustify | fontselect fontsizeselect";  // | closeeditor

                  if( row == 2 )
                    return "bold italic underline strikethrough | outdent indent  | undo redo | link unlink | forecolor backcolor";
                break;
                case "simple-paragraph":
                  if( row == 1 )
                    return "bold italic underline strikethrough | fontselect fontsizeselect";  // | closeeditor

                  if( row == 2 )
                    return "pastetext removeformat charmap | undo redo | link unlink | forecolor backcolor";
                break;
                case "normal-text":
                  if( row == 1 )
                    return "formatselect | fontselect fontsizeselect";   //| closeeditor

                  if( row == 2 )
                    return "charmap strikethrough | bold italic underline | undo redo | link unlink | forecolor";
                break;
                case "simple-text":
                  if( row == 1 )
                    return "fontselect fontsizeselect | charmap strikethrough";   //| closeeditor

                  if( row == 2 )
                    return "bold italic underline | undo redo | forecolor";
                break;
            }
        },

        textEditable: function( selector , type ){
            type = (type) ? type : "title";

            var plugin , toolbar1 , toolbar2 ,
            self = this,
                plugins = [
                    "advlist autolink link lists charmap spellchecker",
                    "wordcount visualchars visualblocks",
                    "directionality textcolor colorpicker paste"
                ];

            this.MceToolBarType = ["title" , "paragraph" , "normal-paragraph" , "simple-paragraph" , "normal-text" , "simple-text" ];

            toolbar1 = self.getMceToolBar( type , 1 );
            toolbar2 = self.getMceToolBar( type , 2 );

            var fontsize_formats = "";

            for (var i=8; i <= 100; i++)  {

                fontsize_formats += i + "px";

                if( i != 100 )
                  fontsize_formats += " ";

            }

            tinymce.init({
                selector: selector,
                plugins: plugins,

                fontsize_formats: fontsize_formats,
                font_formats : api.mceFontFormats ,

                toolbar1: toolbar1,
                toolbar2: toolbar2,

                //contextmenu: "cut copy paste | undo redo | link ",

                menubar: false,
                inline: true,
                toolbar_items_size: 'small',
                resize: false,
                object_resizing : false ,
                paste_as_text: true,

                setup: function(editor) {

                    /*
                     /* //editor.getBody().setAttribute('contenteditable', false);
                    // console.log( "editor-----------------------" ,  editor  );
                    var edId = this.id;
                    $("#" + edId).on('mouseover', function(e) {
                        tinymce.editors[edId].show();
                    });*/

                    editor.on('focus', function(e) {

                        if( !_.isUndefined( $("#" + this.id).data("toolbar1") ) ){
                            if( $.inArray( $("#" + this.id).data("toolbar1") , self.MceToolBarType ) == -1 )
                                editor.settings.toolbar1 = $("#" + this.id).data("toolbar1");
                            else
                                editor.settings.toolbar1 = self.getMceToolBar( $("#" + this.id).data("toolbar1") , 1 );
                        }

                        if( !_.isUndefined( $("#" + this.id).data("toolbar2") ) ){
                            if( $.inArray( $("#" + this.id).data("toolbar2") , self.MceToolBarType ) == -1 )
                                editor.settings.toolbar2 = $("#" + this.id).data("toolbar2");
                            else
                                editor.settings.toolbar2 = self.getMceToolBar( $("#" + this.id).data("toolbar2") , 2 );
                        }

                    });


                    editor.on('change', function(e) {

                        if( !_.isUndefined( e.originalEvent ) && !_.isUndefined( e.originalEvent.command ) && e.originalEvent.command.toLowerCase() == "fontname" ){
                            //e.target.setAttribute('data-sed-font-family' , e.originalEvent.value );
                            var fonts = [];

                            var _cModelId = $("#" + this.id).attr("sed_model_id");

                            var _cModel = api.contentBuilder.getShortcode( _cModelId );

                            var _parentModelId = _cModel['parent_id'];

                            $('[sed_model_id="' + _parentModelId + '"]').find('[style]').each(function(){
                                var ffamily = $(this)[0].style.fontFamily;
                                if( !_.isUndefined( ffamily ) && !_.isEmpty( ffamily )  ){
                                    ffamily = ffamily.replace(/\"/g, "");
                                    ffamily = ffamily.replace(/\'/g, "");
                                    fonts.push( ffamily );
                                }
                            });

                            api.typography.loadFont( e.originalEvent.value , _parentModelId , fonts );
                        }
                              //alert( $( tinymce.dom.getParent(e.target.selection.getNode(), 'span') ).html() );
                        //save content in shortcode models
                        var content = this.getContent({format : 'html'});
                           //api.log( this );
                        var postId = api.pageBuilder.getPostId( $("#" + this.id) ),
                            children = api.contentBuilder.getShortcodeChildren( $("#" + this.id).attr("sed_model_id") );

                        if(children.length != 1){
                            alert("In Text Editor content not Allowed using any shortcodes");
                            return ;
                        }

                        var contentModel = children[0];

                        if(contentModel.tag != "content"){
                            alert("your shortcode incorrect , shortcode not AS content model");
                            return ;
                        }
                        //this.save();
                          ////api.log( contentModel );
                        contentModel.content = content;
                        api.contentBuilder.updateShortcode( contentModel );

                        $("#" + this.id).trigger( "sed.changeMCEContent", [ content ] );

                    });


                    /*$('#' + editor.id).on('mouseout', function() {
                      $('#' + editor.id + '_tbl '+'.mceToolbar').hide();
                    });

                    editor.on('focus', function() {
                      $('#' + editor.id + '_tbl '+'.mceToolbar').show();
                    });*/

                  /*tinymce.activeEditor.selection.onSetContent(function(){

                  });*/

                }
            });



        }

    });

    api.SiteEditorTypography = api.Class.extend({

        initialize: function( options , params ){

            this.fonts = api.fonts;
            this.loadedFonts = [];
            this.googleFontsSettings = "";
            this.baseLoadedFonts = [];

            $.extend( this, params || {} );

        },

        loadFont: function( font , editorParentId , editorFonts ){

            if( $.inArray( font , this.loadedFonts ) != -1 || $.inArray( font , this.baseLoadedFonts ) != -1 ){
                if( !_.isUndefined( editorParentId ) && !_.isUndefined( editorFonts ) ){
                    this.sendData( editorParentId , editorFonts );
                }
                return ;
            }

            if( $.inArray( font , _.keys( this.fonts["custom_fonts"] ) ) != -1 )
                this.loadCustomFont( font );
            else if( $.inArray( font , _.keys( this.fonts["google_fonts"] ) ) != -1  )
                this.loadGoogleFont( font );

            if( !_.isUndefined( editorParentId ) && !_.isUndefined( editorFonts ) ){
                this.sendData( editorParentId , editorFonts );
            }

        },

        loadCustomFont: function( font ){

            var the_font = '@font-face {';
            the_font += 'font-family: ' + this.customFontsSettings[font].name + ';';
            the_font += 'src: url("' + this.customFontsSettings[font].src.eot + '");';
            the_font += 'src:';
            the_font += "url('" + this.customFontsSettings[font].src.eot + "?#iefix') format('eot'),";
            the_font += "url('" + this.customFontsSettings[font].src.woff + "') format('woff'),";
            the_font += "url('" + this.customFontsSettings[font].src.ttf + "') format('truetype'),";
            the_font += "url('" + this.customFontsSettings[font].src.svg + "#" + this.customFontsSettings[font].name + "') format('svg');";
            the_font += "font-weight: 400;";
            the_font += "font-style: normal;";
            the_font += "}";

            var style = '<style type="text/css">' + the_font  + '</style>';

            $( style ).appendTo( $("head") );

            this.loadedFonts.push( font );

        },


        loadGoogleFont: function( font ){

    		var the_font = font.replace(/\s+/g, '+'),
                protocol = ( IS_SSL ) ? "https" : "http" ;

            if( !_.isEmpty( this.googleFontsSettings ) )
                the_font += ":" + this.googleFontsSettings;

    		//add reference to google font family
    		$('head').append('<link href="'+ protocol +'://fonts.googleapis.com/css?family='+ the_font +'" rel="stylesheet" type="text/css">');

            this.loadedFonts.push( font );

        },


        sendData: function( editorParentId , editorFonts ){

            editorFonts = _.uniq( editorFonts );

            editorFonts = editorFonts.join( "," );

            api.contentBuilder.updateShortcodeAttr( "fonts" , editorFonts , editorParentId );

        }


    });

    $( function() {
        api.settings = window._sedAppEditorSettings;
        api.mceFontFormats = window._sedTinymceFontFormats;
        api.fonts = window._sedAppEditorFonts;

        api.typography = new api.SiteEditorTypography({} , {
            googleFontsSettings : window._sedGoogleFontsSettings ,
            customFontsSettings : window._sedCustomFontsSettings ,
            baseLoadedFonts     : window._sedBaseLoadedFonts
        });  

        api.siteIframe = new api.SiteEditorIframe({} , {
            preview : api.preview
        });

    });

}(sedApp, jQuery));