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
        }

    });

    api.sedShortcode = new api.SiteEditorShortcode;

}(sedApp, jQuery));