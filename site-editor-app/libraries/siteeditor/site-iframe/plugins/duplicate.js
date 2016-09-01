(function( exports, $ ) {

    var api = sedApp.editor ;

    api.DuplicatePlugin = api.Class.extend({

        initialize: function( params , options ){
            var self = this;

            $.extend( this, options || {} );

            this.ready();

            this.elementId;
            this.Ids = {};
        },

        ready : function(){
            var self = this;

            api.preview.bind( "duplicateSettingsSynced" , function( synced ){

                if( !_.isUndefined( synced.modelsSettings['sed_pb_modules'] ) ){
                    var sed_page_customized = api.get();
                    $.extend( true, sed_page_customized['sed_pb_modules'] , synced.modelsSettings['sed_pb_modules'] );
                }

                self.styleEditorSettings( synced.modelsSettings );
            });

        },

        styleEditorSettings : function( settings ){

            $.each( settings , function( setting_id , selectors ){

                if( setting_id == 'sed_pb_modules' )
                    return ;

                $.each( selectors , function(selector , value){
                    api.preview.trigger( 'current_css_selector' , selector );

                    var $thisValue = api( setting_id )();
                    $thisValue[ selector ] = value;
                    api( setting_id ).set( $thisValue );
                });

            });
        },

        duplicate : function( element , callback ){

            if( element.hasClass("sed-bp-module") ){
                element = element.find(".sed-pb-module-container:first");
            }else if( element.hasClass("sed-row-pb") ){
                element = element.find(">.sed-pb-module-container .sed-pb-module-container:first");
            }

            this.elementId = element.attr("id");

            api.Events.trigger( "sedBeforeDuplicate" , this.elementId );
            api.Events.trigger( "before-duplicate-" + this.elementId );

            var elementId = this.elementId,
                postId = api.pageBuilder.getPostId( $("#" + elementId) ) ,
                mainShortcode = api.contentBuilder.getShortcode(elementId) ,
                parentId = mainShortcode.parent_id ,
                modulesShortcodes = api.contentBuilder.findAllTreeChildrenShortcode( parentId , postId ),
                moduleSh = api.contentBuilder.getShortcode( parentId ) ,
                rowSh = api.contentBuilder.getShortcode( moduleSh.parent_id ),
                rowIdx = api.contentBuilder.getShortcodeIndex( rowSh.id ),
                modulesShortcodesCopy, index;

            //add row && module shortcode To models
            modulesShortcodes.unshift( rowSh , moduleSh );

            modulesShortcodesCopy = $.extend( true, {} , modulesShortcodes );//_.map( modulesShortcodes , _.clone );

            modulesShortcodesCopy = this.modifyModels( modulesShortcodesCopy , postId );

            if( !_.isUndefined( modulesShortcodesCopy[0].theme_id ) )
                delete modulesShortcodesCopy[0].theme_id;

            if( !_.isUndefined( modulesShortcodesCopy[0].is_customize ) )
                delete modulesShortcodesCopy[0].is_customize;

            index = modulesShortcodesCopy.length + rowIdx;

            api.contentBuilder.addShortcodesToParent( rowSh.id , modulesShortcodesCopy , postId , index );

            api.doShortcodeMode = "normal";
            html = api.contentBuilder.do_shortcode( "sed_row" , modulesShortcodesCopy[0].id , modulesShortcodesCopy[0].id );

            newItem = $(html).insertAfter( $("#" + rowSh.id ) );

            api.preview.send( "duplicateSettingsSync" , {
                modelsParentId : modulesShortcodesCopy[0].id ,
                place          : api.shortcodeCurrentPlace ,
                ids            : this.Ids
            });

            api.Events.trigger( "sedAfterDuplicate" , elementId , newItem );
            api.Events.trigger( "after-duplicate-" + elementId );

            if( typeof callback == "function" )
                callback();

        },

        modifyModels : function( modulesShortcodesCopy , postId ){
            var self = this;
            this.Ids = {};

            modulesShortcodesCopy = _.map( modulesShortcodesCopy , function(shortcode){

                if( shortcode.tag != "content" ){
                    shortcode_info = api.shortcodes[shortcode.tag];

                    if(shortcode_info.asModule){
                        id = api.pageBuilder.getNewId( shortcode_info.moduleName , "module" , postId );
                    }else{
                        id = api.pageBuilder.getNewId( shortcode.tag , "shortcode" , postId );
                    }
                }else{
                    id = api.pageBuilder.getNewId( shortcode.tag , "shortcode" , postId );
                }

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


    $( function() {

        api.duplicatePlugin = new api.DuplicatePlugin();

        api.duplicate = function(elementId , callback){
            api.duplicatePlugin.duplicate(elementId , callback);
        };

    });

}(sedApp, jQuery));