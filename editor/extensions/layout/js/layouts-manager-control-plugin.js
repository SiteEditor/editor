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

    api.LayoutsManagerControl = api.Control.extend({

        ready: function () {
            var control = this;
            this.model = $.extend( true, {} , control.setting() );

            this.currentLayout = !_.isEmpty( api( api.currentPageLayoutSettingId )() ) ? api( api.currentPageLayoutSettingId )() : api.defaultPageLayout;

            this.view();
            this.updateView();

            api.previewer.bind( "ok_sedRemoveLayoutConfirm" , function () {
                control.removeLayout( $("#sed-confirm-message-dialog").data( "layout" ) , false );
                $("#sed-confirm-message-dialog").removeData( "layout" );
            });

            api.previewer.bind( "cancel_sedRemoveLayoutConfirm" , function () {
                $("#sed-confirm-message-dialog").removeData( "layout" );
            });

            $( api.sedDialogSettings.dialogSelector ).find(".sed_go_to_manage_layout_rows").livequery(function(){
                //if( _.isUndefined( self.panelsContents[self.currentSettingsId] ) ){
                $(this).click(function(){

                    api.appLayouts.manageLayoutRows( this );

                });
                //}
            },function(){
                $(this).unbind("click");
            });

        },

        view : function(){
            var actionElement = this.container.find('[data-action]'),
                control = this;

            this.UI = {
                _Edit             : this.container.find('.sed-layout-edit') ,
                _EditInput        : this.container.find('.sed-layout-edit [name="edit-layout-title"]') ,
                _Add              : this.container.find('.sed-add-layout') ,
                _AddTitleInput    : this.container.find('.sed-add-layout [name="add-new-layout-title"]') ,
                _AddSlugInput     : this.container.find('.sed-add-layout [name="add-new-layout-slug"]') ,
                _ErrorBox         : this.container.find(".sed-layout-error-box p")
            };

            actionElement.livequery(function(){
                $(this).on("click" , function(){
                    var action = $(this).data("action");
                    switch ( action ){
                        case "save":
                            control.saveItem();
                            break;
                        case "save-close":
                            control.disableEditMode();
                            break;
                        case "edit":
                            control.currentLayoutEl = $(this).parents("li:first");
                            control.editItem( $(this).data("layoutTitle") , $(this).data("layout") );
                            break;
                        case "add":
                            control.addItem();
                            break;
                        case "delete":
                            control.removeLayout( $(this).data("layout") );
                            break;
                    }
                });
            }, function() {
                // unbind the change event
                $(this).unbind('click');
            });
        },

        refresh : function () {
            this.setting.set( this.model );
            this.updateView();
        },

        printAlert : function ( ) {
            this.UI._ErrorBox.html( this.errortext );
            this.UI._ErrorBox.slideDown( 300 ).delay( 5000 ).fadeOut( 400 );
        },

        updateView : function(){
            var template = api.template( "sed-layouts-manager" ),
                content = template( { layoutsSettings : this.model , currentLayout : this.currentLayout } );

            this.container.find(".sed-layout-lists > ul").html( content );
        },

        addItem : function ( title , slug ) {
            var title = this.UI._AddTitleInput.val(),
                slug = this.UI._AddSlugInput.val();

            if ( _.isEmpty( title ) ) {
                this.errortext = api.I18n.empty_layout_title;
                this.printAlert();
                return;
            }

            if ( !this.titleValidation( title ) ) {
                this.errortext = api.I18n.invalid_layout_title;
                this.printAlert();
                return;
            }

            if ( _.isEmpty( slug ) ) {
                this.errortext = api.I18n.empty_layout_slug;
                this.printAlert();
                return;
            }

            if ( !this.slugValidation( slug ) ) {
                this.errortext = api.I18n.invalid_layout_slug;
                this.printAlert();
                return;
            }

            if ( $.inArray( slug , _.keys( this.model ) ) == -1 ) {
                this.model[slug] = {
                    "title" : title
                };

                this.UI._AddTitleInput.val("");
                this.UI._AddSlugInput.val("");

                this.refresh();
                this.UI._ErrorBox.hide();

                //add main_row(content) model to sed_layouts_models
                var layoutModels = $.extend( true, {} , api('sed_layouts_models')() );

                var lastThemeId = parseInt( api('sed_last_theme_id')() );

                lastThemeId += 1;

                api('sed_last_theme_id').set( lastThemeId );

                var themeId = "theme_id_" + lastThemeId;

                layoutModels[slug] = [];

                layoutModels[slug].push({
                    order       : 0,
                    theme_id    : themeId,
                    exclude     : [], // this row not show in pages with this ids
                    hidden      : [],
                    title       : api.I18n.main_row_content ,
                    main_row    : true
                });

                var control = api.control.instance("main_layout_row_scope_control");

                if (!_.isUndefined(control)) {
                    control.models = layoutModels;
                }

                api('sed_layouts_models').set( layoutModels );

            } else {
                this.errortext = api.I18n.layout_already_exist;
                this.printAlert();
            }
        },

        removeLayout : function ( slug , confirm ) {
            confirm = _.isUndefined( confirm ) ? true : confirm;

            if( confirm === true ) {
                $("#sed-confirm-message-dialog").dialog("open");

                $("#sed-confirm-message-dialog").data("confirmEventId", "sedRemoveLayoutConfirm");

                $("#sed-confirm-message-dialog").data("layout", slug);

                $("#sed-confirm-message-dialog").html($("#sed-remove-layout-confirm-tpl").html());

                return false;
            }

            if( slug == "default" ){
                this.errortext = api.I18n.remove_default_layout;
                this.printAlert();
            }else if( slug == this.currentLayout ){
                this.errortext = api.I18n.remove_current_layout;
                this.printAlert();
            }else if( $.inArray( slug , _.keys( this.model ) ) > -1 ){
                //remove from sed_layouts_settings
                delete this.model[slug];
                this.refresh();

                //remove from sed_pages_layouts
                _.each( pageLayouts , function( layout , pagesGroup ){
                    if( layout == slug ){
                        api('sed_pages_layouts[' + pagesGroup + ']').set( "default" );
                    }
                });

                //remove from sed_layouts_models
                var layoutModels = $.extend( true, {} , api('sed_layouts_models')() );

                var themeIds = _.pluck( layoutModels[slug] , "theme_id" );

                $.each( layoutModels , function (layout, rows) {
                    if( layout != slug ) {
                        $.each(rows, function (idx, options) {
                            var index = $.inArray( options.theme_id , themeIds );
                            if ( index > -1 ){
                                themeIds.splice( index , 1);
                            }
                        });
                    }
                });

                if( !_.isUndefined( layoutModels[slug] ) ){
                    delete layoutModels[slug];

                    var control = api.control.instance("main_layout_row_scope_control");

                    if (!_.isUndefined(control)) {
                        control.models = layoutModels;
                    }

                    api('sed_layouts_models').set( layoutModels );
                }


                //remove theme row only in this layout from sed_layouts_content
                var layoutsContent = api.layoutsRowsContent.getClone();

                _.each( themeIds, function( themeId ){

                    if( !_.isUndefined( layoutsContent[themeId] ) )
                        delete layoutsContent[themeId];

                });

                api.layoutsRowsContent.set( layoutsContent );

            }else{
                this.errortext = api.I18n.layout_not_exist;
                this.printAlert();
            }
        },

        editItem : function ( title , slug ) {
            this.errortext = "";
            this.container.find(".sed-view-mode").removeClass("hide");

            this.UI._Edit.removeClass("hide");

            this.currentLayoutEl.find(".sed-view-mode").addClass("hide");

            this.UI._EditInput.val( title );
            this.UI._EditInput.data( "layout" , slug );

            this.UI._Edit.appendTo( this.currentLayoutEl.find(".sed-edit-mode") );
            this.UI._EditInput.focus();
        },

        saveItem : function ( ) {
            var title = this.UI._EditInput.val(),
                slug = this.UI._EditInput.data( "layout" );

            if ( _.isEmpty( title ) ) {
                this.errortext = api.I18n.empty_layout_title;
                this.printAlert();
                return;
            }

            if ( !this.titleValidation( title ) ) {
                this.errortext = api.I18n.invalid_layout_title;
                this.printAlert();
                return;
            }

            if ( $.inArray( slug , _.keys( this.model ) ) > -1 ) {
                this.model[slug] = {
                    "title" : title
                };
                this.refresh();
                this.UI._ErrorBox.hide();
            } else {
                this.errortext = api.I18n.invalid_layout;
                this.printAlert();
            }

        },

        titleValidation : function( title ){
            var pattern = /^[A-Za-z0-9_\-\s]{3,35}$/;
            return pattern.test( title );
        },


        slugValidation : function( slug ){
            var pattern = /^[A-Za-z0-9_\-]{3,20}$/;
            return pattern.test( slug );
        },

        disableEditMode : function () {
            this.currentLayoutEl.find(".sed-view-mode").removeClass("hide");
            this.UI._Edit.addClass("hide");
        }

    });

    api.controlConstructor = $.extend( api.controlConstructor, {
        layouts_manager : api.LayoutsManagerControl
    });


    $( function() {

    });

})( sedApp, jQuery );