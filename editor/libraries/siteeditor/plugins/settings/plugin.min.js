(function( exports, $ ){

    var api = sedApp.editor;

    api.currentCssSelector = api.currentCssSelector || "";
    //handels of all loaded scripts in siteeditor app
    api.sedAppLoadedScripts = api.sedAppLoadedScripts || [];

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


    api.AppModulesSettings = api.Class.extend({
        initialize: function( options ){

            $.extend( this, options || {} );

            this.initControls = [];
            this.currentDialogSelector = "none";
            this.dialogsContents = {};
            this.dialogsTitles = {};
            this.currentSettingsId;
            this.sedDialog;
            //in this version only using in design panel(for back btn and update settings)
            this.forceUpdate = false;
            this.rowContainerSettingsData = {};
            //this.lastSedDialog;
            this.panelsNeedToUpdate = [];
            //this.lastPanelsNeedToUpdate = [];

            this.ready();
        },

        ready : function(){
            var self = this;

            this.initDialogSettings();

            //when render open settings dialog
            api.previewer.bind( 'openDialogSettings' , function( data ) {
                self.openInitDialogSettings( data );
            });

            //when render open settings dialog
            api.previewer.bind( 'rowContainerSettingData' , function( data ) {
                self.rowContainerSettingsData = data;
            });


            //when render select one module or click on settings icon left side modules
            api.previewer.bind( 'currentModuleSelected' , function( dataElement ) {
                self.moduleSelect( dataElement );
            });

            //when change skin current element updated
            api.previewer.bind( 'changeCurrentElementBySkinChange', function( dataEl ) {
                self.updateByChangePattern( dataEl );
            });

            api.previewer.bind( 'shortcodeControlsUpdate' , function( data  ) {
                self.shortcodeControlsUpdate( data );
            });

            api.previewer.bind( "currentElementId" , function( id ) {
                api.currentTargetElementId = id;
            });

            api.previewer.bind( 'currentPostId' , function( id ) {
                api.currentPostId = id;
            });

            this.readySettingsType();

        },

        readySettingsType : function(){

            api.Events.bind("animationSettingsType" , function(dataElement , extra){  //alert( dataElement.shortcodeName );

                $( "#sed-dialog-settings" ).data('sed.multiLevelBoxPlugin')._callDirectlyLevelBox( "dialog_page_box_" + dataElement.shortcodeName + "_animation"  );

            });

            api.Events.bind("changeSkinSettingsType" , function(dataElement , extra){

                $( "#sed-dialog-settings" ).data('sed.multiLevelBoxPlugin')._callDirectlyLevelBox( "dialog_page_box_" + dataElement.shortcodeName + "_skin" );

                api.Events.trigger( "loadSkinsDirectly" , dataElement.moduleName );

            });

            api.Events.bind("linkToSettingsType" , function(dataElement , extra){

                $( "#sed-dialog-settings" ).data('sed.multiLevelBoxPlugin')._callDirectlyLevelBox( dataElement.shortcodeName + "_link_to_panel_level_box" );

            });

        },

        switchTmpl : function( reset ){
            var self = this ,
                selector = "#sed-dialog-settings";

            reset = !_.isUndefined( reset ) ? reset : true;

            if( !_.isUndefined( self.dialogsContents[self.currentSettingsId] ) ){

                var $currentElDialog = self.dialogsContents[self.currentSettingsId].appendTo( $( selector ) );
                self.dialogsTitles[self.currentSettingsId].appendTo( $( selector ).siblings(".ui-dialog-titlebar:first") );

                api.Events.trigger( "afterAppendModulesSettingsTmpl" , this , $currentElDialog );

                if( reset === true )
                    $( selector ).data('sed.multiLevelBoxPlugin')._reset();

            }else{

                var $currentElDialog = $( $("#sed-tmpl-dialog-settings-" + self.currentSettingsId ).html() ).appendTo( $( selector ) );

                api.Events.trigger( "afterInitAppendModulesSettingsTmpl" , this , $currentElDialog );

                $( selector ).data('sed.multiLevelBoxPlugin').options.innerContainer = $( selector ).find(".dialog-level-box-settings-container");
                $( selector ).data('sed.multiLevelBoxPlugin')._render();

                if( self.sedDialog.data.shortcodeName == "sed_row" ){
                    var html = '<span id="row_back_settings_element" class="icon-close-level-box"><i class="icon-chevron-left"></i></span>';
                    $(selector).siblings(".ui-dialog-titlebar:first").find("[data-self-level-box='dialog-level-box-settings-sed_row-container']").prepend( $(html) );
                }


            }
        },

        resetTmpl : function(){
            var self = this ,
                selector = "#sed-dialog-settings";

            api.Events.trigger( "beforeResetDialogSettingsTmpl" , self.currentSettingsId );

            self.dialogsTitles[self.currentSettingsId] = $( selector ).siblings(".ui-dialog-titlebar:first").children(".multi-level-box-title").detach();
            self.dialogsContents[self.currentSettingsId] = $( selector ).children().detach();

            this.forceUpdate = false;

            //for skins support :: when dialog close or switch remove this event
            if( !_.isUndefined( this._skinSupport ) && _.isFunction( this._skinSupport ) ){
                api.Events.unbind( "skins_loaded_" + this.sedDialog.data.shortcodeName + "_skin" , this._skinSupport );
                delete this._skinSupport;
            }
        },

        initDialogSettings : function(){
            var self = this ,
                selector = "#sed-dialog-settings";

            api.Events.bind( "beforerCreateSettingsControls" , function(id, data , extra){
                if( !_.isUndefined( data ) && !_.isUndefined( data.is_image_size ) ){
                    var $field_id = 'sed_pb_' + id;

                    var optionsStr = "" ;

                    if( !_.isUndefined( data.has_custom_size ) && data.has_custom_size ){
                        optionsStr += '<option value="" > ' + api.I18n.custom_size + ' </option>'
                    }

                    _.each( api.addOnSettings.imageModule.sizes  , function( size , key ){
                        var sizeWidth = !_.isUndefined( size.width ) ? size.width : "" ,
                            sizeHeight = !_.isUndefined( size.height ) ? size.height : "" ;
                        optionsStr += '<option value="' + key + '" > ' + size.label + ' - ' + sizeWidth + " x " + sizeHeight + ' </option>';
                    });

                    $( "#" + $field_id ).html( optionsStr );
                }
            });

            $( selector ).find(".go-panel-element").livequery(function(){
                if( _.isUndefined( self.dialogsContents[self.currentSettingsId] ) ){
                    $(this).click(function(){ //go-accordion-panel
                        var panelId = $(this).data("panelId");
                        if( $.inArray( panelId , self.panelsNeedToUpdate) == -1 ){
                            self.initSettings( panelId );
                            self.panelsNeedToUpdate.push( panelId );
                        }
                    });
                }
            });

            $( selector ).find(".go-row-container-settings").livequery(function(){
                if( _.isUndefined( self.dialogsContents[self.currentSettingsId] ) ){
                    $(this).click(function(){ //go-accordion-panel
                        //self.lastSedDialog = self.sedDialog;
                        self.lastTargetElementId = _.clone( api.currentTargetElementId );
                        //self.lastPanelsNeedToUpdate = self.panelsNeedToUpdate;
                        api.currentTargetElementId = self.rowContainerSettingsData.rowId;
                        api.previewer.send('current_element' , api.currentTargetElementId  );
                        self.openInitDialogSettings( self.rowContainerSettingsData );
                    });
                }
            });

            $("#row_back_settings_element").livequery(function(){
                if( _.isUndefined( self.dialogsContents[self.currentSettingsId] ) ){
                    $(this).click(function(){ //go-accordion-panel
                        api.currentTargetElementId = self.lastTargetElementId;
                        //api.previewer.send('current_element' , api.currentTargetElementId  );
                        api.previewer.send( 'go_back_to_main_module' , api.currentTargetElementId  );
                        //self.panelsNeedToUpdate = self.lastPanelsNeedToUpdate;
                        //self.openInitDialogSettings( self.lastSedDialog , false );
                    });
                }
            });

            //for update after click on back btn
            $( "#sed-dialog-settings" ).siblings(".ui-dialog-titlebar:first").find('[data-self-level-box] >.icon-close-level-box').livequery(function(){
                if( _.isUndefined( self.dialogsContents[self.currentSettingsId] ) ){
                    $(this).click(function(){
                        if( !_.isUndefined( self.sedDialog ) && $(this).parent().data("selfLevelBox") == 'dialog_page_box_'+ self.sedDialog.data.shortcodeName +'_design_panel' && self.forceUpdate === true ){
                            self.initSettings();
                            self.forceUpdate = false;
                        }
                    });
                }
            });

            $( ".accordion-panel-settings" ).livequery(function(){
                if( _.isUndefined( $(this).data( "acInit" ) ) ||  $(this).data( "acInit" ) !== true ){
                    $(this).accordion({
                        active: 0,
                        collapsible: true,
                        event: 'click',
                        heightStyle: 'content',
                        create : function( event, ui ) {
                            $(this).data("acInit" , true);
                            if( $(this).parent().hasClass("sed_style_editor_panel_container") ){
                                api.appStyleEditorSettings.accordionsInit[api.appStyleEditorSettings.currentSettingsId] = true;

                                if( api.appStyleEditorSettings.neededUpdateSuppotSelector === true ){
                                    api.appStyleEditorSettings.currentSuppotSelector();
                                    api.appStyleEditorSettings.neededUpdateSuppotSelector = false;
                                }
                            }
                        }
                    });
                }
            });

            $( selector ).dialog({
                "autoOpen"  : false,
                "modal"     : false,
                //draggable: false,
                resizable: false,
                "width"     : 295,
                "height"    : 600 ,
                "position"  : {
                    "my"    : "right-20",
                    "at"    : "right" ,
                    "of"    : "#sed-site-preview"
                },
                open: function () {
                    self.switchTmpl();
                },
                close : function(){
                    api.previewer.send("isOpenDialogSettings" , false);
                    self.resetTmpl();
                }
            });

            self.initDialogMultiLevelBox( selector );
            self.initDialogScrollBar( selector );


            api.previewer.bind( 'dialogSettingsClose' , function( ) {

                var isOpen = $( selector ).dialog( "isOpen" );
                if( isOpen )
                    $( selector ).dialog( "close" );

            });

        },

        initDialogMultiLevelBox : function( dialogSelector ){

            $( dialogSelector ).multiLevelBoxPlugin({
                titleBar: $( dialogSelector ).siblings(".ui-dialog-titlebar:first"),
                innerContainer : $( dialogSelector ).find(".dialog-level-box-settings-container"),
            });
            $( dialogSelector ).siblings(".ui-dialog-titlebar:first").find(".close-page-box").livequery(function(){
                $(this).click(function(e){
                  $( dialogSelector ).dialog( "close" );
                });
            });

        },

        initDialogScrollBar : function( dialogSelector ){
            var self = this;

            $( dialogSelector ).find('[data-multi-level-box="true"]').livequery(function(){
                if( _.isUndefined( self.dialogsContents[self.currentSettingsId] ) ){
                    $(this).mCustomScrollbar({
                        //autoHideScrollbar:true ,
                        advanced:{
                            updateOnBrowserResize:true, /*update scrollbars on browser resize (for layouts based on percentages): boolean*/
                            updateOnContentResize:true,
                        },
                      scrollButtons:{
                        enable:true
                      },
                      callbacks:{
                          onOverflowY:function(){
                             $(this).find(".mCSB_container").addClass("mCSB_ctn_margin");
                          },
                          onTotalScrollOffset:120,
                          onOverflowYNone:function(){
                            $(this).find(".mCSB_container").removeClass("mCSB_ctn_margin");
                          }
                      }
                    });
                }
            });

        },

        updateSettings: function(  id , data , shortcodeName , extra , needReturn ){

            if( $.inArray(id , this.initControls) == -1 ){

                if( !_.isUndefined( data.category ) && data.category == "style-editor" ){
                    var cssSelector = !_.isUndefined( data.selector ) ? data.selector : '';
                    var sValue = api.appStyleEditorSettings.getCurrentValue( id , data , cssSelector );
                    if( !_.isNull( sValue ) ){
                        data.default_value = _.clone( sValue );
                    }

                }

                api.Events.trigger( "renderSettingsControls" , id, data , extra);
                this.initControls.push( id );

                var control = api.control.instance( id );
                $( control.container ).parents(".row_settings:first").show();
                this.controlFilter( extra.attrs , shortcodeName , control );

            }else if( $.inArray(id , this.initControls) != -1 ){

                /*if( data.is_attr === false )
                    return ;*/

                var control = api.control.instance( id );
                $( control.container ).parents(".row_settings:first").show();
                this.controlFilter( extra.attrs , shortcodeName , control );

                if( control.isStyleControl ){

                    var sValue = api.appStyleEditorSettings.getCurrentValue( control.id , data , control.cssSelector );
                    if( !_.isNull( sValue ) ){
                        var cValue = _.clone( sValue );
                        control.update( cValue );
                    }else{
                        control.update( );
                    }

                }else if( !_.isUndefined(extra.attrs) )
                    control.update(extra.attrs);
                else
                    control.update();

            }

            if( !_.isUndefined(needReturn) && needReturn === true )
                return control;

        },

        controlFilter : function( attrs , shortcodeName , control ){

            $("#sed-dialog-settings").find("fieldset").show();

            if( !_.isUndefined( attrs) && !_.isUndefined( attrs.parent_module ) && !_.isUndefined( attrs.sed_support_id ) ){

                var $support = api.settingsSupports[ attrs.parent_module ];

                if( !_.isUndefined( $support ) && !_.isUndefined( $support.subShortcode ) && !_.isUndefined( $support.subShortcode[attrs.sed_support_id] )  && !_.isUndefined( $support.subShortcode[attrs.sed_support_id].settings )  ){
                    var settings = $support.subShortcode[attrs.sed_support_id].settings ,
                        type = ( !_.isUndefined( settings.type ) ) ? settings.type.toLowerCase() : "include";

                    if( !$.isArray(settings.fields) || settings.fields.length == 0 )
                        return ;

                    if( ($.inArray(control.attr , settings.fields ) == -1 && type == "exclude") ||
                        ($.inArray(control.attr , settings.fields ) != -1 && type == "include")  )
                        $(control.selector).parents(".row_settings:first").show();
                    else
                        $(control.selector).parents(".row_settings:first").hide();

                    $("#sed-dialog-settings").find("fieldset").each(function(){
                        var $i = 0;
                        $(this).find(".row_settings").each(function(){
                            if($(this).is(":visible"))
                                $i++;
                        });
                        if($i == 0)
                            $(this).hide();
                        else
                            $(this).show();
                    });

                }
            }

        },

        filterSettings : function( attrs , shortcodeName ){

            var _showHideGSettings = function( type ){
                _.each( api.modulesGeneralSettings , function( field ){

                    var settingEl = $( "#sed-app-control-" + shortcodeName + "_" + field );
                    if( settingEl.length > 0 ){
                        if( _.isUndefined( type ) || type == "show" )
                            settingEl.parents(".row_settings:first").show();
                        else
                            settingEl.parents(".row_settings:first").hide();
                    }
                });
            };

            _showHideGSettings();

            var _showHideGPanels = function( type ){
                _.each( api.settingsPanels[shortcodeName] , function( panel ){
                    var panelBtn = $( "#sed_pb_" + shortcodeName + "_" + panel.id );
                    if( panelBtn.length > 0 ){
                        if( _.isUndefined( type ) || type == "show" )
                            panelBtn.parents(".row_settings:first").show();
                        else
                            panelBtn.parents(".row_settings:first").hide();
                    }

                });
            };

            _showHideGPanels();

            if( !_.isUndefined( attrs) && !_.isUndefined( attrs.parent_module ) && !_.isUndefined( attrs.sed_support_id ) ){

                var $support = api.settingsSupports[ attrs.parent_module ];

                if( !_.isUndefined( $support ) && !_.isUndefined( $support.subShortcode ) && !_.isUndefined( $support.subShortcode[attrs.sed_support_id] ) ){

                    if( !_.isUndefined( $support.subShortcode[attrs.sed_support_id].general_settings )  ){
                        var general_settings = $support.subShortcode[attrs.sed_support_id].general_settings ,
                            type = ( !_.isUndefined( general_settings.type ) ) ? general_settings.type.toLowerCase() : "include";

                        if( !$.isArray(general_settings.fields) || general_settings.fields.length == 0 )
                            return ;

                        if( type == "include" )
                            _showHideGSettings("hide");

                        _.each( general_settings.fields , function( field ){

                            var settingEl = $( "#sed-app-control-" + shortcodeName + "_" + field );

                            if( settingEl.length > 0 && type == "include"  )
                                settingEl.parents(".row_settings:first").show();
                            else if( settingEl.length > 0 && type == "exclude"  )
                                settingEl.parents(".row_settings:first").hide();
                        });
                    }

                    if( !_.isUndefined( $support.subShortcode[attrs.sed_support_id].panels )  ){
                        var panels = $support.subShortcode[attrs.sed_support_id].panels ,
                            type = ( !_.isUndefined( panels.type ) ) ? panels.type.toLowerCase() : "include";

                        if( !$.isArray(panels.fields) || panels.fields.length == 0 )
                            return ;

                        if( type == "include" )
                            _showHideGPanels("hide");

                        _.each( panels.fields , function( field ){

                            var panelBtn = $( "#sed_pb_" + shortcodeName + "_" + field );

                            if( panelBtn.length > 0 && type == "include"  )
                                panelBtn.parents(".row_settings:first").show();
                            else if( panelBtn.length > 0 && type == "exclude"  )
                                panelBtn.parents(".row_settings:first").hide();
                        });
                    }

                    var _skinSupport = function(){
                        if( !_.isUndefined( $support.subShortcode[attrs.sed_support_id].skins )  ){
                            var skins = $support.subShortcode[attrs.sed_support_id].skins ,
                                type = ( !_.isUndefined( skins.type ) ) ? skins.type.toLowerCase() : "include" ,
                                dialog = $( "#dialog_page_box_" + shortcodeName + "_skin" );

                            if( !$.isArray(skins.fields) || skins.fields.length == 0 )
                                return ;

                            if( type == "include" )
                                dialog.find("li:first").hide();

                            _.each( skins.fields , function( field ){

                                var skinEl = dialog.find("[data-skin-name='" + field + "']").parents("li:first");

                                if( skinEl.length > 0 && type == "include"  )
                                    skinEl.show();
                                else if( skinEl.length > 0 && type == "exclude"  )
                                    skinEl.hide();
                            });
                        }
                    };

                    this._skinSupport = _skinSupport;

                    api.Events.bind( "skins_loaded_" + shortcodeName + "_skin" , this._skinSupport );



            /*if($("#sed-tmpl-modules-skins-" + control.attr + "-" +  control.shortcode).length > 0 ){
                tmpl = $("#sed-tmpl-modules-skins-" + control.attr + "-" +  control.shortcode);
                dialog.find(".skins-dialog-inner").html( tmpl.html() );
            }else{
                control.loadmoduleSkins();
            } */

                }
            }
        },

        postButtonIdUpdate : function( dataElement ){
            //add data-post-id to post edit buttons
            if( !_.isUndefined( dataElement.contextmenuPostId ) ){
                var postEditBtn = $( "#sed-dialog-settings" ).find(".sed_post_edit_button");

                if(postEditBtn.length > 0){
                    postEditBtn.data("postId" , dataElement.contextmenuPostId);
                }
            }
        },

        widgetButtonIdUpdate : function( dataElement ){

            //add data-widget-id-base to widget settings buttons
            if( !_.isUndefined( dataElement.contextmenuWidgetIdBase ) ){
                var widgetIdBase = dataElement.contextmenuWidgetIdBase ,
                    widgetTitle = $("#widget-tpl-" + widgetIdBase ).data( "widgetTitle" );

                //$( "#sed-dialog-settings" ).dialog( "option" , "title", widgetTitle );
                $( "#sed-dialog-settings" ).siblings(".ui-dialog-titlebar:first").find('[data-self-level-box="dialog-level-box-settings-sed_widget-container"] >.ui-dialog-title').text( widgetTitle );

                var widgetIdBaseBtn = $("#sed-dialog-settings").find(".sed_widget_button");

                if(widgetIdBaseBtn.length > 0){
                    widgetIdBaseBtn.data("widgetIdBase" , widgetIdBase);
                }
            }
        },

        getSettings : function( panelId ){
            var self        = this ,
                dataElement = this.sedDialog.data;

            if( !_.isUndefined( panelId ) && panelId && panelId != "root" ){

                return _.filter( api.sedGroupControls[dataElement.shortcodeName] , function( data ){

                    //remove widget instance from settings
                    if( !_.isUndefined( data.shortcode ) && data.shortcode == "sed_widget" && !_.isUndefined( data.attr_name ) && data.attr_name == "instance")
                        return false;

                    if( _.isUndefined( data.panel ) || _.isUndefined( api.settingsPanels[dataElement.shortcodeName] ) || _.isUndefined( api.settingsPanels[dataElement.shortcodeName][data.panel] ) )
                        return false;

                    var panel = api.settingsPanels[dataElement.shortcodeName][data.panel];
                                       
                    if( panel.id == panelId )
                        return true;
                    else
                        return false;
                });

            }else{

                return _.filter( api.sedGroupControls[dataElement.shortcodeName] , function( data ){

                    //remove widget instance from settings
                    if( !_.isUndefined( data.shortcode ) && data.shortcode == "sed_widget" && !_.isUndefined( data.attr_name ) && data.attr_name == "instance")
                        return false;

                    if( _.isUndefined( data.panel ) || _.isUndefined( api.settingsPanels[dataElement.shortcodeName] ) || _.isUndefined( api.settingsPanels[dataElement.shortcodeName][data.panel] ) )
                        return true;

                    var panel = api.settingsPanels[dataElement.shortcodeName][data.panel];

                    if( $.inArray( panel.type , ['dialog' , 'inner_box' , 'accordion' , 'tab'] ) == -1 )
                        return true;
                    else
                        return false;
                });
            }

        },

        initSettings : function( panelId ){

            if( _.isUndefined( this.sedDialog ) )
                return ;

            var self        = this ,
                dataElement = this.sedDialog.data ,
                extra       = $.extend({} , this.sedDialog.extra || {}) ,
                sedDialog   = this.sedDialog ,
                settings;

            var startTime = new Date();

            this.filterSettings( extra.attrs , dataElement.shortcodeName );

            if( _.isUndefined( panelId ) )
                settings = this.getSettings();
            else
                settings = this.getSettings( panelId );
                             //console.log( "Settings ------- : " , settings  );
            _.each( settings , function( data ) {
                //var startTime = new Date();
                self.updateSettings( data.control_id , data , dataElement.shortcodeName , extra);
                //console.log( "Update ------- : " , dataElement.shortcodeName + "_" + data.attr_name , " ----- " , new Date() - startTime );
            });

            //console.log( "Update Settings ------- : " , new Date() - startTime );

            if(this.forceUpdate === true)
                return ;

            this.postButtonIdUpdate( dataElement );
            this.widgetButtonIdUpdate( dataElement );

            if( !_.isUndefined( dataElement.settingsType ) ){
                api.Events.trigger( dataElement.settingsType + "SettingsType" , dataElement , extra );
            }

            //api.log( " extra.attrs ----- : " ,  extra.attrs );

            api.Events.trigger(  "after_shortcode_update_setting" , dataElement.shortcodeName );

            api.Events.trigger(  "after_group_settings_update" , dataElement.shortcodeName );

            api.Events.trigger( dataElement.shortcodeName + "_dialog_settings" , extra.attrs || {} );
        },

        // , needToUpdateSettings
        openInitDialogSettings : function( sedDialog , forceOpen ){
            var self = this,
                isOpen = $( "#sed-dialog-settings" ).dialog( "isOpen" );

            //needToUpdateSettings = !_.isUndefined( needToUpdateSettings) ? needToUpdateSettings : true ;

            forceOpen = !_.isUndefined( forceOpen) ? forceOpen : true ;

            var startTime = new Date();   //alert( !isOpen && forceOpen === true );

            this.sedDialog = sedDialog;

            if( !isOpen && forceOpen === true ){

                this.currentSettingsId = sedDialog.selector;
                this.panelsNeedToUpdate = [];

                $( "#sed-dialog-settings" ).dialog( "open" );

                api.previewer.send("isOpenDialogSettings" , true);

            }else if( isOpen ){

                this.resetTmpl();

                this.currentSettingsId = sedDialog.selector;

                this.panelsNeedToUpdate = [];

                var reset =  !_.isUndefined( sedDialog.reset ) ? sedDialog.reset : true;

                this.switchTmpl( reset );

            }else
                return ;

            //console.log( "Open Dialog ------- : " , new Date() - startTime );
            if( !_.isUndefined( sedDialog.data.panelId ) )
                this.initSettings( sedDialog.data.panelId );
            else
                this.initSettings( );

        },

        //when module select
        moduleSelect : function( sedDialog ){

            var forceOpen = !_.isUndefined( sedDialog.forceOpen ) && sedDialog.forceOpen === true;
            this.openInitDialogSettings( sedDialog , forceOpen );

        },

        //when skin change
        updateByChangePattern : function( dataEl ){
            var self = this;

      		_.each( api.modulesSettingsControls[dataEl.shortcode_name] , function( data ) {

                var id = dataEl.shortcode_name + "_" + data.attr_name;
                api.previewer.trigger( "currentElementId" ,  dataEl.elementId );
                self.updateSettings( id, data , dataEl.shortcode_name , {attrs : dataEl.attrs});
      		});

            api.Events.trigger(  "after_shortcode_update_setting" , dataEl.shortcode_name );

            api.Events.trigger( dataEl.shortcode_name + "_dialog_settings" , dataEl.attrs || {} );

        },

        //after media sync attachment for title & alt & description update value by default media info
        shortcodeControlsUpdate : function( data ){
            var self = this ,
                shortcode = data.shortcode ,
                attrs = data.attrs ,
                targetAttrs = data.targetAttrs || [] ;


      		_.each( api.modulesSettingsControls[shortcode] , function( data ) {
                if(!$.isArray(targetAttrs) || targetAttrs.length == 0  || $.inArray( data.attr_name , targetAttrs) != -1 ){

                    self.updateSettings( shortcode + "_" + data.attr_name , data , shortcode , { attrs : attrs});

                }
      		});
        }

    });


    api.AppWidgetsSettings = api.Class.extend({
        initialize: function( options ){

            $.extend( this, options || {} );

            this.widgetScriptsLoaded = [];
            this.widgetsContents = {};
            this.currentWidgetId;
            this.changeWidget = true;

            this.ready();
        },

        ready : function(){
            var self = this;

            $(".sed_widget_button").livequery(function(){
                if( _.isUndefined( api.appModulesSettings.dialogsContents['sed_widget'] ) ){
                    $(this).click(function(){
                        var widgetIdBase = $(this).data("widgetIdBase");
                        if(!widgetIdBase)
                            return false;

                        self.openWidgetSettings( widgetIdBase );

                    });
                }
            });

            $('[data-self-level-box="dialog-page-box-widgets-settings"] > .icon-close-level-box').livequery(function(){
                if( _.isUndefined( api.appModulesSettings.dialogsContents['sed_widget'] ) ){
                    $(this).click(function(){
                        self.dialogSetWidth();
                    });
                }
            });


            //for widget settings context menu item
            api.Events.bind("widgetSettingsType" , function(dataElement , extra){

                if( _.isUndefined( dataElement.contextmenuWidgetIdBase ) )
                    return ;

                $( "#sed-dialog-settings" ).data('sed.multiLevelBoxPlugin')._callDirectlyLevelBox( "dialog-page-box-widgets-settings" );

                self.openWidgetSettings( dataElement.contextmenuWidgetIdBase );

            });

            api.Events.bind( "beforeResetDialogSettingsTmpl" , function( settingsId ){
                if( settingsId == "sed_widget" && !_.isUndefined( self.currentWidgetId ) && self.currentWidgetId && self.widgetDialogBoxContainer.children().length > 0 ){
                    self.widgetsContents[self.currentWidgetId] = self.widgetDialogBoxContainer.children().detach();
                    self.dialogSetWidth();
                    self.currentWidgetId = "";
                }
            });

        },

        dialogSetWidth : function(){
            var w = $( "#sed-dialog-settings" ).dialog( "option" , "width" );

            if( w > 295 )
                $( "#sed-dialog-settings" ).dialog( "option" , "width", 295 );
        },

        //load widget settings scripts
        loadScripts : function( widgetIdBase ){

            if( !_.isUndefined( api.widgetScripts[widgetIdBase] ) && $.inArray( widgetIdBase , this.widgetScriptsLoaded ) == -1  ){
                var scriptLoader = new api.ModulesScriptsLoader() ,
                    scripts = [] , //wpScripts = _.values( api.wpScripts ) ,
                    scriptsHandles;

                //wpScripts = _.pluck( wpScripts , 'handle' );

                $.each( api.widgetScripts[widgetIdBase] , function( handle , scrs ){
                    scripts.push(  _.values( scrs )  );
                });

                scriptsHandles = _.pluck( _.values( api.widgetScripts[widgetIdBase] ) , 'handle' );

                $.each( scripts , function( i , script){
                    var deps = script[3];
                    $.each(deps , function( d , dep ){
                        if( $.inArray( dep , scriptsHandles ) == -1 && $.inArray( dep , api.sedAppLoadedScripts ) == -1 && $.inArray( dep , api.wpScripts ) != -1   ){
                            scripts.push( _.values( api.wpScripts[dep] ) );
                            scriptsHandles.push( dep );
                        }
                    });
                });

                scripts = _.filter( scripts , function( script ){
                    if($.inArray(script[0] , api.sedAppLoadedScripts) == -1)
                        return true
                    else
                        return false;
                });

                if( scripts.length > 0 ){
                    scriptLoader.moduleScStLoad( scripts , api.sedAppLoadedScripts , function(){
                        $.each( scripts , function(i , script){
                            if($.inArray(script[0] , api.sedAppLoadedScripts) == -1)
                                api.sedAppLoadedScripts.push(script[0]);
                        });
                    });
                }else{
                    this.widgetScriptsLoaded.push( widgetIdBase );
                }
            }

        },

        openWidgetSettings : function( widgetIdBase ){

            var widget = $("#widget-tpl-" + widgetIdBase );

            this.widgetDialogBoxContainer = $("#dialog-page-box-widgets-settings").find(".widgets-box-container");

            if( this.currentWidgetId == widgetIdBase ){
                this.changeWidget = false;
            }else{
                this.changeWidget = true;
                this.currentWidgetId = widgetIdBase;
            }

            if( this.changeWidget === false )
                return ;

            //var form_html = widget.find('.widget-inside').html();
            //form_html.replace('__i__' , 'default');

            //remain load site editor scripts and add to api.sedAppLoadedScripts
            //santize php values and preper for js

            this.loadScripts( widgetIdBase );

            if( !_.isUndefined( this.widgetsContents[this.currentWidgetId] ) ){
                this.widgetsContents[this.currentWidgetId].appendTo( this.widgetDialogBoxContainer );
            }else{
                $( widget.html() ).appendTo( this.widgetDialogBoxContainer );
            }

            var container = this.widgetDialogBoxContainer;

            if( container.find('[name="widget-width"]').length > 0 && container.find('[name="widget-width"]').val() > 295 )
                $( "#sed-dialog-settings" ).dialog( "option" , "width", container.find('[name="widget-width"]').val() );

            var cId = "widget-" + widgetIdBase ,
                data = api.settings.controls[cId] ,
                extra = $.extend({} , api.appModulesSettings.sedDialog.extra || {});

            api.appModulesSettings.updateSettings( cId , data , "sed_widget" , extra );
            /*var control = api.control.instance(  );
            control.update();*/

        },


    });

    api.AppStyleEditorSettings = api.Class.extend({
        initialize: function( options ){

            $.extend( this, options || {} );

            this.panelsContents = {};
            this.stylesContents = {};
            this.accordionsInit = {};
            this.neededUpdateSuppotSelector = false;
            this.currentSettingsId;
            this.currentStyleId;
            this.changeModule = true;
            this.changeStyleId = true;
            this.currentSelector = false;
            this.defaultValues = {};
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
                if( _.isUndefined( api.appModulesSettings.dialogsContents[api.appModulesSettings.currentSettingsId] ) ){
                    $(this).click(function(){

                        self.openPanelSettings( );
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

                        self.openStyleSettings( $(this).data("styleId") , $(this).data("dialogTitle") , $(this).data("selector") );

                    });
                }
            });


            api.Events.bind("editStyleSettingsType" , function(dataElement , extra){

                $( "#sed-dialog-settings" ).data('sed.multiLevelBoxPlugin')._callDirectlyLevelBox( "dialog_page_box_" + dataElement.shortcodeName + "_design_panel" );

                api.appModulesSettings.forceUpdate = true;

                self.openPanelSettings( );

                if( !_.isUndefined( self.accordionsInit[self.currentSettingsId] ) && self.accordionsInit[self.currentSettingsId] === true )
                    self.currentSuppotSelector();
                else
                    self.neededUpdateSuppotSelector = true;
            });


            api.Events.bind( "beforeResetDialogSettingsTmpl" , function( settingsId ){

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

        },

        openPanelSettings : function(  ){

            var shortcodeName = api.appModulesSettings.sedDialog.data.shortcodeName,
                panelTpl = $("#style_editor_panel_" + shortcodeName + "_tmpl" );

            this.dialogBoxContainer = $("#dialog_page_box_" + shortcodeName + "_design_panel").find(".sed_style_editor_panel_container:first");

            if( this.currentSettingsId == api.appModulesSettings.currentSettingsId ){
                this.changeModule = false;
                return ;
            }else{
                this.changeModule = true;
                this.currentSettingsId = _.clone( api.appModulesSettings.currentSettingsId );
            }

            if( !_.isUndefined( this.panelsContents[this.currentSettingsId] ) ){
                this.panelsContents[this.currentSettingsId].appendTo( this.dialogBoxContainer );
            }else{

                $( panelTpl.html() ).appendTo( this.dialogBoxContainer );

            }

        },

        currentSuppotSelector : function(){

            $( "#sed-dialog-settings" ).find( ".accordion-panel-settings .design_ac_header" ).each(function(){

                var selector = $(this).data("selector"),
                    selectorT = ( selector != "sed_current" ) ? '[sed_model_id="' + api.currentTargetElementId + '"] ' + selector : '[sed_model_id="' + api.currentTargetElementId + '"]' ,
                    index = selectorT.indexOf(":");

                if(index > -1){
                    var pseudo = selectorT.substring( index );
                    selectorT = selectorT.substring( 0 , index );
                }
                
                var $el = $("#website")[0].contentWindow.jQuery( selectorT ) ,
                    el = $el[0];

                if( $el.length > 0 ){
                    $(this).show();
                    $(this).next().hide();
                }else{
                    $(this).hide();
                    $(this).next().hide();
                }

            });

            var num = $( "#sed-dialog-settings" ).find( ".accordion-panel-settings .ui-accordion-header.design_ac_header:visible:first" ).index();

            if( !_.isUndefined( num )  && num > -1){
                var active = $( "#sed-dialog-settings" ).find( ".accordion-panel-settings" ).accordion("option" , "active");

                if( active !== false){
                    if( !$( "#sed-dialog-settings" ).find( ".accordion-panel-settings .design_ac_header" ).eq(active).is(":visible") ){
                        //$( "#sed-dialog-settings" ).find( ".accordion-panel-settings .design_ac_header" ).eq(num).next().show();
                        $( "#sed-dialog-settings" ).find( ".accordion-panel-settings" ).accordion("option" , "active" , num/2);
                    }else
                        $( "#sed-dialog-settings" ).find( ".accordion-panel-settings .design_ac_header" ).eq(active).next().show();
                }
            }

        },

        /*
        @styleId :  like border , font , background , ....
        */
        openStyleSettings : function( styleId , dialogTitle , selector ){

            var self = this ,
                shortcodeName = api.appModulesSettings.sedDialog.data.shortcodeName,
                panelTpl = $("#style_editor_settings_" + styleId + "_tmpl" ) ,
                lvlBox = "modules_styles_settings_"+ shortcodeName +"_level_box";

            api.currentCssSelector = ( selector != "sed_current" ) ? '[sed_model_id="' + api.currentTargetElementId + '"] ' + selector : '[sed_model_id="' + api.currentTargetElementId + '"]';

            this.styleBoxContainer = $("#" + lvlBox ).find(".styles_settings_container:first");

            if( this.currentStyleId == styleId ){
                this.changeStyleId = false;
                                          //alert( self.currentSelector );  alert( selector );
                if( !_.isUndefined( self.currentSelector ) && self.currentSelector !== false && self.currentSelector != selector ){
                    self.currentSelector = selector;
                    self.initSettings( styleId , selector );
                }//else
                    //self.updateStyleNeeded = false

                return ;
            }else{

                if( !_.isUndefined( self.currentStyleId ) && self.currentStyleId && self.styleBoxContainer.children().length > 0 )
                    self.stylesContents[self.currentStyleId] = self.styleBoxContainer.children().detach();

                this.changeStyleId = true;
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
                var startTime = new Date();
                self.updateSettings( data.control_id , data , selector );
                //console.log( "Update styles------- : " , data.control_id , " ----- " , new Date() - startTime );
            });
        },

        getStyleValue : function( selector , styleProp ){
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
              break;*/
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
        },

        getDefaultValue : function( data , selector , selectorT ){
            var defaultValue;

            if( !_.isUndefined( this.defaultValues[data.style_props] ) && !_.isUndefined( this.defaultValues[data.style_props][selectorT] ) ){

                defaultValue = _.clone( this.defaultValues[data.style_props][selectorT] );
            }else{
                defaultValue = this.getStyleValue( selector , data.style_props );

                if( _.isUndefined( this.defaultValues[data.style_props] ) )
                    this.defaultValues[data.style_props] =  {};

                this.defaultValues[data.style_props][selectorT] = _.clone( defaultValue );
            }

            return defaultValue;

        },

        getCurrentValue : function( id , data , selector ){

            var selectorT = ( selector && selector != "sed_current" ) ? '[sed_model_id="' + api.currentTargetElementId + '"] ' + selector : '[sed_model_id="' + api.currentTargetElementId + '"]',
                extra  = $.extend({} , api.appModulesSettings.sedDialog.extra || {}) ,
                cValue = null;

            if( !_.isUndefined( api.currenStyleEditorContolsValues[selectorT] ) && !_.isUndefined( api.currenStyleEditorContolsValues[selectorT][data.settings["default"]] ) ){
                cValue = api.currenStyleEditorContolsValues[selectorT][data.settings["default"]];
            }else{

                if( !_.isUndefined( extra.attrs ) && !_.isUndefined( extra.attrs.sed_css ) &&  !_.isUndefined( extra.attrs.sed_css[selectorT] ) && !_.isUndefined( data.settings ) && !_.isUndefined( data.settings["default"] ) && !_.isUndefined( extra.attrs.sed_css[selectorT][data.settings["default"]] ) ){
                    cValue = extra.attrs.sed_css[selectorT][data.settings["default"]];
                }else if( !_.isUndefined( data.style_props ) && data.settings["default"] != "external_background_image" ){
                    cValue = this.getDefaultValue( data , selector , selectorT );
                }

            }

            return !_.isObject( cValue ) ? _.clone( cValue ) : cValue;

        },

        updateSettings: function(  id , data , selector  ){
            //var selectorT = "#" + api.currentTargetElementId + " " + selector ,
                //$el = $("#website")[0].contentWindow.jQuery( selectorT );

            if( $.inArray(id , api.appModulesSettings.initControls) == -1 ){  //&& $el.length > 0

                var cValue = this.getCurrentValue( id , data , selector  );

                if( !_.isNull( cValue ) ){
                    data.default_value = _.clone( cValue );
                }

                api.Events.trigger( "renderSettingsControls" , id, data );
                api.appModulesSettings.initControls.push( id );

                var control = api.control.instance( id );
                $( control.container ).parents(".row_settings:first").show();
                //this.controlFilter( extra.attrs , shortcodeName );

            }else if( $.inArray(id , api.appModulesSettings.initControls) != -1 ){

                var control = api.control.instance( id );
                $( control.container ).parents(".row_settings:first").show();
                //this.controlFilter( extra.attrs , shortcodeName );

                var cValue = this.getCurrentValue( id , data , selector  );
                                         //alert( cValue );
                if( !_.isNull( cValue ) ){
                    control.update( cValue );
                }else{
                    control.update( );
                }

            }

        },


    });

    $( function() {

        api.appModulesSettings = new api.AppModulesSettings({});

        api.appWidgetsSettings = new api.AppWidgetsSettings({});

        api.appStyleEditorSettings = new api.AppStyleEditorSettings({});

        var generalStyleEditor;
        $( "#page_general_settings" ).click(function() {

            if( _.isUndefined( generalStyleEditor ) ){
                generalStyleEditor = {};
                _.each( api.settings.controls , function( data , id){
                    if( !_.isUndefined(data.category) && data.category == 'style-editor' && !_.isUndefined(data.sub_category) && data.sub_category == 'general_settings'){
                        generalStyleEditor[id] = data;
                    }
                });
            }
            $.each( generalStyleEditor , function(id , data ){
                var control = api.control.instance( id );
                $( control.container ).parents(".row_settings:first").show();

                api.currentTargetElementId = "page";
                var targetEl =  "#page" ,
                    $thisValue = control.setting();
                                                                                      // && $el.length > 0
                if( _.isUndefined( $thisValue[targetEl] ) && !_.isUndefined( data.style_props ) ){
                    control.defaultValue = _.clone( api.appStyleEditorSettings.getDefaultValue( data , "" , targetEl ) );
                    if( $.inArray( data.style_props , ["margin-top","margin-bottom","margin-left","margin-right","padding-left","padding-right","padding-top","padding-bottom"] ) != -1 ){
                        control.defaultValue = parseInt( control.defaultValue);
                    }
                }

                control.update( targetEl );
            });

            api.Events.trigger(  "after_group_settings_update" , "general_settings" );
        });
    });

})( sedApp, jQuery );