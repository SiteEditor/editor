(function( exports, $ ) {

    var api = sedApp.editor ;

    api.layoutsRowsContent = api.layoutsRowsContent || {};

    api.AppLayouts = api.Class.extend({

        initialize: function( params , options ){
            var self = this;

            $.extend( this, options || {} );

            api.preview.bind( 'active', function() {
                self.initThemeRows();
            });

            this.ready();
        },

        initThemeRows : function(){
            var layoutModels = api('sed_layouts_models')(),
                self = this;

            this.currentLayout = !_.isEmpty(api('page_layout')()) ? api('page_layout')() : api.defaultPageLayout;

            _.map( api.contentBuilder.pagesThemeContent[this.postId], function(shortcode){
                if( !_.isUndefined( shortcode.theme_id )  ){
                    var rowElement = $('[sed_model_id="' + shortcode.id + '"]');

                    rowElement.data( "themeId" , shortcode.theme_id );
                    rowElement.addClass( "sed-public-theme-row" );


                    _.each(layoutModels[self.currentLayout], function (layoutModel) {
                        if (layoutModel.theme_id == shortcode.theme_id) {

                            rowElement.data("themeOrder", layoutModel.order);

                            return false;
                        }
                    });

                    if( !_.isUndefined( shortcode.is_hidden ) && shortcode.is_hidden === true ){
                        rowElement.addClass("sed-hidden-theme-row");
                    }else if( !_.isUndefined( shortcode.is_customize ) && shortcode.is_customize === true ){
                        rowElement.data( "isCustomize" , "yes" );
                    }

                }
            });

        },


        ready : function(){
            var self = this ,
                postId = $(".sed-site-main-part").data("postId");

            this.postId = postId;

            api.preview.bind("syncSedLayoutsContent" , function( to ){
                api.layoutsRowsContent = to;
            });

            api.preview.bind( "sedLayoutChangeScope" , function( obj ){
                var type = obj.type ,
                    elementId = obj.elementId ,
                    themeId = obj.themeId ;

                var rowElement = $('[sed_model_id="' + elementId + '"]').parents(".sed-pb-module-container:first").parent();

                switch( type ){
                    case "privateToPublic" :
                        self.privateToPublic( rowElement , themeId , elementId );
                    break;
                    case "publicToPrivate" :
                        self.publicToPrivate( rowElement , themeId , elementId );
                    break;
                    case "customizeToPublic" :
                        self.customizeToPublic( rowElement , themeId , elementId , obj.usingDataMode );
                    break;
                    case "hiddenToPublic" :
                        self.hiddenToPublic( rowElement , themeId , elementId );
                    break;
                    case "publicToCustomize" :
                        self.publicToCustomize( rowElement , themeId , elementId );
                    break;
                    case "hiddenToCustomize" :
                        self.hiddenToCustomize( rowElement , themeId , elementId );
                    break;
                    case "customizeToHidden" :
                        self.customizeToHidden( rowElement , themeId , elementId , obj.usingDataMode );
                    break;
                    case "publicToHidden" :
                        self.publicToHidden( rowElement , themeId , elementId );
                    break;
                }

            });

            api.preview.bind( 'active', function() {

                api.preview.send( "sedPagesLayoutsInfo" , {
                    defaultPageLayout    : api.defaultPageLayout ,
                    currentLayoutGroup   : api.currentLayoutGroup
                });

            });

            api.preview.bind( 'customThemeRowChangeType' , function( type ) {

                var elementId = api.currentSedElementId ,
                    rowEl = $( '[sed_model_id="' + elementId + '"]').parents(".sed-pb-module-container:first").parent() ,
                    rowId = rowEl.attr("sed_model_id");

                api.contentBuilder.pagesThemeContent[self.postId] = _.map( api.contentBuilder.pagesThemeContent[self.postId], function(shortcode){
                    if( shortcode.id == rowId ){

                        var relThemeId = "";

                        switch ( type ){
                            case "start":
                            case "end":
                                relThemeId = "";
                                break;
                            case "after":
                                relThemeId = self.getPrevClosestThemeRowId($('[sed_model_id="' + shortcode.id + '"]'));
                                break;
                            case "before":
                                relThemeId = self.getNextClosestThemeRowId($('[sed_model_id="' + shortcode.id + '"]'));
                                break;
                        }

                        shortcode.rel_theme_id  = relThemeId;
                        shortcode.row_type      = type;

                        return shortcode;
                    }else
                        return shortcode;
                });

                api.contentBuilder.sendData( "theme" );

            });

            api.preview.bind( "syncLayoutPublicRowsSort", function( order ) {

                var oldOrder = order.start ,
                    newOrder = order.end ,
                    currElm ,
                    currNewElm ,
                    newModelId ,
                    themeId ,
                    mainModelId ,
                    newThemeId;

                $( ".sed-site-main-part > .sed-row-pb" ).each( function( index , el ){
                    if( !_.isUndefined( $(this).data("themeOrder") ) ){

                        if( oldOrder == $(this).data("themeOrder") ){
                            themeId = $(this).data("themeId");
                            mainModelId = $(this).attr("sed_model_id");
                            currElm = $(this);
                        }else if( newOrder == $(this).data("themeOrder") ){
                            currNewElm = $(this);
                            newModelId = $(this).attr("sed_model_id");
                            newThemeId = $(this).data("themeId");
                        }
                        
                    }
                });

                if( newOrder > oldOrder ){

                    var afterRelModels = _.where( api.contentBuilder.pagesThemeContent[self.postId] , { rel_theme_id : newThemeId , row_type : "after" } );

                    if( afterRelModels.length > 0 ){

                        var lastAfterModelEl = $('[sed_model_id="' + afterRelModels[afterRelModels.length - 1].id + '"]');

                        lastAfterModelEl.after(currElm);

                    }else {
                        currNewElm.after(currElm);
                    }

                }else if( newOrder < oldOrder ){


                    var beforeRelModels = _.where( api.contentBuilder.pagesThemeContent[self.postId] , { rel_theme_id : newThemeId , row_type : "before" } );

                    if( beforeRelModels.length > 0 ){

                        var firstBeforeModelEl = $('[sed_model_id="' + beforeRelModels[0].id + '"]');

                        firstBeforeModelEl.before( currElm );

                    }else {
                        currNewElm.before(currElm);
                    }

                }

                var relModels = _.where( api.contentBuilder.pagesThemeContent[self.postId] , { rel_theme_id : themeId } ),
                    firstIndex ,
                    lengthGroup;

                if( ! _.isEmpty( relModels ) && $.isArray( relModels ) ) {
                    var prevAfterEl ,
                        existBefore = false ,
                        existAfter = false;

                    _.each(relModels, function (shortcode) {
                        if (shortcode.row_type == "before") {
                            currElm.before($('[sed_model_id="' + shortcode.id + '"]'));

                            existBefore = true;
                        } else {
                            if (_.isUndefined(prevAfterEl) || !prevAfterEl) {
                                currElm.after($('[sed_model_id="' + shortcode.id + '"]'));
                            } else {
                                prevAfterEl.after($('[sed_model_id="' + shortcode.id + '"]'));
                            }

                            prevAfterEl = $('[sed_model_id="' + shortcode.id + '"]');

                            existAfter = true;
                        }
                    });

                    if( existBefore === true ){
                        firstModelId = relModels[0].id;
                    }else{
                        firstModelId = mainModelId;
                    }

                    if( existAfter === true ){
                        lastModelId = relModels[relModels.length - 1].id;
                    }else{
                        lastModelId = mainModelId;
                    }

                }else{
                    firstModelId = mainModelId;
                    lastModelId = mainModelId;
                }

                var firstIndex = api.sedShortcode.index( firstModelId , api.contentBuilder.pagesThemeContent[self.postId] ),
                    lastIndex = ( firstModelId == lastModelId ) ? firstIndex : api.sedShortcode.index( lastModelId , api.contentBuilder.pagesThemeContent[self.postId] ),
                    lastModuleModels = api.sedShortcode.findModelsById( api.contentBuilder.pagesThemeContent[self.postId] , lastModelId ) ,
                    lengthGroup = lastIndex + lastModuleModels.length - firstIndex;

                var modelsGroup = api.contentBuilder.pagesThemeContent[self.postId].splice( firstIndex , lengthGroup );

                var newIndex = api.sedShortcode.index( newModelId , api.contentBuilder.pagesThemeContent[self.postId] );

                if( newOrder > oldOrder ){

                    if( afterRelModels.length > 0 ){

                        newIndex = api.sedShortcode.index( afterRelModels[afterRelModels.length - 1].id , api.contentBuilder.pagesThemeContent[self.postId] );

                        var newModuleModels = api.sedShortcode.findModelsById( api.contentBuilder.pagesThemeContent[self.postId] , afterRelModels[afterRelModels.length - 1].id );

                    }else{
                        var newModuleModels = api.sedShortcode.findModelsById( api.contentBuilder.pagesThemeContent[self.postId] , newModelId );
                    }


                    newIndex += newModuleModels.length;

                }else if( newOrder < oldOrder ){

                    if( beforeRelModels.length > 0 ){
                        newIndex = api.sedShortcode.index( beforeRelModels[0].id , api.contentBuilder.pagesThemeContent[self.postId] )
                    }

                }

                var args = $.merge([ newIndex , 0 ] , modelsGroup);

                Array.prototype.splice.apply( api.contentBuilder.pagesThemeContent[self.postId] , args );

                api.contentBuilder.sendData( "theme" );

                self.ordersRefresh( false );

            });

            api.Events.bind( "afterCreateModule" , function( moduleWrapper , moduleName , dropItem , direction ){
                if( moduleWrapper.parent().hasClass("sed-site-main-part") ){

                    api.contentBuilder.pagesThemeContent[self.postId] = _.map( api.contentBuilder.pagesThemeContent[self.postId], function(shortcode){
                        if( shortcode.id == moduleWrapper.attr("sed_model_id") ){

                            var info = self.getCustomRowInfoAfterCreate( moduleWrapper , dropItem , direction );

                            shortcode.rel_theme_id  = _.clone( info.themeId );
                            shortcode.row_type      = _.clone( info.type );

                            return shortcode;
                        }else
                            return shortcode;
                    });

                    api.contentBuilder.sendData( "theme" );

                    //change info for current custom theme row
                    api.preview.send( 'customThemeRowInfoChange' );
                }
            });

            api.Events.bind( "sedAfterDuplicate" , function( elementId , newElement ){

                if( newElement.parent().hasClass("sed-site-main-part") ){

                    var rowEl = $( '[sed_model_id="' + elementId + '"]').parents(".sed-pb-module-container:first").parent();

                    if( !_.isUndefined( rowEl.data("themeId") ) ) {
                        api.contentBuilder.pagesThemeContent[self.postId] = _.map(api.contentBuilder.pagesThemeContent[self.postId], function (shortcode) {
                            if( shortcode.id == newElement.attr("sed_model_id") ) {

                                if (!_.isUndefined(shortcode.is_customize))
                                    delete shortcode.is_customize;

                                if (!_.isUndefined(shortcode.is_hidden))
                                    delete shortcode.is_hidden;

                                shortcode.rel_theme_id = rowEl.data("themeId");
                                shortcode.row_type = "after";

                                delete shortcode.theme_id;

                                return shortcode;
                            } else
                                return shortcode;
                        });

                        api.contentBuilder.sendData("theme");

                        //change info for current custom theme row
                        api.preview.send( 'customThemeRowInfoChange' );
                    }
                }

            });

            this.removeThemeRow();

            this.sortThemeRow();

        },


        sortThemeRow : function(){

            var startInRoot, stopInRoot, self = this;

            api.Events.bind( "moduleSortableStartEvent" , function( ui ){
                var item = ui.item;
                if( item.parent().hasClass("sed-site-main-part") )
                    startInRoot = true;
                else
                    startInRoot = false;
            });

            api.Events.bind( "moduleSortableStopEvent" , function( ui , sortElement ){
                var item = ui.item ;
                if( item.parent().hasClass("sed-site-main-part") )
                    stopInRoot = true;
                else
                    stopInRoot = false;


                if( startInRoot === true && stopInRoot === true ){

                    self.ordersRefresh();

                    api.contentBuilder.pagesThemeContent[self.postId] = _.map( api.contentBuilder.pagesThemeContent[self.postId], function(shortcode){
                        if( item.attr("sed_model_id") == shortcode.id ){

                            var info = self.getCustomRowInfoAfterSort( item );

                            shortcode.rel_theme_id  = _.clone( info.themeId );

                            shortcode.row_type      = _.clone( info.type );

                            return shortcode;
                        }else
                            return shortcode;
                    });

                    api.contentBuilder.sendData( "theme" );

                    //change info for current custom theme row
                    api.preview.send( 'customThemeRowInfoChange' );

                }else if( startInRoot === true && stopInRoot === false ){
                    var themeId = false;

                    if( item.data("themeId") )
                        themeId = item.data("themeId");

                    if( themeId === false ){

                        self.ordersRefresh();

                        api.contentBuilder.pagesThemeContent[self.postId] = _.map( api.contentBuilder.pagesThemeContent[self.postId], function(shortcode){
                            if( !_.isUndefined( shortcode.rel_theme_id ) && item.attr("sed_model_id") == shortcode.id ){

                                delete shortcode.rel_theme_id;
                                delete shortcode.row_type;

                                return shortcode;

                            }else
                                return shortcode;
                        });

                        api.contentBuilder.sendData( "theme" );

                    }

                }else if( startInRoot === false && stopInRoot === true ){

                    api.contentBuilder.pagesThemeContent[self.postId] = _.map( api.contentBuilder.pagesThemeContent[self.postId], function(shortcode){
                        if( item.attr("sed_model_id") == shortcode.id ){

                            var info = self.getCustomRowInfoAfterSort( item );

                            shortcode.rel_theme_id  = _.clone( info.themeId );

                            shortcode.row_type      = _.clone( info.type );

                            return shortcode;

                        }else
                            return shortcode;
                    });

                    api.contentBuilder.sendData( "theme" );

                    //change info for current custom theme row
                    api.preview.send( 'customThemeRowInfoChange' );

                }
            });

        },


        removeThemeRow : function(){
            var self = this;

            var isRemoveRow = false ,
                removedThemeId = false;

            api.Events.bind( "sedBeforeRemove" , function( elementId ){

                if( $('[sed_model_id="' + elementId + '"]').parent().hasClass("sed-site-main-part") ){
                    isRemoveRow = true;

                    if( $('[sed_model_id="' + elementId + '"]').data("themeId") ){
                        removedThemeId = $('[sed_model_id="' + elementId + '"]').data("themeId");
                    }

                }

            });

            api.Events.bind( "sedAfterRemove" , function( elementId ){

                if( isRemoveRow === true ){
                    isRemoveRow = false;

                    if( removedThemeId !== false ){

                        self.removeRowFromLayouts( removedThemeId );
                        self.ordersRefresh();

                        api.contentBuilder.pagesThemeContent[self.postId] = _.map( api.contentBuilder.pagesThemeContent[self.postId], function(shortcode){
                            if( !_.isUndefined( shortcode.rel_theme_id ) && shortcode.rel_theme_id == removedThemeId ){

                                var info = self.getCustomRowInfo( shortcode.id , shortcode.row_type );

                                shortcode.rel_theme_id  = _.clone( info.themeId );

                                shortcode.row_type      = _.clone( info.type );

                                return shortcode;
                            }else
                                return shortcode;
                        });

                        api.contentBuilder.sendData( "theme" );

                        removedThemeId = false;
                    }

                }
            });

        },


        removeRowFromLayouts : function( themeId ){
            api.preview.send( "sedRemoveRowFromLayouts" , themeId );

            //remove from sed theme shortcodes content

        },


        privateToPublic : function( rowElement , themeId , elementId ){
            var self= this;

            rowElement.data( "themeId" , themeId );
            rowElement.addClass( "sed-public-theme-row" );
            this.ordersRefresh();

            var modelId = rowElement.attr("sed_model_id") ,
                currModel = api.contentBuilder.getShortcode( modelId ) ,
                relThemeId = _.clone( currModel.rel_theme_id ) ,
                row_type = _.clone( currModel.row_type );

            api.contentBuilder.pagesThemeContent[this.postId] = _.map( api.contentBuilder.pagesThemeContent[this.postId], function(shortcode){
                if( shortcode.id == rowElement.attr("sed_model_id") ){
                    rowShortcode = shortcode;

                    if( !_.isUndefined( shortcode.rel_theme_id ) ) {
                        delete shortcode.rel_theme_id;
                        delete shortcode.row_type;
                    }

                    shortcode.theme_id = themeId;

                    return shortcode;
                }else if( !_.isUndefined( shortcode.rel_theme_id ) && shortcode.rel_theme_id == relThemeId ) {

                    if( row_type == "after" ){

                        if( $('[sed_model_id="' + shortcode.id + '"]').prevAll('[sed_model_id="' + modelId + '"]').length == 1 ){
                            shortcode.rel_theme_id = themeId;
                        }

                    }else if( row_type == "before" ){

                        if( $('[sed_model_id="' + shortcode.id + '"]').nextAll('[sed_model_id="' + modelId + '"]').length == 1 ){
                            shortcode.rel_theme_id = themeId;
                        }
                    }

                    return shortcode;
                }else
                    return shortcode;
            });

            api.contentBuilder.sendData( "theme" );

        },


        publicToPrivate : function( rowElement , themeId ){
            var self= this;

            rowElement.removeData( "themeId" );
            rowElement.removeClass( "sed-public-theme-row" );

            rowElement.removeData( "themeOrder" );
            rowElement.removeData( "isCustomize" );
            rowElement.removeClass("sed-hidden-theme-row");

            this.ordersRefresh();

            api.contentBuilder.pagesThemeContent[this.postId] = _.map( api.contentBuilder.pagesThemeContent[this.postId], function(shortcode){
                if( shortcode.id == rowElement.attr("sed_model_id") && !_.isUndefined( shortcode.theme_id ) ){
                    delete shortcode.theme_id;

                    if( !_.isUndefined( shortcode.is_customize ) )
                        delete shortcode.is_customize;

                    if( !_.isUndefined( shortcode.is_hidden ) )
                        delete shortcode.is_hidden;

                    var info = self.getCustomRowInfo( shortcode.id , "before" );

                    shortcode.rel_theme_id  = _.clone( info.themeId );

                    shortcode.row_type      = _.clone( info.type );

                    return shortcode;
                }else if( !_.isUndefined( shortcode.rel_theme_id ) && shortcode.rel_theme_id == themeId ){

                    var info = self.getCustomRowInfo( shortcode.id , shortcode.row_type );

                    shortcode.rel_theme_id  = _.clone( info.themeId );

                    shortcode.row_type      = _.clone( info.type );

                    return shortcode;
                }else
                    return shortcode;
            });

            api.contentBuilder.sendData( "theme" );

            //change info for current custom theme row
            api.preview.send( 'customThemeRowInfoChange' );

        },


        customizeToPublic : function( rowElement , themeId , elementId , usingDataMode ){

            if( usingDataMode == "using_public_data" ){
                this.revertCustomizeToPublic( rowElement , themeId );

            }else if( usingDataMode == "using_customize_data" ){
                var rowId = rowElement.attr("sed_model_id");

                api.contentBuilder.pagesThemeContent[this.postId] = _.map( api.contentBuilder.pagesThemeContent[this.postId], function(shortcode){
                    if( shortcode.id == rowId && !_.isUndefined( shortcode.is_customize ) ){
                        delete shortcode.is_customize;
                        return shortcode;
                    }else
                        return shortcode;
                });

                this.ordersRefresh();
                api.contentBuilder.sendData( "theme" );
            }

        },


        revertCustomizeToPublic : function( rowElement , themeId , hide ){

            var rowId = rowElement.attr("sed_model_id"),
                layoutsContent = api.layoutsRowsContent ,
                rowContent = $.extend( true , {} , layoutsContent[themeId] ),
                order = parseInt( $( '[sed_model_id="' + rowId + '"]').data( "themeOrder" ) );

            hide = !_.isUndefined( hide ) ? hide : false;

            if( hide === true ){
                rowContent[0].is_hidden = true;
            }

            var newPattern = api.sedShortcode.clone( rowContent );

            newPattern.splice( 0 , 2);

            //create new pattern
            newPattern = api.pageBuilder.loadPattern( newPattern , rowContent[1].id );

            //set helper id for add shortcode pattern id
            newPattern = api.pageBuilder.setHelperShortcodes( newPattern , rowContent[2].tag , "tag" );

            //shortcode pattern filter
            newPattern = api.pageBuilder.shortcodesPatternFilter( newPattern );

            /**
             *@remove customize module data
             *@insert public data instade customize data
             */

            newPattern.unshift( rowContent[0] , rowContent[1] );

            api.sedShortcode.replaceModel( rowId , newPattern );

            api.contentBuilder.sendData( "theme" );

            //apply design editor css in preview
            api.pageBuilder.syncStyleEditorPreview( newPattern );

            //Current Element Id refresh
            api.currentSedElementId = newPattern[2].id;

            //refresh module in @DOM
            api.contentBuilder.refreshModule( rowContent[1].id );

            api.preview.send( 'changeCurrentElementByCustomizeRevert', {
                elementId       : newPattern[2].id ,
                shortcode_name  : newPattern[2].tag ,
                attrs           : newPattern[2].attrs
            });

            var rowElement = $( '[sed_model_id="' + rowContent[0].id + '"]');

            rowElement.data( "themeId" , themeId );
            rowElement.data( "themeOrder" , order );

            if( ! rowElement.hasClass( "sed-public-theme-row" ) )
                rowElement.addClass( "sed-public-theme-row" );

            if( hide === true && ! rowElement.hasClass( "sed-hidden-theme-row" ) ){
                rowElement.addClass("sed-hidden-theme-row");
            }



        },


        hiddenToPublic : function( rowElement , themeId , elementId ){
            var self= this;

            rowElement.removeClass("sed-hidden-theme-row");

            api.contentBuilder.pagesThemeContent[this.postId] = _.map( api.contentBuilder.pagesThemeContent[this.postId], function(shortcode){
                if( shortcode.id == rowElement.attr("sed_model_id") && !_.isUndefined( shortcode.is_hidden ) ){
                    delete shortcode.is_hidden;
                    return shortcode;
                }else
                    return shortcode;
            });

            api.contentBuilder.sendData( "theme" );
        },


        publicToCustomize : function( rowElement , themeId , elementId ){
            var self= this;

            rowElement.data( "isCustomize" , "yes" );

            api.contentBuilder.pagesThemeContent[this.postId] = _.map( api.contentBuilder.pagesThemeContent[this.postId], function(shortcode){
                if( shortcode.id == rowElement.attr("sed_model_id") && !_.isUndefined( shortcode.theme_id ) ){
                    shortcode.is_customize = true;
                    return shortcode;
                }else
                    return shortcode;
            });

            api.contentBuilder.sendData( "theme" );
        },


        hiddenToCustomize : function( rowElement , themeId , elementId ){
            var self= this;

            rowElement.data( "isCustomize" , "yes" );

            rowElement.removeClass("sed-hidden-theme-row");

            api.contentBuilder.pagesThemeContent[this.postId] = _.map( api.contentBuilder.pagesThemeContent[this.postId], function(shortcode){
                if( shortcode.id == rowElement.attr("sed_model_id") && !_.isUndefined( shortcode.theme_id ) && !_.isUndefined( shortcode.is_hidden ) ){

                    shortcode.is_customize = true;
                    delete shortcode.is_hidden;

                    return shortcode;
                }else
                    return shortcode;
            });

            api.contentBuilder.sendData( "theme" );
        },


        customizeToHidden : function( rowElement , themeId , elementId , usingDataMode ){

            if( usingDataMode == "using_public_data" ){

                this.revertCustomizeToPublic( rowElement , themeId , true );

            }else if( usingDataMode == "using_customize_data" ){
                var rowId = rowElement.attr("sed_model_id");
                rowElement.addClass("sed-hidden-theme-row");

                api.contentBuilder.pagesThemeContent[this.postId] = _.map( api.contentBuilder.pagesThemeContent[this.postId], function(shortcode){
                    if( shortcode.id == rowId && !_.isUndefined( shortcode.is_customize ) ){
                        delete shortcode.is_customize;
                        shortcode.is_hidden = true;
                        return shortcode;
                    }else
                        return shortcode;
                });

                this.ordersRefresh();
                api.contentBuilder.sendData( "theme" );
            }

        },


        publicToHidden : function( rowElement , themeId , elementId ){
            var self= this;
            rowElement.addClass("sed-hidden-theme-row");

            api.contentBuilder.pagesThemeContent[this.postId] = _.map( api.contentBuilder.pagesThemeContent[this.postId], function(shortcode){
                if( shortcode.id == rowElement.attr("sed_model_id") ){
                    shortcode.is_hidden = true;
                    return shortcode;
                }else
                    return shortcode;
            });

            api.contentBuilder.sendData( "theme" );
        },

        //refresh orders in subthemes row and current page row
        ordersRefresh : function( forceUpdate ){
            var self = this ,
                themeRows = {};

            var order = 0;
            $( ".sed-site-main-part > .sed-row-pb" ).each( function( index , el ){

                if( !_.isUndefined( $(this).data("themeId") ) ){

                    var themeId = $(this).data("themeId");

                    $(this).data("themeOrder" , order );

                    themeRows[themeId] = {
                        order: order
                    };

                    order++;

                }

            });

            forceUpdate = !_.isUndefined( forceUpdate ) ? forceUpdate : true;

            if( forceUpdate === true )
                api.preview.send( "updateCurrentLayoutRowsOrders" , themeRows );
        },


        getNextClosestThemeRowId : function(element){

            if( !element.parent().hasClass("sed-site-main-part") ){
                alert("this element is not a theme row");
                return false;
            }

            themeId = "";

            element.nextAll(".sed-row-pb").each( function( index , rowEl ){
                if( !_.isUndefined(  $(this).data("themeId") ) ){
                    themeId = $(this).data("themeId");
                    return false;
                }
            });

            return themeId;
        },

        getPrevClosestThemeRowId : function( element ){

            if( !element.parent().hasClass("sed-site-main-part") ){
                alert("this element is not a theme row");
                return false;
            }

            themeId = "";

            element.prevAll(".sed-row-pb").each( function( index , rowEl ){
                if( !_.isUndefined(  $(this).data("themeId") ) ){
                    themeId = $(this).data("themeId");
                    return false;
                }
            });

            return themeId;
        },

        isPublicRow : function( id ){
            var shortcode = api.contentBuilder.getShortcode( id );

            return !_.isUndefined( shortcode ) && !_.isUndefined( shortcode.theme_id ) && shortcode.theme_id;
        },

        getCustomRowInfoAfterCreate : function( element , dropItem , direction ){

            if( !element.parent().hasClass("sed-site-main-part") ){
                alert("this element is not a theme row");
                return false;
            }

            var relThemeId , type;

            var id = dropItem.attr("sed_model_id");

            if( this.isPublicRow( id ) ){
                relThemeId = dropItem.data("themeId");
                if( direction == "up" ){
                    type = "after";
                }else if( direction == "down" ){
                    type = "before";
                }
            }else{
                var dropItemShortcode = api.contentBuilder.getShortcode( id );
                relThemeId = dropItemShortcode.rel_theme_id;
                type = dropItemShortcode.row_type;
            }

            return {
                themeId : relThemeId ,
                type    : type
            };

        },

        getCustomRowInfoAfterSort : function( element ){

            if( !element.parent().hasClass("sed-site-main-part") ){
                alert("this element is not a theme row");
                return false;
            }

            var next            = element.next(".sed-row-pb") ,
                nextId          = ( next.length == 1 ) ? next.attr("sed_model_id") : "" ,
                prev            = element.prev(".sed-row-pb") ,
                prevId          = ( prev.length == 1 ) ? prev.attr("sed_model_id") : "" ,
                id              = element.attr("sed_model_id") ,
                itemShortcode   = api.contentBuilder.getShortcode( id ) ,
                relThemeId ,
                type;

            if( nextId && ! this.isPublicRow( nextId ) ){
                var nextShortcode = api.contentBuilder.getShortcode( nextId );

                if( nextShortcode.rel_theme_id == itemShortcode.rel_theme_id ){

                    return {
                        themeId : itemShortcode.rel_theme_id ,
                        type    : _.clone( nextShortcode.row_type )
                    };

                }
            }

            if( prevId && ! this.isPublicRow( prevId ) ){
                var prevShortcode = api.contentBuilder.getShortcode( prevId );

                if( prevShortcode.rel_theme_id == itemShortcode.rel_theme_id ){

                    return {
                        themeId : itemShortcode.rel_theme_id ,
                        type    : _.clone( prevShortcode.row_type )
                    };

                }

            }

            if( prevId && this.isPublicRow( prevId ) ){

                return {
                    themeId : prev.data("themeId"),
                    type    : "after"
                };

            }else if( prevId && ! this.isPublicRow( prevId ) ){

                return {
                    themeId : _.clone( prevShortcode.rel_theme_id ),
                    type    : _.clone( prevShortcode.row_type )
                };

            }else if( nextId && this.isPublicRow( nextId ) ){

                return {
                    themeId : next.data("themeId"),
                    type    : "before"
                };

            }else if( nextId && ! this.isPublicRow( nextId ) ){

                return {
                    themeId : _.clone( nextShortcode.rel_theme_id ),
                    type    : _.clone( nextShortcode.row_type )
                };

            }

        },

        getCustomRowInfo : function( id , type ){

            var row_type , rel_theme_id;

            if( type == "after" ) {

                var themeId = this.getNextClosestThemeRowId($('[sed_model_id="' + id + '"]'));
                rel_theme_id = themeId;

                if( _.isEmpty( themeId ) )
                    row_type = "end";
                else
                    row_type = "before";

            }else if(  type == "before" ){

                var themeId = this.getPrevClosestThemeRowId($('[sed_model_id="' + id + '"]'));
                rel_theme_id = themeId;

                if( _.isEmpty( themeId ) )
                    row_type = "start";
                else
                    row_type = "after";
            }

            return {
                themeId : rel_theme_id ,
                type    : row_type
            };

        }


    });


    $( function() {

        api.defaultPageLayout       = window._sedAppDefaultPageLayout;
        api.currentLayoutGroup      = window._sedAppCurrentLayoutGroup;

        api.appLayouts = new api.AppLayouts({});

    });

}(sedApp, jQuery));