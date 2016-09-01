siteEditor.PluginManager.add('subThemes', function(siteEditor) {

    var api = siteEditor.sedAppClass.editor ,
        $ = siteEditor.dom.Sizzle;

    api.mainThemeContentModel = {};

    api.AppSubThemes = api.Class.extend({
        initialize: function( options ){

            $.extend( this, options || {} );

            this.subThemesContent;
            this.mainThemeContentModel;
            this.currentMainContentModule = {};
            this.copyPagesThemeContent;
            this.mainContentThemeId;
            this.mainContentModels;
            this.currentSubThemesContent = {};
            this.sedThemeOptions = {};
            this.currentSubTheme;
            this.currentPageId;

            this.ready();
        },

        getThemeIds : function(){

        },

        ready : function(){
            var self = this;


            api.previewer.bind("currentPageSubTheme" , function( info ){
                self.currentSubTheme              = info.subTheme;
                self.currentPageId                = info.page_id;
                api.settings.page.defaultSubTheme = info.subTheme;
                var control = api.control.instance( "page_layout" );
                control.update( info.subTheme );
            });

            api.bind( 'save' , function(){
                api.pagesThemeContent[api.settings.page.id] = self.copyPagesThemeContent;
                self.currentSubThemesContent = {};
                api.previewer.send( "sedSave" );
            });

            api.previewer.bind( 'currentMainContentModule' , function( model ){
                self.currentMainContentModule.module = model.module;
                self.currentMainContentModule.theme_id = model.themeId;
            });

            api.previewer.bind( 'page_mce_used_fonts' , function( fonts ){
                api('page_mce_used_fonts').set( fonts );   console.log("fonts------" , fonts);
            });

            api.previewer.bind( 'sed_last_theme_id' , function( lastThemeId ){
                api('sed_last_theme_id').set( lastThemeId );

                //api.log( "lastThemeId-------- : " , lastThemeId );
            });

            api.previewer.bind( 'page_theme_rows_orders' , function( customOrders ){
                api('page_theme_rows_orders').set( customOrders );

                //api.log( "customOrders-------- : " , customOrders );
            });

            api.previewer.bind( 'sub_themes_models_update' , function( subThemes ){

                //api.log( "subThemes-------- : " , subThemes );

                api('sed_layouts_models').set( subThemes );

                var subThemesArr = _.values( subThemes ),
                    ids = [];

                _.each( subThemesArr , function( subTheme ){
                    ids = $.merge( ids , _.pluck( subTheme , "theme_id") );
                });

                _.uniq( ids );

                var pageOrders = api('page_theme_rows_orders')();

                $.each( pageOrders , function( themeId , order ){
                    if( $.inArray( themeId , ids ) == -1 )
                        delete pageOrders[themeId];
                });

                api('page_theme_rows_orders').set( pageOrders );

                $.each( self.subThemesContent , function( themeId , content ){
                    if( $.inArray( themeId , ids ) == -1 ){
                        delete self.subThemesContent[themeId];
                        delete self.sedThemeOptions[themeId];
                    }
                });

                var currentMainIds = _.pluck( self.mainThemeContentModel, 'theme_id' );
                _.uniq( currentMainIds );

                $.each( currentMainIds , function( idx , themeId ){
                    if( $.inArray( themeId , ids ) == -1 ){
                        self.mainThemeContentModel = _.filter( self.mainThemeContentModel , function( model ){
                            if(model.theme_id == themeId)
                                return false
                            else
                                return true;
                        });
                    }

                });

            });

            api.Events.bind("beforeRefresh" , function(){

                if( api('page_layout')() && self.currentSubTheme != api('page_layout')() ){

                    var subThemesModels = api('sed_layouts_models')(),
                        nextModel = subThemesModels[api('page_layout')()] ,
                        currModel = subThemesModels[self.currentSubTheme] ,
                        ids = _.pluck( nextModel , "theme_id") ,
                        findMainContent = false ,
                        nextSubThemeMode = "normal" ,
                        nextMainThemeId,
                        currSubThemeMode = "normal" ,
                        isOverride = api('changed_sub_theme_override')() ,
                        mainShortcodeId;

                    _.each( nextModel , function( row ){

                        if( row.main_row ){
                            nextSubThemeMode = "has_main_content";
                            nextMainThemeId = row.theme_id;
                        }

                    });

                    var _removeExclude = function( theme_id ){
                        currModel = _.map( currModel , function( row ){

                          if( row.theme_id == theme_id ){
                              var index = $.inArray( self.currentPageId , row.exclude );

                              if( index != -1 )
                                  row.exclude.splice( index , 1 );
                          }

                          return row;

                        });
                    };

                    var _addExclude = function( theme_id ){
                        nextModel = _.map( nextModel , function( row ){

                            if( row.theme_id == theme_id ){
                                var index = $.inArray( self.currentPageId , row.exclude );

                                if( index == -1 )
                                    row.exclude.push( self.currentPageId );
                            }

                            return row;

                        });
                    };

                    _.each( api.pagesThemeContent[api.settings.page.id] , function( shortcode , index ){

                        if(  !_.isUndefined( shortcode.attrs ) &&  !_.isUndefined( shortcode.attrs.sed_main_content_row ) ){

                            mainShortcodeId = shortcode.id;

                            if( !_.isUndefined( shortcode.theme_id ) && !_.isUndefined( shortcode.is_customize ) )
                                currSubThemeMode = "is_customize";
                            else if( !_.isUndefined( shortcode.theme_id ) && _.isUndefined( shortcode.is_customize ) )
                                currSubThemeMode = "has_main_content";


                            if( currSubThemeMode == "normal" && nextSubThemeMode == "has_main_content" ){
                                if( !isOverride ){
                                    shortcode.is_customize = true;
                                    shortcode.theme_id = nextMainThemeId;
                                    _addExclude( nextMainThemeId );
                                }

                            }else if( currSubThemeMode == "is_customize" && nextSubThemeMode == "normal" ){
                                _removeExclude( shortcode.theme_id );
                                delete shortcode.theme_id;
                                delete shortcode.is_customize;
                            }else if( currSubThemeMode == "is_customize" && nextSubThemeMode == "has_main_content" && $.inArray( shortcode.theme_id , ids ) == -1 ){
                                if( !isOverride ){
                                    _removeExclude( shortcode.theme_id );
                                    shortcode.is_customize = true;
                                    shortcode.theme_id = nextMainThemeId;
                                    _addExclude( nextMainThemeId );
                                }
                            }

                        }else if( !_.isUndefined( shortcode.theme_id ) && !_.isUndefined( shortcode.is_customize ) ){

                            _removeExclude( shortcode.theme_id );

                            if( $.inArray( shortcode.theme_id , ids ) == -1 ){
                                delete shortcode.theme_id;
                                delete shortcode.is_customize;
                            }else{
                                _addExclude( shortcode.theme_id );
                            }
                        }


                    });

                    //console.log( "api('sed_layouts_models')()-----" , api('sed_layouts_models')() );
                    api("changed_sub_theme_mode").set( currSubThemeMode );

                    if( currSubThemeMode == "is_customize" && nextSubThemeMode == "has_main_content" && $.inArray( mainShortcodeId , ids ) == -1 ){
                        if( isOverride ){
                            _removeExclude( mainShortcodeId );
                        }
                    }else if( currSubThemeMode == "is_customize" && nextSubThemeMode == "has_main_content" && $.inArray( mainShortcodeId , ids ) != -1 ){
                        _removeExclude( mainShortcodeId );
                        _addExclude( mainShortcodeId );
                    }

                    if( currSubThemeMode == "normal" && nextSubThemeMode == "has_main_content" || ( currSubThemeMode == "is_customize" && nextSubThemeMode == "has_main_content" && $.inArray( mainShortcodeId , ids ) == -1 ) ){
                        if( isOverride ){
                            var tChildren = self.findAllTreeChildrenShortcode( api.pagesThemeContent[api.settings.page.id] , mainShortcodeId ),
                                newIndex = self.getShortcodeIndex(mainShortcodeId , api.pagesThemeContent[api.settings.page.id] );

                            self.deleteModule( mainShortcodeId , tChildren , api.pagesThemeContent[api.settings.page.id] , newIndex );
                        }

                    }



                    api("changed_sub_theme").set( true );
                }else
                    api("changed_sub_theme").set( false );

                self.createThemeContent();
                self.createThemeOptions();

                if( api('page_layout')() && self.currentSubTheme != api('page_layout')() ){
                    var sed_page_customized = api.get();

                    _.each( nextModel , function( row ){

                        if( $.inArray( self.currentPageId , row.exclude ) == -1 ){
                            sed_page_customized = $.extend( true , sed_page_customized , self.sedThemeOptions[row.theme_id] || {} );
                        }

                    });

                }

            });

            api.Events.bind("beforeSave" , function(){
                api("changed_sub_theme").set( false );
                self.createThemeContent();
                self.createThemeOptions();
            });

            var firstTimeActive = true;
            api.previewer.bind( 'previewerActive', function( ) {
                if( firstTimeActive === true ){

                    self.subThemesContent = _.isEmpty( api('sed_layouts_content')() ) ? {} : api('sed_layouts_content')();
                    self.mainThemeContentModel = api( 'sed_main_theme_content' )();

                    self.sedThemeOptions = _.isEmpty( api('sed_theme_options')() ) ? {} : api('sed_theme_options')();

                    firstTimeActive = false;
                }

                //console.log( "self.sedThemeOptions ----- , " , self.sedThemeOptions );
                //console.log( "api('sed_layouts_models')() , " , api('sed_layouts_models')() );


            });

        },


        getOptions : function( models ,sed_pb_modules_ids , sed_page_customized , styleEditorSettings , sed_pb_modules ){
            var shortcodes = $.extend( true, {} , models ) ,
                options = {} ,
                shortcodeIds = _.chain(shortcodes)
                .pluck("id")
                .uniq()
                .value();

            _.each( sed_pb_modules_ids , function( id ){
                if( $.inArray( id , shortcodeIds ) != -1 ){
                    if( _.isUndefined( options['sed_pb_modules'] ) )
                        options['sed_pb_modules'] = {};

                    options['sed_pb_modules'][id] = sed_pb_modules[id];
                }
            });


            /*var reg = "#(" + shortcodeIds.join("|") + ")\s+" ,
                patt = new RegExp( reg , 'i' );
                patt.test( selector ); */


            _.each( styleEditorSettings , function( id ){

                var values = sed_page_customized[id],
                    selectors = _.keys( values );

                _.each( selectors , function( selector ){
                    _.each( shortcodeIds , function( sid ){
                        var str = "#" + sid;  //console.log( "selector : " , selector );
                        if( str == selector || selector.indexOf( str + " " ) > -1 ){

                            if( _.isUndefined( options[id] ) )
                                options[id] = {};

                            options[id][selector] = sed_page_customized[id][selector];
                        }
                    });
                });

            });

            shortcodes = _.chain(shortcodes)
              .pluck("tag")
              .uniq()
              .value();

            var rowSettings = [];

            _.each( shortcodes , function( shortcode ){
                var settings = _.pluck( api.modulesSettingsControls[shortcode] , "settings" );
                settings =  _.chain( settings )
                .map( function( setting ){
                    return setting["default"];
                })
                .filter(function( setting ){
                    if( api.settings.settings[setting].type == "general" )
                        return true;
                    else
                        return false;
                })
                .uniq()
                .value();

                rowSettings = $.merge( rowSettings , settings );

            });

            $.each( _.uniq( rowSettings ) , function( idx , setting ){
                options[setting] = sed_page_customized[setting];
            });

            var mceFonts = {};
            $.each( api('page_mce_used_fonts')() , function( editorId , fonts ){
                if( $.inArray( editorId , shortcodeIds ) != -1 ){
                    mceFonts[editorId] = fonts;
                }
            });

            options['page_mce_used_fonts'] = mceFonts;
                   console.log( "mceFonts------------" , mceFonts );
            return options;
        },

        createThemeOptions : function(){
            var startTime = new Date() ,
             self = this ,
             sed_pb_modules = api( 'sed_pb_modules' )() ,
             sed_pb_modules_ids = _.keys( sed_pb_modules ) ,
             sed_page_customized = api.get(),
             settingsIds = _.keys( api.settings.settings ),
             styleEditorSettings = _.chain( settingsIds )
            .filter(function( id ){
                if( api.settings.settings[id].type == "style-editor" )
                    return true;
                else
                    return false;
            })
            .value();


            $.each( this.currentSubThemesContent , function( themeId , models ){

                var options = self.getOptions( models ,sed_pb_modules_ids , sed_page_customized , styleEditorSettings , sed_pb_modules );

                //console.log("theme_options----" , themeId + "------ : " , options );

                var mainContentModels = _.filter( self.mainThemeContentModel , function( model ){
                    return model.theme_id == themeId;
                });

                if( mainContentModels.length > 0 ){
                    _.each( mainContentModels , function( model ){
                        if( !_.isUndefined( model.options ) )
                            options = $.extend( true, options , model.options );
                    });
                }

                //console.log("theme_options_final----" , themeId + "------ : " , options );

                self.sedThemeOptions[themeId] = options;

            });

            api( 'sed_theme_options' ).set( self.sedThemeOptions );

            /*if( !_.isUndefined( api('background_color')()['body'] ) ){
                api('background_color')()['default'] = api('background_color')()['body'];
                delete  api('background_color')()['body']; alert( api('background_color')()['default'] );
            }

            if( !_.isUndefined( api('background_position')()['body'] ) ){
                api('background_position')()['default'] = api('background_position')()['body'];
                delete  api('background_position')()['body']; alert( api('background_position')()['default'] );
            }

            if( !_.isUndefined( api('background_attachment')()['body'] ) ){
                api('background_attachment')()['default'] = api('background_attachment')()['body'];
                delete  api('background_attachment')()['body']; alert( api('background_attachment')()['default'] );
            }

            if( !_.isUndefined( api('background_image_scaling')()['body'] ) ){
                api('background_image_scaling')()['default'] = api('background_image_scaling')()['body'];
                delete  api('background_image_scaling')()['body']; alert( api('background_image_scaling')()['default'] );
            }

            if( !_.isUndefined( api('background_image')()['body'] ) ){
                api('background_image')()['default'] = api('background_image')()['body'];
                delete  api('background_image')()['body']; alert( api('background_image')()['default'] );
            }

            if( !_.isUndefined( api('parallax_background_image')()['body'] ) ){
                api('parallax_background_image')()['default'] = api('parallax_background_image')()['body'];
                delete  api('parallax_background_image')()['body']; alert( api('parallax_background_image')()['default'] );
            }

            if( !_.isUndefined( api('parallax_background_ratio')()['body'] ) ){
                api('parallax_background_ratio')()['default'] = api('parallax_background_ratio')()['body'];
                delete  api('parallax_background_ratio')()['body']; alert( api('parallax_background_ratio')()['default'] );
            }

            if( !_.isUndefined( api('background_gradient')()['body'] ) ){
                api('background_gradient')()['default'] = api('background_gradient')()['body'];
                delete  api('background_gradient')()['body']; alert( api('background_gradient')()['default'] );
            }*/

            //console.log( "rowShortcodes : ------ : " , rowShortcodes );

            //console.log( "createThemeOptions : ------ : " , new Date() - startTime );
        },

        createThemeContent : function(){

            var self = this ,
                pagesThemeContent = _.map( api.pagesThemeContent[api.settings.page.id] , _.clone);

            _.each( pagesThemeContent , function( shortcode , index ){
                if( !_.isUndefined( shortcode.theme_id ) && _.isUndefined( shortcode.is_customize ) ){

                    if( !_.isUndefined( shortcode.attrs ) &&  !_.isUndefined( shortcode.attrs.sed_main_content_row ) ){
                        var mainContentModel , mIndex;
                        _.each( pagesThemeContent , function( shortcode , idx ){
                            if( !_.isUndefined( shortcode.attrs ) && !_.isUndefined( shortcode.attrs.sed_main_content ) && shortcode.attrs.sed_main_content ){
                                mainContentModel = shortcode;
                                mIndex = idx;
                            }
                        });

                        var mTChildren = self.findAllTreeChildrenShortcode( api.pagesThemeContent[api.settings.page.id] , mainContentModel.id ),
                            mainContentShortcodes = _.map( mTChildren , _.clone);

                        mainContentShortcodes.unshift( mainContentModel );

                        //api.mainThemeContentModel.content = mainContentShortcodes;
                        self.createMainThemeContentModel( mainContentShortcodes );

                        self.mainContentThemeId = shortcode.theme_id;

                        self.mainContentModels = _.map( mainContentShortcodes , _.clone);

                        self.deleteModule( mainContentModel.id , mTChildren , api.pagesThemeContent[api.settings.page.id] , false );

                    }

                    var tChildren = self.findAllTreeChildrenShortcode( api.pagesThemeContent[api.settings.page.id] , shortcode.id ),
                        rowShortcodes = _.map( tChildren , _.clone);

                    rowShortcodes.unshift( shortcode );

                    self.subThemesContent[shortcode.theme_id] = rowShortcodes;

                    self.currentSubThemesContent[shortcode.theme_id] = rowShortcodes;

                    var newIndex = self.getShortcodeIndex(shortcode.id , api.pagesThemeContent[api.settings.page.id] );

                    self.deleteModule( shortcode.id , tChildren , api.pagesThemeContent[api.settings.page.id] , newIndex );

                }
            });

            var newPagesThemeContent = _.map( api.pagesThemeContent[api.settings.page.id] , _.clone) ,
                newThemeContent = [];

            _.each( newPagesThemeContent , function( shortcode ){
                if( shortcode.parent_id == "root" ){
                    var tChildren = self.findAllTreeChildrenShortcode( api.pagesThemeContent[api.settings.page.id] , shortcode.id ),
                        rowShortcodes = _.map( tChildren , _.clone);

                    rowShortcodes.unshift( shortcode );

                    newThemeContent.push( rowShortcodes );

                }
            });
                           //console.log("self.subThemesContent-----:" , self.subThemesContent );
            api.pagesThemeContent[api.settings.page.id] = newThemeContent;
                           //console.log("newThemeContent-----:" , newThemeContent );

            api( 'theme_content' ).set( api.pagesThemeContent[api.settings.page.id] );

            self.copyPagesThemeContent = pagesThemeContent;

            api( 'sed_layouts_content' ).set( self.subThemesContent );
        },

        getShortcodeIndex: function( id , contentModel ){

            var index;

            $.each(contentModel , function(i , shortcode){
                if(shortcode.id == id){
                    index = i;
                    return false;
                }
            });

            return index;
        },

        deleteModule : function(parent_id , tChildren , contentModel , index ){

            for(var j=0; j < tChildren.length ; j++) {
                for(var i=0; i < contentModel.length ; i++) {
                    var shortcode = contentModel[i];

                    if( shortcode.id == tChildren[j].id ){
                        contentModel.splice( i , 1 );
                        break;
                    }
                }
            }

            if(index !== false)
                contentModel.splice( index , 1 );

        },

        findAllTreeChildrenShortcode: function( shortcodesModels , parent_id ){
            var self = this , allChildren = [];

            $.each( shortcodesModels , function(index , shortcode){
                if(shortcode.parent_id == parent_id){
                    allChildren.push(shortcode);
                    allChildren = $.merge( allChildren , self.findAllTreeChildrenShortcode( shortcodesModels , shortcode.id  ) );
                }
            });

            return allChildren;
        },

        createMainThemeContentModel : function( mainContentShortcodes ){
            var self = this , find = false ,
                sed_pb_modules = api( 'sed_pb_modules' )() ,
                sed_pb_modules_ids = _.keys( sed_pb_modules ) ,
                sed_page_customized = api.get(),
                settingsIds = _.keys( api.settings.settings ),
                styleEditorSettings = _.chain( settingsIds )
                .filter(function( id ){
                  if( api.settings.settings[id].type == "style-editor" )
                      return true;
                  else
                      return false;
                })
                .value(),
                options = self.getOptions( mainContentShortcodes ,sed_pb_modules_ids , sed_page_customized , styleEditorSettings , sed_pb_modules );

                     //console.log("main_theme_options----" , this.currentMainContentModule.theme_id + "------ : " , options );
            if( self.mainThemeContentModel.length > 0 ){

                self.mainThemeContentModel = _.map( self.mainThemeContentModel , function( model ){
                    if( model.module == self.currentMainContentModule.module && model.theme_id == self.currentMainContentModule.theme_id ){
                        model.content = mainContentShortcodes;
                        model.options = options;
                        find = true;
                        return model;
                    }else
                        return model;
                });

            }

            if( find === false || self.mainThemeContentModel.length == 0 ){
                self.mainThemeContentModel.push( {
                    content     : mainContentShortcodes ,
                    module      : this.currentMainContentModule.module ,
                    theme_id    : this.currentMainContentModule.theme_id ,
                    options     : options
                });
            }

            api( 'sed_main_theme_content' ).set( self.mainThemeContentModel );
               //console.log( "self.mainThemeContentModel ----------" , self.mainThemeContentModel );
        }

    });


    /*api.generalThemeOptions = {

      "sheet_width" : {
         scope : {
            sub_themes : ["portfolio" , "portfolio_single"] ,
            exclude    : []
         },
         value : 1100
      },

      "page_length" : {
         scope : {
            sub_themes : ["archive" , "single_post"] ,
            exclude    : []
         },
         value : 1100
      },

    };*/

    api.GeneralSettingsScope = api.Class.extend({
        initialize: function( options ){

            $.extend( this, options || {} );

            this.currentSubTheme;
            this.currentPageId;
            this.models = {};
            this.initSettings = true;

            var paddings    = ["padding_top" , "padding_bottom" , "padding_left" , "padding_right" ],
                //margins     = ["margin_top" , "margin_bottom" , "margin_left" ,  "margin_right" ],
                backgrounds = ["background_color" , "background_image" , "parallax_background_image" , "parallax_background_ratio" , "background_attachment" , "background_image_scaling" , "background_position" , "background_gradient" ];

            this.generalSettingsIds = $.merge( $.merge( paddings , backgrounds ) , ["sheet_width" , "page_length"]);

            //console.log("this.generalSettingsIds----" , this.generalSettingsIds);

            this.ready();
        },

        ready : function(){
            var self = this , type ,
                customizeSelector = "#sed-general-settings-sub-themes .customize-settings-action" ,
                subThemeSelector = "#sed-general-settings-sub-themes .sub-theme-item" ,
                allSubThemeSelector = "#sed-general-settings-sub-themes li.item";

            api.previewer.bind("currentPageSubTheme" , function( info ){
                self.currentSubTheme = info.subTheme;
                self.currentPageId   = info.page_id;
                self.initSettings    = false;
                self.models          = _.isEmpty( api('sed_general_theme_options')() ) ? {} : $.extend( true , {} , api('sed_general_theme_options')() );
            });

            api.Events.bind("beforeRefresh" , function(){
                self.setSettingsValues();
            });

            api.Events.bind("beforeSave" , function(){
                self.setSettingsValues();
            });

            api.bind( 'save' , function(){
                if( !_.isUndefined( self.models["sheet_width"] ) && self.models["sheet_width"].scope.sub_themes.length > 0 ){
                    $(customizeSelector).show();
                    if( $.inArray( self.currentPageId , self.models["sheet_width"].scope.exclude ) == -1 ){
                    	$(customizeSelector).find(".sed-settings-theme-type").filter( function() {
                    		return this.value === "public";
                    	}).prop( 'checked', true );
                    }
                }
            });


            $( "#page_general_settings" ).click(function() {
                if(self.initSettings === false){
                    if( !_.isUndefined( self.models["sheet_width"] ) && self.models["sheet_width"].scope.sub_themes.length > 0 ){
                        $(customizeSelector).show();

                        if( $.inArray( self.currentPageId , self.models["sheet_width"].scope.exclude ) == -1 ){
                            type = "public";
                        }else{
                            type = "private";
                        }

                    	$(customizeSelector).find(".sed-settings-theme-type").filter( function() {
                    		return this.value === type;
                    	}).prop( 'checked', true );

                        if(type == "private")
                            $(allSubThemeSelector).hide();
                        else
                            $(allSubThemeSelector).show();

                        $(subThemeSelector).find('[name="sed-sub-theme"]').each(function(){
                            if( $.inArray( $(this).val() , self.models["sheet_width"].scope.sub_themes) != -1 ){
                                $(this).prop( "checked" , true);
                            }else{
                                $(this).prop( "checked" , false);
                            }
                        });

                        self.checkAll();

                    }else{
                        $(customizeSelector).hide();
                    }

                    self.initSettings = true;
                }
            });

            $(subThemeSelector).livequery(function(){
                $(this).find('.sed-sub-themes-check-box').click(function( e ){

                    var subTheme = $(this).find('[name="sed-sub-theme"]').val() ;

                    if( $(this).find('[name="sed-sub-theme"]').is(":checked") ){

                        _.each( self.generalSettingsIds , function( id ){
                            self.addOptionsToModel( id , api.settings.settings[id].type , subTheme );
                        });

                        if( self.currentSubTheme != subTheme && $.inArray( self.currentSubTheme , self.models["sheet_width"].scope.sub_themes ) == -1 ){
                            var selector = '[data-sub-theme-name="'+ self.currentSubTheme +'"] input[name="sed-sub-theme"]';
                            $(this).parent().siblings().find(selector).prop("checked" , true);
                            _.each( self.generalSettingsIds , function( id ){
                                self.addOptionsToModel( id , api.settings.settings[id].type , self.currentSubTheme );
                            });
                        }


                    }else{

                        _.each( self.generalSettingsIds , function( id ){
                            self.removeOptionsFromModel( id , subTheme );
                        });


                        if( self.currentSubTheme == subTheme ){

                            var subThemes = _.map( self.models["sheet_width"].scope.sub_themes , _.clone ),
                                $element = $(this);
                            _.each( subThemes , function( subT ){
                                var selector = '[data-sub-theme-name="'+ subT +'"] input[name="sed-sub-theme"]';
                                $element.parent().siblings().find(selector).prop("checked" , false);
                                _.each( self.generalSettingsIds , function( id ){
                                    self.removeOptionsFromModel( id , subT );
                                });
                            });

                        }

                    }

                    self.checkAll();

                });
            });


            //when click on radio button
            $(customizeSelector).livequery(function(){
                var el = $(this);
                $(this).find(".sed-settings-theme-type").click(function( e ){

                    var value = $(this).val();

                    if( value == "public" ){

                        $(allSubThemeSelector).show();

                        _.each( self.generalSettingsIds , function( id ){
                            self.updateExcludeSettings( id , "remove" );
                        });

                    }else {//"private" ,
                        $(allSubThemeSelector).hide();

                        _.each( self.generalSettingsIds , function( id ){
                            self.updateExcludeSettings( id , "add" );
                        });

                    }

                });
            });

            $("#sed-general-settings-sub-themes .sed-all-sub-themes").find('[name="sed-sub-theme"]').click(function( e ){

                var allIsChecked = $(this).is(":checked");

                $(subThemeSelector).find('[name="sed-sub-theme"]').each(function(){

                    var subTheme = $(this).val();

                    if( allIsChecked ){

                        if( !$(this).is(":checked") ){

                            $(this).prop( "checked" , true );

                            _.each( self.generalSettingsIds , function( id ){
                                self.addOptionsToModel( id , api.settings.settings[id].type , subTheme );
                            });
                        }

                    }else{

                        if( $(this).is(":checked") ){

                            $(this).prop( "checked" , false );

                            _.each( self.generalSettingsIds , function( id ){
                                self.removeOptionsFromModel( id , subTheme );
                            });

                        }

                    }

                });

            });


        },

        checkAll : function(){
            var self= this , subThemeSelector = "#sed-general-settings-sub-themes .sub-theme-item" ;
            if( !_.isUndefined( self.models["sheet_width"] ) && self.models["sheet_width"].scope.sub_themes.length == $(subThemeSelector).find('[name="sed-sub-theme"]').length ){
                $("#sed-general-settings-sub-themes .sed-all-sub-themes").find('[name="sed-sub-theme"]').prop( "checked" , true);
            }else{
                $("#sed-general-settings-sub-themes .sed-all-sub-themes").find('[name="sed-sub-theme"]').prop( "checked" , false);
            }
        },


        setSettingsValues : function(){
             var self = this;

             $.each( this.models , function( settingId , data ){

                var index = $.inArray( self.currentPageId , self.models[settingId].scope.exclude );

                if( index > -1 )
                    return false;

                var value;
                if(data.type == "style-editor"){
                    if( _.isUndefined( api( settingId )()["#page"] ) ){
                        value = api( settingId )()["default"];
                    }else if( _.isObject( api( settingId )()["#page"] ) ){
                        value = _.clone( api( settingId )()["#page"] );
                    }else{
                        value = api( settingId )()["#page"];
                    }
                }else{
                    value = api( settingId )();
                }

                self.models[settingId].value = value;
             });
                         console.log( "this.models----now---------" , this.models );

             api( 'sed_general_theme_options' ).set( $.extend( true , {} , this.models ) );
        },

        updateExcludeSettings : function( settingId , type ){
            if( type == "add" ){
                var index = $.inArray( this.currentPageId , this.models[settingId].scope.exclude );

                if( index == -1 )
                    this.models[settingId].scope.exclude.push( this.currentPageId );

            }else if( type == "remove" ){
                var index = $.inArray( this.currentPageId , this.models[settingId].scope.exclude );
                         alert( index );
                if( index != -1 )
                    this.models[settingId].scope.exclude.splice( index , 1 );


            }

            this.setSettingsValues();


        },

        addOptionsToModel : function( settingId , settingType , subtheme ){

            if( _.isUndefined( this.models[settingId] ) ){
                this.models[settingId] = {
                   scope : {
                      sub_themes : [subtheme] ,
                      exclude    : []
                   },
                   value : "" ,
                   type  : settingType
                };
            }else{
                if( $.inArray( settingId , this.models[settingId].scope.sub_themes ) == -1 )
                    this.models[settingId].scope.sub_themes.push( subtheme );

            }

            this.setSettingsValues();
            //console.log( "this.models------" , this.models );

        },

        removeOptionsFromModel : function( settingId , subtheme ){

            var index = $.inArray( subtheme , this.models[settingId].scope.sub_themes ),
                customizeSelector = "#sed-general-settings-sub-themes .customize-settings-action";

            if( index > -1 ){
                this.models[settingId].scope.sub_themes.splice( index , 1 )
            }

            if(this.models[settingId].scope.sub_themes.length == 0 ){
                delete this.models[settingId];
                $(customizeSelector).hide();
            }

            this.setSettingsValues();

        },

    });



    $( function() {

        api.appSubThemes = new api.AppSubThemes({});

        api.generalSettingsScope = new api.GeneralSettingsScope({});

    });

});