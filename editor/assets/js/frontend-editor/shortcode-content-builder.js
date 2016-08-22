(function( exports, $ ) {

    var api = sedApp.editor;
    api.shortcodeCreate = api.shortcodeCreate || {};

    api.ShortcodeBuilder = api.Class.extend({
        initialize: function( params , options ){
            var self = this;

            this.contentModel;
            this.compiledShortcodes = [];
            this.loadedSkins = [];

            $.extend( this, options || {} );

            $.each( this.postsContent , function(indexS , shortcodes){
                if( $.isArray( shortcodes ) ){
                    $.each(shortcodes , function(index , shortcode){
                        $( '[sed_model_id="' + shortcode.id + '"]' ).attr("sed-shortcode" , "true");
                    });
                }
            });


            api.Events.bind( 'afterPageInfoReady', function() {

                self.preview.send( 'pages_theme_content_ready' , {
                    content     :   self.pagesThemeContent ,
                    postType    :   api.currentPageInfo.post_type 
                });

                self.preview.send( 'posts_content_ready' , self.postsContent );

            });

        },
        /*
        @params -------
        @typeS :::  0 : default === pre | 1=== next
        */
        addShortcodeModule: function(moduleContent , postId , nextPreId , typeS  ){
            var self = this, indexNextPre;

            this.checkContentType();

            this.contentModel[postId] = this.contentModel[postId] || [];

            if(!nextPreId){
                $.each( moduleContent , function(index , shortcode){
                    self.contentModel[postId].push( self.modifyShortcode( shortcode ) );
                });
            }else{

                moduleContent = _.map( moduleContent , function(shortcode){
                    shortcode = self.modifyShortcode( shortcode );
                    return shortcode;
                });

                indexNextPre = this.getShortcodeIndex( nextPreId );

                if( typeS == 1){
                    var nextPreShortcode = this.getShortcode( nextPreId ) ,
                        tCh = this.findAllTreeChildren( nextPreId , postId ) ,
                        currIdx = indexNextPre + tCh.length + 1;

                    this.addShortcodesToParent( nextPreShortcode.parent_id , moduleContent , postId , currIdx );
                }else{

                    var args = $.merge([indexNextPre ,0 ] , moduleContent);

                    Array.prototype.splice.apply(this.contentModel[postId] , args);
                }

            }

            this.sendData();
            ////api.log( this.get() );
        },
                    
        checkContentType : function( ){
            if(api.shortcodeCurrentPlace == "theme")
                this.contentModel = this.pagesThemeContent;
            else
                this.contentModel = this.postsContent;

        },

        addShortcodesToParent : function( parent_id , shortcodes , postId , index ){
            var self = this;

            this.checkContentType();

            /*if(parent_id == "root"){
                this.appendShortcodes( parent_id , shortcodes , postId );
                return ;
            }*/

            if( !_.isUndefined(index) && _.isNumber(index) && index > -1 )
                var newIndex = index;
            else{
                var tCh = this.findAllTreeChildren( parent_id , postId ) ,
                    pIdx = this.getShortcodeIndex( parent_id );
                var newIndex = tCh.length + pIdx + 1;
            }

            shortcodes = _.map( shortcodes , function( shortcode ){
                shortcode = self.modifyShortcode( shortcode );
                return shortcode;
            });


            if( newIndex  <= self.contentModel[postId].length ){
                var args = $.merge([ newIndex ,0 ] , shortcodes);

                Array.prototype.splice.apply(this.contentModel[postId] , args);
            }else
                this.appendShortcodes( parent_id , shortcodes , postId );

            this.sendData();

        },

        appendShortcodes : function( parent_id , shortcodes , postId ){
            var self = this;

            this.checkContentType(); 

            $.each( shortcodes , function(index , shortcode){
                shortcode = self.modifyShortcode( shortcode );
                self.contentModel[postId].push( shortcode );
            });

            this.sendData();

        },

        getContentModel : function( contentModel ){

            if( !_.isUndefined( contentModel ) ){
                contentModelName = contentModel;
                if( contentModel == "theme")
                    contentModel = this.pagesThemeContent;
                else if( contentModel == "post" )
                    contentModel = this.postsContent;
            }else
                contentModel = this.contentModel;

            return contentModel;
        },

        getHelperTreeIds : function( elementId , postId , contentModelName ){
            var self = this ,
                helperTreeIds = [] ,
                contentModel = this.getContentModel( contentModelName );

            _.each( contentModel[postId] , function( shortcode ){
                if( !_.isUndefined( shortcode.attrs ) && !_.isUndefined( shortcode.attrs.module_helper_id ) && shortcode.attrs.module_helper_id == elementId ){
                    helperTreeIds.push(shortcode.id);
                    helperTreeIds = $.merge( helperTreeIds , self.findAllTreeChildren( shortcode.id , postId , contentModelName  ) || [] );
                }
            });

            return helperTreeIds;

        },

        deleteModule : function( elementId , postId , tChildren , contentModelName ){
            var self = this;

            this.checkContentType();
                                    ////api.log( contentModelName );
            var contentModel = this.getContentModel( contentModelName );

            if($.isArray(tChildren) && tChildren.length > 0 )
                this.deleteModuleTreeChildren( elementId , postId , tChildren , contentModelName );
            else
                this.deleteModuleTreeChildren( elementId , postId , '' , contentModelName );

            var helperTreeIds = this.getHelperTreeIds( elementId , postId , contentModelName );

            contentModel[postId] = _.filter( contentModel[postId] , function(shortcode){
                if($.inArray( shortcode.id , helperTreeIds) != -1)
                    return false;
                else
                    return true;
            });

            this.deleteShortcode( elementId , postId , contentModelName );
        },

        deleteModuleTreeChildren: function( parent_id , postId , tChildren , contentModelName ){
            var self = this;

            this.checkContentType();

            var contentModel = this.getContentModel( contentModelName );

            /*$.each(self.contentModel[postId] , function(index , shortcode){
                if(shortcode.parent_id == parent_id){ alert(shortcode.id);
                    self.deleteModuleTreeChildren( shortcode.id , postId  );
                    self.contentModel[postId].splice( index , 1 );
                }
            });*/
            tChildren = ( !$.isArray(tChildren) ) ?  self.findAllTreeChildren( parent_id , postId , contentModelName ) : tChildren;
            for(var j=0; j < tChildren.length ; j++) {
                for(var i=0; i < contentModel[postId].length ; i++) {
                    var shortcode = contentModel[postId][i];

                    if( shortcode.id == tChildren[j] ){
                        contentModel[postId].splice( i , 1 );
                        break;
                    }
                }
            }

            this.sendData( contentModelName );

        },

        deleteShortcode: function( id , postId , contentModelName ){

            this.checkContentType();

            var contentModel = this.getContentModel( contentModelName );

            var index = this.getShortcodeIndex( id , contentModelName );
            contentModel[postId].splice( index , 1 );

            this.sendData( contentModelName );
        },

        findAllTreeChildren: function( parent_id , postId , contentModelName ){
            var self = this , allChildren = [];

            this.checkContentType();

            var contentModel = this.getContentModel( contentModelName ); 

            $.each( contentModel[postId] , function(index , shortcode){
                if(shortcode.parent_id == parent_id){
                    allChildren.push(shortcode.id);
                    allChildren = $.merge( allChildren , self.findAllTreeChildren( shortcode.id , postId , contentModelName  ) );
                }
            });

            return allChildren;
        },

        findAllTreeChildrenShortcode: function( parent_id , postId , contentModelName ){
            var self = this , allChildren = [];

            this.checkContentType();

            var contentModel = this.getContentModel( contentModelName );

            $.each( contentModel[postId] , function(index , shortcode){
                if(shortcode.parent_id == parent_id){
                    allChildren.push(shortcode);
                    allChildren = $.merge( allChildren , self.findAllTreeChildrenShortcode( shortcode.id , postId , contentModelName  ) );
                }
            });

            return allChildren;
        },

        modifyShortcode: function( shortcode ){

            //alert( shortcode.id );
            var new_obj = {};
            for(var prop in shortcode["attrs"]){
                new_obj[prop] = shortcode["attrs"][prop];
            }
            new_obj.sed_model_id = shortcode.id;
            shortcode["attrs"] = new_obj; ////api.log( new_obj );
            return shortcode;
        },

        getShortcode: function( id , contentModelName ){

            if($( '[sed_model_id="' + id + '"]' ).length > 0){
                var parentC = $( '[sed_model_id="' + id + '"]' ).parents(".sed-pb-post-container:first");
                api.shortcodeCurrentPlace = parentC.data("contentType");
                  // alert( api.shortcodeCurrentPlace );
                this.checkContentType();
            }

            var contentModel = this.getContentModel( contentModelName );

            var $thisShortcode;  ////api.log( this.contentModel );
            $.each( contentModel , function(postId , shortcodes){
                $.each(shortcodes , function(index , shortcode){
                    if(shortcode.id == id){
                        $thisShortcode = shortcode;
                        return false;
                    }
                });
            });
            return $thisShortcode;
        },

        getAttrs: function( id , includeContent ){

            includeContent = !!( !_.isUndefined( includeContent ) && includeContent === true );

            if($( '[sed_model_id="' + id + '"]' ).length > 0){
                var parentC = $( '[sed_model_id="' + id + '"]' ).parents(".sed-pb-post-container:first");
                api.shortcodeCurrentPlace = parentC.data("contentType");

                this.checkContentType();
            }

            var $thisShortcode = this.getShortcode( id );

            if( !$thisShortcode ){
                //api.log("for : " + id + " not found shortcode");
                return ;
            }else {

                if( includeContent === false )
                    return $thisShortcode.attrs;

                var contentModel = this.getContentModel( ) ,
                    postId = api.pageBuilder.getPostId( $( '[sed_model_id="' + id + '"]' ) );

                var shortcodeContent = _.findWhere( contentModel[postId] , { tag : "content" , parent_id : id } );

                if( ! shortcodeContent ){
                    return $thisShortcode.attrs;
                }else{

                    $thisShortcode.attrs.sed_shortcode_content = decodeURI( shortcodeContent.content );

                }

                return $.extend( true , {} , $thisShortcode.attrs );
            }
        },

        getShortcodeIndex: function( id , contentModelName ){

            if($( '[sed_model_id="' + id + '"]' ).length > 0){
                var parentC = $( '[sed_model_id="' + id + '"]' ).parents(".sed-pb-post-container:first");
                api.shortcodeCurrentPlace = parentC.data("contentType");

                this.checkContentType();
            }

            var contentModel = this.getContentModel( contentModelName );

            var index;
            $.each( contentModel , function(postId , shortcodes){
                $.each(shortcodes , function(i , shortcode){
                    if(shortcode.id == id){
                        index = i;
                        return false;
                    }
                });
            });
            return index;
        },

        //when sort modules   .empty-column
        updateModulesOrder: function( ui , sender , currentSortArea , contentModelName ){
            if( !currentSortArea || !ui.item.attr("sed_model_id") )
                return ;

            var element = ui.item , parentId = currentSortArea.data("parentId") ,
                id = element.attr("sed_model_id") ,
                $thisShortcode = this.getShortcode( id , contentModelName ) ,
                //$thisIndex = this.getShortcodeIndex( id , contentModelName ),
                next = element.next(".sed-row-pb") , prev = element.prev() ,
                modulesShortcodes = [] , postId = api.pageBuilder.getPostId( element );

            if( sender ){
                if(!$thisShortcode){
                	//api.log( "error :  ---" ,$thisShortcode , "--- not defined in line 189 : : siteeditor/site-iframe/shortcode-content-builder.min.js" );
                    return ;
                }
                //$thisShortcode.parent_id = parentId;
            }

            modulesShortcodes = this.findAllTreeChildrenShortcode( id , postId , contentModelName );

            modulesShortcodes.unshift( $thisShortcode );

            this.deleteModule(  id , postId , '' ,contentModelName );

            var modShLen = modulesShortcodes.length;

            if(next.length > 0){
                var nextId = next.attr("sed_model_id") , nextIndex = this.getShortcodeIndex( nextId ) ,
                    nextShortcode = this.getShortcode( nextId );

                parentId = nextShortcode.parent_id;

                var args = $.merge([nextIndex ,0 ] , modulesShortcodes);

                Array.prototype.splice.apply(this.contentModel[postId] , args);
            }else if(prev.length > 0){
                var prevId = prev.attr("sed_model_id") , prevIndex = this.getShortcodeIndex( prevId ) ,
                    prevShortcode = this.getShortcode( prevId );

                parentId = prevShortcode.parent_id;

                var tCh = this.findAllTreeChildren( prevId , postId ) ,
                currIdx = prevIndex + tCh.length + 1;

                this.addShortcodesToParent( parentId , modulesShortcodes , postId , currIdx );
            }else if( sender ){
                this.addShortcodesToParent( parentId , modulesShortcodes , postId );
            }

            var currThisShortcode = this.getShortcode( id );
            currThisShortcode.parent_id = parentId;

            this.sendData();

        },

        //whene sort or update modules
        //param :: {id} :::: id is shorcode id
        getShortcodeById: function( id ){

        },

        //whene update modules by settings or other actions like contextmenu actions & ...
        updateShortcode: function( new_shortcode ){
            var self = this;

            if($( '[sed_model_id="' + new_shortcode.id + '"]' ).length > 0){
                var parentC = $( '[sed_model_id="' + new_shortcode.id + '"]' ).parents(".sed-pb-post-container:first");
                api.shortcodeCurrentPlace = parentC.data("contentType");

                this.checkContentType();
            }

            this.contentModel = $.each( this.contentModel , function(page_id , shortcodes){
                self.contentModel[page_id] = $.map(shortcodes , function(shortcode){
                                                  if(new_shortcode.id == shortcode.id){
                                                      shortcode = $.extend( shortcode , new_shortcode);
                                                      return shortcode;
                                                  }else{
                                                      return shortcode;
                                                  }
                                             });
            });

            ////api.log( this.get() );
            this.sendData();
        },

        updateShortcodeAttr: function(attr , value , id){

            id = (!id) ? ((!api.currentSedElementId) ? "": api.currentSedElementId) : id;
            if( !id )
                return ;

            var $thisShortcode = this.getShortcode( id );

            if( !$thisShortcode ){
                //api.log("for : " + id + " not found shortcode");
                return ;
            }

            $thisShortcode.attrs[attr] = value;
            this.updateShortcode( $thisShortcode );

        },

        updateShortcodeContent: function( id , value ){

            if( _.isUndefined( id ) || !id || _.isUndefined( value ) )
                return ;

            var contentModel = this.getContentModel( ) ,
                postId = api.pageBuilder.getPostId( $( '[sed_model_id="' + id + '"]' ) );

            var $thisShortcode = _.findWhere( contentModel[postId] , { tag : "content" , parent_id : id } );

            if( !$thisShortcode ){
                //api.log("for : " + id + " not found shortcode");
                return ;
            }

            $thisShortcode.content = encodeURI( value );

            this.updateShortcode( $thisShortcode );
        },

        do_shortcode_cache: function( shortcode ){
			// Search the query cache for matches.
            var query = _.find( this.compiledShortcodes, function( query ) {
                return _.isEqual( shortcode.param, query.param ) && shortcode.tpl == query.tpl ;
            });

            if( query )
               return query.content;
            else
                return false;

        },

        saveDoShortcodeResult: function( param , content , tplId ){

            var newParam = $.extend( {} , param);

            /*delete newParam.content;

            if( !_.isUndefined( newParam.className ) ){
                newParam.class = _.clone( newParam.className );
                delete newParam.className;
            } */

                        ////api.log( newParam );

            this.compiledShortcodes.push({
                tpl             : tplId,
                content         : content ,
                param           : newParam
            });
        },

        do_shortcode: function( shortcode_name , id , mainShortcodeId ){

            var self = this , param , template , currModule , data;

            if( mainShortcodeId != id && shortcode_name == "sed_row" && $( '[sed_model_id="' + id + '"]' ).length > 0 ){
                var moduleId = $( '[sed_model_id="' + id + '"]' ).find(">.sed-pb-module-container .sed-pb-module-container:first").attr("sed_model_id"),
                    shortcode = this.getShortcode( moduleId ),
                    moduleName = api.shortcodes[shortcode.tag].moduleName;

                if( _.isUndefined( api.modulesSettings[moduleName].refresh_in_drag_area ) || !api.modulesSettings[moduleName].refresh_in_drag_area )
                    return $( '[sed_model_id="' + id + '"]' )[0].outerHTML;
            }

            //moduleSkin = ( _.isUndefined( moduleSkin ) || !$.trim(moduleSkin) ) ?  "default" : moduleSkin;

            if( typeof id === 'undefined'){
                var message = "SED_ERROR : id is not defined for shortcode " + shortcode_name;
                api.log( message );
            }else
                data = this.loadShortcodeParam( id );

            if(!_.isObject(data) || _.isUndefined( data.param ) || _.isUndefined( data.module ) )
                return ;

            param = data.param;
            currModule = data.module;
                         
            if( !_.isUndefined( api.shortcodeCreate[shortcode_name] ) && _.isFunction( api.shortcodeCreate[shortcode_name] ) ) {
                param = api.shortcodeCreate[shortcode_name](id, param);
            }
            //{{shortcode_content}} is reserved key for do shortcode content

            var source   = $("#" + param.skinId).html();
            if(typeof( source ) == 'undefined' ){
                console.error('tpl ' + param.skinId + " is not defined.");

            }
            var children = this.getShortcodeChildren( id );
            ////api.log( children );
            var content = "";
                          //alert( source ); //alert( id );
            $.each( children , function( index , child ){
                if(child.tag == "content")
                    content += child.content;
                else{
                    var newContent = self.do_shortcode( child.tag , child.id , mainShortcodeId );
                    if( _.isUndefined(newContent) || newContent === false ){
                        content = false;
                        return ;
                    }else{
                        content += newContent;
                    }
                }

            });

            if(content === false){
                //api.log("module content is invalid." );
                return ;
            }
                 //alert(content);
            param.context.content = content;
            if( _.isUndefined(source) ){
                //api.log("invalid tpl source And undefined tpl with this id : " , param.skinId);
                return ;
            }

            if( !_.isUndefined( api.modulesSettings[currModule].tpl_type ) && api.modulesSettings[currModule].tpl_type == "underscore"  ){
                param.context.className = _.clone( param.context.class ) ;
                delete param.context.class;
                template = api.template( param.skinId );
            }else
                template = Handlebars.compile(source);

            /*var cacheContent = this.do_shortcode_cache({
                tpl         : param.skinId ,
                param       : param.context ,
            });
                                    //api.log( cacheContent );
            if( cacheContent !== false)
                return cacheContent; */

            content  = template(param.context);

            //this.saveDoShortcodeResult( param.context , content , param.skinId );

            content = api.applyFilters( 'doShortcodeFilter' , content , shortcode_name , id , mainShortcodeId );

            return content;
        },


        loadShortcodeParam: function( id ){

            var shortcode = this.getShortcode( id ) ,
                module_name = !_.isUndefined( shortcode.attrs ) && !_.isUndefined( shortcode.attrs.parent_module ) ? shortcode.attrs.parent_module : "" ,
                shortcode_info = api.shortcodes[shortcode.tag] ,
                param = {} , atts , currEl = $( '[sed_model_id="' + id + '"]' ) , currentSkin , paramSkinId ;

            //load this main shortcode scripts
            //load ===="module"====== scripts && module styles
            api.pageBuilder.moduleScriptsLoad( api.shortcodesScripts[shortcode.tag] );
            api.pageBuilder.moduleStylesLoad( api.shortcodesStyles[shortcode.tag] );

            if( !module_name && shortcode_info.asModule ){
                module_name = shortcode_info.moduleName;
            }

            if(!module_name){
                var message = "invalid module name or parent module name for this shortcode : " + shortcode.tag;
                //api.log( message );

                return ;
            }

            atts = $.extend({} , shortcode_info.attrs , shortcode.attrs);


            if( $.inArray( shortcode_info.name , ["sed_module" , "sed_row"] ) == -1 && _.isUndefined( atts["contextmenu_disabled"] ) ){
                if(!$.trim(atts['class']))
                    atts['class'] = "module_" + shortcode.tag + "_contextmenu";
                else
                    atts['class'] = $.trim( atts['class'] ) + " module_" + shortcode.tag + "_contextmenu";
            }

            if( !_.isUndefined( atts["has_cover"] ) ){
                atts["has_cover"] = 'sed-module-cover=has-cover';
            }

            if( shortcode_info.asModule && _.isUndefined( atts["settings_disabled"] ) ){
                atts['class'] += " sed-pb-module-container";
            }

            var animateSettings = atts["animation"];

            animateSettings = animateSettings.split(",");

            var animateAttr  = "";

            if( animateSettings[3] ){
                atts['class'] += " " + animateSettings[3]; //"wow " +
                animateAttr += 'data-sed-animation=' + animateSettings[3] + ' ';
            }

    		if( animateSettings[0] != "" )
    			animateAttr += 'data-wow-delay=' + animateSettings[0] + 'ms ';

    		if( animateSettings[1] != "" )
    			animateAttr += 'data-wow-iteration=' + animateSettings[1] + ' ';

    		if( animateSettings[2] != "")
    			animateAttr += 'data-wow-duration=' + animateSettings[2] + 'ms ';

    		if( animateSettings[4] != "")
    			animateAttr += 'data-wow-offset=' + animateSettings[4] + ' ';

            //set
            var sed_attrs = '';

            sed_attrs += animateAttr;

            sed_attrs += 'sed_model_id=' + atts.sed_model_id + ' ';

            atts["sed_attrs"] = sed_attrs;

            var parentModule = ( !_.isUndefined( shortcode.attrs.parent_module ) && shortcode.attrs.parent_module ) ?  shortcode.attrs.parent_module : "";

            if(parentModule){
                if( !_.isUndefined( shortcode.attrs ) && !_.isUndefined( shortcode.attrs.skin ) ){
                    currentSkin = _.clone( shortcode.attrs.skin );
                }else{     
                    var parentModel = this.findParentModule( shortcode.parent_id , parentModule, shortcode );

                    currentSkin = ( !_.isUndefined( parentModel ) && !_.isUndefined( parentModel.attrs ) && !_.isUndefined( parentModel.attrs.skin ) ) ? parentModel.attrs.skin: atts.skin;
                }
                module_name = parentModule;
            }


            if(!parentModule)
                paramSkinId = "sed-tpl-" + atts.skin + "-" + shortcode.tag;
            else
                paramSkinId = "sed-tpl-" + currentSkin + "-" + shortcode.tag;


            if(!parentModule && atts.sub_skin)
                paramSkinId += "-" + atts.sub_skin;
            else if(atts.parent_sub_skin && parentModule )
                paramSkinId += "-" + atts.parent_sub_skin;

            param.skinId = paramSkinId + "-" + module_name ;

            if( parentModule && $("#" + param.skinId).length == 0 ){
                param.skinId = "sed-tpl-" + atts.skin + "-" + shortcode.tag + "-" + shortcode_info.moduleLocation ;
                module_name = shortcode_info.moduleLocation;
                currentSkin = _.clone( atts.skin );
            }

            if(!parentModule)
                currentSkin = _.clone( atts.skin );

            var moduleSkin = { module : module_name  , skin : currentSkin  } ,
                has_mskin = _.findWhere( this.loadedSkins , moduleSkin );



            //load ===="skin"====== scripts && skin styles
            if( _.isUndefined( has_mskin ) ){
                var skinInfo = api.modulesInfo[module_name]['skins'][currentSkin];
                if( !_.isUndefined( skinInfo ) ){
                    var scripts = skinInfo['scripts'];

                    if( $.isArray( scripts ) && scripts.length > 0 )
                        api.pageBuilder.moduleScriptsLoad( scripts );

                    var styles = skinInfo['styles'];

                    if( $.isArray( styles ) && styles.length > 0 )
                        api.pageBuilder.moduleStylesLoad( styles );

                }

                this.loadedSkins.push( moduleSkin );
            }

            param.context = atts;

            if( shortcode_info.asModule ){
                //using in save page As one template
                api.pageModulesUsing.push({
                    id      :  atts.id,
                    module  :  shortcode_info.moduleName,
                    skin    :  atts.skin
                });
            }

            return {
                param  : param ,
                module : module_name
            };
        },

        getShortcodeChildren: function( parent_id ){

            this.checkContentType();

            var children = [];
            ////api.log( this.contentModel );
            $.each(this.contentModel , function(postId , shortcodes){
                $.each(shortcodes , function(i , shortcode){
                    //alert(shortcode.parent_id);
                    if(shortcode.parent_id == parent_id){
                        children.push( shortcode );
                    }
                });
            });

            return children;
        },

        //find first parent module matched with parentModuleName
        findParentModule: function( parentModuleId , parentModule , shortcode ){

           this.checkContentType();

           var shortcodeParent , shortcodeParentInfo;
                                       ////api.log( shortcode.attrs );
           if( !_.isUndefined( shortcode.attrs ) && !_.isUndefined( shortcode.attrs.module_helper_id ) && shortcode.attrs.module_helper_id ){
                shortcodeParent = this.getShortcode( shortcode.attrs.module_helper_id );

                if( shortcodeParent ){
                    shortcodeParentInfo = api.shortcodes[shortcodeParent.tag] ;
                    if(shortcodeParentInfo.asModule && shortcodeParentInfo.moduleName == parentModule )
                        return shortcodeParent;
                }

           }

            shortcodeParent = this.getShortcode( parentModuleId );
            ////api.log( shortcodeParent );
            if( _.isUndefined(shortcodeParent) ){
                //api.log( "error :  ---" , parentModuleId , "--- is incorrect in line 467 : : siteeditor/site-iframe/shortcode-content-builder.min.js" );
                return ;
            }
            shortcodeParentInfo = api.shortcodes[shortcodeParent.tag];

            if(shortcodeParent.id == "root"){
                //api.log( "error :  ---" , parentModule , "--- not module shortcode in line 467 : : siteeditor/site-iframe/shortcode-content-builder.min.js" );
                return ;
            }else if(shortcodeParentInfo.asModule && shortcodeParentInfo.moduleName == parentModule ){
                return shortcodeParent;
            }else{
                return this.findParentModule( shortcodeParent.parent_id , parentModule , shortcodeParent );
            }
        },

        refreshModule: function( elementId ){
            var html , shortcode = this.getShortcode( elementId );

            this.checkContentType();

            if( !_.isUndefined(shortcode.attrs) && !_.isUndefined(shortcode.attrs.parent_module) && $.trim(shortcode.attrs.parent_module) ){
                var parentModel = this.findParentModule( shortcode.parent_id , shortcode.attrs.parent_module , shortcode );

                if( !_.isUndefined(shortcode.attrs) && !_.isUndefined(parentModel.attrs.parent_module) && $.trim(parentModel.attrs.parent_module)  )
                    parentModel = this.findParentModule( parentModel.parent_id , parentModel.attrs.parent_module , parentModel );

                if(!parentModel || _.isEmpty(parentModel)){
                	//api.log( "error : this parent module  ---" , shortcode.attrs.parent_module , "--- is incorrect in line 1075 : : siteeditor/site-iframe/pagebuilder.min.js" );
                    return ;
                }
                                
                //html = this.do_shortcode( parentModel.tag , parentModel.id );
                var transport = api.pageBuilder.getModuleTransport( parentModel.tag  , "shortcode");
                          
                if( transport == "default" ){
                    
                    api.doShortcodeMode = "normal";
                    html = this.do_shortcode( "sed_module" , parentModel.parent_id , parentModel.parent_id );
                    $( '[sed_model_id="' + parentModel.parent_id + '"]' ).replaceWith( html );

                    api.Events.trigger( "sedAfterRefreshModule" , elementId , shortcode , html );

                }else if( transport == "ajax" ){
                    var _success = function( response ){
                            $( '[sed_model_id="' + parentModel.parent_id + '"]' ).replaceWith( response.data );
                            api.Events.trigger( "sedAfterRefreshModule" , elementId , shortcode , response.data );
                        };

                    api.pageBuilder.ajaxLoadModules( parentModel.parent_id , _success );
                }

                //$("#" + parentModel.id).parents(".sed-pb-module-container:first").html( html );
            }else{

                api.doShortcodeMode = "normal";

                if(shortcode.tag == "sed_module"){
                    html = this.do_shortcode( shortcode.tag , elementId , elementId );
                    $( '[sed_model_id="' + elementId + '"]' ).replaceWith( html );
                }else{
                    html = this.do_shortcode( "sed_module" , shortcode.parent_id , shortcode.parent_id );
                    $( '[sed_model_id="' + shortcode.parent_id + '"]' ).replaceWith( html );
                }

                api.Events.trigger( "sedAfterRefreshModule" , elementId , shortcode , html );
                //$("#" + elementId).parent().html( html );
                /*
                if(shortcode.tag == "sed_module")
                    $("#" + elementId).replaceWith( html );
                else
                    $("#" + elementId).parents(".sed-pb-module-container:first").html( html );
                */
            }
        },

        stateChange: function(){

        },

        sendData: function( currentPlace ){

            this.checkContentType(); console.log( "------------this.postsContent---------------" , this.postsContent );

            var place = ( !_.isUndefined( currentPlace ) ) ? currentPlace : api.shortcodeCurrentPlace;

            if( place == "theme")
                this.preview.send( 'pages_theme_content_update' , this.pagesThemeContent);
            else
                this.preview.send( 'posts_content_update' , this.postsContent);

        },

        //for send to top parent and save in db
        get: function(){
            var self = this;
            return this.contentModel;
        }

    });




    /*api.ShortcodeContentBuilder = api.ShortcodeBuilder.extend({


    }); */

    $( function() {
        api.postsContent        = window._sedAppPostsContent ;


        api.modulesInfo         = window._sedAppPageBuilderModulesInfo;
        api.defaultPatterns     = window._sedShortcodesDefaultPatterns ;

        api.pagesThemeContent   = window._sedAppPagesThemeContent;
        //using in save page As one template
        api.pageModulesUsing    = window._sedAppPageModulesUsing;

        //api.log( "api.shortcodes ------- : " , api.shortcodes );

        api.preview.bind( "sed_api_shortcodes" , function( shortcodes ){
            api.shortcodes = shortcodes ;
        });

        api.preview.bind( "sed_api_shortcodes_scripts" , function( shortcodesScripts ){
            api.shortcodesScripts   = shortcodesScripts;
        });

        api.preview.bind( "sed_api_shortcodes_styles" , function( shortcodesStyles ){
            api.shortcodesStyles  = shortcodesStyles;
        });

        api.preview.bind( "sed_api_modules_settings" , function( modulesSettings ){
            api.modulesSettings  = modulesSettings;
			
			//after load api.modulesSettings && api.shortcodes
            $('.sed-bp-element').livequery(function(){
                if( $(this).is("[sed-disable-editing='yes']") )
                    return ;

                var element = $(this);

                var moduleId = element.find(">.sed-pb-module-container .sed-pb-module-container:first").attr("sed_model_id");

                if( _.isUndefined( moduleId ) || !moduleId )
                    return ;


                var shortcode = api.contentBuilder.getShortcode( moduleId );

                var shortcode_info = api.shortcodes[ shortcode.tag ] ,
                    moduleName ,
                    module ;

                if( shortcode_info.asModule  ){
                    moduleName = shortcode_info.moduleName;
                    module = api.modulesSettings[moduleName];
                    if( !_.isUndefined( module.is_special ) && module.is_special ){
                        element.addClass("sed-pb-row-module-special");
                    }
					
                    if( !_.isUndefined( module.has_extra_spacing ) && module.has_extra_spacing ){
                        element.addClass("sed-pb-row-extra-spacing");
                    }					
					
                }			
			});
        });

        api.preview.bind( "sed_api_modules_editor_js" , function( modulesEditorJs ){
            api.ModulesEditorJs  = modulesEditorJs;
        });

        api.preview.bind( "defaultModulePatterns" , function( patterns ){
            api.defaultPatterns = patterns;
        });

        api.contentBuilder = new api.ShortcodeBuilder({} , {
            preview             : api.preview ,
            postsContent        : api.postsContent ,
            pagesThemeContent   : api.pagesThemeContent ,
            shortcodes          : api.shortcodes
        });

    });

}(sedApp, jQuery));