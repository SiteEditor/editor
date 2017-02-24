(function( exports, $ ) {

    var api = sedApp.editor ;
    api.shortcodeUpdate = api.shortcodeUpdate || {};
    api.shortcodeAutoUpdate = api.shortcodeAutoUpdate || {};
    api.moduleAttrUpdate = api.moduleAttrUpdate || {};
    api.sedModulesAttrsValues = api.sedModulesAttrsValues || {};
    api.fn = api.fn || {};

    api.PageBuilder = api.Class.extend({
        initialize: function( params , options ){
            var self = this;
            //, $parent = $('[sed-role="row-pb"]').parent()
            //$parent.addClass("sed-pb-rows-box sed-pb-component");
                                  
            $.extend( this, options || {} );

            this.jsCounter = 0;

            this.currentModuleContent = [];

            this.currentAjaxModule = false;

            this.ajaxModuleScripts = {};

            this.ajaxModuleStyles = {};

            this.currentDragType;

            this.resizing = false;

            this.columnsMod = "create";

            this.lastColumnsWidth = [];

            this.modulesAjaxRequests = {};

            this.ready();
        },

        ready: function(){

            var firstC = $("body").find(".sed-pb-post-container:first");

            //this.currentPostId = firstC.data("postId");
            this.disablePostsFullContentEditing();

            this.modulesHandles();

        },


        modulesDrag: function( option , args ){
            var pagebuilder = this;
                                        //sed-pb-empty-sortable-area
            if( option == "sortStop" ){
                var ui = args.ui ,
                    $element = args.element ,
                    name = $element.attrs["sed-module-name"] ,
                    $this = $('[current-sortable-item="yes"]');

                $this.removeAttr("current-sortable-item");

                pagebuilder.currentDragType = "regular"; // drag with sortable

                pagebuilder.currentModuleContent = [];
                pagebuilder.currentPostId = pagebuilder.getPostId( $this );

                api.currentModule = name;

                if(name == "widget"){

                    var _callback = function(){
                        api.widgetBuilder.widgetsHandler($element , name, $this, ui.direction );
                    },jsModules = [];

                    jsModules.push( api.ModulesEditorJs[name] );

                    var scripts = this._checkLoadedScript( jsModules , this.wpScripts );

                    if($.isArray( scripts )  && scripts.length > 0 )
                        this.moduleScriptsLoad( scripts , _callback );
                    else
                        _callback();

                }else{
                    /*var dropItem = ( $this.attr("sed_model_id") ) ? $this : $this.parents(".sed-pb-component:first") ,
                        direction =  ( $this.attr("sed_model_id") ) ? ui.direction : "none";*/

                    if( !_.isUndefined( api.modulesSettings[name] ) && !_.isUndefined( api.modulesSettings[name].transport ) && api.modulesSettings[name].transport == "ajax" ){
                        pagebuilder.ajaxHandlerModules(name, $this, ui.direction );
                    }else{
                        pagebuilder.directlyHandlerModules(name, $this , ui.direction );
                    }
                }
            }else if( option == "drop" ){
                var ui = args.ui ,
                    $element = args.element ,
                    name = $element.attrs["sed-module-name"] ,
                    $this = $('[current-droppable-item="yes"]');

                $this.removeAttr("current-droppable-item");

                pagebuilder.currentDragType = "free"; // drag with droppable

                pagebuilder.currentModuleContent = [];
                pagebuilder.currentPostId = pagebuilder.getPostId( $this );
                pagebuilder.currentHelperOffset = ui.offset;

                if(name == "widget"){

                    var _callback = function(){
                        api.widgetBuilder.widgetsHandler($element , name, $this, "none" );
                    },jsModules = [];

                    jsModules.push( api.ModulesEditorJs[name] );

                    var scripts = this._checkLoadedScript( jsModules , this.wpScripts );

                    if($.isArray( scripts )  && scripts.length > 0 )
                        this.moduleScriptsLoad( scripts , _callback );
                    else
                        _callback();

                }else{
                    if(   !_.isUndefined( api.modulesSettings[name] ) && !_.isUndefined( api.modulesSettings[name].transport ) && api.modulesSettings[name].transport == "ajax"  ){
                        pagebuilder.ajaxHandlerModules(name, $this, "none" );
                    }else{
                        pagebuilder.directlyHandlerModules(name, $this, "none" );
                    }
                }
            }

        },


        ajaxLoadModules : function( module_id , successCallback , errorCallback , container ){
            var self = this ,
                pattern ,
                postId,
                mRowSh = api.contentBuilder.getShortcode( module_id );


            if( $('[sed_model_id="' + module_id + '"]').length > 0 )
                postId = this.getPostId( $('[sed_model_id="' + module_id + '"]') );
            else
                postId = this.currentPostId;

            container = ( !_.isUndefined( container )  ) ? container : ( $('[sed_model_id="' + module_id + '"]').length > 0 ) ? $('[sed_model_id="' + module_id + '"]') : "body";

            if( _.isUndefined( postId ) || postId == 0 ){
                api.log("---------postId Error : postId Is Undefined. pagebuilder.min.js *ajaxLoadModules* method  ");
                return ;
            }

            pattern = api.contentBuilder.findAllTreeChildrenShortcode( module_id , postId );
            pattern.unshift( mRowSh );

            var data = {
                pattern         :  JSON.stringify( pattern ) ,
                parent_id       :  mRowSh.parent_id ,
                action          :  "load_modules" ,
                nonce           :  api.settings.nonce.module.load ,
                sed_page_ajax   :  'sed_load_modules',
            };
                                             // alert( postId );
           // if( api.shortcodeCurrentPlace == "post" ){
                data.post_id = postId;
            //}

            $(container).addClass("module-ajax-loading-container");

            var moduleAjaxLoader = new api.Ajax({
                data : data,

                success : function(){
                    $(container).removeClass("module-ajax-loading-container");
                    self.modulesAjaxRequests[module_id].processing = false;
                    var resp = this.response;
                    //_alertView( this.response.data.output , "success" );
                    if( !_.isUndefined( successCallback && typeof successCallback == "function" ) )
                        successCallback( resp );
                },

                error : function(){
                   $(container).removeClass("module-ajax-loading-container");
                   self.modulesAjaxRequests[module_id].processing = false;
                   //_alertView( this.response.data.output , "error" );
                    if( !_.isUndefined( errorCallback ) && typeof errorCallback == "function" )
                        errorCallback( resp );
                } ,

                loadingType : "medium" ,

            }, {
                container   : container ,
                repeatRequest : true
            });

            moduleAjaxLoader.request.fail(function(){
                self.modulesAjaxRequests[module_id].processing = false;
            });

            this.modulesAjaxRequests[module_id] = {
                processing  :  true  ,
                request     :  moduleAjaxLoader.request
            };

            //console.log("this.modulesAjaxRequests-----------" , this.modulesAjaxRequests);

        },

        ajaxHandlerModules: function( name , dropItem, direction ){
            var self = this ,
                module_id ,
                loadingC ,
                newItem;

           // loadingC = '<div class="module-loading-container"></div>';

            loadingC = '<div class="module-loading-container" ></div>';

            if(direction == "down"){
                newItem = $(loadingC).insertBefore(  dropItem );
            }else if(direction == "up"){
                newItem = $(loadingC).insertAfter( dropItem );
            }else{
                newItem = $(loadingC).appendTo( dropItem );
            }

            self.currentAjaxModule = name;

            self.ajaxModuleScripts[name] = [];

            self.ajaxModuleStyles[name] = [];

            var skinInfo = api.modulesInfo[name]['skins']['default'];
            if( !_.isUndefined( skinInfo ) ){
                var scripts = skinInfo['scripts'];

                if( $.isArray( scripts ) && scripts.length > 0 )
                    self.ajaxModuleScripts[self.currentAjaxModule] = $.merge( self.ajaxModuleScripts[self.currentAjaxModule] , scripts);

                var styles = skinInfo['styles'];

                if( $.isArray( styles ) && styles.length > 0 )
                    self.ajaxModuleStyles[self.currentAjaxModule] = $.merge( self.ajaxModuleStyles[self.currentAjaxModule] , styles);

            }

            module_id = this.createNewModule( name , dropItem , direction );

            api.Events.trigger( "afterCreateNewModuleModel" , name , module_id );

            var _success = function( response ){

                var _callback = function(){
                    newItem.remove();
                    self.addModuleToPost( name , dropItem , direction , response.data );
                };

                if( !_.isUndefined( api.ModulesEditorJs[name] ) ){
                    var scripts = self._checkLoadedScript( [api.ModulesEditorJs[name]] , self.wpScripts );
                    if($.isArray( scripts )  && scripts.length > 0 )
                        self.moduleScriptsLoad( scripts , _callback );
                    else
                        _callback();
                }else
                    _callback();

            };

            if( $.isArray( self.ajaxModuleScripts[name] ) && self.ajaxModuleScripts[name].length > 0 )
                api.pageBuilder.moduleScriptsLoad( self.ajaxModuleScripts[name] );

            if( $.isArray( self.ajaxModuleStyles[name] ) && self.ajaxModuleStyles[name].length > 0 )
                api.pageBuilder.moduleStylesLoad( self.ajaxModuleStyles[name] );

            this.ajaxLoadModules( module_id , _success , '' , newItem );

            self.currentAjaxModule = false;
        },

        getShortcodePatternByModuleName: function( moduleName ){
            var $shortcode_name;
            $.each(api.shortcodes , function( name, shortcode){
                if(shortcode.asModule && shortcode.moduleName == moduleName){
                    $shortcode_name = name;
                    return false;
                }
            });

            if(!$shortcode_name)
                return false;
            else
                return api.shortcodes[$shortcode_name].pattern;
        },

        //this pattern is array of patterns :: not shortcode.pattern
        getPatternByModuleName: function( moduleName ){
            var $shortcode_name;
            $.each(api.shortcodes , function( name, shortcode){
                if(shortcode.asModule && shortcode.moduleName == moduleName){
                    $shortcode_name = name;
                    return false;
                }
            });

            if(!$shortcode_name)
                return false;
            else
                return api.applyFilters( 'sedDefaultPatternFilter' , api.defaultPatterns[$shortcode_name] , $shortcode_name );
        },

        //for modules with default transport
        createNewModule: function( name , dropItem , direction ){
            var elementId;

            if(this.currentDragType == "free" || direction != "none" )
                elementId = dropItem.attr("sed_model_id");
            else
                elementId = dropItem.data("parentId");

            var module_id , nextPre;

            nextPre = (direction != "none") ? true: false;

            module_id = this.addShortcodeModule( elementId , direction , name , nextPre , dropItem );

            return module_id;
        } ,
                     //-------------sed-row-boxed
        /******param
        //if item is sortable direction is "down" or "up" else if item is dropped direction is "none"
        ******/
        addModuleToPost: function( name , dropItem , direction , html){
            var elementId;

            if(this.currentDragType == "free" || direction != "none" )
                elementId = dropItem.attr("sed_model_id");
            else
                elementId = dropItem.data("parentId");

            var newItem , module_id ;

            //for modules with default transport
            if( _.isUndefined( html ) ) {
                module_id = this.createNewModule( name , dropItem , direction );

                html = api.contentBuilder.do_shortcode( "sed_row" , module_id , module_id );
            }

            this.columnsMod = "create";

            if(direction == "down"){
                newItem = $(html).insertBefore(  dropItem );
            }else if(direction == "up"){
                newItem = $(html).insertAfter( dropItem );
            }else{
                newItem = $(html).appendTo( dropItem );
            }

            api.Events.trigger( "afterCreateModule" , newItem , name , dropItem , direction  );

                 ////api.log( this.wpStyles );
            if(name == "row"){
                api.Events.trigger( "createNewRowDraggableEl" , newItem.find('[data-type-row="draggable-element"]:first') );
            }

            var shortcode = this.getShortcodeByModuleName( name );

            var _addCtxtIdToModule = function( shortcode , newItem ){

                var $moduleContextmenuId;
                $.each( api.contextMenuSettings , function( id, data ){
                    if(data.shortcode && data.shortcode == shortcode.name){
                        $moduleContextmenuId = data.menu_id;
                        return false;
                    }
                });

                if( $moduleContextmenuId ){

                    newItem.data("moduleContextmenu" , $moduleContextmenuId);
                }

            };

            if(this.currentDragType == "free"){

                if( shortcode ){

                    api.Events.trigger( "createNewModuleDraggable" , name , newItem , dropItem );

                    _addCtxtIdToModule( shortcode , newItem );

                    var top = this.currentHelperOffset.top - dropItem.offset().top,
                        left = this.currentHelperOffset.left - dropItem.offset().left;

                    newItem.css({
                        "width"  : shortcode.attrs.default_width || "100%",
                        "height" : shortcode.attrs.default_height || "auto" ,
                        "top"    : top +"px" ,
                        "left"   : left + "px"
                    });

                    if(left + newItem.outerWidth( true ) > dropItem.width())
                        newItem.css({
                            "left"   : (dropItem.width() - newItem.outerWidth( true )) +"px"
                        });


                    if(top + newItem.outerHeight( true ) > dropItem.height())
                        dropItem.height( top + newItem.outerHeight( true ) );
                }
            }else{

                //set sheet width for main rows (with add class)
                /*if(newItem.parent().hasClass("sed-site-main-part")){
                    newItem.addClass("sed-row-boxed");
                    var row_shortcode = api.contentBuilder.getShortcode( newItem.attr("id") );

                    if( _.isUndefined(row_shortcode.attrs) )
                        row_shortcode.attrs = {};

                    if( _.isUndefined( row_shortcode.attrs['class'] ) || !$.trim( row_shortcode.attrs['class'] ))
                        row_shortcode.attrs['class'] = "sed-row-boxed";
                    else
                        row_shortcode.attrs['class'] = $.trim( row_shortcode.attrs['class'] ) + " sed-row-boxed";

                    //api.contentBuilder.updateShortcodeAttr( "class" , attrValue , newItem.attr("id"));
                }*/
            }



            //apply align And spacing of the module container
            var postId              = this.getPostId( newItem );
                id                  = newItem.find(">.sed-pb-module-container .sed-pb-module-container:first").attr("sed_model_id") ,
                module_container    = $( '[sed_model_id="' + id + '"]' ).parents(".sed-pb-module-container:first");


			/*if(module_container.length > 0 && !_.isUndefined( api.shortcodes[shortcode.name] ) && !_.isUndefined( api.shortcodes[shortcode.name].params ) ){

                //api.preview.trigger( 'current_css_selector' , "#" + module_container.attr("id") );

                var controls = ['align' , 'spacing_left' , 'spacing_top', 'spacing_bottom' , 'spacing_right' , 'spacing_lock'];
                var settings = ['text_align' , 'padding_left' , 'padding_top', 'padding_bottom' , 'padding_right' , 'padding_lock'];
                        //api.log( api.shortcodes[shortcode.name].params );
                _.each( settings , function( setting , index ){
                     var control;
                     if( controls[index] == 'align' ){
                        control = api.shortcodes[shortcode.name].params[controls[index]];
                     }else if( !_.isUndefined( api.shortcodes[shortcode.name].params.fieldset_spacing ) ){
                        control = api.shortcodes[shortcode.name].params.fieldset_spacing[controls[index]];
                     }

                    if( !_.isUndefined( control ) ){
                        //var $thisValue = _.clone( api( setting )() );
                        //$thisValue[ "#" + module_container.attr("id")  ] = control.value;
                        //api( setting ).set( $thisValue );       ,
                        api.preview.send( 'sync_align_spacing' , {
                            id          :  setting,
                            cId         :  shortcode.name + "_" + controls[index],
                            value       :  control.value,
                            selector    :  "#" + module_container.attr("id")
                        } );
                    }
                });
            }*/

            var forceOpen = false;
            //show modules settings on create
            if( !_.isUndefined( api.modulesSettings[name] ) && !_.isUndefined( api.modulesSettings[name].show_settings_on_create ) && api.modulesSettings[name].show_settings_on_create )
                forceOpen = true;

            api.selectPlugin.select( $( '[sed_model_id="' + id + '"]' ) , forceOpen );

            return newItem;
        },

        getShortcodeByModuleName : function( name ){
            var $thisShortcode = false;
            $.each( api.shortcodes , function( shortcode_name , shortcode_info ){
                ////api.log( shortcode_info.asModule && shortcode_info.moduleName == name )
                if(shortcode_info.asModule && shortcode_info.moduleName == name){
                    $thisShortcode = shortcode_info;
                    return false;
                }
            });
            return $thisShortcode;
        },

        //name : module name
        addAlignSpacingAttrToModule : function( name , attrs ){

            //apply align And spacing of the module container
            var shortcode = this.getShortcodeByModuleName( name );

			if( !_.isUndefined( api.shortcodes[shortcode.name] ) && !_.isUndefined( api.shortcodes[shortcode.name].params ) ){

                var controls = ['module_align' , 'spacing_left' , 'spacing_top', 'spacing_bottom' , 'spacing_right' , 'spacing_lock'];
                var settings = ['text_align' , 'padding_left' , 'padding_top', 'padding_bottom' , 'padding_right' , 'padding_lock'];

                _.each( settings , function( setting , index ){
                     var control;
                     if( controls[index] == 'module_align' ){
                        control = api.shortcodes[shortcode.name].params[controls[index]];
                     }else if( !_.isUndefined( api.shortcodes[shortcode.name].params.fieldset_spacing ) ){
                        control = api.shortcodes[shortcode.name].params.fieldset_spacing[controls[index]];
                     }

                    if( !_.isUndefined( control ) )
                        attrs[ controls[index] ] = control.value;

                });
            }

            return attrs;

        },

        addShortcodeModule : function( elementId , direction , name , nextPre , dropItem ){
            var elShortcode , pattern , parentId , self = this;

            if(elementId){
                if(direction == "none"){
                    parentId = elementId;
                }else{
                    elShortcode = this.contentBuilder.getShortcode( elementId );

                    if(!elShortcode)
                        return ;

                    parentId = elShortcode.parent_id;
                }

            }else{
                return ;
                //parentId = newItem.parents('[sed-shortcode="true"]:first').attr("id");
            }

            pattern = this.getPatternByModuleName(name);
            if(!$.isArray( pattern ) || pattern.length == 0)
                return ;

            //pattern = api.applyFilters( 'sedDefaultPatternFilter' , pattern , name );

            var currPattern = $.extend( true, {} , pattern ) ,
                modulePattern = $.extend( true, {} , api.defaultPatterns['sed_module'] ) ,
                rowPattern = $.extend( true, {} , api.defaultPatterns['sed_row'] ) ,
                moduleShortcode = api.modulesSettings[name].shortcode ,
                className = self.setModuleContextmenuClass( modulePattern[0]  , moduleShortcode  ) ,
                attrs = {} , newPattern;

            attrs.class = className;

            attrs = this.addAlignSpacingAttrToModule( name , attrs );

            if( self.currentDragType == "regular" ){
                rowPattern[0].attrs.type = 'static-element';

                var rowClassName = self.setModuleContextmenuClass( rowPattern[0]  , moduleShortcode  );

                rowPattern[0].attrs.class = rowClassName;

                if(name == "row-container"){
                    rowPattern[0].attrs.length = "boxed";
                }else{
                    rowPattern[0].attrs.length = "wide";
                }

            }else if(self.currentDragType == "free"){
                attrs.class += "module-element-draggable";
            }

            modulePattern[0].attrs = attrs;

            var mainModelParentId = _.clone( currPattern[0].parent_id );
            currPattern = _.map( currPattern , function( shModel ){
                if( shModel.parent_id == mainModelParentId ){
                    shModel.parent_id = modulePattern[0].id;
                }
                return shModel;
            });

            if( self.currentDragType == "regular" ){
                modulePattern[0].parent_id = rowPattern[0].id;
                newPattern = $.merge( $.merge( $.merge( [], rowPattern ), modulePattern ) , currPattern );
            }else if(self.currentDragType == "free"){
                newPattern = $.merge( $.merge( [], modulePattern ), currPattern ) ;
            }

            var shortcodes = this.loadPattern( newPattern , parentId ),
            postId , typeS;
            if(elementId == "root" && direction == "none"){
                api.shortcodeCurrentPlace = dropItem.data("contentType");
                postId = dropItem.data("postId");
            }else{
                postId = this.getPostId( $( '[sed_model_id="' + elementId + '"]' ) );
            }

            var id = shortcodes[0].id;

            shortcodes = this.setHelperShortcodes( shortcodes , name );
            shortcodes = this.shortcodesPatternFilter( shortcodes );

            this.syncStyleEditorPreview( shortcodes );

            if(direction == "down"){
              typeS = 0;
            }else if(direction == "up"){
              typeS = 1;
            }else if(direction == "none"){
              this.contentBuilder.addShortcodesToParent(  parentId , shortcodes , postId );
              return id;
            }
                                     
            var nextPreId = (nextPre) ? elementId: "";

            this.contentBuilder.addShortcodeModule( shortcodes , postId, nextPreId , typeS);
            return id;
        },

        //shortcodes Pattern : add module_helper_id to shortcodes is in siblings pattern
        setHelperShortcodes : function( shortcodes , name , type ){
            type = (!type) ? "moduleName" : type;
            if(type == "moduleName"){
                var shortcodeInfo = this.getShortcodeByModuleName( name ) ,
                    mainShortcodeTag = shortcodeInfo.name;
            }else if(type == "tag"){
                var mainShortcodeTag = name;
            }else{
                alert( "type : " + type + " for helper shortcodes is incorrect ");
                return ;
            }

            var newMainShortcode  = _.findWhere(shortcodes, {tag : mainShortcodeTag} ),
                children = _.filter(shortcodes , function(shortcode){
                    return shortcode.parent_id == newMainShortcode.parent_id && shortcode.id != newMainShortcode.id;
                }), ids = _.pluck( children , 'id');

            _.each( shortcodes , function(shortcode){
                if($.inArray( shortcode.id , ids ) != -1){
                    if(_.isUndefined(shortcode.attrs))
                        shortcode.attrs = {};
                    shortcode.attrs.module_helper_id = newMainShortcode.id;
                }

                //for module include sed_row and sed_module in shortcode pattern
                if( !_.isUndefined(shortcode.attrs) && !_.isUndefined(shortcode.attrs.have_helper_id) ){
                    _.each( shortcodes , function( model ){

                        if( model.parent_id == shortcode.parent_id && model.id != shortcode.id ){
                            if( !_.isUndefined(model.attrs) && !_.isUndefined(model.attrs.is_helper_id) ){

                                model.attrs.module_helper_id = shortcode.id;
                                delete model.attrs.is_helper_id;

                            }
                        }

                    });

                    delete shortcode.attrs.have_helper_id;
                }
            });

            return shortcodes;
        },

        shortcodesPatternFilter : function( shortcodes ){
            var sedIds = _.filter( shortcodes  , function(shortcode){
                return !_.isUndefined( shortcode.attrs ) && !_.isUndefined( shortcode.attrs.sed_id ) ;
            });
            _.each( sedIds , function( shSedId ){
                var sed_id = shSedId.attrs.sed_id ,
                    id = shSedId.id;

                shortcodes = _.map( shortcodes , function( shortcode ){
                    if( !_.isUndefined( shortcode.attrs ) ){
                        _.each( shortcode.attrs , function( value , attr){
                          //alert(typeof sed_id);
                            if( _.isString( value ) && value.indexOf( sed_id ) > -1){
                               shortcode.attrs[attr] = value.replace(sed_id , id);
                            }
                        });
                    }
                    return shortcode;
                });

            });

            return shortcodes;
        },

        setModuleContextmenuClass: function( module , moduleShortcode  ) {
            var className , attrs = _.clone( module.attrs );
            if(!_.isUndefined( attrs ) && !_.isUndefined( attrs['class'] ))
                className = "module_" + moduleShortcode + "_contextmenu_container " + attrs['class'];
            else if( !_.isUndefined( attrs ) )
                className = "module_" + moduleShortcode + "_contextmenu_container ";
            else {
                className = "module_" + moduleShortcode + "_contextmenu_container ";
            }

            return className;
        },

        //name :: module name
        moduleScriptsLoad: function( scripts , callback  ) {
            var pagebuilder = this ,
                scripts = scripts || [],
                wpScripts = pagebuilder.wpScripts || [];
                if(scripts.length == 0)
                    return ;
                              //alert( scripts.length + 1 );
                var scriptLoader = new api.ModulesScriptsLoader();
                scriptLoader.moduleScStLoad( scripts , wpScripts , function(){
                    $.each( scripts , function(i , script){
                        if($.inArray(script[0] , pagebuilder.wpScripts) == -1)
                            pagebuilder.wpScripts.push(script[0]);
                    });
                    //api.Events.trigger("scriptsLoadedComplate");
                    if( callback )
                        callback();
                });
        },

        //name :: module name
        moduleStylesLoad: function( styles , callback ) {
            var pagebuilder = this ,
                styles = styles || [],
                wpStyles = pagebuilder.wpStyles || [];
                if(styles.length == 0)
                    return ;

                var styleLoader = new api.ModulesScriptsLoader();
                styleLoader.moduleScStLoad( styles , wpStyles , function(){
                    $.each( styles , function(i , style){
                        //alert(i);
                        if($.inArray(style[0] , pagebuilder.wpStyles) == -1)
                            pagebuilder.wpStyles.push(style[0]);
                            //alert( style[0] );
                    });
                    if( callback )
                        callback();
                } , "css");
        },

        _checkLoadedScript : function( scripts , wpScripts ){

            if($.isArray(scripts)){

                scripts = $.grep(scripts , function(script , index){
                    if( $.inArray(script[0] , wpScripts) != -1 || !script[0] || !script[1]){
                        return false;
                    }else{
                        return true;
                    }
                });

            }

            return scripts;
        },

        directlyHandlerModules: function( name , dropItem, direction ){
            //var html = $("#sed-shortcode-module-" + name + "-tmpl").html() ,
            var inline_js , inline_css , newItem , self = this;

            var _callback = function(){
                self.addModuleToPost( name , dropItem , direction );
            };

            var modules = api.modulesSettings[name].sub_modules,
                jsModules = [];

            modules.push( name );

            _.each( modules , function( module ){
                if( !_.isUndefined( api.ModulesEditorJs[module] ) )
                    jsModules.push(api.ModulesEditorJs[module]);
            });

            if( jsModules.length > 0 ){
                var scripts = this._checkLoadedScript( jsModules , this.wpScripts );

                if($.isArray( scripts )  && scripts.length > 0 )
                    this.moduleScriptsLoad( scripts , _callback );
                else
                    _callback();
            }else
                _callback();

        },

        getPostId: function( item ){
            var parentC = item.parents(".sed-pb-post-container:first"); //this.currentPostId
            api.shortcodeCurrentPlace = parentC.data("contentType");
            //alert( parentC.data("postId"));
            return parentC.data("postId");
        },

        sedGuid : function() {
            return this.SEDID() + this.SEDID() + "-" + this.SEDID();
        },

        SEDID : function () {
            return (65536 * (1 + Math.random()) | 0).toString(16).substring(1);
        },

        getNewId : function( ){

            //return Date.now() + "-" + this.sedGuid();
            //return "sed-gid-" + Date.now();
            return _.uniqueId("sed-gid-");
        },

        loadPattern : function( pattern , parent_id ){
                      ////api.log(pattern);
            if(!$.isArray(pattern) || pattern.length == 0)
                return ;

            pattern[0].parent_id = parent_id;

            var shortcodes = [] ,
                self = this ,
                scripts ,
                styles ,
                patternIds = {};
                            
            shortcodes = _.map( pattern , function( shortcode , index ){
                var id;

                if( self.currentAjaxModule ){

                    self.ajaxModuleScripts[self.currentAjaxModule] = $.merge( self.ajaxModuleScripts[self.currentAjaxModule] , api.shortcodesScripts[shortcode.tag] || []);

                    self.ajaxModuleStyles[self.currentAjaxModule] = $.merge( self.ajaxModuleStyles[self.currentAjaxModule] , api.shortcodesStyles[shortcode.tag] || []);
                }

                if( shortcode.tag != "content" ){
                    var shortcode_info = api.shortcodes[shortcode.tag];

                    if( typeof( shortcode_info ) == 'undefined' ){
                        //api.log("=================== SED ERROR ==========================");
                        console.error("shortcode " + shortcode.tag +" is not defined");
                        return ;
                    }
                }

                id = self.getNewId( );

                patternIds[shortcode.id] = id;

                shortcode.id = id;

                if( _.isUndefined( shortcode.attrs ) ){
                    shortcode.attrs = {};
                }

                shortcode.attrs.sed_model_id = id;

                shortcode.attrs = self.sanitizeAttrsValues(shortcode.attrs) || {};

                return shortcode;

            });

            if( parent_id != "root" )
                patternIds["root"] = parent_id;

            shortcodes = _.map( shortcodes , function(shortcode){

                if( !_.isUndefined( patternIds[shortcode.parent_id] ) )
                    shortcode.parent_id = patternIds[shortcode.parent_id];

                return shortcode;
            });

            return shortcodes;

        },

        syncStyleEditorPreview : function( shortcodes ){
            var self = this;
            _.each( shortcodes , function( shortcode ){
                if( !_.isUndefined( shortcode.attrs ) && !_.isUndefined( shortcode.attrs.sed_css ) && !_.isEmpty( shortcode.attrs.sed_css ) ) {

                    var sedCssCopy = $.extend( true, {} , shortcode.attrs.sed_css );

                    api.contentBuilder.updateShortcodeAttr( "sed_css" , {} , shortcode.id );

                    _.each( sedCssCopy , function (properties, selector) {

                        var patt = /\[\s*(sed_model_id)\s*=\s*["\']?([^"\']*)["\']?\s*\]/g;
                        var new_selector = selector.replace( patt , '[sed_model_id="' + shortcode.id + '"]' );

                        $.each(properties, function (setting_id, value) {

                            api.preview.trigger('current_css_setting_type', "module");
                            api.preview.trigger('current_css_selector', new_selector);
                            api.preview.trigger('current_element' , shortcode.id );
                            api(setting_id).set(value);

                        });

                    });
                }
            })
        },

        sanitizeAttrsValues : function( attrs ){

            if( _.isObject( attrs ) && !_.isEmpty( attrs ) ){
                _.each( attrs , function( value , attr ){

                   if( value === "true" )
                      value = true;
                   else if( value === "false" )
                      value = false;

                    attrs[attr] = value;
                });
            }

            return attrs;

        },

        //this function for add new items by sed_add_shortcode
        addNewShortcodeElement : function( elmId , attrs , parent_id ){

            var children = api.contentBuilder.getShortcodeChildren( elmId );
            attrs = attrs || [];
                                 ////api.log( attrs );
            if(!$.isArray(children) || children.length == 0){
                return [];
            }else{
                var shortcodes = [] , new_shortcode , self = this, shortcode_info;

                $.each( children , function( index , shortcode){

                    var id = self.getNewId();
                    var $thisAttrs = attrs[index];

                    new_shortcode = {
                      parent_id : parent_id,
                      tag       : shortcode.tag,
                      attrs     : $.extend(true, shortcode.attrs || {}, $thisAttrs || {}),
                      id        : id,
                      //type      :
                    };

                    if( shortcode.tag == "content" ){
                        new_shortcode.content = shortcode.content;
                    }

                    shortcodes.push( new_shortcode );

                    if(attrs.length > 0)
                        var shifted = attrs.shift();

                    var shortcodes_children = self.addNewShortcodeElement( shortcode.id , attrs , id );
                    shortcodes = $.merge( shortcodes , shortcodes_children || []);

                });

                return shortcodes;
            }
        },

        /*
        @param :
        @name           :::   module name   ,
        @addType        :::   nextPre |  parent   ,
        @containerId    :::   nextPreId | parentId ,
        @direction      :::   down | up
        */

        modulesSortable: function(){
            var self = this , oldWidth , sortAreaStart ,
                domUpdate = false , sender = null , currentSortArea , contentModel;
            //sortable all element in each page
            var options = {
                //axis: "y",
                //containment: '[sed-role="content"]',
                appendTo: document.body,
                containment: 'body',
                cursorAt: { top: 15,left: 114 },
                cursor: "move",
                connectWith: '.sed-pb-component,.sed-pb-main-component',
                cancel: ".empty-column",
                items : ">.sed-row-pb" ,
                zIndex: 1070 ,
                revert : true ,
                //cursorAt: { left: 5 },
                //forceHelperSize: true,
                //forcePlaceholderSize: true,
                //tolerance: "pointer",
                placeholder: "sed-state-highlight-row",
                handle: ".drag_pb_btn",//self.dnpHandleSelector,
                helper: function() {
                    return $( $("#tmpl-drag-n-drop-helper").html() );
                },

                start: function(event, ui){
                    sortAreaStart = $(this);
                    var parentC = ui.item.parents(".sed-pb-post-container:first"); //this.currentPostId

                    contentModel = parentC.data("contentType");

                    //$(".sed-app-editor #site-editor-main-part").css("padding" , "25px 0 40px");
                    $("body").addClass("module-dragging-mode");

                    //for sub_theme module-----
                    api.Events.trigger( "moduleSortableStartEvent" , ui );
                },

                over: function(event, ui){

                    if(ui.sender && sortAreaStart[0] == ui.sender[0]){
                        self.addRemoveSortableClass( ui.sender , 1 );
                    }

                    var $this = $(this);
                    ////api.log("over : " , $this);
                    if($this.hasClass("sed-pb-empty-sortable-area")){
                        $(this).removeClass("sed-pb-empty-sortable-area");
                    }

                },

                out: function(event, ui){

                    self.addRemoveSortableClass( $(this) );

                },

                stop : function( event, ui ){

                    //$(".sed-app-editor #site-editor-main-part").css("padding" , "");
                    $("body").removeClass("module-dragging-mode");

                    $('.sed-pb-component').each(function( i , element ){

                        self.addRemoveSortableClass( $(this) );

                    });

                    if( !ui.item.parent().hasClass("sed-site-main-part") && ui.item.hasClass("sed-main-content-row-role") ){
                        self.addRemoveSortableClass( ui.item.parents(".sed-pb-component:first") , 1 );
                        sortAreaStart.removeClass("sed-pb-empty-sortable-area");
                        alert("You do not Allow to Content layout drag in to any drop area only allow sort it with layout rows");
                        domUpdate = false;
                        return false;
                    }

                    api.Events.trigger( "moduleBeforeSortableStopEvent" , ui , $(this) );

                    var result = api.applyFilters( 'modulesSortableFilters' , {
                        sort        : true ,
                        domUpdate   : domUpdate
                    } , ui , sender , currentSortArea , sortAreaStart , contentModel );

                    domUpdate = _.clone( result.domUpdate );

                    if( result.sort === false ){
                        return false;
                    }

                    if(domUpdate === true){
                        api.contentBuilder.updateModulesOrder( ui , sender , currentSortArea , contentModel );
                        domUpdate = false;
                    }

                    var module = ui.item.find(">.sed-pb-module-container .sed-pb-module-container:first");
                    module.trigger( "sed.moduleSortableStop" );

                    module.find(".sed-row-pb > .sed-pb-module-container").each(function(){
                        $(this).find(".sed-pb-module-container:first").trigger("sed.moduleSortableStop");
                    });

                    //for sub_theme module-----
                    api.Events.trigger( "moduleSortableStopEvent" , ui , $(this) );
                },

                update : function( event, ui ){
                    domUpdate = true;
                    sender = ui.sender;
                    currentSortArea = $(this);
                },
                //opacity for helper
                revert: 100,
                //tolerance: "pointer",
                //zIndex: 9999
                //scrollSensitivity: 100,
                scrollSpeed: 40,
                //distance: 5
                //dropOnEmpty: false
            };

            $('.sed-pb-component').livequery(function(){
                $(this).sortable( options );

                self.addRemoveSortableClass( $(this) );

            });

            var options_main = $.extend( true , {} , options );

            options_main.cancel = ".sed-public-theme-row,.empty-column";
            options_main.connectWith = ".sed-pb-component";

            $('.sed-pb-main-component').sortable( options_main );

        },

        addRemoveSortableClass: function( areaEl , num ){
            num = (!num) ? 0 : num;

            if(this.checkEmptySortableArea( areaEl ) == num)
                areaEl.addClass("sed-pb-empty-sortable-area");
            else if(this.checkEmptySortableArea( areaEl ) > num)
                areaEl.removeClass("sed-pb-empty-sortable-area");

        },

        checkEmptySortableArea: function( areaEl ){
            return areaEl.children(".sed-bp-element").length;
        },


        disablePostsFullContentEditing : function(){
            $(".sed-pb-post-container-disable-editing").find(".sed-bp-element").attr( "sed-disable-editing" , "yes" );
            $(".sed-pb-post-container-disable-editing").find('[sed-module-cover="has-cover"]').attr( "sed-disable-editing" , "yes" );
            $(".sed-pb-post-container-disable-editing").find('.sed-column-pb').attr( "sed-disable-editing" , "yes" );
            $(".sed-pb-post-container-disable-editing").find('.sed-pb-component').addClass('sed-pb-component-no-editable').removeClass('sed-pb-component');
            $(".sed-pb-post-container-disable-editing").find( ".sed-pb-module-container" ).attr( "sed-disable-editing" , "yes" );

        },

        modulesHandles: function(){
            var self = this;

            var moduleSelectors = [ '.sed-bp-element[sed_model_id]' ] ,
                moduleHandlers = {};

            $.each( api.settings.staticModules , function( id , module ){
                if( !_.isUndefined( module.selector ) ){
                    moduleSelectors.push( module.selector );
                    $( module.selector ).data( "staticModuleId" , id );
                    $( module.selector ).data( "sedModuleAction" , module.actions );
                }
            });

            api.selectiveRefresh.bind( "partial-content-rendered" , function( data ){

                $.each( api.settings.staticModules , function( id , module ){
                    if( !_.isUndefined( module.selector ) &&  ! $( module.selector ).data( "staticModuleId" ) ){
                        //moduleSelectors.push( module.selector );
                        $( module.selector ).data( "staticModuleId" , id );
                        $( module.selector ).data( "sedModuleAction" , module.actions );
                    }
                });
                
            });

            console.log( "------------moduleSelectors-------------" , moduleSelectors );

            var moduleSelectorsString = moduleSelectors.join(",");

            /**
             * add controller to each element in pages include settings And Delete And Move
             * Modules : Dynamic || Static
             */
            $( moduleSelectorsString ).livequery(function(){ //alert("test 1");
                if( $(this).is("[sed-disable-editing='yes']") )
                    return ;

                var element = $(this);

                if( ! element.hasClass("clearfix") )
                    element.addClass("clearfix");

                if( ! element.hasClass("sed-bp-element") ){
                    element.addClass("sed-static-module");

                    element.on("click" , function(e){     // $(this).is(".drag_pb_btn")
                        //api.styleEditor.editorState == "on" ||
                        if( self.resizing === true || api.appPreview.mode == "on" || $(this).is("[sed-disable-editing='yes']") )
                            return ;

                        e.preventDefault();
                        e.stopPropagation();

                        api.hideContextmenu(e);  //api.log( $(this) );
                        api.selectPlugin.select( $(this) , $(e.target).hasClass("sed_setting_btn_cmp") );

                    });

                }

                element.not("[data-type-row='draggable-element']").hover(function(e){
                    //api.styleEditor.editorState == "on" ||
                    if(self.resizing === true)
                        return ;

                    e.stopPropagation();

                    if( element.hasClass("sed-bp-element") && _.isUndefined( $(this).data( "sedModuleType" ) ) ){

                        var elementId = element.find(">.sed-pb-module-container .sed-pb-module-container:first").attr("sed_model_id") ,
                            shortcode = api.contentBuilder.getShortcode( elementId ) ,
                            ModuleActions = api.shortcodes[shortcode.tag]['actions'];

                        element.data( "sedModuleAction" , ModuleActions );

                    }

                    var type , actions;

                    if( !_.isUndefined( $(this).data( "themeId" ) ) ){
                        type = "public";
                        actions = $(this).data( "sedModuleAction" );

                        var index = $.inArray( "move" , actions );

                        if( index > -1 ){
                            actions.splice( index , 1 );
                        }

                    }else if( $(this).hasClass( "sed-static-module" ) ){
                        type = "static";
                        actions = $(this).data( "sedModuleAction" ) ;
                    }else{
                        type = "normal";
                        actions = $(this).data( "sedModuleAction" );
                    }

                    if( _.isUndefined( $(this).data( "sedModuleType" ) ) || $(this).data( "sedModuleType" ) != type ) {

                        if( !_.isUndefined( $(this).data( "sedModuleType" ) ) ){
                            $(this).find(">.sed-handle-sort-row,>.sed-pb-handle-row-top,>.sed-pb-handle-row-right,>.sed-pb-handle-row-bottom,>.sed-pb-handle-row-left").remove();
                        }

                        var template = api.template("tmpl-sed-pb-element-handle"),
                            html = template({
                                type: type,
                                actions: actions
                            });

                        var dnp = $(html).appendTo($(this));

                        $(this).data( "sedModuleType" , type );

                    }else{
                        var dnp = $(this).find(">.sed-handle-sort-row,>.sed-pb-handle-row-top,>.sed-pb-handle-row-right,>.sed-pb-handle-row-bottom,>.sed-pb-handle-row-left");
                    }

                    if( $.inArray( 'remove' , actions ) > -1 )
                        dnp.find('.remove_pb_btn').data( "moduleRelId" , element.attr("sed_model_id") );


                    $('.sed-bp-element , .sed-static-module').find(">.sed-handle-sort-row,>.sed-pb-handle-row-top,>.sed-pb-handle-row-right,>.sed-pb-handle-row-bottom,>.sed-pb-handle-row-left").hide();

                    if( element.offset().top <= 11 )
                        element.addClass("sed-pb-row-top-site");
                    else
                        element.removeClass("sed-pb-row-top-site");

                    var offsetLeft = element.offset().left ,
                        offsetRight = $("body").outerWidth(true) - element.outerWidth() - offsetLeft ;

                    if( offsetLeft <= 13 || offsetRight <= 13 )
                        element.addClass("sed-pb-row-full-width");
                    else
                        element.removeClass("sed-pb-row-full-width");

                    dnp.show();

                },function(e){
                    //api.styleEditor.editorState == "on" ||
                    if( self.resizing === true)
                        return ;

                    //e.stopPropagation();
                    if( $(this).parents(".sed-bp-element:first , .sed-static-module:first").length > 0 )
                        $(this).parents(".sed-bp-element:first , .sed-static-module:first").eq(0).find(">.sed-handle-sort-row,>.sed-pb-handle-row-top,>.sed-pb-handle-row-right,>.sed-pb-handle-row-bottom,>.sed-pb-handle-row-left").show();

                    $(this).find(">.sed-handle-sort-row,>.sed-pb-handle-row-top,>.sed-pb-handle-row-right,>.sed-pb-handle-row-bottom,>.sed-pb-handle-row-left").hide();

                });

            });

            $('[sed-module-cover="has-cover"]').livequery(function(){

                var id , cid;

                if( $(this).is("[sed-disable-editing='yes']") ){

                    id = $(this).attr("sed_model_id");

                    cid = id + "-cover";

                    $('<div id="' + cid + '" class="module-element-force-cover"></div').insertAfter( $(this) )
                }else{
                    id = $(this).attr("sed_model_id");

                    var shortcode= api.contentBuilder.getShortcode( id ) ,
                        $moduleContextmenuId;
                   
                    $.each( api.contextMenuSettings , function( id, data ){
                        if(data.shortcode && data.shortcode == shortcode.tag){
                            $moduleContextmenuId = data.menu_id;
                            return false;
                        }
                    });

                    //add cover to module needed to cover like youtube , video , google-map , ...

                    cid = id + "-cover";

                    var cover = $('<div id="' + cid + '" class="module-element-force-cover"></div').insertAfter( $(this) );

                    cover.addClass( $moduleContextmenuId + "_cover");
                }
            });

            //&& !$(this).parents(".sed-pb-module-container:first").is("[sed-disable-editing='yes']")
            $(".jp-jplayer video,.jp-jplayer audio,.fullwidth-video video").livequery(function(){
                if( api.appPreview.mode == "off" ){

                    var moduleId = $(this).parents(".sed-pb-module-container:first").attr("sed_model_id");

                    /*if( $('[sed_model_id="' + moduleId + '"]').is("[sed-disable-editing='yes']") ){
                        alert( $('[sed_model_id="' + moduleId + '"]').find(".module-element-force-cover").length );
                        $('[sed_model_id="' + moduleId + '"]').find(".module-element-force-cover").addClass("force-show");
                    } */

                    if( _.isUndefined( api.videoAudioTags ) ){
                        api.videoAudioTags = {};
                    }

                    var oldKey;
                    $.each( api.videoAudioTags , function( vid , mid ){
                        if( mid == moduleId ){
                            oldKey = vid;
                            return false;
                        }
                    });

                    if( !_.isUndefined( oldKey ) && oldKey )
                        delete api.videoAudioTags[oldKey];

                    api.videoAudioTags[$(this).attr("id")] = moduleId;

                    $(this).detach();
                }
            });

        },

        _getBorder : function( element ){
            return parseInt( element.css("padding-left") ) + parseInt( element.css("padding-right") )
        },

        _getMargin : function( element ){
            return parseInt( element.css("margin-left") ) + parseInt( element.css("margin-right") )
        },

        _getPadding : function( element ){
            return parseInt( element.css("padding-left") ) + parseInt( element.css("padding-right") );
        },

        resizeColumns: function(){
            //resize Column
            var self = this;
            $(".sed-column-pb").livequery(function(){
                if( $(this).is("[sed-disable-editing='yes']") )
                    return ;
                $(this).each( function (i,el) {
                   //var htm = $(this).html();
                   //$(this).html("");
                   //var inner = $("<div class='sed-pb-column-inner'></div>").appendTo($(this));
                   //inner.append(htm);

                });

                var prevWidth , prevOWidth, ppad , pbor , pmar , ppW , modules , modulesPrev;

                var lazyResize = _.debounce(function(){     //alert("test");
                    modules.trigger( "sed.moduleResize" );
                    modulesPrev.trigger( "sed.moduleResize" );
                }, 500);

                ////api.log( $(this).attr("class") );
                if(!$(this).hasClass("sed-pb-first-col")){ //alert("jclejeri");
                    var _rtl = ( $("body").hasClass("rtl-body") ) ? true : false;
                    $( this ).sedColumnResize({
                        unit : "%" ,
                        isRtl : _rtl ,
                        resizeStart : function(event , item){
                            prevWidth = item.prev().width();
                            prevOWidth = item.prev().outerWidth();
                            ppW = item.prev().parent().width();
                            ppad = self._getPadding( item.prev() );
                            pbor = self._getBorder( item.prev() );
                            pmar = self._getMargin( item.prev() );
                            self.resizing = true;
                            modules = item.find(".sed-pb-module-container");
                            modulesPrev = item.prev().find(".sed-pb-module-container");
                            modules.trigger( "sed.moduleResizeStart" );
                            modulesPrev.trigger( "sed.moduleResizeStart" );
                           // console(item.prev().attr("class"));
                        },
                        resize : function(event, d, item, maxWidth , options) {
                            var minWidth = options.minWidth , unit = options.unit ;
                            self.resizing = true;
                            var p = item.prev();   //alert( maxWidth );

                            if( options.isRtl === true ){

                                if(prevWidth - d >= minWidth && prevWidth - d <= maxWidth){
                                    if(unit == "%")                       //+ ppad + pbor + pmar
                                       p.css("width", ( ( ( prevOWidth - d  ) / ppW) * 100 ) + "%");
                                    else
                                       p.width( prevOWidth - d );
                                    //p.find(">.sed-column-contents-pb > [sed-layout-role='pb-module']").css( 'width', (prevWidth + d) + "px" );
                                }

                            }else{

                                if(prevWidth + d >= minWidth && prevWidth + d <= maxWidth){
                                    if(unit == "%")                       //+ ppad + pbor + pmar
                                       p.css("width", ( ( ( prevOWidth + d  ) / ppW) * 100 ) + "%");
                                    else
                                       p.width( prevOWidth + d );
                                    //p.find(">.sed-column-contents-pb > [sed-layout-role='pb-module']").css( 'width', (prevWidth + d) + "px" );
                                }

                            }

                            lazyResize();
                            modules.trigger( "sed.moduleResizing" );
                            modulesPrev.trigger( "sed.moduleResizing" );

                        },
                        stop : function(event, item) {
                            self.resizing = false;

                            //var cols = item.siblings(".sed-column-pb");

                            $.each( [item , item.prev()] , function(i,item){

                                api.contentBuilder.updateShortcodeAttr( "width"  , ( (item.outerWidth()/ppW) * 100) + "%" , item.attr("sed_model_id") );
                            });

                            modules.trigger( "sed.moduleResizeStop" );
                            modulesPrev.trigger( "sed.moduleResizeStop" );

                        }
                    });
                }



            });
        },

        updateColumnWidth: function(){
            var self = this;

            $(".sed-columns-pb").livequery(function(){
                if( $(this).is("[sed-disable-editing='yes']") )
                    return ;
                self.setColsWidth( $(this) );
            });

        },

        setColsWidth: function(element){
            var self = this ,
                $this = element,
                cols = $this.children(".sed-column-pb") ,
                colsNum = cols.length ,
                W = $this.width() ,
                colsW = W/colsNum,
                ppad = self._getPadding( cols.eq(0) ),
                pbor = self._getBorder( cols.eq(0) ),
                pmar = self._getMargin( cols.eq(0) ) ;
                //nnC = self.lastColumnsWidth.length - colsNum,
                //extra = ( ( (ppad + pbor + pmar) * nnC )/W ) / colsNum;  // - || +

            cols.each(function(i,el){

                var _w;

                if( i < self.lastColumnsWidth.length && self.columnsMod == "update" ){
                                     ////api.log( self.lastColumnsWidth[i] , self.lastColumnsWidth.length , colsW);
                    _w = ( self.lastColumnsWidth[i] * (self.lastColumnsWidth.length/colsNum) ) + "%";
                    $(this).css( "width" , _w );
                }else if(self.columnsMod == "update"){
                    _w = ( ( colsW /W ) *100 ) + "%";   //(colsW - ppad - pbor - pmar)
                    $(this).css( "width" , _w );
                }

                if(i == 0){
                    $(this).addClass("sed-pb-first-col");
                }

                if(self.columnsMod == "update")
                    api.contentBuilder.updateShortcodeAttr( "width"  , _w , $(this).attr("sed_model_id") );

            });
        },

        updateColumnsNumber: function( elementId , attrValue){
            var tr = $( '[sed_model_id="' + elementId + '"]' ) ,
                tds = tr.children(".sed-column-pb") ,
                tdsLen = tds.length;

            if( !$.isNumeric( attrValue ) )
                return ;

            attrValue = parseInt( attrValue );

            if( attrValue < 1 )
                return ;

            if( attrValue > 20 ){
                alert("max column is 20");
                return ;
            }



            this.lastColumnsWidth = [];

            for (var j=0; j < tdsLen ; j++)  {
                this.lastColumnsWidth.push( ((tds.eq(j).outerWidth()/tr.width()) * 100) );
            }

              this.columnsMod = "update";

            if(tdsLen == attrValue){
                return ;
            }else if(tdsLen < attrValue){

                for (var i=0; i < attrValue - tdsLen ; i++)  {
                    var id = $( '[sed_model_id="' + elementId + '"]' ).parents(".sed-cols-table:first").next().attr("sed_model_id");
                    api.Events.trigger("sed_add_new_shortcode_element" , id , [] , "sed_columns" , '' , elementId);
                }

            }else{

                for (var i = 1; i <=  tdsLen - attrValue; i++)  {
                    ////api.log( tds.eq(tdsLen - i) );
                    api.remove( tds.eq(tdsLen - i).attr("sed_model_id") );
                }

                this.setColsWidth( tr );

                for (var i = 0; i < attrValue; i++)  {
                    tds.eq(i).find(".sed-row-pb > .sed-pb-module-container").each(function(){
                        $(this).find(".sed-pb-module-container:first").trigger("sedAfterRemoveColumns");
                    });
                }

            }

        },

        /*
        *@args :
        *----name : module OR shortcode name
        *----field : module OR shortcode
        */
        getModuleTransport : function( name , field ){
            field = ( !_.isUndefined( field ) ) ? field : "module";

            var moduleName = name;

            if(field == "shortcode"){
                var shModel = api.shortcodes[ api.currentShortcode ];
                moduleName = shModel.moduleLocation;
            }


            if( _.isUndefined( moduleName ) || _.isUndefined( api.modulesSettings[moduleName] ) )
                return ;

            var transport = ( !_.isUndefined( api.modulesSettings[moduleName].transport ) ) ?  api.modulesSettings[moduleName].transport : "default";
            return transport;
        },

                      /*  api.pageBuilder.masonryNumberColumns( elementId , attrValue);
                    break;
                    case "masonry_spacing":
                        api.pageBuilder.masonrySpacing( elementId , attrValue); */

        masonryNumberColumns: function( elementId , number , masonrySelector , masonryItemSelector ){
            var $element = $( '[sed_model_id="' + elementId + '"]' ),
                masonrySelector = masonrySelector || '.sed-archive-masonry',
                masonryItemSelector = masonryItemSelector || '.sed-archive-masonry > div',
                $container = $element.find( masonrySelector );

            $.each([1,2,3,4,5,6] , function( i , val ){
                if( $element.find( masonryItemSelector ).hasClass('sed-column-'+val) ){
                    $element.find( masonryItemSelector ).removeClass('sed-column-'+val);
                }
            });

            if( to > 6 ){
              to = 6;
            }else if( to < 1 || _.isUndefined( to ) || !to ){
              to = 1;
            }

            var  column_class='sed-column-' + to;

            $element.find( masonryItemSelector ).addClass(column_class);
            $container.masonry();
        },

        masonrySpacing: function(){

        },

        sendRowData : function( $thisElement ){
            var rowElement      = $thisElement.parents(".sed-pb-module-container:first").parent(),
                rowDialogData   = $.extend( {} , api.selectPlugin.getDataContextMenu( rowElement ) || {} , {
                    shortcodeName : "sed_row"
                });

            api.preview.send( 'rowContainerSettingData' , {
                selector        :  "sed_row" ,
                data            :  rowDialogData,
                extra :  {
                    attrs : api.contentBuilder.getAttrs( rowElement.attr("sed_model_id") ) || {}
                },
                rowId : rowElement.attr("sed_model_id")
            });
        },

        render: function(){
            //this.modulesDrag();
            this.modulesSortable();
            this.updateColumnWidth();
            this.resizeColumns();
        }

    });

    $( function() {

              ////api.log( window._sedAppPageBuilderModulesStyles );
        api.currentPageInfo = window._sedAppCurrentPageInfo;
        api.attachmentsSettings = window._sedAppPBAttachmentsSettings;
        //api.mainContentShortcode = window._sedAppMainContentShortcode;

        api.contextMenuSettings = window._sedAppEditorContextMenuSettings;
        api.itemContextMenuSettings = window._sedAppEditorItemContextMenuSettings;

        api.pageStaticContentInfo = window._sedAppPageContentInfo;

        api.pageBuilder = new api.PageBuilder({} , {
            preview         : api.preview,
            contentBuilder  : api.contentBuilder,
            wpScripts       : window._wpScripts ,
            wpStyles       : window._wpStyles
        });

        api.pageBuilder.render();    ////api.log( scripts );

        api.preview.bind( 'moduleDragHandler', function( handle ) {
            api.pageBuilder.modulesDrag( handle.option , handle.args );
        });

        if( $("#sed-post-content-container").length > 0 ){
            var content = decodeURIComponent( $("#sed_template_post_content").html() );
            $("#sed-post-content-container").html( content );
        }

        //update current page settings
        api.preview.bind( 'active', function() {

            api.preview.send( 'resetpageInfoSettings' , {
                //settings    : api.settings.values  ,
                pageId      : api.currentPageInfo.id  ,
                pageType    : api.currentPageInfo.type ,
                postType    : api.currentPageInfo.post_type
                //isFrontPage : api.currentPageInfo.isFrontPage ,
                //isHome      : api.currentPageInfo.isHome
                //previewUrl  : api.currentPageInfo.preview_url
            } );

            api.preview.send( 'checkModuleDragSync' );

            //api.preview.send( 'mainContentShortcodeUpdate' , api.mainContentShortcode);
            
			api.preview.send( 'syncAttachmentsSettings' , api.attachmentsSettings);

            api.preview.send( 'currentPageInfo' , api.currentPageInfo);

            api.preview.send( 'pageStaticContentInfo' , api.pageStaticContentInfo);

            api.Events.trigger( 'afterPageInfoReady' );
         /* api.preview.send( 'set_editor_current_page' , {
                changePage  : false ,
                page        : api.currentPageInfo
            }); */
        });

        api.fn.getPostMetaSettingId = function( metaKey ){

            if( api.currentPageInfo.type != "post" ){
                alert( "Error : this page is not single post page" );
                return ;
            }

            return "postmeta[" + api.currentPageInfo.post_type + "][" + api.currentPageInfo.id + "][" + metaKey + "]";
        };

        //Todo : Validation Css Class before add to html element
        api.fn.setCssClass = function( element , to , dataName ) {

            if( element.data( dataName ) ) {

                element.removeClass( element.data( dataName ) ).addClass( to );

            }else{

                element.addClass( to );

            }

            element.data( dataName , to );

        };



        //select module from top iframe
        api.preview.bind( 'go_back_to_main_module' , function( elementId ) {
            api.selectPlugin.select( $( '[sed_model_id="' + elementId + '"]' ) , false );
        });

        api.preview.bind( 'changePreviewType', function( attr ) {
            //reset settings if preview type is new
            api.preview.send( 'resetSettings' , {
                //settings    : api.settings.values  ,
                pageId      : api.currentPageInfo.id  ,
                pageType    : api.currentPageInfo.type ,
                //isFrontPage : api.currentPageInfo.isFrontPage ,
                //isHome      : api.currentPageInfo.isHome
                //previewUrl  : api.currentPageInfo.preview_url
            } );
		});

		api.preview.bind( 'current_attr', function( attr ) {
            api.currentAttr = attr;
		});

		api.preview.bind( 'current_shortcode', function( shortcode ) {
            api.currentShortcode = shortcode;
		});

		api.preview.bind( 'current_attr_status', function( status ) {
            api.currentAttrStatus = status;      //"force_refresh" || "normal"
		});

        api.preview.bind( 'dataModuleSkins', function( data ) {
            api.dataModulesSkins = $.extend(true , api.dataModulesSkins || {} , data);
            api.log( data );
            api.preview.send( 'dataModulesSkinsCache' , api.dataModulesSkins );
		});


        /*api.Events.bind("openUpdateModuleSettings" , function( shortcode , elementId){
            if( shortcode.tag == "sed_image" && !_.isUndefined( shortcode.attrs )
                && !_.isUndefined( shortcode.attrs.post_id ) && shortcode.attrs.post_id > 0 ){
                var attachment = _.findWhere( api.attachmentsSettings , { id : shortcode.attrs.post_id}  );
                if(attachment){
                    api.preview.send( 'addAttachmentSizes' , {
                        id :  attachment.id,
                        sizes : attachment.sizes
                    });
                }
            }
        });*/


        api.preview.bind( 'moduleSkinsTpl', function( skinsTpl ) {
            api.preview.send( 'moduleSkinsTplCache' , skinsTpl );
            $.each(skinsTpl , function( id , content){
                if( $("#" + id).length == 0 )
                    $( "body" ).append( '<script type="text/x-handlebars-template" id="' + id + '">' + content + '</script>'  );
            });
		});

        //for prevent select event render in select.min.js
        api.preview.bind( "isOpenDialogSettings" , function( state ){
            api.isOpenDialogSettings = state;
        } );

        var _lazyAjaxLoad = null;

        api( 'sed_pb_modules', function( value ) {
    		value.bind( function( currentAttrValue ) {
    		    var elementId = api.currentSedElementId;

                if( _.isUndefined( elementId ) || !elementId  )
                    return ;

                if( _.isUndefined( api.sedModulesAttrsValues[elementId] ) ){
                    api.sedModulesAttrsValues[elementId] = {};
                }

                api.sedModulesAttrsValues[elementId][api.currentAttr] = currentAttrValue;

                var modules = api.sedModulesAttrsValues || {};

                var _filterFloat = function (value) {
                    if(/^(\-|\+)?([0-9]+(\.[0-9]+)?|Infinity)$/
                      .test(value))
                      return Number(value);
                  return false;
                };

                var attrValue = modules[elementId][api.currentAttr];

                attrValue = ( _filterFloat(attrValue) === false ) ? attrValue : _filterFloat(attrValue);

                if( !_.isUndefined( api.shortcodeAutoUpdate[api.currentShortcode] ) && !_.isUndefined( api.shortcodeAutoUpdate[api.currentShortcode][api.currentAttr] ) ){
                    api.shortcodeAutoUpdate[api.currentShortcode][api.currentAttr]( elementId , attrValue );
                    return ;
                }

                if( api.currentAttr != "skin" &&  api.currentAttr != "sed_shortcode_content" )
                    api.contentBuilder.updateShortcodeAttr( api.currentAttr  , attrValue , elementId );

                if( api.currentAttr == "sed_shortcode_content" ){
                    api.contentBuilder.updateShortcodeContent( elementId , attrValue );
                }

                if( api.currentAttr == "hidden_in_mobile" || api.currentAttr == "show_mobile_only" ){
            
                    var $module = $( '[sed_model_id="' + elementId + '"]' ).parents(".sed-pb-module-container:first").parent();
                    api.contentBuilder.updateShortcodeAttr( api.currentAttr  , attrValue , $module.attr("sed_model_id") );

                    var $row = $module.parent();
                    api.contentBuilder.updateShortcodeAttr( api.currentAttr  , attrValue , $row.attr("sed_model_id") );

                }

                var transport = api.pageBuilder.getModuleTransport( api.currentShortcode  , "shortcode");

                //abort module ajax request if new setting change in module ajax processing mode && send new request
                if( transport == "ajax" ){

                    var shortcode = api.contentBuilder.getShortcode( elementId ),
                        ajaxInfo = api.pageBuilder.modulesAjaxRequests[shortcode.parent_id];

                    if( !_.isUndefined( ajaxInfo ) && ajaxInfo.processing === true ){

                        var _success = function( response ){
                            $( '[sed_model_id="' + shortcode.parent_id + '"]' ).replaceWith( response.data );
                        };

                        if (_lazyAjaxLoad)//if there is already such event this cancels the setTimeout()
                            clearTimeout(_lazyAjaxLoad);


                        //ajaxInfo.request.abort();

                        _lazyAjaxLoad = setTimeout(function () //executes a code some time in the future
                        {
                            //if (mode == "remove")
                            api.pageBuilder.ajaxLoadModules( shortcode.parent_id , _success );

                        }, 1000 );

                        return ;
                    }
                }


                api.Events.trigger( api.currentShortcode + "_" + api.currentAttr , modules , elementId , attrValue );


                if( !_.isUndefined( api.moduleAttrUpdate[api.currentAttr] ) ){
                    api.moduleAttrUpdate[api.currentAttr]( elementId , attrValue );
                    return ;
                }

                if( !_.isUndefined( api.shortcodeUpdate[api.currentShortcode] ) && !_.isUndefined( api.shortcodeUpdate[api.currentShortcode][api.currentAttr] ) ){
                    api.shortcodeUpdate[api.currentShortcode][api.currentAttr]( elementId , attrValue );
                    return ;
                }

                if(api.currentAttrStatus == "force_refresh" && api.currentAttr != "skin"){
                    api.preview.send( 'moduleForceRefresh' );
                    return ;
                }

                                                          //&& api.currentAttr != "carousel_infinite"
                if( api.currentAttr.search("carousel_") == 0   ){
                    var key_setting = api.currentAttr.substring( 9 );

                    var _ucfirst = function(str) {
                        //  discuss at: http://phpjs.org/functions/ucfirst/
                        str += '';
                        var f = str.charAt(0).toUpperCase();

                        return f + str.substr(1);
                    };


                    key_setting = key_setting.split( "_" );

                    key_setting = _.filter( key_setting , function( value ){
                        return value;
                    });

                    key_setting = _.map( key_setting , function( value , idx ){
                        return (idx == 0) ? value : _ucfirst( value ) ;
                    });

                    key_setting = key_setting.join("");

                    var $element = $( '[sed_model_id="' + elementId + '"]' ).find('.sed-carousel');

                    $element.data( key_setting , attrValue );

                    $element.trigger( "sedChangeDataCarousel" );

                    return ;
                }


                var hoverEffectGroupTest = api.currentAttr.substring( api.currentAttr.length - 19 );

                if( hoverEffectGroupTest == "_group_hover_effect" ){
                    var hoverEffectGroup = api.currentAttr.substring( 0 , api.currentAttr.length - 19 );

                    api.Events.trigger( "setHoverEffectGroup" , modules , elementId , hoverEffectGroup );
                    return ;
                }

                var skinGroupTest = api.currentAttr.substring( api.currentAttr.length - 11 );

                if( skinGroupTest == "_group_skin" ){
                    var skinGroup = api.currentAttr.substring( 0 , api.currentAttr.length - 11 );
                    api.Events.trigger( "loadGroupModulesSkin" , modules , elementId , skinGroup );
                    return ;
                }

                /*var alignSpacingAttrs = {
                    'module_align'  :   'text-align'   ,
                    'spacing_left'  :   'padding-left' ,
                    'spacing_top'   :   'padding-top'  ,
                    'spacing_bottom':   'padding-bottom'  ,
                    'spacing_right' :   'padding-right'
                };

                if( $.inArray( api.currentAttr , _.keys( alignSpacingAttrs ) ) > -1 ){
                    var moduleParent = $( '[sed_model_id="' + elementId + '"]' ).parents(".sed-pb-module-container:first") ,
                        moduleParentId = moduleParent.attr("sed_model_id") ,
                        moduleParentSh = api.contentBuilder.getShortcode( moduleParentId );

                    if( moduleParentSh.tag != "sed_module" )
                        return ;

                    api.contentBuilder.updateShortcodeAttr( api.currentAttr  , attrValue , moduleParentId );

            	    var css = '', styleId, body = $('body');

                    styleId = moduleParentId + "_inline_css";

                    $.each( alignSpacingAttrs , function( key , styleProp ){
                        if ( !_.isUndefined( modules[elementId][key] ) && ( modules[elementId][key] != 'default' || modules[elementId][key] != 'auto' ) ){

                            if( styleProp == "padding-left" && api.isRTL )
                                styleProp = "padding-right";
                            else if( styleProp == "padding-right" && api.isRTL )
                                styleProp = "padding-left";

                            if( key == 'module_align' ){
                                var $prop_val = modules[elementId][key];

                                if( modules[elementId][key] == "right" && api.isRTL )
                                    $prop_val = "left";
                                else if( modules[elementId][key] == "left" && api.isRTL )
                                    $prop_val = "right";

                                css += styleProp + " : " + $prop_val;
                            }else
                                css += styleProp + " : " + modules[elementId][key] + "px";

                            css += " !important;";
                        }
                    });


             		$( "#" + styleId ).remove();
                	style = $('<style type="text/css" id="' + styleId + '">#' + moduleParentId + '{ ' + css + ' }</style>').appendTo( body );
                    return ;
                } */

                switch ( api.currentAttr ) {
                    case "group_icon_size" :

                        api.fn.updateGroupIcons( elementId , "font_size"  , attrValue );
                        var currEl = $( '[sed_model_id="' + elementId + '"]' ).find(".hi-icon"),
                            currMo = $( '[sed_model_id="' + elementId + '"]' );
                        currEl.css("fontSize" , attrValue + "px");
                        currMo.css("fontSize" , attrValue + "px");
                        currEl.trigger( "sed.changeIconSize", [ attrValue ] );

                    break;
                    case "group_icon_color" :

                        api.fn.updateGroupIcons( elementId , "color"  , attrValue );
                        var currEl = $( '[sed_model_id="' + elementId + '"]' ).find(".hi-icon");
                        currEl.css("color" , attrValue);

                    break;
                    case "id" :

                        $( '[sed_model_id="' + elementId + '"]' ).attr("id" , $.trim( attrValue ) );

                    break;
                    /*case "class" :

                        $( '[sed_model_id="' + elementId + '"]' ).attr("id" , $.trim( attrValue ) );

                    break;*/
                    case "group_images_show_title" :
                        api.Events.trigger( "setImageGroupAttrs" , modules , elementId , "images_group" , "show_title" );
                    break;
                    case "group_images_show_description" :
                        api.Events.trigger( "setImageGroupAttrs" , modules , elementId , "images_group" , "show_description" );
                    break;
                    case "group_images_image_click" :
                        api.Events.trigger( "setImageGroupAttrs" , modules , elementId , "images_group" , "image_click" );
                    break;
                    /*if( attrValue )
                        $( '[sed_model_id="' + elementId + '"]' ).find(".ih-item h3").show();
                    else
                        $( '[sed_model_id="' + elementId + '"]' ).find(".ih-item h3").hide();

                    if( attrValue )
                        $( '[sed_model_id="' + elementId + '"]' ).find(".ih-item p").show();
                    else
                        $( '[sed_model_id="' + elementId + '"]' ).find(".ih-item p").hide();*/

                    case "pb_columns":
                        api.pageBuilder.updateColumnsNumber( elementId , attrValue);
                    break;
                    case "equal_column_width":
                        if( attrValue === true ){
                            var numCols = $( '[sed_model_id="' + elementId + '"]' ).find(">.sed-column-pb").length;
                            $( '[sed_model_id="' + elementId + '"]' ).find(">.sed-column-pb").each(function(){
                                api.contentBuilder.updateShortcodeAttr( "width"  , ( 100/numCols ) + "%" , $(this).attr("sed_model_id") );
                            });
                            api.Events.trigger( "syncModuleTmpl" , elementId , api.currentShortcode );
                        }
                    break;
                    case "masonry_number_columns":
                        api.pageBuilder.masonryNumberColumns( elementId , attrValue);
                    break;
                    case "masonry_spacing":
                        api.pageBuilder.masonrySpacing( elementId , attrValue);
                    break;
                    case "length" :
                        var targEl;
                        if( !_.isUndefined( $( '[sed_model_id="' + elementId + '"]' ).attr("length_element") ) )
                            targEl = $( '[sed_model_id="' + elementId + '"]' );
                        else
                            targEl = $( '[sed_model_id="' + elementId + '"]' ).find("[length_element]");

                        if(attrValue == "boxed")
                            targEl.addClass( "sed-row-boxed" ).removeClass("sed-row-wide");
                        else
                            targEl.addClass( "sed-row-wide" ).removeClass("sed-row-boxed");

                        $( '[sed_model_id="' + elementId + '"]' ).find(".sed-row-pb > .sed-pb-module-container").trigger( "sedChangeModulesLength" , [attrValue] );

                    break;
                    case "skin":
                        var refresh = false ;

                        if(api.currentAttrStatus == "force_refresh")
                            refresh = true;
                        else if( transport == "ajax" )
                            refresh = "ajax";

                        api.Events.trigger( "loadModuleskin" , modules , elementId , api.currentShortcode , modules[elementId][api.currentAttr] , "single" , refresh );
                    break;
                    case "instance":
                        api.Events.trigger( "setWidgetInstance" , modules , elementId );
                    break;
                    case "thumbnail_using_size" :
                    case "main_using_size" :
                        api.Events.trigger( "mediaGroupUsingSize" , modules , elementId , api.currentShortcode , api.currentAttr );
                    break;
                    case "animation" :
                        api.Events.trigger( "set_animation" , modules , elementId );
                    break;
                    default:

                        switch ( transport ) {
                            case "default":
                                api.Events.trigger( "syncModuleTmpl" , elementId , api.currentShortcode );
                            break;
                            case "ajax":

                                var shortcode = api.contentBuilder.getShortcode( elementId ),
                                    _success = function( response ){
                                        $( '[sed_model_id="' + shortcode.parent_id + '"]' ).replaceWith( response.data );
                                    };

                                api.pageBuilder.ajaxLoadModules( shortcode.parent_id , _success );

                            break;
                            case "refresh":

                                api.preview.send( 'moduleForceRefresh' );
                            break;
                        }
                }
    		});
        });

        api.Events.bind( "syncModuleTmpl" , function( elementId , shortcode_tag ){
            var startTime = new Date();
            api.contentBuilder.refreshModule( elementId );
            //api.log( new Date() - startTime );

        });

        var _getGroupItems = function( modules , elementId , groupAttr , groupVal ){
            var mainShortcode = api.contentBuilder.getShortcode(elementId) ,
                postId = api.pageBuilder.getPostId( $( '[sed_model_id="' + elementId + '"]' ) ) ,
                currentAttr = api.currentAttr ,
                modulesShortcodes = api.contentBuilder.findAllTreeChildrenShortcode( mainShortcode.id , postId );

            var groupItems = _.filter( modulesShortcodes , function( shortcode ){
                return !_.isUndefined(shortcode.attrs) && !_.isUndefined(shortcode.attrs[groupAttr]) && shortcode.attrs[groupAttr] == groupVal;
            });

            return groupItems;
        };

        api.Events.bind( "setHoverEffectGroup" , function( modules , elementId , hoverEffectGroup ){

            var currentHoverEffect = modules[elementId][api.currentAttr] ,
                groupItems = _getGroupItems( modules , elementId , "sed_hover_effect_group" , hoverEffectGroup );

            _.each( groupItems  , function( shortcode ){ //alert("test");
                api.contentBuilder.updateShortcodeAttr( 'hover_effect'  , currentHoverEffect , shortcode.id);
            });

            api.contentBuilder.refreshModule( elementId );

        });

        api.Events.bind( "setImageGroupAttrs" , function( modules , elementId , group , attr ){

            var attrValue = modules[elementId][api.currentAttr] ,
                groupItems = _getGroupItems( modules , elementId , "sed_image_group" , group );

            _.each( groupItems  , function( shortcode ){ //alert("test");
                api.contentBuilder.updateShortcodeAttr( attr  , attrValue , shortcode.id);
            });

            api.contentBuilder.refreshModule( elementId );

        });

        api.Events.bind( "loadGroupModulesSkin" , function( modules , elementId , skinGroup ){
            var mainShortcode = api.contentBuilder.getShortcode(elementId) ,
                postId = api.pageBuilder.getPostId( $( '[sed_model_id="' + elementId + '"]' ) ) ,
                currentSkin = modules[elementId][api.currentAttr] ,
                modulesShortcodes = api.contentBuilder.findAllTreeChildrenShortcode( mainShortcode.id , postId );

            var groupItems = _.filter( modulesShortcodes , function( shortcode ){
                return !_.isUndefined(shortcode.attrs) && !_.isUndefined(shortcode.attrs.sed_skin_group) && shortcode.attrs.sed_skin_group == skinGroup;
            });
          
            _.each( groupItems  , function( shortcode ){
             
                api.Events.trigger( "loadModuleskin" , modules , shortcode.id , shortcode.tag , currentSkin , "group" );
            });

            api.contentBuilder.refreshModule( elementId );

        });

        //When Change Skin for some modules like post , archive , ... , and refresh this modules
        api.preview.bind( 'ModuleRefreshChangeSkin', function( type ) {
            var pattern , name, postId;

            postId = $('body').find("[data-post-id]:first").data("postId");
            api.shortcodeCurrentPlace = "theme";

            if(type == "page"){
                pattern = api.defaultPatterns["sed_posts"];
                name = "posts";
            }else if(type == "posts"){
                pattern = api.defaultPatterns["sed_archive"];
                name = "archive";
            }

            var mainContent = _.find( api.contentBuilder.pagesThemeContent[postId] ,function(shortcode){
                  return !_.isUndefined(shortcode.attrs) && !_.isUndefined(shortcode.attrs.sed_main_content) && shortcode.attrs.sed_main_content;
               }) ,
               children = api.contentBuilder.getShortcodeChildren( mainContent.id ) ,
               shModule = children[0];

            api.pageBuilder.currentPostId = postId;
            var shortcodes = api.pageBuilder.loadPattern( pattern , shModule.id );

            shortcodes = api.pageBuilder.setHelperShortcodes( shortcodes , name );
            shortcodes = api.pageBuilder.shortcodesPatternFilter( shortcodes );

            api.contentBuilder.deleteModuleTreeChildren( shModule.id , postId )

            api.contentBuilder.addShortcodesToParent(  shModule.id , shortcodes , postId );

        });

        /*
        *@args :
        *---- @modules          : all modules shortcode object with attributes
        *---- @elementId        : current module container element Id
        *---- @shortcode_name   : module shortcode name
        *---- @currentSkin      : new skin
        *---- @type             : skin type inlude change group skin  or change normal(single) skin
        *---- @refresh          : refresh page after create new skin for modules have not tpl & transport ajax or theme modules
        */
        api.Events.bind( "loadModuleskin" , function( modules , elementId , shortcode_name , currentSkin , type , refresh ){
            var shortcode_info = api.shortcodes[shortcode_name] ,
                refresh = !_.isUndefined( refresh ) ? refresh : false ,
                module , dataSkins , scripts , styles , pattern, shortcodes ,
                postId = ( $( '[sed_model_id="' + elementId + '"]' ).length > 0 ) ? api.pageBuilder.getPostId( $( '[sed_model_id="' + elementId + '"]' ) ) : api.pageBuilder.currentPostId ,
                mainShortcode = api.contentBuilder.getShortcode(elementId) ,
                parentId , newMainShortcode;
                                            //api.log( api.dataModulesSkins , "----" , mainShortcode , "----" , shortcode_info.asModule  );
            if( !shortcode_info.asModule || !api.dataModulesSkins || !mainShortcode )
                return ;

            parentId = mainShortcode.parent_id;

            module = shortcode_info.moduleName;

            if(!api.dataModulesSkins[module])
                return ;

            dataSkin = api.dataModulesSkins[module][currentSkin];

            ////api.log( dataSkin);

            //one array include childe shortcode for this module
            pattern = dataSkin.pattern;
            if(!$.isArray( pattern ) || pattern.length == 0 ){
                pattern = api.defaultPatterns[shortcode_name];//[shortcode_info];
            }
                        ////api.log( pattern );
            if(_.isEmpty(pattern) || !pattern){
                alert("invalid skin pattern , this skin not have valid files ");
                return ;
            }

            if( $( '[sed_model_id="' + elementId + '"]' ).length > 0 )
                api.pageBuilder.currentPostId = api.pageBuilder.getPostId( $( '[sed_model_id="' + elementId + '"]' ) );

            shortcodes = api.pageBuilder.loadPattern( pattern , parentId );

              ////api.log( _.clone(shortcodes) );
            var mainIndex = api.contentBuilder.getShortcodeIndex( elementId );

            var modulesShortcodes = api.contentBuilder.findAllTreeChildrenShortcode( elementId , postId );

            modulesShortcodes.unshift( mainShortcode );

            var helperTreeIds = api.contentBuilder.getHelperTreeIds( elementId , postId );

            _.each( helperTreeIds , function( id ){
                modulesShortcodes.push( api.contentBuilder.getShortcode( id ) );
            });

            var modulesShortcodesCopy = $.extend( true, {} , modulesShortcodes );//_.map( modulesShortcodes , _.clone );
                         
            //delete pre pattern && replace new pattren         , modulesShortcodes
            api.contentBuilder.deleteModule( elementId , postId);

            var _mergeByProperty = function (arr1, arr2, prop) {

                newMainShortcode  = _.findWhere(arr2, {tag : mainShortcode.tag} );

                if( !_.isUndefined( newMainShortcode.attrs.override_skins ) ){
                    var skins = newMainShortcode.attrs.override_skins.split(",");
                    skins = _.map( skins , function( skin ){
                        return $.trim(skin).toLowerCase();
                    }) ,
                    skin = ( !_.isUndefined( mainShortcode.attrs ) && !_.isUndefined( mainShortcode.attrs.skin ) ) ? mainShortcode.attrs.skin : "default";

                    if( $.inArray( skin.toLowerCase() , skins ) > -1 ){
                        return arr2;
                    }

                }
                //if(newMainShortcode.attrs.merge_skins == ) )
                var arr1Copy = _.map(arr1 , _.clone);

                var newArr = _.map(arr2, function(arr2obj , key) {
                    var arr1obj = _.find(arr1Copy, function(arr1obj) {
                        return arr1obj[prop] === arr2obj[prop];
                    });



                    if(!arr1obj){

                        return arr2obj;

                    }else{

                      arr1Copy = _.filter( arr1Copy , function( obj ){
                          return !_.isEqual( obj , arr1obj) ;
                      });

                       if(arr2obj[prop] == "content"){
                          arr2obj.content = _.clone(arr1obj.content);
                       }

                       arr2objCopy = _.clone(arr2obj);
                       //retain previous attribute but id , class , skin
                       arr2obj.attrs = $.extend({} ,arr1obj.attrs , arr2obj.attrs);

                       //retain attrs , not change after merge
                       if( !_.isUndefined( api.shortcodes[arr2obj[prop]] ) && !_.isUndefined( api.shortcodes[arr2obj[prop]].retain_attrs ) && $.isArray( api.shortcodes[arr2obj[prop]].retain_attrs ) ){
                          _.each( api.shortcodes[arr2obj[prop]].retain_attrs , function( attr ){
                              if( !_.isUndefined( arr1obj.attrs[attr] ) ){
                                  arr2obj.attrs[attr] = _.clone( arr1obj.attrs[attr] ) ;
                              }
                          });
                       }

                       if( newMainShortcode.id == arr2obj.id ){

                           newMainShortcode = arr2obj;
                       }

                       //apply new id attribute
                       //arr2obj.attrs.sed_model_id = arr2objCopy.attrs.sed_model_id;
                       //arr2obj.attrs.class = arr2objCopy.attrs.class;
                       //arr2obj.attrs.skin = arr2objCopy.attrs.skin;

                       return arr2obj;

                    }
                });

                return newArr;
            };

            shortcodes = $.extend( true, {} , _mergeByProperty( modulesShortcodesCopy , shortcodes , "tag" )  );

            shortcodes = api.pageBuilder.setHelperShortcodes( shortcodes , mainShortcode.tag , "tag" );
            shortcodes = api.pageBuilder.shortcodesPatternFilter( shortcodes );
 
            //if( type == "group" ){
                newMainShortcode.attrs.skin =  currentSkin;
            //}

 //add parent_module to sub module pattern like change skin one image in gallery
            if(!_.isUndefined(mainShortcode.attrs) && !_.isUndefined(mainShortcode.attrs.parent_module) ){
                shortcodes = _.map( shortcodes , function( shortcode ){
                    if( _.isUndefined(shortcode.attrs) )
                        shortcode.attrs = {};
                    if(shortcode.id == newMainShortcode.id)
                        shortcode.attrs.parent_module = mainShortcode.attrs.parent_module;

                   return shortcode;
                });
            }
                  ////api.log( shortcodes );
            api.contentBuilder.addShortcodesToParent( parentId , shortcodes , postId , mainIndex );

            ////api.log(shortcodes);
            ////api.log(modulesShortcodesCopy);


            scripts = dataSkin.js;
            styles = dataSkin.css;

            var _callback = function(){
                                         ////api.log( api.contentBuilder.getShortcodeChildren( newMainShortcode.parent_id ) );
                api.contentBuilder.updateShortcodeAttr( "skin"  , currentSkin , newMainShortcode.id ); 

                /*//using in save page As one template
                api.pageModulesUsing = _.map(api.pageModuleUsing , function( module ){
                    if(module.id == newMainShortcode.id)
                        module.skin = currentSkin;

                    return module;
                });*/


                //not needed in group skins
                if( type != "group" ){
                    api.currentSedElementId = newMainShortcode.id;
                }

                /*var html = api.contentBuilder.do_shortcode( "sed_items_elastic_slider" , parentId );
                                //alert( html );
                if( _.isUndefined(html) ){
                    api.contentBuilder.deleteModule( parentId , postId );
                    api.contentBuilder.addShortcodesToParent( parentId , modulesShortcodesCopy , postId );
                    alert("skin is invalid");
                    return ;
                }*/


                if( type != "group" ){
                    //$( '[sed_model_id="' + parentId + '"]' )[0].outerHTML = html;
                    if( refresh === false )
                        api.contentBuilder.refreshModule( parentId );

                    //not needed in group skins
                    newMainShortcode.attrs.sed_model_id = newMainShortcode.id;

        			var module_container = $( '[sed_model_id="' + newMainShortcode.id + '"]'  ).parents(".sed-pb-module-container:first");

                    //api.Events.trigger("openUpdateModuleSettings" , newMainShortcode , newMainShortcode.id);

                   //not needed in group skins
                    api.preview.send( 'changeCurrentElementBySkinChange', {
                        elementId       : newMainShortcode.id ,
                        shortcode_name  : shortcode_name ,
                        attrs           : newMainShortcode.attrs
                    });

                    /*api.selectPlugin.select( $( "#" + newMainShortcode.id ) , false , false );*/

                }

            };

            if( refresh === true ){
                _callback();
                api.preview.send( 'moduleForceRefresh' );
                return ;
            }else if( refresh == "ajax" ){
                _callback();
                var _success = function( response ){
                        $( '[sed_model_id="' + newMainShortcode.parent_id + '"]'  ).replaceWith( response.data );
                    };

                api.pageBuilder.ajaxLoadModules( newMainShortcode.parent_id , _success );
            }


            /*if($.isArray( scripts )  && scripts.length > 0 ){

                if($.isArray( styles )  && styles.length > 0 )
                    api.pageBuilder.moduleStylesLoad( styles );

                api.pageBuilder.moduleScriptsLoad( scripts , _callback );

            }else if($.isArray( styles )  && styles.length > 0 ){

                api.pageBuilder.moduleStylesLoad( styles , _callback );
            }else{ */
                _callback();
           // }


        });

        //prevent render animation when attr animate not changed
        $('.wow').livequery(function(){
            $(this).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
                $(this).css('animation-name' , 'none');
            });
        })

        $("#page").find('[data-sed-animation]').livequery(function(){
            $(this).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){

                var styleId = 'sed-pb-animations-'+ $(this).attr("sed_model_id");

                $(this).removeClass( 'animated');
                $(this).css('animation-name' , 'none');
                if($( "#" + styleId ).length > 0)
                    $( "#" + styleId ).remove();
            });
        });

        api.Events.bind( "startChangePreviewMode" , function( mode ){
            if( mode == "on" ){

                $("#page").find('[data-sed-animation]').addClass("wow" ); //.removeClass( 'animated' )

            }else if( mode == "off" ){
                $("#page").find('[data-sed-animation]').removeClass( "wow" );

            }
        } );


        var _isInteger = function  (s) {
           var isInteger_re     = /^\s*(\+|-)?\d+\s*$/;
           return String(s).search (isInteger_re) != -1
        }

        var _testAnim = function(x , $element , styleId ) {

            var animation = $element.data("sedAnimation");

            if( animation ){
                $element.removeClass( animation );
                $element.removeClass( 'animated');
                $element.removeClass( 'wow');
                $element.css({   //.removeClass( x + ' wow' )
                    "animationName" : "" ,
                    "visibility" : "" ,
                    "animationIterationCount" : "" ,
                    "animationDuration" : "" ,
                    "animationDelay" : "" ,
                });
            }

            $element.addClass( x + ' animated' ).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){

                $(this).removeClass( 'animated');
                $(this).css('animation-name' , 'none');
                if($( "#" + styleId ).length > 0)
                    $( "#" + styleId ).remove();

            });

        };

        api.fn.setAnimation = function( animateSettings , cssSelector ){

            var styleId = 'sed-pb-animations-'+ _.uniqueId( "animations_preview" ) , css = "" , count , style;

            var $element = $( cssSelector );

            animateSettings = animateSettings.split(",");

            if(!animateSettings){
                $element.removeData( 'sedAnimation' );
                $element.removeAttr( 'data-sed-animation' );

                if($( "#" + styleId ).length > 0)
                    $( "#" + styleId ).remove();

                return ;
            }

            if( _.isUndefined( animateSettings[3] ) || !_.isString( animateSettings[3] ) || !$.trim( animateSettings[3] ) ){

                var sedAnimation = $element.data( 'sedAnimation' );

                if( sedAnimation && $element.hasClass( sedAnimation ) )
                    $element.removeClass( sedAnimation );

                $element.removeData( 'sedAnimation' );
                $element.removeAttr( 'data-sed-animation' );

                if($( "#" + styleId ).length > 0)
                    $( "#" + styleId ).remove();

                return ;

            }

            if( !_.isUndefined( animateSettings[2] ) && _isInteger( animateSettings[2] ) ){
                $element.attr( 'data-wow-duration' , animateSettings[2] + "ms"  );
                css += '-webkit-animation-duration: ' + animateSettings[2] + 'ms;animation-duration: ' + animateSettings[2] + 'ms;';
            }else{
                $element.removeAttr( 'data-wow-duration' );
                $element.removeData( 'wowDuration' );
            }

            if( !_.isUndefined( animateSettings[0] ) && _isInteger( animateSettings[0] ) ){
                $element.attr( 'data-wow-delay' , animateSettings[0] + "ms" );
                css += '-webkit-animation-delay: ' + animateSettings[0] + 'ms;animation-delay: ' + animateSettings[0] + 'ms;';
            }else{
                $element.removeAttr( 'data-wow-delay' );
                $element.removeData( 'wowDelay' );
            }


            if( !_.isUndefined( animateSettings[1] ) && _isInteger( animateSettings[1] ) ){
                $element.attr( 'data-wow-iteration' , animateSettings[1] );
                count = (animateSettings[1] == -1) ?  'infinite': animateSettings[1];
                css += '-webkit-animation-iteration-count: ' + count + ';animation-iteration-count: ' + count + ';';
            }else{
                $element.removeAttr( 'data-wow-iteration' );
                $element.removeData( 'wowIteration' );
            }

            if(  !_.isUndefined( animateSettings[4] ) && _isInteger( animateSettings[4] ) )
                $element.attr( 'data-wow-offset' , animateSettings[4] );
            else{
                $element.removeAttr( 'data-wow-offset' );
                $element.removeData( 'wowOffset' );
            }

            if(!css)
                return ;

            if($( "#" + styleId ).length > 0)
                $( "#" + styleId ).remove();
            //[sed-role="mm-element"]
            style = $('<style type="text/css" id="' + styleId + '">' + cssSelector + '{ ' + css + ' }</style>').appendTo( $('head') );


            _testAnim( animateSettings[3] , $element , styleId  );  //+ " wow"
            $element.data( 'sedAnimation' , animateSettings[3] );
            $element.attr( 'data-sed-animation' , animateSettings[3] );
            
        };

        api.Events.bind( "set_animation" , function( modules , elementId ){
            var animateSettings = modules[elementId]["animation"],
                $element = $( '[sed_model_id="' + elementId + '"]' ) ,
                cssSelector = '[sed_model_id="' + elementId + '"]';

            api.fn.setAnimation( animateSettings , cssSelector );

        });

        /*
        @params :
        #parent_id   : sed_add_shortcode id ,

        #attrs      : child Shortcode Attrs( override attrs for add new item if needed like add new images to galleries)
        @sample :
            0:sed_button      1:sed_test        2:sed_icons     3:sed_text_title
          [{title:"test"} , {testAtrr:"test2"} ,     {} ,     {tag:"div"}]

        [sed_add_shortcode]
            [sed_button class="btn-icon" title="button" link="#"]   0
                [sed_test testAtrr="test"]  1
                    [sed_icons class="icon-cloud-upload" size="20px"][/sed_icons] 2
                [/sed_test]
                [sed_text_title tag="span"]button[/sed_text_title]  3
            [/sed_button]
        [/sed_add_shortcode]

        #callback : one callback function after added new items
        */


        api.addModelPattern = function( options ){
            var o = {
                //newValue        :   ,
                //oldValue        :   ,
                type            : "single"  ,
                //id              :   ,
                //patternId       :   ,
                attrs           : [],
                //shortcode_name  :   ,
                refresh         : true ,
                //group           :   ,
                //max             :   ,
                //min             :   ,
                //list            :   ,
                onRemove        : function(){} ,
                onCreate        : function(){} ,
                onAddPatten     : function(){} ,
                onAfterCreate   : function(){} ,
                onAfterSingleCreate :  function(){} ,
                onAfterRemove   : function(){}
             };

            _.extend( o , options );

            var newValue = o.newValue ,
                oldValue = o.oldValue ,
                type     = o.type ;

            if( !$.isNumeric( newValue ) )
                return ;

            newValue = parseInt( newValue );

            if( !_.isUndefined( o.min ) ){
                if( newValue < o.min )
                    return ;
            }

            if( !_.isUndefined( o.max ) ){
                if( newValue > o.max ){
                    alert("max Item is " + o.max);
                    return ;
                }
            }

            if(oldValue == newValue){
                return ;
            }else if(oldValue < newValue){

                for (var i=0; i < newValue - oldValue ; i++)  {
                    if(type == "single"){

                        o.onCreate();
                        api.Events.trigger("sed_add_new_shortcode_element" , o.patternId , o.attrs[i] || [] , o.shortcode_name , o.onAddPatten , o.id , o.refresh);

                    }else if(type == "multiple"){

                        o.group.each(function(idx , element){
                            var list = ( !_.isUndefined( o.list ) ) ? $(this).find( o.list ) : $(this);
                            o.onCreate();
                            api.Events.trigger("sed_add_new_shortcode_element" , o.patternId , o.attrs[i] || [] , o.shortcode_name , o.onAddPatten , list.attr("sed_model_id") , o.refresh);
                        });

                    }
                    o.onAfterSingleCreate( );
                }

                o.onAfterCreate( );

            }else{

                for (var i = 1; i <=  oldValue - newValue; i++)  {
                    ////api.log( tds.eq(oldValue - i) );

                    if(type == "single"){

                        o.onRemove( o.list.eq(oldValue - i) );
                        api.remove( o.list.eq(oldValue - i).attr("sed_model_id") );

                    }else if(type == "multiple"){

                        o.group.each(function(idx , element){
                            var list = ( !_.isUndefined( o.list ) ) ? $(this).find( o.list ) : $(this);
                            o.onRemove( list.find( o.memberG ).eq( oldValue - i ) );
                            api.remove( list.find( o.memberG ).eq( oldValue - i ).attr("sed_model_id") );
                        });

                    }

                }

                o.onAfterRemove( );

            }

        };


        api.Events.bind("sed_add_new_shortcode_element" ,function( patternId , attrs , currentShortcode , callback , parent_id , refresh){

            var  id = patternId, parent_id = (!parent_id) ? $( '[sed_model_id="' + id + '"]' ).prev().attr("sed_model_id") : parent_id ,
                shortcodes = api.pageBuilder.addNewShortcodeElement(id , attrs , parent_id) ,
                postId = api.pageBuilder.getPostId( $( '[sed_model_id="' + id + '"]' ) ) ,
                refresh = (_.isUndefined( refresh )) ? true : refresh;

            api.contentBuilder.addShortcodesToParent( parent_id , shortcodes , postId );
            if(refresh)
                api.Events.trigger( "syncModuleTmpl" , parent_id , currentShortcode );
            if(typeof callback == "function")
                callback();
        });


        api.fn.setNumberColumns = function( to , $element ){

            var $id  = $element.attr("sed_model_id") ,
                $container  = $element.find('.sed-products-list');


            $.each([1,2,3,4,5,6] , function( i , val ){
                if( $container.find('.sed-item-product').hasClass('sed-column-'+val) ){
                    $container.find('.sed-item-product').removeClass('sed-column-'+val);
                }
            });

            if( to > 6 ){
              to = 6;
            }else if( to < 1 ){
              to = 1;
            }

            var  column_class= 'sed-column-' + to;


            $container.find('.sed-item-product').addClass(column_class);

            if( $container.hasClass('sed-products-masonry') ){
                $container.masonry();
            }else if( $container.hasClass('sed-products-grid') ){
                var css = '[sed_model_id="' + module_id + '"] .sed-products-grid .sed-item-product:nth-of-type(' + to + 'n+1){ clear: both;}';
                $("#sed-products-grid-clear").remove();
                $("head").append( '<style id="sed-products-grid-clear" type="text/css">' + css + '</style>' );
            }

        };

        api.fn.setSpacing = function( to , $element ) {
            var $container  = $element.find('.sed-products-list');

            $container.find( '.sed-item-product' ).css({
                padding: to +'px',
            });


            if( $container.hasClass('.sed-products-masonry') ){
                $element.find('.sed-products-masonry').masonry();
            }

        };

        api.fn.setBoundary = function( to , $element ) {
            var $container  = $element.find('.sed-products-list');

            if( to )
                $container.addClass("product-boundary");
            else
                $container.removeClass("product-boundary");

        };

         api.preview.bind( "subShortcodesAttrUpdate" , function( obj ){
              var className = obj["class"] ,
                  parentModelId = api.currentSedElementId ,
                  attr = obj.attr ,
                  value = obj.value;

              if( $.isArray( value ) ){
                  value = value.join(",");
              }

              var mainShortcode = api.contentBuilder.getShortcode( parentModelId ) ,
                  postId = api.pageBuilder.getPostId( $( '[sed_model_id="' + parentModelId + '"]' ) ) ,
                  shortcodes = api.contentBuilder.findAllTreeChildrenShortcode( mainShortcode.parent_id , postId );

              var groupItems = _.filter( shortcodes , function( shortcode ){
                  return !_.isUndefined(shortcode.attrs) && !_.isUndefined(shortcode.attrs["class"]) && shortcode.attrs["class"] == className;
              });

              _.each( groupItems  , function( shortcode ){
                  api.contentBuilder.updateShortcodeAttr( attr  , value , shortcode.id);
              });
         });


        api.fn.updateGroupIcons = function( elementId , attr , value ){
            var mainShortcode = api.contentBuilder.getShortcode(elementId) ,
                postId = api.pageBuilder.getPostId( $( '[sed_model_id="' + elementId + '"]' ) ) ,
                modulesShortcodes = api.contentBuilder.findAllTreeChildrenShortcode( mainShortcode.parent_id , postId );

            var groupItems = _.filter( modulesShortcodes , function( shortcode ){
                return !_.isUndefined(shortcode.tag) && shortcode.tag == "sed_icons";
            });

            _.each( groupItems  , function( shortcode ){
                api.contentBuilder.updateShortcodeAttr( attr  , value , shortcode.id);
            });
        };

        api.fn.changeIcon = function( icon ){
            var iconClass = icon.className ,
                currEl = $( '[sed_model_id="' + api.currentSedElementId + '"]' ).find(".hi-icon");

            currEl.removeClass( currEl.attr("sed-icon") ).addClass(iconClass);
            currEl.attr("sed-icon" , iconClass);
            currEl.trigger( "sed.changeIconClass", [ iconClass ] );
        };

        api.preview.bind("change_icon" , function(icon){

            api.Events.trigger( "change_icon_" + icon.shortcodeName , icon );

        });

        api.fn.getAttachmentImageFullSrc = function( attach_id ){
            var attachment;
            if( attach_id > 0 ){
                attachment = _.findWhere( api.attachmentsSettings , { id : attach_id }  );
            }

            var imgUrl = SEDNOPIC.url;
            if( !_.isUndefined( attachment ) && attachment && !_.isUndefined( attachment.url ) ){
                if( !_.isUndefined( attachment.sizes) && !_.isUndefined( attachment.sizes.full ) ){
                    imgUrl = attachment.sizes.full.url;
                }else{
                    imgUrl = attachment.url;
                }
            }

            return imgUrl;
        };

        api.fn.getAttachmentImage = function( attach_id , size , is_custom ){
            var attachment , imgUrl , _height , _width , _caption , _description , _title;
            attach_id = parseInt( attach_id );
            if( attach_id > 0 ){
                attachment = _.findWhere( api.attachmentsSettings , { id : attach_id }  );
            }

            if( !_.isUndefined( attachment ) && attachment && !_.isUndefined( attachment.url ) ){

                if( attachment.type != "image" ){
                    alert( "Attachment With ID : " + attach_id + " is not image." );
                    return false;
                }

                size = ( !_.isUndefined( size ) ) ? size : "thumbnail" ;
                is_custom = ( !_.isUndefined( is_custom ) ) ? is_custom : false ;

                if( !is_custom ){
                    if( !_.isUndefined( attachment.sizes) && !_.isUndefined( attachment.sizes[size] ) ){
                        imgUrl = attachment.sizes[size].url;
                    }else if( !_.isUndefined( attachment.sizes) && !_.isUndefined( attachment.sizes.large ) ){
                        imgUrl = attachment.sizes.large.url;
                        size = "large";
                    }else if( !_.isUndefined( attachment.sizes) && !_.isUndefined( attachment.sizes.full ) ){
                        imgUrl = attachment.sizes.full.url;
                        size = "full";
                    }
                }else if( _.isString( size ) ){
                    size = size.toLowerCase();
                    var sizes = size.split("x");
                    if( sizes.length == 2 ){
                        _width = parseInt(sizes[0]);
                        _height = parseInt(sizes[1]);
                        imgUrl = attachment.url;
                    }
                }

                if( !imgUrl ){
                    imgUrl = attachment.url;
                    _height = attachment.height;
                    _width = attachment.width;
                }else if( !_.isUndefined( attachment.sizes) && !_.isUndefined( attachment.sizes[size] ) ){
                    _height = attachment.sizes[size].height;
                    _width = attachment.sizes[size].width;
                }

                _caption = attachment.caption;
                _description = attachment.description;
                _title = attachment.title;

            }else{
                imgUrl = SEDNOPIC.url;
                _height = "auto";
                _width = "auto";
                _caption = "";
                _description = "";
                _title = "";
            }

            return {
                src         : imgUrl,
                width       : _width ,
                height      : _height ,
                title       : _title ,
                caption     : _caption ,
                description : _description
            };
        };

        api.fn.getAttachmentImageHtml = function( attach_id , size , is_custom , attrs ){
            var image = api.fn.getAttachmentImage( attach_id , size , is_custom ),
                html , attrsString = "";

            if( !image || !_.isObject( image ) )
                return "";

            var className = "" ,
                alt = image.title;

            if( _.isObject( attrs ) ){
                _.each( attrs , function( attr , value ){
                    if( attr == "class" ){
                        className += value;
                    }else if( attr == "alt" ){
                        alt = value;
                    }else{
                        attrsString += attr + '="' + value + '" ';
                    }
                });
            }

            html = '<img width="' + image.width + '" height="' + image.height + '" src="' + image.src + '" alt="' + alt + '" ' + attrsString + ' class="' + className + '">';
            return html;
        };

        api.fn.getSedAttachmentImageHtml = function( image_source , attachment_id , image_url , default_image_size , external_image_size, attrs ){
            var sedImageHtml = "";

            if( image_source == "attachment" ){
                var is_custom_size = ( _.isEmpty( default_image_size ) ) ? true : false;
                if( !is_custom_size )
                    sedImageHtml = api.fn.getAttachmentImageHtml( attachment_id , default_image_size , false , attrs );
                else
                    sedImageHtml = api.fn.getAttachmentImageHtml( attachment_id , custom_image_size , is_custom_size , attrs );

            }else if( image_source == "external" ){
                if( !_.isEmpty( image_url ) ){
                    var _width = "auto" ,
                        _height = "auto";

                    if( _.isString( external_image_size ) ){
                        var size = external_image_size.toLowerCase();
                        var sizes = size.split("x");
                        if( sizes.length == 2 ){
                            _width = parseInt(sizes[0]);
                            _height = parseInt(sizes[1]);
                        }
                    }

                    sedImageHtml = '<img src="' + image_url + '" height="' + _height + '" width="' + _width + '" />';
                }
            }

            return sedImageHtml;
        };


        api( "archive_pagination_type" , function( value ) {
            value.bind( function( to ) {

                if( to != "pagination" )
                    $(".module-pagination").addClass("hide");
                else
                    $(".module-pagination").removeClass("hide");


                if( to != "button" )
                    $(".load-more-posts-btn").addClass("hide");
                else
                    $(".load-more-posts-btn").removeClass("hide");

                //$element.attr('data-archive-pagination_type', to );

            });
        });

        api.preview.bind("moduleDragStart" , function(){
            //$(".sed-app-editor #site-editor-main-part").css("padding" , "15px 0 40px");
            $("body").addClass("module-dragging-mode");
        });

        api.preview.bind("moduleDragStop" , function(){
            //$(".sed-app-editor #site-editor-main-part").css("padding" , "");
            $("body").removeClass("module-dragging-mode");
        });

        api.preview.bind("modulesGuidelineOff" , function(){
            $("body").removeClass("modules-guideline-on");
        });

        api.preview.bind("modulesGuidelineOn" , function(){
            $("body").addClass("modules-guideline-on");
        });

    });

}(sedApp, jQuery));