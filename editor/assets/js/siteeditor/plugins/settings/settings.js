(function( exports, $ ){

    var api = sedApp.editor;

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

            var self = this;

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
        ready : function(){},

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

                var $currentElDialog = self.dialogsContents[self.currentSettingsId].appendTo( $( selector ) );
                self.dialogsTitles[self.currentSettingsId].appendTo( $( selector ).siblings(".ui-dialog-titlebar:first") );

                api.Events.trigger( "afterAppendSettingsTmpl" , $currentElDialog , this.settingsType , this.currentSettingsId );

                if( reset === true )
                    $( selector ).data('sed.multiLevelBoxPlugin')._reset();

            }else{

                if( this.templateType == "ajax" ) {
                    this._ajaxLoadSettings();
                }else if( this.templateType == "html" ){

                    var $currentElDialog = $( $("#sed-tmpl-dialog-settings-" + self.currentSettingsId ).html() ).appendTo( $( selector ) );

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

            api.Events.trigger( "beforeResetSettingsTmpl" , self.currentSettingsId , this.settingsType );

            self.dialogsTitles[self.currentSettingsId] = $( selector ).siblings(".ui-dialog-titlebar:first").children(".multi-level-box-title").detach();
            self.dialogsContents[self.currentSettingsId] = $( selector ).children().detach();

            api.Events.trigger( "afterResetSettingsTmpl" , self.currentSettingsId , this.settingsType );

        },

        /**
         *
         * @private
         */
        _ajaxLoadSettings : function( ){
            var initLayoutsControls = [],
                self = this,
                selector = this.dialogSelector;

            var data = {
                action          : 'sed_load_options',
                setting_id      : this.currentSettingsId,
                nonce           : api.settings.nonce.options.load,
                sed_page_ajax   : 'sed_options_loader'
            };

            data = api.applyFilters( 'sedAjaxLoadOptionsDataFilter' , data );

            var optionsAjaxloader = new api.Ajax({

                type: "POST",
                //url: api.settings.url.ajax,
                data : data,
                success : function(){

                    var output = this.response.data.output ,
                        controls = this.response.data.controls,
                        relations = this.response.data.relations;

                    var $currentElDialog = $( output ).appendTo( $( selector ) );

                    api.Events.trigger( "afterInitAppendSettingsTmpl" , $currentElDialog , self.settingsType , self.currentSettingsId );

                    $( selector ).data('sed.multiLevelBoxPlugin').options.innerContainer = $( selector ).find(".dialog-level-box-settings-container");
                    $( selector ).data('sed.multiLevelBoxPlugin')._render();

                    api.Events.trigger( "endInitAppendSettingsTmpl" , $currentElDialog , self.settingsType , self.currentSettingsId );

                    if( !_.isUndefined( relations ) && !_.isEmpty( relations ) && _.isObject( relations ) ){
                        var groupRelations = {};

                        groupRelations[self.currentSettingsId] = relations;

                        api.settingsRelations = $.extend( api.settingsRelations , groupRelations);
                        //console.log( " ---------api.settingsRelations2 ----------------- " , api.settingsRelations );
                    }

                    if( !_.isEmpty( controls ) ){
                        _.each( controls , function( data , id ){

                            if( $.inArray( id , initLayoutsControls ) == -1  ){
                                api.settings.controls[id] = data;
                                api.Events.trigger( "renderSettingsControls" , id , data );
                                initLayoutsControls.push( id );
                            }

                            /*var control = api.control.instance( id );

                             control.update( );*/

                        });
                        //console.log( " ---------ajax control load ----------------- " , controls );
                        api.Events.trigger(  "after_group_settings_update" , self.currentSettingsId );
                    }


                },
                error : function(){
                    alert( this.response.data.output );
                }

            },{
                container   : this.dialogSelector
            });

        },

    });

})( sedApp, jQuery );