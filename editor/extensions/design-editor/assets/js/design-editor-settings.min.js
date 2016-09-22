/**
 * @plugin.js
 * @App Design Editor Settings Manager Plugin JS
 *
 * @License: http://www.siteeditor.org/license
 * @Contributing: http://www.siteeditor.org/contributing
 */

/*global siteEditor:true */
(function( exports, $ ){
    var api = sedApp.editor;

    api.designEditorTpls = api.designEditorTpls || {};

    api.fn.getStyle = function (el, styleProp , pseudo) {
        if( _.isUndefined( el ) || $(el).length == 0 )
            return false;

        var value, defaultView = (el.ownerDocument || document).defaultView;
        // W3C standard way:
        if (defaultView && defaultView.getComputedStyle) {
            // sanitize property name to css notation
            // (hypen separated words eg. font-Size)
            pseudo = ( !_.isUndefined( pseudo ) && pseudo ) ? pseudo : null;
            styleProp = styleProp.replace(/([A-Z])/g, "-$1").toLowerCase();
            return defaultView.getComputedStyle(el, pseudo).getPropertyValue(styleProp);
        } else if (el.currentStyle) { // IE
            // sanitize property name to camelCase
            styleProp = styleProp.replace(/\-(\w)/g, function(str, letter) {
                return letter.toUpperCase();
            });
            value = el.currentStyle[styleProp];
            // convert other units to pixels on IE
            if (/^\d+(em|pt|%|ex)?$/i.test(value)) {
                return (function(value) {
                    var oldLeft = el.style.left, oldRsLeft = el.runtimeStyle.left;
                    el.runtimeStyle.left = el.currentStyle.left;
                    el.style.left = value || 0;
                    value = el.style.pixelLeft + "px";
                    el.style.left = oldLeft;
                    el.runtimeStyle.left = oldRsLeft;
                    return value;
                })(value);
            }
            return value;
        }
    };

    api.AppStyleEditorSettings = api.Class.extend({
        initialize: function( options ){

            $.extend( this, options || {} );

            this.panelsContents = {};
            this.stylesContents = {};
            this.accordionsInit = {};
            this.neededUpdateSuppotSelector = false;
            this.currentSettingsId = "";
            this.currentStyleId = "";
            this.currentSelector = false;
            this.defaultValues = {};

            /**
             * Css Setting Type include "page" , "mosule" , "site"
             * @type {string}
             */
            this.settingType = "";
            //this.borderSidesLoaded = [];
            //this.updateStyleNeeded = false;

            this.ready();
        },


        ready : function(){
            var self = this;

            //when change skin current element updated
            api.previewer.bind( 'changeCurrentElementBySkinChange', function( dataEl ) {
                self.currentSelector = "";
            });

            $( "#sed-dialog-settings" ).find(".sed_style_editor_btn").livequery(function(){
                if( _.isUndefined( api.sedDialogSettings.dialogsContents[api.sedDialogSettings.currentSettingsId] ) ){

                    $(this).click(function(){

                        self.openPanelSettings( $(this).data("optionGroup") , $(this).data("settingType") );

                        if( !_.isUndefined( self.accordionsInit[self.currentSettingsId] ) && self.accordionsInit[self.currentSettingsId] === true )
                            self.currentSuppotSelector();
                        else
                            self.neededUpdateSuppotSelector = true;

                    });

                }
            });

            /*$( "#sed-dialog-settings" ).find(".sed-border-side-panel-header").livequery(function(){
             if( _.isUndefined( self.panelsContents[self.currentSettingsId] ) ){
             $(this).click(function(){

             if( self.currentStyleId == "border" && self.updateStyleNeeded === true )
             self.initSettings( "border" , self.currentSelector );

             });
             }
             });*/

            $( "#sed-dialog-settings" ).find(".sted_element_control_btn").livequery(function(){
                if( _.isUndefined( self.panelsContents[self.currentSettingsId] ) ){
                    $(this).click(function(){
                        $( "#sed-dialog-settings" ).data('sed.multiLevelBoxPlugin')._pageBoxNext( this );

                        self.openStyleSettings( $(this).data("styleId") , $(this).data("dialogTitle") , $(this).data("selector") , $(this).data("optionGroup") );

                    });
                }
            });

            //call directly design option panel (from context menu)
            api.Events.bind("editStyleSettingsType" , function(dataElement , extra){

                $( "#sed-dialog-settings" ).data('sed.multiLevelBoxPlugin')._callDirectlyLevelBox( "dialog_page_box_" + dataElement.shortcodeName + "_design_panel" );

                api.appModulesSettings.forceUpdate = true;

                self.openPanelSettings( dataElement.shortcodeName , "module" );

                if( !_.isUndefined( self.accordionsInit[self.currentSettingsId] ) && self.accordionsInit[self.currentSettingsId] === true )
                    self.currentSuppotSelector();
                else
                    self.neededUpdateSuppotSelector = true;
            });


            api.Events.bind( "beforeResetSettingsTmpl" , function( settingsId ){

                if( !_.isUndefined( self.currentSettingsId ) && self.currentSettingsId && self.dialogBoxContainer.children().length > 0 ){
                    self.panelsContents[self.currentSettingsId] = self.dialogBoxContainer.children().detach();
                    self.currentSettingsId = "";
                }

                if( !_.isUndefined( self.currentStyleId ) && self.currentStyleId && self.styleBoxContainer.children().length > 0 ){
                    self.stylesContents[self.currentStyleId] = self.styleBoxContainer.children().detach();
                    self.currentStyleId = "";
                    self.currentSelector = false;
                    //self.updateStyleNeeded = false;
                    //self.borderSidesLoaded = [];
                }

            });

            api.Events.bind( "accordionPanelSettingsInit" , function( event, ui , $element ){

                if( $element.parents(".sed-app-settings-normal:first").parent().hasClass("sed_style_editor_panel_container") ){
                    self.accordionsInit[self.currentSettingsId] = true;

                    if( self.neededUpdateSuppotSelector === true ){
                        self.currentSuppotSelector();
                        self.neededUpdateSuppotSelector = false;
                    }
                }

            });

        },

        openPanelSettings : function( optionGroup , settingType ){

            this.settingType = settingType;

            var panelTpl = api.designEditorTpls[optionGroup];

            this.dialogBoxContainer = $("#dialog_page_box_" + optionGroup + "_design_panel").find(".sed_style_editor_panel_container:first");

            if( this.currentSettingsId == api.sedDialogSettings.currentSettingsId ){
                return ;
            }else{
                this.currentSettingsId = _.clone( api.sedDialogSettings.currentSettingsId );
            }

            if( !_.isUndefined( this.panelsContents[this.currentSettingsId] ) ){
                this.panelsContents[this.currentSettingsId].appendTo( this.dialogBoxContainer );
            }else{

                $( panelTpl ).appendTo( this.dialogBoxContainer );

                delete api.designEditorTpls[optionGroup];

            }

        },

        currentSuppotSelector : function(){

            var self = this;

            $( "#sed-dialog-settings" ).find( ".accordion-panel-settings .design_ac_header" ).each(function(){

                var selector = $(this).data("selector"),
                    selectorT = "" ,
                    index;

                if( self.settingType == "module" ){
                    selectorT = ( selector != "sed_current" ) ? '[sed_model_id="' + api.currentTargetElementId + '"] ' + selector : '[sed_model_id="' + api.currentTargetElementId + '"]';
                }else{
                    selectorT = selector;
                }

                index = selectorT.indexOf(":");

                if(index > -1){
                    var pseudo = selectorT.substring( index );
                    selectorT = selectorT.substring( 0 , index );
                }

                var $el = $("#website")[0].contentWindow.jQuery( selectorT );

                if( $el.length > 0 ){
                    $(this).show();
                    $(this).next().hide();
                }else{
                    $(this).hide();
                    $(this).next().hide();
                }

            });

            $("#sed-dialog-settings .sed_style_editor_panel_container").find( ".accordion-panel-settings" ).each(function(){

                var num = $( this ).find( ".ui-accordion-header.design_ac_header:visible:first" ).index();

                if( !_.isUndefined( num )  && num > -1){
                    var active = $( this ).accordion("option" , "active");

                    if( active !== false){
                        if( !$( this ).find( ".design_ac_header" ).eq(active).is(":visible") ){
                            //$( "#sed-dialog-settings" ).find( ".accordion-panel-settings .design_ac_header" ).eq(num).next().show();
                            $( this ).accordion("option" , "active" , num/2);
                        }else
                            $( this ).find( ".design_ac_header" ).eq(active).next().show();
                    }
                }

            });

        },

        /*
         @styleId :  like border , font , background , ....
         */
        openStyleSettings : function( styleId , dialogTitle , selector , optionGroup ){

            var self = this ,
                panelTpl = $("#group_settings_" + styleId + "_tmpl" ) ,
                lvlBox = "modules_styles_settings_"+ optionGroup +"_design_group_level_box";

            this.styleBoxContainer = $("#" + lvlBox ).find(".styles_settings_container:first");

            if( this.currentStyleId == styleId ){

                if( !_.isUndefined( self.currentSelector ) && self.currentSelector !== false && self.currentSelector != selector ){
                    self.currentSelector = selector;
                    self.initSettings( styleId , selector );
                }//else
                //self.updateStyleNeeded = false

                return ;
            }else{

                if( !_.isUndefined( self.currentStyleId ) && self.currentStyleId && self.styleBoxContainer.children().length > 0 )
                    self.stylesContents[self.currentStyleId] = self.styleBoxContainer.children().detach();

                this.currentStyleId = styleId;
                this.currentSelector = selector;
            }

            $( "#sed-dialog-settings" ).siblings(".ui-dialog-titlebar:first").find('[data-self-level-box="'+ lvlBox +'"] >.ui-dialog-title').text( dialogTitle );

            if( !_.isUndefined( this.stylesContents[this.currentStyleId] ) ){
                this.stylesContents[this.currentStyleId].appendTo( this.styleBoxContainer );
            }else{
                $( panelTpl.html() ).appendTo( this.styleBoxContainer );
            }

            self.initSettings( styleId , selector );

        },

        initSettings : function( styleId , selector ){
            var self = this;

            //this.updateStyleNeeded = true;

            _.each( api.stylesSettingsControls[styleId] , function( data ) {
                self.updateSettings( data.control_id , data , selector );
            });

            api.Events.trigger(  "after_group_settings_update" , styleId );
        },

        //TODO : recover this feature in next versions
        /*getStyleValue : function( selector , styleProp ){
         var selectorT = ( selector != "sed_current" ) ? '[sed_model_id="' + api.currentTargetElementId + '"] ' + selector : '[sed_model_id="' + api.currentTargetElementId + '"]' ,
         index = selectorT.indexOf(":") ,
         pseudo;

         if(index > -1){
         pseudo = selectorT.substring( index );
         selectorT = selectorT.substring( 0 , index );
         }

         var $el = $("#website")[0].contentWindow.jQuery( selectorT ) ,
         el = $el[0];

         switch ( styleProp ) {
         case "image-scaling":
         var bgSize = api.fn.getStyle( el , "background-size" , pseudo ),
         bgRepeat = api.fn.getStyle( el , "background-repeat" , pseudo ) ,
         imageScaling = "";

         if( bgSize == "100% 100%" ){
         imageScaling = "fullscreen";
         }else if( bgSize == "cover" ){
         imageScaling = "cover";
         }else if( bgSize == "100% auto" && bgRepeat == "repeat-y" ){
         imageScaling = "fit";
         }else if( bgSize == "auto auto" && bgRepeat == "repeat" ){
         imageScaling = "tile";
         }else if( bgSize == "auto auto" && bgRepeat == "repeat-x" ){
         imageScaling = "tile-horizontally";
         }else if( bgSize == "auto auto" && bgRepeat == "repeat-y" ){
         imageScaling = "tile-vertically";
         }else if( bgSize == "auto auto" && bgRepeat == "no-repeat" ){
         imageScaling = "normal";
         }

         return imageScaling;
         break;
         case "background-image":
         var image = api.fn.getStyle( el , "background-image" , pseudo ) ,
         patt  = /^url\(.+?\);/g ;

         image = $.trim(image);

         if( !patt.test(image) )
         image = "none";

         return image;
         break;
         case "background-position":

         switch ( api.fn.getStyle( el , "background-position" , pseudo ) ) {
         case "0% 0%":
         return "left top";
         break;
         case "0% 50%":
         return "left center";
         break;
         case "0% 100%":
         return "left bottom";
         break;
         case "50% 0%":
         return "center top";
         break;
         case "50% 50%":
         return "center center";
         break;
         case "50% 100%":
         return "center bottom";
         break;
         case "100% 0%":
         return "right top";
         break;
         case "100% 50%":
         return "right center";
         break;
         case "100% 100%":
         return "right bottom";
         break;
         default:
         return "";
         }

         break;
         /*case "box-shadow-color":
         api.fn.getStyle( el , "box-shadow" );
         return "";
         break;
         case "box-shadow":
         return "";
         break;
         case "text-shadow-color":
         return "";
         break;
         case "text-shadow":
         alert( api.fn.getStyle( el , styleProp ) );
         return "";
         break;*//*
         default:

         var propsWithPx = [
         "border-top-width" ,
         "border-right-width" ,
         "border-bottom-width" ,
         "border-left-width" ,
         "padding-top",
         "padding-right",
         "padding-bottom",
         "padding-left",
         "margin-top",
         "margin-right",
         "margin-bottom",
         "margin-left",
         "border-top-left-radius" ,
         "border-top-right-radius" ,
         "border-bottom-left-radius" ,
         "border-bottom-right-radius" ,
         "font-size" ,
         "line-height"
         ];

         if( $.inArray( styleProp , propsWithPx ) > -1 ){
         return parseInt( api.fn.getStyle( el , styleProp , pseudo ) );
         }

         if( this.currentStyleId == "trancparency" ){
         return api.fn.getStyle( el , styleProp , pseudo ) * 100 ;
         }

         return api.fn.getStyle( el , styleProp , pseudo );
         }
         },*/

        getDefaultValue : function( data , selector , selectorT ){
            var defaultValue = null ,
                settingId = data.settings["default"];

            if( !_.isUndefined( data.default_value ) && !_.isNull( data.default_value ) ){

                if( _.isUndefined( this.defaultValues[settingId] ) )
                    this.defaultValues[settingId] =  {};

                this.defaultValues[settingId][selectorT] = _.clone( data.default_value );

                return data.default_value;
            }

            if( !_.isUndefined( this.defaultValues[settingId] ) && !_.isUndefined( this.defaultValues[settingId][selectorT] ) ){

                defaultValue = _.clone( this.defaultValues[settingId][selectorT] );
            }/*else if( $.inArray( settingId , ["external_background_image" , "margin_lock" , "padding_lock" , ... ]  ) == -1 ){
             defaultValue = this.getStyleValue( selector , data.style_props );

             if( _.isUndefined( this.defaultValues[settingId] ) )
             this.defaultValues[settingId] =  {};

             this.defaultValues[settingId][selectorT] = _.clone( defaultValue );
             }*/

            return defaultValue;

        },

        /**
         * Get Current Value For a "style-editor" Control
         *
         * @param id
         * @param data
         * @param selector
         * @param settingType
         * @returns {*}
         */
        getCurrentValue : function( id , data , selector , settingType ){
            var selectorT = "";

            if( settingType == "module" ){
                selectorT = ( selector && selector != "sed_current" ) ? '[sed_model_id="' + api.currentTargetElementId + '"] ' + selector : '[sed_model_id="' + api.currentTargetElementId + '"]';
            }else{
                selectorT = selector;
            }

            var cssSettingType = _.isUndefined( data.css_setting_type ) ? self.settingType : data.css_setting_type,
                extra  = ( cssSettingType == "module" ) ? $.extend({} , api.appModulesSettings.sedDialog.extra || {}) : {} ,
                cValue = null;

            if( !_.isUndefined( api.currenStyleEditorContolsValues[selectorT] ) && !_.isUndefined( api.currenStyleEditorContolsValues[selectorT][data.settings["default"]] ) ){
                cValue = api.currenStyleEditorContolsValues[selectorT][data.settings["default"]];
            }else{

                var sed_css;

                if( cssSettingType == "module" ) {

                    if( !_.isUndefined( extra.attrs ) && !_.isUndefined( extra.attrs.sed_css ) ){
                        sed_css = extra.attrs.sed_css;
                    }

                }else if( cssSettingType == "page" ) {

                    var settingId;

                    if (api.settings.page.type == "post") {

                        settingId = "postmeta[" + api.settings.currentPostType + "][" + api.settings.page.id + "][page_custom_design_settings]";

                    } else {

                        settingId = "sed_" + api.settings.page.id + "_settings[page_custom_design_settings]";

                    }

                    sed_css = api(settingId).get();

                }else if( cssSettingType == "site" ){

                    sed_css = api( 'site_custom_design_settings' ).get();

                }

                if( !_.isUndefined( sed_css ) && !_.isUndefined( sed_css[selectorT] ) && !_.isUndefined( data.settings ) && !_.isUndefined( data.settings["default"] ) && !_.isUndefined( sed_css[selectorT][data.settings["default"]] ) ){
                    cValue = sed_css[selectorT][data.settings["default"]];
                }else if( !_.isUndefined( data.style_props ) && !_.isEmpty( data.settings["default"] ) ){ //&& data.settings["default"] != "external_background_image"
                    cValue = this.getDefaultValue( data , selector , selectorT );
                }

            }

            return !_.isObject( cValue ) ? _.clone( cValue ) : cValue;

        },

        updateSettings: function(  id , data , selector  ){

            var control = api.control.instance( id );

            if( $.inArray( id , _.keys( api.settings.controls ) ) == -1 || ! control ){  //&& $el.length > 0

                data.selector = selector;

                data.css_setting_type = _.clone( this.settingType );

                api.settings.controls[id] = data;

                api.Events.trigger( "renderSettingsControls" , id, data );

                control = api.control.instance( id );

                $( control.container ).parents(".row_settings:first").show();

            } else {

                control.cssSettingType = _.clone( this.settingType );

                control.cssSelector = selector;

                $( control.container ).parents(".row_settings:first").show();

                control.update( );

            }

        }


    });


    $( function() {

        api.appStyleEditorSettings  = new api.AppStyleEditorSettings({});

    });
})( sedApp, jQuery );