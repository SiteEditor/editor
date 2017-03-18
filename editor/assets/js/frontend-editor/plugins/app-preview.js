(function( exports, $ ) {

    var api = sedApp.editor ;

    api.AppPreviewPlugin = api.Class.extend({

        initialize: function( params , options ){
            var self = this;

            $.extend( this, options || {} );

            this.ready();

            this.type; //desktop , mobile , tablet
            this.mode = "off"; //on || off

        },

        ready : function(){
            var self = this;
            $("body").addClass("sed-app-editor");

            api.Events.bind( "changePreviewMode" , function( mode ){

                if( !_.isUndefined( api.videoAudioTags ) ){

                    $.each( api.videoAudioTags , function( vid , moduleId ){
                        if( $( '[sed_model_id="' + moduleId + '"]' ).is("[sed-disable-editing='yes']") )
                            return ;

                        if( $( '[sed_model_id="' + moduleId + '"]' ).length > 0 )
                            api.contentBuilder.refreshModule( moduleId );
                        else
                            delete api.videoAudioTags[vid];
                    });

                }

            });

            api.preview.bind("previewMode" , function( mode ){

                api.Events.trigger( "startChangePreviewMode" , mode );

                if(mode == "on"){
                    self.mode = "on";
                    self.previewEnable();
                }else if(mode == "off"){
                    self.mode = "off";
                    self.previewDisable();
                }

                api.Events.trigger( "changePreviewMode" , self.mode );
            });

        },

        scrollBarReset : function( element ){
            $(window).scrollTop( this.editorScrollPos )
        },

        scrollBarReposition : function( element ){
            $(window).scrollTop( 0 )
        },

        //restart aniamation
        callWOWJs : function( element ){
            var wow = new WOW(
              {
                boxClass:     'wow',      // animated element css class (default is wow)
                animateClass: 'animated', // animation css class (default is animated)
                offset:       0,          // distance to the element when triggering the animation (default is 0)
              }
            );
            wow.init();
        },

        previewDisable : function( element ){
            $("body").addClass("sed-app-editor").removeClass("sed-app-preview");
            this.textEditorsEnable();
            this.freeDraggableMode( false );
            this.scrollBarReset();

        },

        previewEnable : function( element ){
            this.editorScrollPos = $(window).scrollTop();
            $("body").addClass("sed-app-preview").removeClass("sed-app-editor");
            $( ".sed-app-preview .mce-content-body" ).off( "click" );
            this.textEditorsDisable();
            this.scrollBarReposition();
            this.callWOWJs();
            this.freeDraggableMode( true );
            this.contextmenuDisable();

        },

        textEditorsDisable : function( ){
          if( _.isUndefined( tinymce ) || _.isUndefined( tinymce.activeEditor ) ||  _.isNull( tinymce.activeEditor ) )
            return ;

                 console.log( "tinymce.activeEditor-------------" , tinymce.activeEditor );

            if( $("#" + tinymce.activeEditor.id).hasClass("mce-edit-focus") )
                this.activeEditorId = tinymce.activeEditor.id;
            else
                this.activeEditorId = false;

            _.each( tinymce.editors , function( ed ){
                $( ed.getBody() ).prop("contenteditable" , false);
            });

        },

        textEditorsEnable : function( ){

          if( typeof tinymce == "undefined" )
            return ;

            _.each( tinymce.editors , function( ed ){
                $( ed.getBody() ).prop("contenteditable" , true);
            });
            //tinymce.execInstanceCommand( this.activeEditorId , "mceFocus");
            //tinymce.execCommand('mceFocus', false , tinymce.activeEditor.id);
            /*if( this.activeEditorId )
                tinymce.get( this.activeEditorId ).focus();
            else if( !_.isNull( tinymce.activeEditor )  )
                tinymce.execCommand( 'mceFocus' , false , tinymce.activeEditor.id );*/
                //tinymce.get( tinymce.activeEditor.id ).execCommand ('mceFocus', false);
                //.focus( false );
        },

        freeDraggableMode : function( disabled ){
            disabled = ( !_.isUndefined( disabled ) ) ? disabled: true;
            $('.module-element-draggable').draggable({ disabled: disabled });
        },

        contextmenuDisable : function( ){
            //api.hideContextmenu();
        },

        //modify select.min.js , not need func here
        /*selectDisable : function( element ){

        },*/

        linkEnabled : function( element ){

        },

        keyboardShortcutDisable : function( element ){

        }

    });


    $( function() {

        api.appPreview = new api.AppPreviewPlugin();

    });

}(sedApp, jQuery));