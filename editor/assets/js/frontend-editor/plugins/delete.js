(function( exports, $ ) {

    var api = sedApp.editor ;

    api.DeletePlugin = api.Class.extend({

        initialize: function( params , options ){

            $.extend( this, options || {} );

            this.ready();

            this.elementId = null;
        },

        ready : function(){
            this.removeAlert();
        },

        remove : function( elementId , callback , closeDialog ){
            this.elementId = elementId;

            api.Events.trigger( "sedBeforeRemove" , elementId );
            api.Events.trigger( "before-remove-" + elementId );


            this.removeFromShortcodes();
            this.removeFromUsingModules();
            this.removeFromStyleEditor();
            this.removeFromDom();

            closeDialog = _.isUndefined( closeDialog ) ? false : closeDialog;

            if( closeDialog === true ) {
                this.closeSettingsDialog();
            }


            api.Events.trigger( "sedAfterRemove" , elementId );
            api.Events.trigger( "after-remove-" + elementId );

            if( callback )
                callback();

        },

        removeAlert : function( ){
             var self = this;

            $('.sed-handle-sort-row .remove_pb_btn').livequery( function(){
                if( !$(this).parents(".sed-row-pb:first").hasClass('sed-main-content-role') && !$(this).parents(".sed-row-pb:first").hasClass('sed-main-content-row-role') ){
                    /*$(this).popover({
                        html : true ,
                        content :  function(){
                            return $("#sed-remove-alert-tmpl").html();
                        } ,
                        placement : "auto top" ,
                        container: 'body'
                    });*/
                    $(this).on( "click" , function(){
                        api.preview.send( "sedRemoveModuleElementsSync" , $(this).data("moduleRelId") );
                    });

                }else{
                    $(this).addClass('disabled');
                }
            });

            api.preview.bind( "sedRemoveModulesApply" , function( moduleId ){

                var dropArea = $( '[sed_model_id="' + moduleId + '"]' ).parents(".sed-pb-component:first");

                self.remove( moduleId , '' , true );

                if( dropArea.length > 0 )
                    api.pageBuilder.addRemoveSortableClass( dropArea );

            });

            /*$(".close-popover").livequery( function(){
                $(this).click(function(){
                    var id = $(this).parents(".popover:first").attr("id");
                    $('[aria-describedby="' + id + '"]').popover('hide');
                });
            });

            $(".sed-module-remove-btn").livequery( function(){
                $(this).click(function(){
                    var id = $(this).parents(".popover:first").attr("id");
                    var moduleId = $('[aria-describedby="' + id + '"]').data( "moduleRelId" ) ,
                        dropArea = $( '[sed_model_id="' + moduleId + '"]' ).parents(".sed-pb-component:first");
                    $('[aria-describedby="' + id + '"]').popover('hide');
                    self.remove( moduleId );

                    api.pageBuilder.addRemoveSortableClass( dropArea );
                    //$("")
                });
            });

            $('body').on('click', function (e) {
                $('.sed-handle-sort-row .remove_pb_btn').not('.disabled').each(function () {

                    if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0 ) {
                        $(this).popover('hide');
                        //$(this).next('.popover').hide();
                    }
                });

            });*/

        },

        removeFromDom : function( ){
            $( '[sed_model_id="' + this.elementId + '"]'  ).detach();
        },

        removeFromShortcodes : function( ){
            var postId = api.pageBuilder.getPostId( $( '[sed_model_id="' + this.elementId + '"]' ) );
            api.contentBuilder.deleteModule( this.elementId , postId);
        },

        removeFromUsingModules : function(){
            var self = this;
            api.pageModulesUsing = _.filter( api.pageModulesUsing , function( module ){
                return module.id != self.elementId;
            });
        },

        removeFromStyleEditor : function( ){

        },

        closeSettingsDialog : function(){

            api.preview.send('dialogSettingsClose');
            api.isOpenDialogSettings = false;
            api.currentSedElementId = "";

        }

    });


    $( function() {

        api.removePlugin = new api.DeletePlugin();

        api.remove = function(elementId , callback , closeDialog ){
            api.removePlugin.remove(elementId , callback , closeDialog );
        };

    });

}(sedApp, jQuery));