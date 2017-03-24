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

    //1.no public to public ----------- add theme_id to main shortcode model
    //2.public to no public ----------- remove theme_id from main shortcode model && remove related shortcodes from sed_layout_content
    //3.public to customize ------------ add is_customize to main shortcode model && not update in sed_layout_content
    //4.customize to public ------------ remove is_customize from main shortcode model && replce main shortcodes with customize shortcodes
    //5.hidden to customize ------------ remove is_hidden & add is_customize to main shortcode model && not update in sed_layout_content
    //6.customize to hidden ------------- remove is_customize & add is_hidden to main shortcode model && replce main shortcodes with customize shortcodes
    //7.hidden to public ------------ remove is_hidden from main shortcode model
    //8.public to hidden -------------  add is_hidden to main shortcode model

    /*
     confirm alert
     1. after convert public to private (customize or hidden or normal to private )
     2. after customize to hidden
     3. after customize to public
     4. after remove public row (customize or hidden or normal)
     5. after drag & drop public row to inner other modules
     */

    api.LayoutScopeControl = api.Control.extend({

        ready: function () {
            var control = this;

            this.lastThemeId = parseInt(api.instance('sed_last_theme_id').get());

            this.currentLayout = api.fn.getPageLayout();

            control.publicScopeEl = control.container.find('[name="sed_layout_scope_public"]');

            control.sedScopeLayoutEl = control.container.find('[name="sed_scope_layout"]');

            control.layoutPublicTypeEl = control.container.find('[name="sed_layout_public_type"]');

            control.allLayoutCheckedEl = control.container.find('.sed-all-sub-themes-check-box > input');

            control.editLayoutRowsEl = control.container.find('.edit-layout-rows');

            this.lastLayoutPublicType = "normal";

            this.themeId = "";

            this.confirmDialogEl = $("#sed-confirm-message-dialog");

            this.confirmEventIds = {
                "publicToPrivate"       : "changeScopePublicToPrivateConfirm",
                "customizeToPublic"     : "changeScopeCustomizeToPublicConfirm",
                "customizeToHidden"     : "changeScopeCustomizeToHiddenConfirm",
                "removeLayoutRow"       : "scopeRemoveLayoutRowConfirm",
                "removeAllLayoutRow"    : "scopeRemoveAllLayoutRowConfirm"
            };

            api.previewer.bind("ok_" + this.confirmEventIds.publicToPrivate, function () {
                control.changeScopePublicToPrivate();
            });

            api.previewer.bind("cancel_" + this.confirmEventIds.publicToPrivate, function () {
                $(control.selector).find('[name="sed_layout_scope_public"]').prop("checked", true);
            });

            api.previewer.bind("ok_" + this.confirmEventIds.customizeToPublic, function () {
                control.changeScopePublicTypes("normal", false);
            });

            api.previewer.bind("cancel_" + this.confirmEventIds.customizeToPublic, function () {
                control.updateRadioField( $(control.selector).find('[name="sed_layout_public_type"]') , "customize" );
            });

            api.previewer.bind("ok_" + this.confirmEventIds.customizeToHidden, function () {
                control.changeScopePublicTypes("hidden", false);
            });

            api.previewer.bind("cancel_" + this.confirmEventIds.customizeToHidden, function () {
                control.updateRadioField( $(control.selector).find('[name="sed_layout_public_type"]') , "customize" );
            });

            api.previewer.bind("ok_" + this.confirmEventIds.removeLayoutRow, function () {
                
                var $this = control.confirmDialogEl.data( "scopeLayoutEl" );
                
                control.removeRowFromModel( $this.val() );
                
                control.allLayoutCheckedEl.prop("checked", false);

                $this.parents(".sub-theme-item:first").find(".edit-layout-rows").addClass("hide");
                
            });

            api.previewer.bind("cancel_" + this.confirmEventIds.removeLayoutRow, function () {

                var $this = control.confirmDialogEl.data( "scopeLayoutEl" );

                $this.prop("checked", true);

            });

            api.previewer.bind("ok_" + this.confirmEventIds.removeAllLayoutRow, function () {

                var sedScopeLayoutEl = control.confirmDialogEl.data( "scopeLayoutEl" );

                sedScopeLayoutEl.prop("checked", false);

                sedScopeLayoutEl.each(function () {

                    var layout = $(this).val();

                    if (layout == control.currentLayout) {
                        $(this).prop("checked", true);
                        return;
                    }

                    $(this).parents(".sub-theme-item:first").find(".edit-layout-rows").addClass("hide");

                    if (control.existThemeIdInLayout(layout)) {
                        api.Events.trigger( "beforeChangePublicRowLayout" , control.themeId , layout , false );

                        control.removeRowFromModel(layout);
                    }

                });


            });

            api.previewer.bind("cancel_" + this.confirmEventIds.removeAllLayoutRow, function () {

                var $this = control.confirmDialogEl.data( "scopeCheckedAllEl" );

                $this.prop("checked", true);

            });

            this.publicScopeEl.on("change", function () {

                if ($(this).prop('checked')) {
                    var container = $(this).parents("li:first");
                    $(".sed-scope-mode-label .scope-mode").text( api.I18n.public_scope );

                    //show public options like public type && all layout
                    container.find("ul.select-pubic-scope").removeClass("hide");

                    //"normal" public layout type is default value for public scope
                    control.updateRadioField(container.find('[name="sed_layout_public_type"]'), "normal");
                    control.lastLayoutPublicType = "normal";

                    //show all layout && select current page layout AS default value for layouts in "normal" public layout type
                    container.find("ul.select-layouts-custom").removeClass("hide");
                    control.updateMultiCheckboxField(container.find('[name="sed_scope_layout"]'), [control.currentLayout]);

                    $(control.selector).find('.select-layouts-custom .edit-layout-rows').addClass("hide");

                    $(control.selector).find('.select-layouts-custom .edit-layout-rows').filter(function(){
                        return $(this).data("layout") == control.currentLayout;
                    }).removeClass("hide");

                    //always disable current page layout for prevent user control , it's can not unchecked when current row has any public type scope
                    container.find('.sub-theme-item input[value="' + control.currentLayout + '"]').prop("disabled", true);

                    //create new theme id & add current row to public Layouts Model
                    control.themeId = control.generateThemeId();
                    control.addRowToModel(control.currentLayout);

                    //update Row Title From default module name
                    var shortcodeName = api.appModulesSettings.sedDialog.data.shortcodeName ,
                        title = api.shortcodes[shortcodeName].title;
                    control.updateRowTitle( control.currentLayout , control.themeId , title);

                    $("#sed_theme_custom_row_type_container").addClass("hide");

                    /*
                     * @Event
                     * @Name : sedLayoutChangeScope
                     * @args : @type
                     */
                    api.previewer.send('sedLayoutChangeScope', {
                        'type': 'privateToPublic',
                        'elementId': api.currentTargetElementId,
                        'themeId': control.themeId
                    });

                } else {

                    if( control._get("main_row") )
                        return ;

                    control.confirmDialogEl.dialog("open");

                    control.confirmDialogEl.data("confirmEventId", control.confirmEventIds.publicToPrivate);

                    control.confirmDialogEl.html($("#change-public-to-private-confirm-tpl").html());

                }

            });

            this.layoutPublicTypeEl.on("change", function () {
                var type = $(this).val();
                control.changeScopePublicTypes(type);
            });

            this.sedScopeLayoutEl.livequery(function () {
                var $this = $(this);
                $this.on("change", function () {

                    api.Events.trigger( "beforeChangePublicRowLayout" , control.themeId , $(this).val() , $(this).prop('checked') );

                    if ($(this).prop('checked')) {

                        control.addRowToModel( $(this).val() , control._get( "order" ) );

                        var shortcodeName = api.appModulesSettings.sedDialog.data.shortcodeName ,
                            title = api.shortcodes[shortcodeName].title;
                        control.updateRowTitle($(this).val(), control.themeId, title);

                        if ($(control.selector).find('.select-layouts-custom input[name="sed_scope_layout"]').length == control.container.find("ul.select-layouts-custom .sub-theme-item input:checked").length) {
                            control.allLayoutCheckedEl.prop("checked", true);
                        }

                        $(this).parents(".sub-theme-item:first").find(".edit-layout-rows").removeClass("hide");

                    } else {

                        control.confirmDialogEl.dialog("open");

                        control.confirmDialogEl.data("confirmEventId", control.confirmEventIds.removeLayoutRow);

                        control.confirmDialogEl.data( "scopeLayoutEl", $(this) );

                        control.confirmDialogEl.html($("#remove-layout-row-confirm-tpl").html());
                        
                    }

                });
            }, function() {
                // unbind the change event
                $(this).unbind('change');
            });

            this.allLayoutCheckedEl.on("change", function () {
                var sedScopeLayoutEl = $(this).parents(".select-layouts-custom:first").find('[name="sed_scope_layout"]');

                if ($(this).prop('checked')) {
                    sedScopeLayoutEl.prop("checked", true);


                    sedScopeLayoutEl.each(function () {
                        var layout = $(this).val();

                        //if (layout != control.currentLayout)
                        $(this).parents(".sub-theme-item:first").find(".edit-layout-rows").removeClass("hide");

                        if (!control.existThemeIdInLayout(layout)) {

                            api.Events.trigger( "beforeChangePublicRowLayout" , control.themeId , layout , true );

                            control.addRowToModel( layout , control._get( "order" ) );

                            var shortcodeName = api.appModulesSettings.sedDialog.data.shortcodeName ,
                                title = api.shortcodes[shortcodeName].title;

                            control.updateRowTitle( layout , control.themeId , title );

                        }
                    });

                } else {

                    control.confirmDialogEl.dialog("open");

                    control.confirmDialogEl.data("confirmEventId", control.confirmEventIds.removeAllLayoutRow);

                    control.confirmDialogEl.data( "scopeLayoutEl", sedScopeLayoutEl );

                    control.confirmDialogEl.data( "scopeCheckedAllEl", $(this) );

                    control.confirmDialogEl.html( $("#remove-all-layout-row-confirm-tpl").html() );

                }

            });

            this.editLayoutRowsEl.livequery(function () {
                $(this).on("click", function () {

                    api.appLayouts.manageLayoutRows( this , control.themeId );

                });
            }, function() {
                // unbind the change event
                $(this).unbind('click');
            });

            var RowsPageBoxSelector = "#dialog_page_box_manage_layout_theme_rows";

            $( RowsPageBoxSelector ).find(".layout-row-container").livequery(function () {
                $(this).sortable({
                    handle: ".sort.action" ,
                    // Keep track of the starting position
                    start: function (event, ui) {
                        ui.item.startPos = ui.item.index();
                    },

                    update: function (e, ui) {
                        var order = 0,
                            themeRows = {};

                        $( RowsPageBoxSelector ).find(".layout-row-container > .sed-layout-row-box").each(function () {
                            var themeId = $(this).data("rowId");
                            themeRows[themeId] = {
                                order: order
                            };
                            order++;
                        });

                        var layout = $( RowsPageBoxSelector ).data("layout");

                        control.ordersRefresh(themeRows, layout);

                        var endPos    = ui.item.index();
                        var startPos  = ui.item.startPos;

                        if( control.currentLayout == layout ){
                            api.previewer.send( "syncLayoutPublicRowsSort" , {
                                start   : startPos ,
                                end     : endPos
                            });
                        }

                    }
                }).disableSelection();
            }, function() {
                // unbind the change event
                //$(this).unbind('click');
            });

            var layoutRowItem = $( RowsPageBoxSelector ).find(".layout-row-container > .sed-layout-row-box");

            var _editRowTitle = function( $el ){
                $el.addClass("editing");
                $el.find(".layout-row-title-edit").focus();
            };

            var _updateRowTitle = function( $el , title ){

                if( !_titleValidation( title ) ){
                    _printAlert( api.I18n.invalid_layout_row_title );
                    return ;
                }

                $el.removeClass("editing");
                $el.find(".row-title-label").text( title );
                var layout = $( RowsPageBoxSelector ).data("layout");
                control.updateRowTitle( layout , $el.data("rowId") , title );
            };

            var _titleValidation = function( title ){
                var pattern = /^[A-Za-z0-9_\-\s]{2,35}$/;
                return pattern.test( title );
            };

            var _printAlert = function ( errortext ) {
                $( RowsPageBoxSelector ).find(".sed-layout-row-error-box p").html( errortext );
                $( RowsPageBoxSelector ).find(".sed-layout-row-error-box p").slideDown( 300 ).delay( 5000 ).fadeOut( 400 );
            };

            layoutRowItem.livequery(function(){

                var $el = $(this);

                $el.find('[data-action="edit"]').on("click" , function(){

                    _editRowTitle( $el ); //$(this).parents(".sed-layout-row-box:first")

                });

                $el.find('.row-title-label').on("click" , function(){

                    _editRowTitle( $el ); //$(this).parents(".sed-layout-row-box:first")

                });

                $el.find(".layout-row-title-edit").on("blur" , function(){

                    _updateRowTitle( $el , $(this).val() );

                });

                $el.find(".layout-row-title-edit").on("keypress" , function(e){

                    if (e.keyCode == 13)
                        _updateRowTitle( $el , $(this).val() );

                });

            });
        },


        changeScopePublicToPrivate: function () {
            var control = this;

            api.Events.trigger( "beforeChangeScopePublicToPrivate" , control.themeId );

            $(".sed-scope-mode-label .scope-mode").text( api.I18n.private_scope );

            var leyouts = this.getLayoutsByThemeId(control.themeId);

            _.each(leyouts, function (leyout) {
                control.removeRowFromModel(leyout);
            });

            control.container.find("ul.select-pubic-scope").addClass("hide");
            /*
             * @Event
             * @Name : sedLayoutChangeScope
             * @args : @type
             */
            api.previewer.send('sedLayoutChangeScope', {
                'type': 'publicToPrivate',
                'elementId': api.currentTargetElementId,
                'themeId': control.themeId
            });

        },

        changeScopePublicTypes: function (type, showConfirm) {
            var control = this;

            showConfirm = ( !_.isUndefined(showConfirm) ) ? showConfirm : true;
            switch (type) {
                case "normal":
                    $(".sed-scope-mode-label .scope-mode").text( api.I18n.public_scope );

                    if (control.lastLayoutPublicType == "customize" && showConfirm === true) {

                        control.confirmDialogEl.dialog("open");

                        control.confirmDialogEl.data("confirmEventId", control.confirmEventIds.customizeToPublic);

                        control.confirmDialogEl.html($("#change-customize-to-public-confirm-tpl").html());

                        return true;
                    }

                    control.container.find("ul.select-layouts-custom").removeClass("hide");

                    switch (control.lastLayoutPublicType) {
                        case "customize":
                            var usingDataMode = $(".select-customize-to-public-data-mode").find('[name="change-customize-to-public-mode"]:checked').val();
                            /*
                             * @Event
                             * @Name : sedLayoutChangeScope
                             * @args : @type
                             */
                            api.previewer.send('sedLayoutChangeScope', {
                                'type': 'customizeToPublic',
                                'elementId': api.currentTargetElementId,
                                'themeId': control.themeId,
                                'usingDataMode': usingDataMode
                            });
                            break;
                        case "hidden":
                            /*
                             * @Event
                             * @Name : sedLayoutChangeScope
                             * @args : @type
                             */
                            api.previewer.send('sedLayoutChangeScope', {
                                'type': 'hiddenToPublic',
                                'elementId': api.currentTargetElementId,
                                'themeId': control.themeId
                            });
                            break;
                    }

                    break;
                case "customize":
                    $(".sed-scope-mode-label .scope-mode").text( api.I18n.customize_scope );

                    control.updateExcludeRows("add");
                    control.container.find("ul.select-layouts-custom").addClass("hide");

                    switch (control.lastLayoutPublicType) {
                        case "normal":
                            /*
                             * @Event
                             * @Name : sedLayoutChangeScope
                             * @args : @type
                             */
                            api.previewer.send('sedLayoutChangeScope', {
                                'type': 'publicToCustomize',
                                'elementId': api.currentTargetElementId,
                                'themeId': control.themeId
                            });
                            break;
                        case "hidden":
                            /*
                             * @Event
                             * @Name : sedLayoutChangeScope
                             * @args : @type
                             */
                            api.previewer.send('sedLayoutChangeScope', {
                                'type': 'hiddenToCustomize',
                                'elementId': api.currentTargetElementId,
                                'themeId': control.themeId
                            });
                            break;
                    }

                    break;
                case "hidden":

                    if( control._get("main_row") )
                        return ;

                    $(".sed-scope-mode-label .scope-mode").text( api.I18n.hidden_scope );

                    if (control.lastLayoutPublicType == "customize" && showConfirm === true) {

                        control.confirmDialogEl.dialog("open");

                        control.confirmDialogEl.data("confirmEventId", control.confirmEventIds.customizeToHidden);

                        control.confirmDialogEl.html($("#change-customize-to-hidden-confirm-tpl").html());

                        return true;
                    }

                    control.updateHiddenRows("add");
                    control.container.find("ul.select-layouts-custom").addClass("hide");

                    switch (control.lastLayoutPublicType) {
                        case "customize":
                            var usingDataMode = $(".select-customize-to-public-data-mode").find('[name="change-customize-to-public-mode"]:checked').val();
                            /*
                             * @Event
                             * @Name : sedLayoutChangeScope
                             * @args : @type
                             */
                            api.previewer.send('sedLayoutChangeScope', {
                                'type': 'customizeToHidden',
                                'elementId': api.currentTargetElementId,
                                'themeId': control.themeId ,
                                'usingDataMode' : usingDataMode
                            });
                            break;
                        case "normal":
                            /*
                             * @Event
                             * @Name : sedLayoutChangeScope
                             * @args : @type
                             */
                            api.previewer.send('sedLayoutChangeScope', {
                                'type': 'publicToHidden',
                                'elementId': api.currentTargetElementId,
                                'themeId': control.themeId
                            });
                            break;
                    }

                    break;
            }


            switch (control.lastLayoutPublicType) {
                case "customize":
                    control.updateExcludeRows("remove");
                    break;
                case "hidden":
                    control.updateHiddenRows("remove");
                    break;
            }

            control.lastLayoutPublicType = type;
        },

        //update radio fields & multi checkboxes field
        updateRadioField: function (element, to) {
            element.filter(function () {
                return this.value === to;
            }).prop('checked', true);
        },

        updateMultiCheckboxField: function (element, to) {
            if (_.isEmpty(to) || !_.isArray(to))
                return;

            element.filter(function () {
                return $.inArray(this.value, to) > -1;
            }).prop('checked', true);

            element.filter(function () {
                return $.inArray(this.value, to) == -1;
            }).prop('checked', false);

        },

        //refresh orders in Layouts row and current page row
        ordersRefresh: function (themeRows, layout) {
            var control = this;

            layout = ( !_.isUndefined(layout) && layout ) ? layout : control.currentLayout;

            if (!_.isEmpty(themeRows)) {

                var models = this.getClone();

                models[layout] = _.map(models[layout], function (options) {

                    if ($.inArray(options.theme_id, _.keys(themeRows)) != -1) {
                        options.order = themeRows[options.theme_id].order;
                    }

                    return options;
                });

                control.refresh( models );

            }

        },

        getMultiCheckboxVal: function (element) {
            var val = [];

            element.filter(":checked").each(function () {
                val.push($(this).val());
            });

            return val;
        },

        //return copy from current value
        getClone : function () {

            return $.extend( true, {} , this.setting() );

        },

        refresh: function ( models ) {

            models = $.extend( true , {} , models );

            var from = this.setting();

            if( !_.isEqual( from , models  ) ){

                this.setting.set( models );

            }

        },

        update: function (themeId) {
            var control = this;

            var layouts = !_.isUndefined(themeId) ? this.getLayoutsByThemeId(themeId) : [],
                publicScopeEl = $(control.selector).find('[name="sed_layout_scope_public"]'),
                layoutPublicTypeEl = $(control.selector).find('[name="sed_layout_public_type"]');

            //update layouts
            var template = api.template( "sed-all-layouts-checkbox-scope" ),
                content = template( { layoutsSettings : api('sed_layouts_settings')() } );

            $(control.selector).find('.select-layouts-custom > li.sub-theme-item').remove();
            $( content ).appendTo( $(control.selector).find('.select-layouts-custom') );

            //reset if disabled in main row
            publicScopeEl.prop("disabled", false);

            $(control.selector).find('.select-layouts-custom > li input[type="checkbox"]').prop("disabled", false);

            $(control.selector).find('[name="sed_layout_public_type"]').filter( function(){
                return $(this).prop("value") == "hidden";
            }).prop("disabled", false);

            if (_.isEmpty(layouts)) {
                $(".sed-scope-mode-label .scope-mode").text( api.I18n.private_scope );

                publicScopeEl.prop("checked", false);
                $(control.selector).find("ul.select-pubic-scope").addClass("hide");
                control.allLayoutCheckedEl.prop("checked", false);
                $(control.selector).find('.select-layouts-custom .edit-layout-rows').addClass("hide");

            } else if ($.inArray(this.currentLayout, layouts) == -1) {
                $(".sed-scope-mode-label .scope-mode").text( api.I18n.private_scope );

                publicScopeEl.prop("checked", false);
                $(control.selector).find("ul.select-pubic-scope").addClass("hide");
                control.allLayoutCheckedEl.prop("checked", false);
                $(control.selector).find('.select-layouts-custom .edit-layout-rows').addClass("hide");

                this.themeId = themeId;
                _.each(layouts, function (leyout) {
                    control.removeRowFromModel(leyout);
                });

            } else {

                this.themeId = themeId;

                //disable layouts and hidden condition , if current module is main row
                if( this._get("main_row") ){

                    publicScopeEl.prop("disabled", true);

                    $(control.selector).find('.select-layouts-custom > li input[type="checkbox"]').prop("disabled", true);

                    $(control.selector).find('[name="sed_layout_public_type"]').filter( function(){
                        return $(this).prop("value") == "hidden";
                    }).prop("disabled", true);

                }

                publicScopeEl.prop("checked", true);
                $(control.selector).find("ul.select-pubic-scope").removeClass("hide");

                this.updateMultiCheckboxField( $(control.selector).find('[name="sed_scope_layout"]') , layouts); //alert( layouts );

                $(control.selector).find('.sub-theme-item input[value="' + control.currentLayout + '"]').prop("disabled", true);

                if (this.isCustomize()) {
                    $(".sed-scope-mode-label .scope-mode").text( api.I18n.customize_scope );
                    $(control.selector).find("ul.select-layouts-custom").addClass("hide");
                    this.updateRadioField(layoutPublicTypeEl, "customize");
                    control.lastLayoutPublicType = "customize";
                } else if (this.isHidden()) {
                    $(".sed-scope-mode-label .scope-mode").text( api.I18n.hidden_scope );
                    $(control.selector).find("ul.select-layouts-custom").addClass("hide");
                    this.updateRadioField(layoutPublicTypeEl, "hidden");
                    control.lastLayoutPublicType = "hidden";
                } else {
                    $(".sed-scope-mode-label .scope-mode").text( api.I18n.public_scope );
                    $(control.selector).find("ul.select-layouts-custom").removeClass("hide");
                    this.updateRadioField(layoutPublicTypeEl, "normal");
                    control.lastLayoutPublicType = "normal";
                }

                $(control.selector).find('.select-layouts-custom input[name="sed_scope_layout"]').each(function () {
                    if ( $(this).prop('checked') ) { //&& $(this).val() != control.currentLayout
                        $(this).parents(".sub-theme-item:first").find(".edit-layout-rows").removeClass("hide");
                    } else {
                        $(this).parents(".sub-theme-item:first").find(".edit-layout-rows").addClass("hide");
                    }
                });

                if ($(control.selector).find('.select-layouts-custom input[name="sed_scope_layout"]').length == layouts.length) {
                    control.allLayoutCheckedEl.prop("checked", true);
                } else {
                    control.allLayoutCheckedEl.prop("checked", false);
                }

            }

        },

        getLayoutsByThemeId: function (themeId) {

            var layouts = [];

            $.each( this.getClone() , function (layout, rows) {
                $.each(rows, function (idx, options) {
                    if (options.theme_id == themeId)
                        layouts.push(layout);
                });
            });

            return layouts;
        },

        isCustomize: function (themeId, layout) {
            var isCustom = false;

            themeId = ( _.isUndefined(themeId) || !themeId ) ? this.themeId : themeId;

            layout = ( _.isUndefined(layout) || !layout ) ? this.currentLayout : layout;

            var models = this.getClone();

            _.each( models[layout], function (options) {
                if (options.theme_id == themeId) {
                    var index = $.inArray(api.currentPageInfo.id, options.exclude);
                    if (index > -1) {
                        isCustom = true;
                        return false;
                    }
                }
            });

            return isCustom;
        },

        /**
         * get row attributes
         * @param attr include : exclude , hidden , order , title , main_row
         * @param themeId
         * @param layout
         * @private
         */
        _get : function( attr , layout , themeId ){

            if( _.isUndefined( attr ) )
                return false;

            themeId = ( _.isUndefined(themeId) || !themeId ) ? this.themeId : themeId;

            layout = ( _.isUndefined(layout) || !layout ) ? this.currentLayout : layout;

            if( attr == "themeId" )
                return themeId;

            var val , models = this.getClone();

            _.each( models[layout], function (options) {
                if ( options.theme_id == themeId && !_.isUndefined( options[attr] ) ) {
                    val = options[attr];
                }
            });

            if( _.isUndefined( val ) )
                return false;

            return val;
        },

        isHidden: function (themeId, layout) {
            var isHide = false;

            themeId = ( _.isUndefined(themeId) || !themeId ) ? this.themeId : themeId;

            layout = ( _.isUndefined(layout) || !layout ) ? this.currentLayout : layout;

            var models = this.getClone();

            _.each( models[layout] , function (options) {
                if (options.theme_id == themeId) {
                    var index = $.inArray(api.currentPageInfo.id, options.hidden);
                    if (index > -1) {
                        isHide = true;
                        return false;
                    }
                }
            });
            return isHide;
        },

        updateRowTitle: function (layout, themeId, title) {

            var control = this;

            var models = this.getClone();

            models[layout] = _.map( models[layout] , function (options) {
                if (options.theme_id == themeId) {
                    options.title = title;
                    return options;
                } else
                    return options;

            });

            control.refresh( models );
        },

        updateExcludeRows: function (type) {
            var control = this;

            var models = this.getClone();

            /**
             * @Using included layout instade current layout
             * Instade only using current layout, we using from all layouts that include this themeId
             */
            var layouts = this.getLayoutsByThemeId( control.themeId );

            _.each( layouts , function( layout ){

                models[layout] = _.map( models[layout] , function (options) {

                    if (options.theme_id == control.themeId) {
                        var index = $.inArray(api.currentPageInfo.id, options.exclude);

                        if (type == "add" && index == -1) {
                            options.exclude.push(api.currentPageInfo.id);
                        }

                        if (type == "remove" && index != -1) {
                            options.exclude.splice(index, 1);
                        }

                        return options;
                    } else
                        return options;

                });

            });

            control.refresh( models );

        },

        updateHiddenRows: function (type) {

            var control = this;

            var models = this.getClone();

            /**
             * @Using included layout instade current layout
             * Instade only using current layout, we using from all layouts that include this themeId
             */
            var layouts = this.getLayoutsByThemeId( control.themeId );

            _.each( layouts , function( layout ){

                models[layout] = _.map(models[layout], function (options) {
                    if (options.theme_id == control.themeId) {
                        var index = $.inArray(api.currentPageInfo.id, options.hidden);

                        if (type == "add" && index == -1) {
                            options.hidden.push(api.currentPageInfo.id);
                        }

                        if (type == "remove" && index != -1) {
                            options.hidden.splice(index, 1);
                        }

                        return options;
                    } else
                        return options;

                });

            });

            control.refresh( models );

        },

        generateThemeId: function () {

            this.lastThemeId += 1;

            api('sed_last_theme_id').set(this.lastThemeId);

            return "theme_id_" + this.lastThemeId;

        },

        removeRowFromModel: function (leyout) {

            var control = this;

            var models = this.getClone();

            models[leyout] = _.filter(models[leyout], function (row) {
                return row.theme_id != control.themeId;
            });

            control.refresh( models );

        },

        /*removeRowFromAllLayouts: function (themeId) {

            var control = this;

            _.each(this.models, function (rows, leyout) {
                control.models[leyout] = _.filter(control.models[leyout], function (row) {
                    return row.theme_id != themeId;
                });
            });

            control.refresh(); console.log( "----------this.models------------" , this.models );

        },*/

        existThemeIdInLayout: function (leyout) {
            var control = this;

            var models = this.getClone();

            if (_.isUndefined(models[leyout])) {
                return false;
            }

            var exist = false;

            _.each(models[leyout], function (layoutModel) {
                if (layoutModel.theme_id == control.themeId) {
                    exist = true;
                    return false;
                }
            });

            return exist;
        },

        addRowToModel: function (leyout , order) {

            if( this._get("main_row") )
                return;

            var control = this,
                options = {
                    order: order || 0,
                    theme_id: this.themeId,
                    exclude: [], // this row not show in pages with this ids
                    hidden: [],
                    title: ""
                };

            var models = this.getClone();

            if (_.isUndefined(models[leyout]))
                models[leyout] = [];

            models[leyout].push(options);

            control.refresh( models );

        }
    });

    api.controlConstructor = $.extend( api.controlConstructor, {
        layout_scope    : api.LayoutScopeControl
    });


    $( function() {


    });

})( sedApp, jQuery );