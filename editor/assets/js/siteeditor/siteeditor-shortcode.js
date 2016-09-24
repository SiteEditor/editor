(function( exports, $ ) {

    var api = sedApp.editor;

    api.SiteEditorShortcode = api.Class.extend({

        findChildren: function( shortcodesModels , parent_id ){
            var self = this , allChildren = [];

            $.each( shortcodesModels , function(index , shortcode){
                if(shortcode.parent_id == parent_id){
                    allChildren.push(shortcode);
                    allChildren = $.merge( allChildren , self.findChildren( shortcodesModels , shortcode.id  ) );
                }
            });

            return allChildren;
        },

        findParentOf : function( shortcodesModels , parent_id ){
            var self = this , allChildren = [];

            $.each( shortcodesModels , function(index , shortcode){
                if(shortcode.parent_id == parent_id){
                    allChildren.push(shortcode);
                }
            });

            return allChildren;
        },

        index: function( id , shortcodesModels ){

            return _.findIndex( shortcodesModels , {id : id} );

        },

        findModelsById : function( shortcodesModels , id ){

            var parentModel = _.findWhere( shortcodesModels , {id : id} );

            if( _.isUndefined( parentModel ) || !parentModel )
                return [];

            var models = this.findChildren( shortcodesModels , id ) || [];
            models.unshift( parentModel );

            return models;
        },

        clone : function( shortcodesModels ){

            var cloneModels = _.map( shortcodesModels , function( model ){
                var copyModel = $.extend( true, {} , model );
                return copyModel;
            });

            return cloneModels;
        },

        getModelsType : function(){
            
            var $el = $("#website")[0].contentWindow.jQuery( '[sed_model_id="' + api.currentTargetElementId + '"]' );
            var parentC = $el.parents(".sed-pb-post-container:first");

            return parentC.data("contentType");
        },

        readyPattern : function( shortcodesModels ){
            
            var modulesShortcodesCopy = this.clone( shortcodesModels );

            modulesShortcodesCopy = this.modifyModelsIds( modulesShortcodesCopy );

            if( !_.isUndefined( modulesShortcodesCopy[0].theme_id ) )
                delete modulesShortcodesCopy[0].theme_id;

            if( !_.isUndefined( modulesShortcodesCopy[0].is_customize ) )
                delete modulesShortcodesCopy[0].is_customize;
            
            return modulesShortcodesCopy;
        },

        modifyModelsIds : function( modulesShortcodesCopy ){
            var self = this;
            this.Ids = {};

            modulesShortcodesCopy = _.map( modulesShortcodesCopy , function(shortcode){

                var id = api.pageBuilder.getNewId( );

                self.Ids[shortcode.id] = id;

                shortcode.id = id;
                shortcode.attrs.sed_model_id = id;

                return shortcode;
            });

            modulesShortcodesCopy = _.map( modulesShortcodesCopy , function(shortcode){

                if( !_.isUndefined( self.Ids[shortcode.parent_id] ) )
                    shortcode.parent_id = self.Ids[shortcode.parent_id];

                return shortcode;
            });

            return modulesShortcodesCopy;
        },

        /**
         * only using in front end editor
         * @param Model Id
         * @returns array , collection of shortcode models
         */
        getContentModel : function( modelId ){

            var parentC = $( '[sed_model_id="' + modelId + '"]' ).parents(".sed-pb-post-container:first"),
                type = parentC.data("contentType") ,
                postId = api.pageBuilder.getPostId( $( '[sed_model_id="' + modelId + '"]' ) );

            if(type == "theme")
                var contentModel = api.pagesThemeContent[postId];
            else
                var contentModel = api.postsContent[postId];

            return contentModel;
        },

        replaceModel : function( oldModelId , newModel ){

            var contentModel = this.getContentModel( oldModelId ) ,
                oldIndex = this.index( oldModelId , contentModel ) ,
                newModelLength = newModel.length;

            var args = $.merge([ oldIndex , newModelLength ] , newModel);

            Array.prototype.splice.apply( contentModel , args );

        },

        getShortcode: function( modelId  ){

            var contentModel = this.getContentModel( modelId );

            var $thisShortcode = _.findWhere( contentModel , {id : modelId} );

            return $thisShortcode;
        },

        getAttrs : function( modelId , includeContent ){

            includeContent = !!( !_.isUndefined( includeContent ) && includeContent === true );

            var $thisShortcode = this.getShortcode( modelId );

            if( !$thisShortcode ){
                //api.log("for : " + id + " not found shortcode");
                return ;
            }else {

                if( includeContent === false )
                    return $thisShortcode.attrs;

                var contentModel = this.getContentModel( modelId );

                var shortcodeContent = _.findWhere( contentModel , { tag : "content" , parent_id : modelId } );

                if( ! shortcodeContent ){
                    return $thisShortcode.attrs;
                }else{

                    $thisShortcode.attrs.sed_shortcode_content = decodeURI( shortcodeContent.content );

                }

                return $.extend( true , {} , $thisShortcode.attrs );
            }

        }

    });

    api.sedShortcode = new api.SiteEditorShortcode;

}(sedApp, jQuery));