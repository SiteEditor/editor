(function( exports, $ ){

    var api = sedApp.editor;

    api.currentCssSelector = api.currentCssSelector || "";
    //handels of all loaded scripts in siteeditor app
    api.sedAppLoadedScripts = api.sedAppLoadedScripts || [];

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

    api.SiteEditorDialogSettings = api.Class.extend({

        initialize: function( options ){

            $.extend( this, options || {} );

            /**
             * Load Settings From 'js' or 'html' Template Or Load with 'Ajax'
             * @type {string}
             */
            this.templateType = "html";

            /**
             * Settings Type : include 'module' , 'app'
             * @type {string}
             */
            this.settingsType = "module";

            this.dialogsContents = {};

            this.dialogsTitles = {};

            this.ajaxProcessing = {};

            this.ajaxResetTmpls = {};

            this.ajaxCachTmpls = {};

            this.ajaxCachControls = {};

            this.backgroundAjaxload = {};

            this.optionsGroup = "";

            this.needToRefreshGroups = [];

            this.loading = {};

            this.currentSettingsId = "";

            this.dialogSelector = "#sed-dialog-settings";

            this._dialogInit();

            this.ready();
        },

        /**
         *
         * @private
         */
        _dialogInit : function(){

            var self = this ,
                selector = this.dialogSelector;

            $( selector ).dialog({
                "autoOpen"  : false,
                "modal"     : false,
                //draggable: false,
                resizable   : true,
                "width"     : 295,
                "height"    : 600 ,
                "position"  : {
                    "my"    : "right-20",
                    "at"    : "right" ,
                    "of"    : "#sed-site-preview"
                },
                open: function () {
                    self._switchTmpl();
                },
                close : function(){
                    api.previewer.send("isOpenDialogSettings" , false);
                    self._resetTmpl();
                }
            });

            this._initDialogMultiLevelBox( selector );
            this._initDialogScrollBar( selector );

        },

        /**
         * for override in extends classes
         */
        ready : function(){
            var self = this;

            api.Events.bind( "moduleDragStartEvent" , function( moduleName ){

                var shortcodeName;
                $.each(api.shortcodes , function( name, shortcode){
                    if(shortcode.asModule && shortcode.moduleName == moduleName){
                        shortcodeName = name;
                        return false;
                    }
                });

                if( ! shortcodeName )
                    return ;

                if( _.isUndefined( self.backgroundAjaxload[shortcodeName] ) ) {
                    self._sendRequest(shortcodeName, "module" , shortcodeName);

                    self.backgroundAjaxload[shortcodeName] = 1;
                }

            });

            api.Events.bind( "afterResetpageInfoSettings" , function(){

                if( !_.isEmpty( self.needToRefreshGroups ) ){

                    _.each( self.needToRefreshGroups , function( optionsGroup ){

                        var settingId = optionsGroup + "_" + api.settings.page.id ,
                            isOpen = $( self.dialogSelector ).dialog( "isOpen" );

                        if( _.isUndefined( self.backgroundAjaxload[settingId] ) && _.isUndefined( self.dialogsContents[settingId] ) ) {

                            if( isOpen && self.optionsGroup == optionsGroup ){

                                self._resetTmpl();
                                self.currentSettingsId = settingId;
                                self._addLoading();

                                self._sendRequest(settingId, "app" , optionsGroup);
                            }else{

                                self._sendRequest(settingId, "app" , optionsGroup);
                                self.backgroundAjaxload[settingId] = 1;

                            }

                        }else if( !_.isUndefined( self.dialogsContents[settingId] ) ){

                            if( isOpen && self.optionsGroup == optionsGroup ) {
                                self._resetTmpl();
                                self._switchTmpl();
                            }
                        }

                    });

                }

            });

        },

        /**
         *
         * @param dialogSelector
         * @private
         */
        _initDialogMultiLevelBox : function( dialogSelector ){

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

        /**
         *
         * @param dialogSelector
         * @private
         */
        _initDialogScrollBar : function( dialogSelector ){
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

        /**
         *
         * @param reset
         * @private
         */
        _switchTmpl : function( reset ){
            var self = this ,
                selector = this.dialogSelector;

            reset = !_.isUndefined( reset ) ? reset : true;

            if( !_.isUndefined( self.dialogsContents[self.currentSettingsId] ) ){

                var $currentElDialog = self.dialogsContents[self.currentSettingsId].appendTo( $( selector ) ).fadeIn( "slow" );
                self.dialogsTitles[self.currentSettingsId].appendTo( $( selector ).siblings(".ui-dialog-titlebar:first") );

                api.Events.trigger( "afterAppendSettingsTmpl" , $currentElDialog , this.settingsType , this.currentSettingsId );

                if( reset === true )
                    $( selector ).data('sed.multiLevelBoxPlugin')._reset();

            }else{

                if( this.templateType == "ajax" ) {
                    this._ajaxLoadSettings();
                }else if( this.templateType == "html" ){

                    var $currentElDialog = $( $("#sed-tmpl-dialog-settings-" + self.currentSettingsId ).html() ).appendTo( $( selector ) ).fadeIn( "slow" );

                    api.Events.trigger( "afterInitAppendSettingsTmpl" , $currentElDialog , this.settingsType , this.currentSettingsId );

                    $( selector ).data('sed.multiLevelBoxPlugin').options.innerContainer = $( selector ).find(".dialog-level-box-settings-container");
                    $( selector ).data('sed.multiLevelBoxPlugin')._render();

                    api.Events.trigger( "endInitAppendSettingsTmpl" , $currentElDialog , this.settingsType , this.currentSettingsId );

                }

            }
        },

        /**
         *
         * @private
         */
        _resetTmpl : function(){
            var self = this ,
                selector = this.dialogSelector;

            if( !_.isUndefined( this.ajaxProcessing[self.currentSettingsId] ) ){
                this.ajaxResetTmpls[self.currentSettingsId] = 'yes';
                return ;
            }

            api.Events.trigger( "beforeResetSettingsTmpl" , self.currentSettingsId , this.settingsType );

            self.dialogsTitles[self.currentSettingsId] = $( selector ).siblings(".ui-dialog-titlebar:first").children(".multi-level-box-title").detach();
            self.dialogsContents[self.currentSettingsId] = $( selector ).children().hide().detach();

            api.Events.trigger( "afterResetSettingsTmpl" , self.currentSettingsId , this.settingsType );

        },

        /**
         *
         * @private
         */
        _ajaxLoadSettings : function( ) {
            var self = this ,
                selector = this.dialogSelector;

            if (!_.isUndefined(self.ajaxResetTmpls[self.currentSettingsId])) {
                delete self.ajaxResetTmpls[self.currentSettingsId];
                return;
            }
 
            if (!_.isUndefined(self.ajaxCachTmpls[self.currentSettingsId])) {

                var output = self.ajaxCachTmpls[self.currentSettingsId];

                var $currentElDialog = $(output).appendTo($(selector));

                api.Events.trigger("afterInitAppendSettingsTmpl", $currentElDialog, self.settingsType, self.currentSettingsId);

                $(selector).data('sed.multiLevelBoxPlugin').options.innerContainer = $(selector).find(".dialog-level-box-settings-container");
                $(selector).data('sed.multiLevelBoxPlugin')._render();

                api.Events.trigger("endInitAppendSettingsTmpl", $currentElDialog, self.settingsType, self.currentSettingsId);

                delete self.ajaxCachTmpls[self.currentSettingsId];

                var controls = $.extend( true , {} , self.ajaxCachControls[self.currentSettingsId]);

                self.setControls( controls , self.currentSettingsId , self.settingsType );

                delete self.ajaxCachControls[self.currentSettingsId];

                return;
            }

            this._addLoading();

            if (!_.isUndefined(this.backgroundAjaxload[this.currentSettingsId])) {
                delete this.backgroundAjaxload[this.currentSettingsId];
                return;
            }

            this._sendRequest( this.currentSettingsId , this.settingsType , this.optionsGroup );
        },

        _addLoading : function( ){

            if( _.isUndefined( this.loading[this.currentSettingsId] ) ) {
                var tpl = api.template("sed-ajax-loading"), html;

                html = tpl({type: "medium"}); // loadingType : "small" || "medium" || ""

                this.loading[this.currentSettingsId] = $(html).appendTo($(this.dialogSelector));//

                this.loading[this.currentSettingsId].show();
            }

        },

        _sendRequest : function( settingIdReq , settingsTypeReq , optionsGroup ) {

            var self = this,
                selector = this.dialogSelector;

            this.ajaxProcessing[settingIdReq] = 1;

            var data = {
                action          : 'sed_load_options',
                setting_id      : settingIdReq ,
                setting_type    : settingsTypeReq ,
                options_group   : optionsGroup ,
                nonce           : api.addOnSettings.optionsEngine.nonce.load,
                sed_page_ajax   : 'sed_options_loader'
            };

            data = api.applyFilters( 'sedAjaxLoadOptionsDataFilter' , data );

            var ajaxOptionsRequest = api.wpAjax.send({

                type: "POST",
                //url: api.settings.url.ajax,
                data : data,
                success : function( responseData ){

                    var output = responseData.output ,
                        controls = responseData.controls,
                        relations = responseData.relations ,
                        settings = responseData.settings ,
                        panels = responseData.panels ,
                        settingId = responseData.settingId ,
                        settingType = responseData.settingType,
                        groups  = responseData.groups ,
                        designTemplate = responseData.designTemplate;

                    delete self.ajaxProcessing[settingId];

                    if( !_.isUndefined( self.loading[settingId] ) ) {
                        self.loading[settingId].hide();
                        self.loading[settingId].remove();
                        delete self.loading[settingId];
                    }

                    self.setDependencies( relations , settingId );

                    self.setSettings( settings , settingId );

                    self.setGroups( groups , settingId );

                    self.setPanels( panels , settingId );

                    self.sedDesignTemplate( designTemplate , settingId );

                    if( _.isUndefined( self.ajaxResetTmpls[settingId] ) && _.isUndefined( self.backgroundAjaxload[settingId] ) ) {

                        var $currentElDialog = $(output).appendTo($(selector));

                        api.Events.trigger( "afterInitAppendSettingsTmpl" , $currentElDialog , settingType , settingId );

                        $( selector ).data('sed.multiLevelBoxPlugin').options.innerContainer = $( selector ).find(".dialog-level-box-settings-container");
                        $( selector ).data('sed.multiLevelBoxPlugin')._render();

                        api.Events.trigger( "endInitAppendSettingsTmpl" , $currentElDialog , settingType , settingId );

                        self.setControls( controls , settingId , settingType );

                    }else{

                        self.ajaxCachTmpls[settingId] = output;

                        self.ajaxCachControls[settingId] = controls;

                        if( ! _.isUndefined( self.ajaxResetTmpls[settingId] ) )
                            delete self.ajaxResetTmpls[settingId] ;

                        if( ! _.isUndefined( self.backgroundAjaxload[settingId] ) )
                            delete self.backgroundAjaxload[settingId] ;

                    }


                },

                error : function( responseData ){

                    var settingId = responseData.settingId;

                    if( !_.isUndefined( self.loading[settingId] ) ) {
                        self.loading[settingId].hide();
                        self.loading[settingId].remove();
                        delete self.loading[settingId];
                    }

                    if( !_.isUndefined( self.ajaxResetTmpls[settingId] ) ) {
                        delete self.ajaxResetTmpls[settingId];
                    }

                    if( !_.isUndefined( self.backgroundAjaxload[settingId] ) ) {
                        delete self.backgroundAjaxload[settingId];
                    }

                    delete self.ajaxProcessing[settingId];

                    var template = api.template("sed-load-options-errors"), html;

                    html = template( { message: responseData.message } ); // loadingType : "small" || "medium" || ""

                    $(html).appendTo( $(self.dialogSelector) );

                    //for show dialog title
                    $( selector ).data('sed.multiLevelBoxPlugin').options.innerContainer = $( selector ).find(".dialog-level-box-settings-container");
                    $( selector ).data('sed.multiLevelBoxPlugin')._render();
                }


            });
                //container   : this.dialogSelector

        },

        setDependencies : function( relations , settingId ){

            if( !_.isUndefined( relations ) && !_.isEmpty( relations ) && _.isObject( relations ) ){
                var groupRelations = {};

                groupRelations[settingId] = relations;

                api.settingsRelations = $.extend( api.settingsRelations , groupRelations);

            }

        },

        sedDesignTemplate : function( designTemplate , settingId ){

            if( !_.isUndefined( designTemplate ) && !_.isEmpty( designTemplate ) ){

                api.designEditorTpls[settingId] = designTemplate;

            }

        },

        setSettings : function( settings , settingId ){

            if( !_.isEmpty( settings ) ) {
                var createdSettings = {};

                _.each( settings , function (settingArgs, id) {

                    if ( ! api.has( id ) ) {

                        var setting = api.create(id, id, settingArgs.value, {
                            transport: settingArgs.transport || "refresh",
                            previewer: api.previewer,
                            stype: settingArgs.type || "general",
                            dirty: settingArgs.dirty
                        });

                        api.settings.settings[id] = settingArgs;

                        createdSettings[id] = settingArgs.value;

                    }

                });

                api.previewer.send( "settings" , createdSettings );

                $.each( settings , function ( id , settingArgs ){

                    if( $.inArray( id , _.keys( createdSettings ) ) == -1 )
                        return true; //continue

                    if ( settingArgs.dirty ) {
                        var setting = api(id);
                        setting.callbacks.fireWith(setting, [setting.get(), {}]);
                    }

                });

            }

        },

        setPanels : function( panels , settingId ){

            if( _.isUndefined( api.settingsPanels[settingId] ) ) {
                api.settingsPanels[settingId] = {};
            }

            _.each( panels , function( data , id ){
                api.settingsPanels[settingId][id] = data;
            });

        },

        /**
         * Create Controls after load & append in settings dialog
         * @todo create app settings controls just on time like module settings
         *
         * @param controls
         * @param settingId
         * @param settingType
         */
        setControls : function( controls , settingId , settingType ){

            if( !_.isEmpty( controls ) ){

                if( _.isUndefined( api.sedGroupControls[settingId] ) ) {
                    api.sedGroupControls[settingId] = [];
                }

                if( settingType == "module" ){
                    
                    _.each( controls , function ( data , id ) {
                        api.sedGroupControls[settingId].push( data );
                    });

                    if ( !_.isUndefined( api.appModulesSettings.sedDialog.data.panelId ) )
                        api.appModulesSettings.initSettings( api.appModulesSettings.sedDialog.data.panelId );
                    else
                        api.appModulesSettings.initSettings();

                } else {

                    _.each(controls, function (data, id) {

                        if ($.inArray(id, _.keys(api.settings.controls)) == -1) {

                            api.settings.controls[id] = data;

                            api.Events.trigger("renderSettingsControls", id, data);

                        }

                        api.sedGroupControls[settingId].push( data );

                    });

                    api.Events.trigger("after_group_settings_update", settingId);
                }
            }

        },

        setGroups : function( groups , settingId ){
            var self = this;

            if( !_.isEmpty( groups ) ){

                _.each( groups , function( data , id ){

                    if( $.inArray( id , _.keys( api.settings.groups ) ) == -1  ){

                        api.settings.groups[id] = data;
                    }

                    if( data.pages_dependency && $.inArray( id , self.needToRefreshGroups ) == -1 ){
                        self.needToRefreshGroups.push( id );
                    }

                });

            }

        },

        openInitDialogSettings : function( settingId , forceOpen , reset , settingsType , templateType , optionsGroup ){
            var isOpen = $( this.dialogSelector ).dialog( "isOpen" );

            //needToUpdateSettings = !_.isUndefined( needToUpdateSettings) ? needToUpdateSettings : true ;
            
            forceOpen = !_.isUndefined( forceOpen) ? forceOpen : true ; 

            if( !isOpen && forceOpen === true ){

                this.currentSettingsId = settingId;
                //this.panelsNeedToUpdate = [];

                this.optionsGroup = optionsGroup;

                this.templateType = templateType;

                this.settingsType = settingsType;

                $( this.dialogSelector ).dialog( "open" );

                api.previewer.send("isOpenDialogSettings" , true);

            }else if( isOpen ){

                this._resetTmpl();

                this.optionsGroup = optionsGroup;

                this.templateType = templateType;

                this.settingsType = settingsType;

                this.currentSettingsId = settingId;

                //this.panelsNeedToUpdate = [];

                reset =  !_.isUndefined( reset ) ? reset : true;

                this._switchTmpl( reset );

            }else
                return ;
        }

    });

    api.AppModulesSettings = api.Class.extend({

        initialize: function( options ){

            $.extend( this, options || {} );

            this.currentDialogSelector = "none";

            this.sedDialog;
            //in this version only using in design panel(for back btn and update settings)
            this.forceUpdate = false;
            this.rowContainerSettingsData = {};
            //this.lastSedDialog;
            this.panelsNeedToUpdate = [];
            //this.lastPanelsNeedToUpdate = [];

            this.dialogSelector = "#sed-dialog-settings";

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
            var self = this;

            api.Events.bind("animationSettingsType" , function(dataElement , extra){  //alert( dataElement.shortcodeName );

                $( self.dialogSelector ).data('sed.multiLevelBoxPlugin')._callDirectlyLevelBox( "dialog_page_box_" + dataElement.shortcodeName + "_animation"  );

            });

            api.Events.bind("changeSkinSettingsType" , function(dataElement , extra){

                $( self.dialogSelector ).data('sed.multiLevelBoxPlugin')._callDirectlyLevelBox( "dialog_page_box_" + dataElement.shortcodeName + "_skin" );

                api.Events.trigger( "loadSkinsDirectly" , dataElement.moduleName );

            });

            api.Events.bind("linkToSettingsType" , function(dataElement , extra){

                $( self.dialogSelector ).data('sed.multiLevelBoxPlugin')._callDirectlyLevelBox( dataElement.shortcodeName + "_link_to_panel_level_box" );

            });

            api.Events.bind("afterAppendSettingsTmpl" , function( dialog , settingsType , currentSettingsId ){

                if( settingsType == "module" ){

                    api.Events.trigger( "afterAppendModulesSettingsTmpl" , self , dialog );

                }

            });

            api.Events.bind("afterInitAppendSettingsTmpl" , function( dialog , settingsType , currentSettingsId ){

                if( settingsType == "module" ){ 

                    api.Events.trigger( "afterInitAppendModulesSettingsTmpl" , self , dialog );

                }

            });

            api.Events.bind("endInitAppendSettingsTmpl" , function( dialog , settingsType , currentSettingsId ){

                if( settingsType == "module" ){

                    if( self.sedDialog.data.shortcodeName == "sed_row" ){
                        var html = '<span id="row_back_settings_element" class="icon-close-level-box"><i class="icon-chevron-left"></i></span>';
                        $(self.dialogSelector).siblings(".ui-dialog-titlebar:first").find("[data-self-level-box='dialog-level-box-settings-sed_row-container']").prepend( $(html) );
                    }

                }

            });

            api.Events.bind("beforeResetSettingsTmpl" , function( currentSettingsId , settingsType ){

                if( settingsType == "module" ){

                    api.Events.trigger( "beforeResetDialogSettingsTmpl" , currentSettingsId );

                }

            });

        },

        initDialogSettings : function(){
            var self = this ,
                selector = self.dialogSelector;

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

            $( selector ).find(".go-panel-element-update").livequery(function(){
                if( _.isUndefined( api.sedDialogSettings.dialogsContents[self.currentSettingsId] ) ){
                    $(this).click(function(){ //go-accordion-panel

                        var panelId = $(this).data("panelId");

                        if( panelId ) {

                            if ($.inArray(panelId, self.panelsNeedToUpdate) == -1) {
                                self.initSettings(panelId);
                                self.panelsNeedToUpdate.push(panelId);
                            }

                        }

                    });
                }
            });

            $( selector ).find(".go-row-container-settings").livequery(function(){
                if( _.isUndefined( api.sedDialogSettings.dialogsContents[self.currentSettingsId] ) ){
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
                if( _.isUndefined( api.sedDialogSettings.dialogsContents[self.currentSettingsId] ) ){
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
            $( self.dialogSelector ).siblings(".ui-dialog-titlebar:first").find('[data-self-level-box] >.icon-close-level-box').livequery(function(){
                if( _.isUndefined( api.sedDialogSettings.dialogsContents[self.currentSettingsId] ) ){
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
                        active: false,
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


            api.previewer.bind( 'dialogSettingsClose' , function( ) {

                var isOpen = $( self.dialogSelector ).dialog( "isOpen" );
                if( isOpen )
                    $( self.dialogSelector ).dialog( "close" );

            });

        },

        updateSettings: function(  id , data , shortcodeName , extra , needReturn ){

            if( $.inArray(id , _.keys( api.settings.controls ) ) == -1 ){

                if( !_.isUndefined( data.category ) && data.category == "style-editor" ){
                    var cssSelector = !_.isUndefined( data.selector ) ? data.selector : '';
                    var sValue = api.appStyleEditorSettings.getCurrentValue( id , data , cssSelector );
                    if( !_.isNull( sValue ) ){
                        data.default_value = _.clone( sValue );
                    }

                }

                api.settings.controls[id] = data;

                api.Events.trigger( "renderSettingsControls" , id, data , extra);

                var control = api.control.instance( id );
                $( control.container ).parents(".row_settings:first").show();

            }else {

                /*if( data.is_attr === false )
                    return ;*/

                var control = api.control.instance( id );
                $( control.container ).parents(".row_settings:first").show();

                if( control.isStyleControl ){

                    var sValue = api.appStyleEditorSettings.getCurrentValue( control.id , data , control.cssSelector );
                    if( !_.isNull( sValue ) ){
                        var cValue = _.clone( sValue );
                        control.update( cValue );
                    }else{
                        control.update( );
                    }

                }else if( !_.isUndefined(extra.attrs) ) {
                    control.update(extra.attrs);
                }else {
                    control.update();
                }

            }

            if( !_.isUndefined(needReturn) && needReturn === true )
                return control;

        },

        postButtonIdUpdate : function( dataElement ){
            //add data-post-id to post edit buttons
            if( !_.isUndefined( dataElement.contextmenuPostId ) ){
                var postEditBtn = $( this.dialogSelector ).find(".sed_post_edit_button");

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

                //$( this.dialogSelector ).dialog( "option" , "title", widgetTitle );
                $( this.dialogSelector ).siblings(".ui-dialog-titlebar:first").find('[data-self-level-box="dialog-level-box-settings-sed_widget-container"] >.ui-dialog-title').text( widgetTitle );

                var widgetIdBaseBtn = $(this.dialogSelector).find(".sed_widget_button");

                if(widgetIdBaseBtn.length > 0){
                    widgetIdBaseBtn.data("widgetIdBase" , widgetIdBase);
                }
            }
        },

        /**
         * Get settings in root or 'inner_box' Or 'expanded'
         * @Todo : if 'default' panel in 'inner_box' Or 'expanded' , create and update in
         * -first time And not sync with update panel , and it's need to update with panel
         *
         * @param panelId
         * @returns {*}
         */
        getSettings : function( panelId ){
            var dataElement = this.sedDialog.data;

            if( !_.isUndefined( panelId ) && panelId && panelId != "root" ){

                return _.filter( api.sedGroupControls[dataElement.shortcodeName] , function( data ){

                    //remove widget instance from settings
                    if( !_.isUndefined( data.shortcode ) && data.shortcode == "sed_widget" && !_.isUndefined( data.attr_name ) && data.attr_name == "instance")
                        return false;

                    if( _.isUndefined( data.panel ) || _.isUndefined( api.settingsPanels[dataElement.shortcodeName] ) || _.isUndefined( api.settingsPanels[dataElement.shortcodeName][data.panel] ) )
                        return false;

                    var panel = api.settingsPanels[dataElement.shortcodeName][data.panel];

                    return panel.id == panelId;

                });

            }else{

                return _.filter( api.sedGroupControls[dataElement.shortcodeName] , function( data ){

                    //remove widget instance from settings
                    if( !_.isUndefined( data.shortcode ) && data.shortcode == "sed_widget" && !_.isUndefined( data.attr_name ) && data.attr_name == "instance")
                        return false;

                    if( _.isUndefined( data.panel ) || _.isUndefined( api.settingsPanels[dataElement.shortcodeName] ) || _.isUndefined( api.settingsPanels[dataElement.shortcodeName][data.panel] ) )
                        return true;

                    var panel = api.settingsPanels[dataElement.shortcodeName][data.panel];

                    return $.inArray( panel.type , [ 'inner_box' , 'expanded' ] ) == -1; //, 'expanded'

                });
            }

        },

        initSettings : function( panelId ){

            if( _.isUndefined( this.sedDialog ) )
                return ;

            var self        = this ,
                dataElement = this.sedDialog.data ,
                extra       = $.extend({} , this.sedDialog.extra || {}) ,
                settings;

            if( _.isUndefined( panelId ) )
                settings = this.getSettings();
            else
                settings = this.getSettings( panelId );

            _.each( settings , function( data ) {
                self.updateSettings( data.control_id , data , dataElement.shortcodeName , extra);

            });

            if(this.forceUpdate === true)
                return ;

            this.postButtonIdUpdate( dataElement );
            this.widgetButtonIdUpdate( dataElement );

            if( !_.isUndefined( dataElement.settingsType ) ){
                api.Events.trigger( dataElement.settingsType + "SettingsType" , dataElement , extra );
            }

            api.Events.trigger(  "after_shortcode_update_setting" , dataElement.shortcodeName );

            api.Events.trigger(  "after_group_settings_update" , dataElement.shortcodeName );

            api.Events.trigger( dataElement.shortcodeName + "_dialog_settings" , extra.attrs || {} );
        },

        // , needToUpdateSettings
        openInitDialogSettings : function( sedDialog , forceOpen ){

            //needToUpdateSettings = !_.isUndefined( needToUpdateSettings) ? needToUpdateSettings : true ;

            this.sedDialog = sedDialog;

            this.currentSettingsId = sedDialog.selector;

            this.panelsNeedToUpdate = [];

            var reset =  !_.isUndefined( sedDialog.reset ) ? sedDialog.reset : true;

            var needToUpdate = !_.isUndefined( api.sedGroupControls[this.currentSettingsId] );

            api.sedDialogSettings.openInitDialogSettings( this.currentSettingsId , forceOpen , reset , "module" , "ajax" , this.currentSettingsId );//"html"

            /**
             * update settings if created current settings template already
             */
            if( needToUpdate ) {
                if (!_.isUndefined(sedDialog.data.panelId))
                    this.initSettings(sedDialog.data.panelId);
                else
                    this.initSettings();
            }

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
                if( _.isUndefined( api.sedDialogSettings.dialogsContents['sed_widget'] ) ){
                    $(this).click(function(){
                        var widgetIdBase = $(this).data("widgetIdBase");
                        if(!widgetIdBase)
                            return false;

                        self.openWidgetSettings( widgetIdBase );

                    });
                }
            });

            $('[data-self-level-box="dialog-page-box-widgets-settings"] > .icon-close-level-box').livequery(function(){
                if( _.isUndefined( api.sedDialogSettings.dialogsContents['sed_widget'] ) ){
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
                    if($.inArray(script[0] , api.sedAppLoadedScripts) == -1) {
                        return true;
                    }else {
                        return false;
                    }
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
                if( _.isUndefined( api.sedDialogSettings.dialogsContents[api.appModulesSettings.currentSettingsId] ) ){
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
                panelTpl = api.designEditorTpls[shortcodeName];

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

                $( panelTpl ).appendTo( this.dialogBoxContainer );

                delete api.designEditorTpls[shortcodeName];

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
                lvlBox = "modules_styles_settings_"+ shortcodeName +"_design_group_level_box";

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

        initSettings : function( styleId , selector ){ console.log( "--------api.stylesSettingsControls[styleId]-----" , api.stylesSettingsControls[styleId] );
            var self = this;

            //this.updateStyleNeeded = true;

            _.each( api.stylesSettingsControls[styleId] , function( data ) {
                self.updateSettings( data.control_id , data , selector );
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

            var control = api.control.instance( id );

            if( $.inArray( id , _.keys( api.settings.controls ) ) == -1 || ! control ){  //&& $el.length > 0

                var cValue = this.getCurrentValue( id , data , selector  );

                if( !_.isNull( cValue ) ){
                    data.default_value = _.clone( cValue );
                }

                api.Events.trigger( "renderSettingsControls" , id, data );

                control = api.control.instance( id );

                $( control.container ).parents(".row_settings:first").show();

            } else {

                $( control.container ).parents(".row_settings:first").show();

                var cValue = this.getCurrentValue( id , data , selector  );
                                         //alert( cValue );
                if( !_.isNull( cValue ) ){
                    control.update( cValue );
                }else{
                    control.update( );
                }

            }

        }


    });

    $( function() {

        api.sedDialogSettings       = new api.SiteEditorDialogSettings({});

        api.appModulesSettings      = new api.AppModulesSettings({});

        api.appWidgetsSettings      = new api.AppWidgetsSettings({});

        api.appStyleEditorSettings  = new api.AppStyleEditorSettings({});

        /*var generalStyleEditor;
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

                api.currentTargetElementId = "site-editor-page-part";
                var targetEl =  "#site-editor-page-part" ,
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
        });*/

    });

})( sedApp, jQuery );