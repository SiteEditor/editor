/**
 * @plugin.js
 * @App Layout Plugin JS
 *
 * @License: http://www.siteeditor.org/license
 * @Contributing: http://www.siteeditor.org/contributing
 */

/*global siteEditor:true */
(function( exports, $ ){

    var api = sedApp.editor;

    api.RemovedRowsCollection = api.Class.extend({

        initialize: function (params, options) {
            var self = this;

            this.value = {};

            $.extend(this, options || {});

            this.ready();
        },

        ready: function () {

            var self = this;

            //Before remove a public row
            api.Events.bind( "beforeRemovedThemePublicRow" , function( themeId ){
                self.beforeDestroyPublicRow( themeId );
            });

            //Before Change Public To Private Scope
            api.Events.bind( "beforeChangeScopePublicToPrivate" , function( themeId ){
                self.beforeDestroyPublicRow( themeId );
            });

            //when checked special layout or checked all layouts in scope settings
            api.Events.bind( "beforeChangePublicRowLayout" , function( themeId , layout , isAdded ){

                if( ! isAdded ){
                    var nextModel = self.getNextPublicRow( leyout , themeId );

                    var prevModel = self.getPrevPublicRow( leyout , themeId );

                    self.refresh( themeId , leyout , prevModel , nextModel );
                }else{
                    self.removeModel( themeId , layout );
                }

            });

        },

        beforeDestroyPublicRow : function( themeId ){

            var leyouts = this.getLayoutsByThemeId( themeId ) ,
                self = this;

            if( !_.isEmpty( leyouts ) ){

                _.each( leyouts , function( leyout ){

                    var nextModel = self.getNextPublicRow( leyout , themeId );

                    var prevModel = self.getPrevPublicRow( leyout , themeId );

                    self.refresh( themeId , leyout , prevModel , nextModel );

                });

            }

        },

        refresh : function( themeId , leyout , prevModel , nextModel ){

            if( _.isUndefined( themeId ) || ! themeId )
                return ;

            var removedRowsModels = $.extend( true , {} , api('sed_layouts_removed_rows')() || {} );

            var afterRelThemeId =  ( _.isUndefined( nextModel ) || ! nextModel ) ? "" : nextModel.theme_id ;

            var afterRowType    =  ( _.isUndefined( nextModel ) || ! nextModel ) ? "end" : "before" ;

            var beforeRelThemeId =  ( _.isUndefined( prevModel ) || ! prevModel ) ? "" : prevModel.theme_id ;

            var beforeRowType    =  ( _.isUndefined( prevModel ) || ! prevModel ) ? "start" : "after" ;

            if( _.isUndefined( removedRowsModels[leyout] ) )
                removedRowsModels[leyout] = [];

            removedRowsModels[leyout].push({
                theme_id    :  themeId ,
                after       :  {
                    rel_theme_id    : afterRelThemeId  ,
                    row_type        : afterRowType
                } ,
                before      :  {
                    rel_theme_id    : beforeRelThemeId  ,
                    row_type        : beforeRowType
                }
            });

            api('sed_layouts_removed_rows').set( removedRowsModels );

        },

        removeModel: function ( themeId , layout ) {

            var removedRowsModels = $.extend( true , {} , api('sed_layouts_removed_rows')() || {} );

            if( _.isUndefined( removedRowsModels[layout] ) )
                return ;

            removedRowsModels[layout] = _.filter( removedRowsModels[layout] , function( model ) {
                return model.theme_id != themeId;
            });

            api('sed_layouts_removed_rows').set( removedRowsModels );

        },

        getLayoutsByThemeId: function ( themeId ) {

            var layouts = [];

            var layoutsModels = $.extend( true , {} , api('sed_layouts_models')() );

            $.each( layoutsModels , function (layout, rows) {
                $.each(rows, function (idx, options) {
                    if (options.theme_id == themeId)
                        layouts.push(layout);
                });
            });

            return layouts;

        },

        getNextPublicRow : function( layout , themeId ){

            var layoutsModels = $.extend( true , {} , api('sed_layouts_models')() );

            if( _.isUndefined( layoutsModels[layout] ) ){
                return false;
            }

            var models = _.sortBy( layoutsModels[layout] , 'order' );

            models = models.reverse();

            var currIndex = _.findIndex( models , { theme_id : themeId } );

            if( currIndex < ( models.length - 1 )  ){
                return models[ currIndex + 1 ];
            }else{
                return false;
            }

        },

        getPrevPublicRow : function( layout , themeId ){

            var layoutsModels = $.extend( true , {} , api('sed_layouts_models')() );

            if( _.isUndefined( layoutsModels[layout] ) ){
                return false;
            }

            var models = _.sortBy( layoutsModels[layout] , 'order' );

            models = models.reverse();

            var currIndex = _.findIndex( models , { theme_id : themeId } );

            if( currIndex > 0  ){
                return models[ currIndex - 1 ];
            }else{
                return false;
            }

        }

    });



    $( function() {


        var confirmActionType = "cancel";
        $("#sed-confirm-message-dialog").dialog({
            autoOpen      : false,
            modal         : true,
            width         : 350,
            height        : 150 ,   //default is "auto"
            resizable     : false ,
            close         : function(){
                if( confirmActionType == "cancel" ){
                    var confirmEventId = $(this).data("confirmEventId");
                    api.previewer.trigger( "cancel_" + confirmEventId );
                    $( this ).html("");
                }else{
                    confirmActionType = "cancel";
                }
            },
            buttons: [
                {
                    text: api.I18n.ok_confirm,
                    click: function() {
                        confirmActionType = "ok";
                        $( this ).dialog( "close" );
                        var confirmEventId = $(this).data("confirmEventId");
                        api.previewer.trigger( "ok_" + confirmEventId );
                        $( this ).html("");
                    }
                },
                {
                    text:  api.I18n.cancel_confirm,
                    click: function () {
                        confirmActionType = "cancel";
                        $(this).dialog("close");
                    }
                }
            ]
        });

    });
    
})( sedApp, jQuery );