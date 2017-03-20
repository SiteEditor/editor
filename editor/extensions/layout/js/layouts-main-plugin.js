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

    api.AppLayouts = api.Class.extend({

        initialize: function (params, options) {
            var self = this;

            $.extend(this, options || {});

            this.currentLayout;

            this.ready();
        },

        ready: function () {
            var self = this;

            var initLayoutScopeControl = false;

            api.Events.bind("afterInitAppendModulesSettingsTmpl", function (moduleSettingsObj, currentElDialog) {

                var currentElement = $("#website")[0].contentWindow.jQuery('[sed_model_id="' + api.currentTargetElementId + '"]'),
                    currentRow = currentElement.parents(".sed-pb-module-container:first").parent();

                var scopeEl = $($("#layouts-scope-settings-button-tpl").html()).prependTo(currentElDialog);

                if (!_.isUndefined(__layoutsScopeContent) && !_.isEmpty(__layoutsScopeContent)) {

                    var scopeSettingsEl = $( __layoutsScopeContent ).prependTo($("#dialog_page_box_main_layout_row_scope_control"));

                    scopeSettingsEl.after( $("#manage-layout-theme-rows-page-box-tpl").html() );


                } else {

                    var content = $("#layouts-scope-settings-content-tpl").html();

                    content += $("#manage-layout-theme-rows-page-box-tpl").html();

                    $( content ).prependTo($("#dialog_page_box_main_layout_row_scope_control"));

                }

                if (currentRow.parent().hasClass("sed-site-main-part")) {
                    scopeEl.show();
                } else {
                    scopeEl.hide();
                }

            });

            api.Events.bind("afterAppendModulesSettingsTmpl", function (moduleSettingsObj, currentElDialog) {
                var currentElement = $("#website")[0].contentWindow.jQuery('[sed_model_id="' + api.currentTargetElementId + '"]'),
                    currentRow = currentElement.parents(".sed-pb-module-container:first").parent();

                $(__layoutsScopeContent).prependTo($("#dialog_page_box_main_layout_row_scope_control"));
                var scopeEl = $("#sed-scope-settings-main_layout_row_scope_control").parents(".row_settings:first");

                if (currentRow.parent().hasClass("sed-site-main-part")) {
                    scopeEl.show();
                } else {
                    scopeEl.hide();
                }

            });

            var __layoutsScopeContent , __isOnceCreateScopeContent = true;
            //Create Scope Content only one time and keep with all events
            api.Events.bind("beforeResetDialogSettingsTmpl", function (settingsId) {
                if( __isOnceCreateScopeContent === true ) {
                    __layoutsScopeContent = $("#dialog_page_box_main_layout_row_scope_control").children().detach();
                    __isOnceCreateScopeContent = false;
                }
            });

            api.previewer.bind("updateCurrentLayoutRowsOrders", function (themeRows) {
                var control = api.control.instance("main_layout_row_scope_control");
                //console.log("---------------themeRows-----------------", themeRows);
                if (!_.isUndefined(control)) {
                    control.ordersRefresh(themeRows);
                }
            });

            api.previewer.bind("sedPagesLayoutsInfo", function (info) {
                api.defaultPageLayout = info.defaultPageLayout;
                api.currentLayoutGroup = info.currentLayoutGroup;

                self.currentLayout = !_.isEmpty(api( api.currentPageLayoutSettingId )()) ? api( api.currentPageLayoutSettingId )() : api.defaultPageLayout;

                var scopeControl = api.control.instance("main_layout_row_scope_control");

                if (!_.isUndefined(scopeControl)) {
                    scopeControl.currentLayout = !_.isEmpty(api( api.currentPageLayoutSettingId )()) ? api( api.currentPageLayoutSettingId )() : api.defaultPageLayout;
                }

                var layoutsManagerControl = api.control.instance("sed_add_layout_layouts_manager");

                if (!_.isUndefined(layoutsManagerControl)) {
                    layoutsManagerControl.currentLayout = !_.isEmpty(api( api.currentPageLayoutSettingId )()) ? api( api.currentPageLayoutSettingId )() : api.defaultPageLayout;
                }

            });


            api.previewer.bind("sedRemoveModuleElementsSync", function (moduleId) {
                self.removeModule(moduleId);
            });

            api.previewer.bind("ok_sedRemoveModulesConfirm", function () {
                api.previewer.send("sedRemoveModulesApply", $("#sed-confirm-message-dialog").data("moduleId"));
                $("#sed-confirm-message-dialog").removeData("moduleId")
            });

            api.previewer.bind("cancel_sedRemoveModulesConfirm", function () {
                $("#sed-confirm-message-dialog").removeData("moduleId")
            });

            api.previewer.bind("customThemeRowInfoChange", function () {
                if ($("#sed_theme_custom_row_type").length > 0) {
                    self.updateRowTypeSelectField();
                }
            });

            $(".sed_go_to_scope_settings").livequery(function () {

                $(this).on("click.openScopeSettings", function () {

                    var currentElement = $("#website")[0].contentWindow.jQuery('[sed_model_id="' + api.currentTargetElementId + '"]'),
                        currentRow = currentElement.parents(".sed-pb-module-container:first").parent();

                    var themeId = currentRow.data("themeId"); 

                    if (initLayoutScopeControl === false) {
                        api.Events.trigger("renderSettingsControls", 'main_layout_row_scope_control', api.settings.controls['main_layout_row_scope_control']);
                        initLayoutScopeControl = true;
                    }

                    var control = api.control.instance("main_layout_row_scope_control");

                    if (!_.isUndefined(themeId) && !_.isEmpty(themeId) && themeId) {
                        control.update(themeId);
                    } else {
                        control.update();
                    }

                    self.updateRowTypeSelectField();
                });

            }, function () {

                $(this).unbind("click.openScopeSettings");

            });

            $("#sed_theme_custom_row_type").livequery(function () {

                $(this).on("change", function () {
                    var val = $(this).val();
                    api.previewer.send("customThemeRowChangeType", val);
                });

            }, function () {

                $(this).unbind("change");

            });

            //when customize revert to hidden or normal public scope ::  current element updated
            api.previewer.bind('changeCurrentElementByCustomizeRevert', function (dataEl) {
                api.appModulesSettings.updateByChangePattern(dataEl);
            });

            //before change page layout
            api.Events.bind( 'beforeRefreshPreviewer' , function ( id ){

                var currGroupId = "sed_pages_layouts[" + api.currentLayoutGroup + "]";

                if( id ==  api.currentPageLayoutSettingId || currGroupId == id ) {
                    //self.beforeUpdatePageLayout();
                }

            });

            //Update Layouts for controls
            _.each( [ "afterAppendSettingsTmpl" , "endInitAppendSettingsTmpl" ] , function( _EvSettingsAppend ){

                api.Events.bind( _EvSettingsAppend , function( $dialog , settingsType , settingsId ){

                    if( $dialog.find(".sed_all_layouts_options_select").length > 0 ) {

                        _.each( api.sedGroupControls[settingsId] , function( data ){

                            if( $( "#sed-app-control-" + data.control_id ).find(".sed_all_layouts_options_select").length > 0 ) {

                                var template = api.template("sed-layouts-select-options"),
                                    content = template({layoutsSettings: api('sed_layouts_settings')()});

                                $("#sed-app-control-" + data.control_id).find(".sed_all_layouts_options_select").html( content );

                                var control = api.control.instance( data.control_id );

                                if ( !_.isUndefined( control ) ) {

                                    var currVal = control.currentValue;

                                    control.update( currVal );

                                }

                            }

                        });

                    }

                });

            });

            api.addFilter( 'sedPreviewerTransportFilter' , function( transport , id ){

                var currGroupId = "sed_pages_layouts[" + api.currentLayoutGroup + "]";

                if (_.isEmpty(api( api.currentPageLayoutSettingId )()) && id == currGroupId) {

                    transport = "refresh";

                }else if( id ==  api.currentPageLayoutSettingId ) {

                    var newLayout = !_.isEmpty(api( api.currentPageLayoutSettingId )()) ? api( api.currentPageLayoutSettingId )() : api( currGroupId )();

                    if( newLayout == self.currentLayout ) {

                        transport = "postMessage";

                    }

                }

                return transport;
            });

        },

        beforeUpdatePageLayout : function(){

            var currGroupId = "sed_pages_layouts[" + api.currentLayoutGroup + "]";

            var newLayout = !_.isEmpty(api( api.currentPageLayoutSettingId )()) ? api( api.currentPageLayoutSettingId )() : api( currGroupId )() ,
                curLayout = this.currentLayout;

            if( _.isUndefined( this.cacheChangeLayoutThemeContent ) )
                this.cacheChangeLayoutThemeContent = {};

            this.cacheChangeLayoutThemeContent[curLayout] = api.sedShortcode.clone( api( api.currentPageThemeContentSettingId )() );

            if( !_.isUndefined( this.cacheChangeLayoutThemeContent[newLayout] ) ) {
                api( api.currentPageThemeContentSettingId ).set(this.cacheChangeLayoutThemeContent[newLayout]);
            }

        },

        updateRowTypeSelectField: function () {

            var id = $("#website")[0].contentWindow.jQuery('[sed_model_id="' + api.currentTargetElementId + '"]').parents(".sed-pb-module-container:first").parent().attr("sed_model_id");

            var shortcode = _.findWhere(api.pagesThemeContent[api.settings.page.id], {id: id});

            if (!_.isUndefined(shortcode.theme_id)) {

                $("#sed_theme_custom_row_type_container").addClass("hide");

                return;

            } else {

                $("#sed_theme_custom_row_type_container").removeClass("hide");

                $("#sed_theme_custom_row_type > option").removeClass("hide");

                var rowType = shortcode.row_type,
                    relThemeId = shortcode.rel_theme_id;

                $("#sed_theme_custom_row_type").val(rowType);

            }

            var otherRowType = "before",
                hasBeforePublicRow = false,
                hasAfterPublicRow = false,
                themeRows = _.where(api.pagesThemeContent[api.settings.page.id], {parent_id: "root"}),
                num = 0,
                currentIndex;

            _.each(api.pagesThemeContent[api.settings.page.id], function (shortcode, index) {


                if (id != shortcode.id && shortcode.parent_id == "root" && !_.isUndefined(shortcode.theme_id)) {

                    if (otherRowType == "before") {
                        hasBeforePublicRow = true;
                    } else if (otherRowType == "after") {
                        hasAfterPublicRow = true;
                        return false;
                    }

                } else if (id == shortcode.id) {
                    otherRowType = "after";
                    currentIndex = num;
                }

                if (shortcode.parent_id == "root") {
                    num += 1;
                }

            });

            if (hasBeforePublicRow === true) {
                $('#sed_theme_custom_row_type > option[value="start"]').addClass("hide");
            } else {
                $('#sed_theme_custom_row_type > option[value="after"]').addClass("hide");
            }

            if (hasAfterPublicRow === true) {
                $('#sed_theme_custom_row_type > option[value="end"]').addClass("hide");
            } else {
                $('#sed_theme_custom_row_type > option[value="before"]').addClass("hide");
            }

            if (rowType == "before" && currentIndex > 0) {
                if (!_.isUndefined(themeRows[currentIndex - 1].rel_theme_id) && themeRows[currentIndex - 1].rel_theme_id == relThemeId) {
                    $('#sed_theme_custom_row_type > option[value="after"]').addClass("hide");
                    $('#sed_theme_custom_row_type > option[value="start"]').addClass("hide");
                }
            }

            if (rowType == "after" && themeRows.length > ( currentIndex + 1 )) {
                if (!_.isUndefined(themeRows[currentIndex + 1].rel_theme_id) && themeRows[currentIndex + 1].rel_theme_id == relThemeId) {
                    $('#sed_theme_custom_row_type > option[value="before"]').addClass("hide");
                    $('#sed_theme_custom_row_type > option[value="end"]').addClass("hide");
                }
            }

            if (rowType == "start" && themeRows.length > ( currentIndex + 1 )) {
                if (!_.isUndefined(themeRows[currentIndex + 1].rel_theme_id) && themeRows[currentIndex + 1].row_type == rowType) {
                    $('#sed_theme_custom_row_type > option[value="before"]').addClass("hide");
                }
            }

            if (rowType == "end" && currentIndex > 0) {
                if (!_.isUndefined(themeRows[currentIndex - 1].rel_theme_id) && themeRows[currentIndex - 1].row_type == rowType) {
                    $('#sed_theme_custom_row_type > option[value="after"]').addClass("hide");
                }
            }

        },

        removeModule: function (moduleId) {
            var control = this;

            $("#sed-confirm-message-dialog").dialog("open");

            $("#sed-confirm-message-dialog").data("confirmEventId", "sedRemoveModulesConfirm");

            $("#sed-confirm-message-dialog").data("moduleId", moduleId);

            $("#sed-confirm-message-dialog").html($("#sed-remove-module-confirm-tpl").html());

        },

        manageLayoutRows: function ( elm , themeId ) {

            $(api.sedDialogSettings.dialogSelector).data('sed.multiLevelBoxPlugin')._pageBoxNext( elm );

            //$( api.sedDialogSettings.dialogSelector ).data('sed.multiLevelBoxPlugin')._callDirectlyLevelBox( "dialog_page_box_manage_layout_theme_rows"  );

            var layout = $( elm ).data("layout"),
                template = api.template("sed-layout-edit-rows"),
                models = api('sed_layouts_models')(),
                content = template({
                    layoutRows: models[layout],
                    noTitle: api.I18n.no_title,
                    currThemeId: themeId || ""
                });

            $("#dialog_page_box_manage_layout_theme_rows .sed-dialog-page-box-inner").html(content);

            $("#dialog_page_box_manage_layout_theme_rows").data("layout" , layout);
        }

    });

    
    $( function() {

        api.appLayouts = new api.AppLayouts({});

        api.previewer.bind('pageStaticContentInfo' , function( info ){
            api.pageStaticContentInfo = info;
        });

    });

})( sedApp, jQuery );