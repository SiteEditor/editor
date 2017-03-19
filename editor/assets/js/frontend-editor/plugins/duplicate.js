(function( exports, $ ) {

    var api = sedApp.editor ;

    api.DuplicatePlugin = api.Class.extend({

        initialize: function( params , options ){
            var self = this;

            $.extend( this, options || {} );

            this.ready();

            this.elementId;
            this.newElementId;
            this.Ids = {};
        },

        ready : function(){
            var self = this;

            $('.sed-handle-sort-row .duplicate_pb_btn').livequery( function(){

                $(this).on( "click" , function(){
                    self.duplicate( $(this).parents(".sed-bp-element:first" ) );
                });

            });

        },

        syncStyleEditorPreview : function( sed_css ){
            var self = this;
            $.each( sed_css , function( selector , properties ){

                var patt = /\[\s*(sed_model_id)\s*=\s*["\']?([^"\']*)["\']?\s*\]/g;
                var new_selector = selector.replace( patt , '[sed_model_id="' + self.newElementId + '"]' );
                             
                $.each( properties , function( setting_id , value ){
                    api.preview.trigger( 'current_css_setting_type' , "module" );
                    api.preview.trigger( 'current_css_selector' , new_selector );
                    api( setting_id ).set( value );

                });

            });
        },

        duplicate : function( element , callback ){

            var self = this;

            if( element.hasClass("sed-bp-module") ){
                element = element.find(".sed-pb-module-container:first");
            }else if( element.hasClass("sed-row-pb") ){
                element = element.find(">.sed-pb-module-container .sed-pb-module-container:first");
            }

            this.elementId = element.attr("sed_model_id");

            api.Events.trigger( "sedBeforeDuplicate" , this.elementId );
            api.Events.trigger( "before-duplicate-" + this.elementId );

            var elementId = this.elementId,
                postId = api.pageBuilder.getPostId( $( '[sed_model_id="' + elementId + '"]' ) ) ,
                mainShortcode = api.contentBuilder.getShortcode(elementId) ,
                parentId = mainShortcode.parent_id ,
                modulesShortcodes = api.contentBuilder.findAllTreeChildrenShortcode( parentId , postId ),
                moduleSh = api.contentBuilder.getShortcode( parentId ) ,
                rowSh = api.contentBuilder.getShortcode( moduleSh.parent_id ),
                rowIdx = api.contentBuilder.getShortcodeIndex( rowSh.id ),
                index;

            //add row && module shortcode To models
            modulesShortcodes.unshift( rowSh , moduleSh );

            var newPattern = api.sedShortcode.clone( modulesShortcodes );

            newPattern = this.modifyModels( newPattern );

            //create new pattern
            //newPattern = api.pageBuilder.loadPattern( newPattern , parentId );

            //set helper id for add shortcode pattern id
            newPattern = api.pageBuilder.setHelperShortcodes( newPattern , mainShortcode.tag , "tag" );

            //shortcode pattern filter
            newPattern = api.pageBuilder.shortcodesPatternFilter( newPattern ); console.log( "----------------newPattern------------" , newPattern );

            this.newElementId = newPattern[2].id;

            index = newPattern.length + rowIdx;

            api.contentBuilder.addShortcodesToParent( rowSh.id , newPattern , postId , index );

            //apply design editor css in preview
            api.pageBuilder.syncStyleEditorPreview( newPattern ); console.log( "------------newPattern--------" , newPattern );

            var _completePatternLoad = function( html ){

                var newItem = $( html ).insertAfter( $( '[sed_model_id="' + rowSh.id + '"]'  ) );

                api.selectPlugin.select( $( '[sed_model_id="' + self.newElementId + '"]' ) , false );

                api.Events.trigger( "sedAfterDuplicate" , elementId , newItem );
                api.Events.trigger( "after-duplicate-" + elementId );

                if( typeof callback == "function" )
                    callback();

            };

            var transport = api.sedShortcode.getPatternTransport( newPattern );

            if( transport == "ajax" ){

                var _success = function( response ){

                    _completePatternLoad( response.data );

                };

                api.pageBuilder.ajaxLoadModules( newPattern[0].id , _success );

            }else{

                var html = api.contentBuilder.do_shortcode( "sed_row" , newPattern[0].id , newPattern[0].id );

                _completePatternLoad( html );

            }

            /*var elementId = this.elementId,
                postId = api.pageBuilder.getPostId( $( '[sed_model_id="' + elementId + '"]' ) ) ,
                mainShortcode = api.contentBuilder.getShortcode(elementId) ,
                parentId = mainShortcode.parent_id ,
                modulesShortcodes = api.contentBuilder.findAllTreeChildrenShortcode( parentId , postId ),
                moduleSh = api.contentBuilder.getShortcode( parentId ) ,
                rowSh = api.contentBuilder.getShortcode( moduleSh.parent_id ),
                rowIdx = api.contentBuilder.getShortcodeIndex( rowSh.id ),
                modulesShortcodesCopy, index;

            //add row && module shortcode To models
            modulesShortcodes.unshift( rowSh , moduleSh );

            modulesShortcodesCopy = api.sedShortcode.clone( modulesShortcodes );//_.map( modulesShortcodes , _.clone );

            modulesShortcodesCopy = this.modifyModels( modulesShortcodesCopy ); 

            if( !_.isUndefined( mainShortcode.attrs ) && !_.isUndefined( mainShortcode.attrs.sed_css ) && !_.isEmpty( mainShortcode.attrs.sed_css ) && _.isObject( mainShortcode.attrs.sed_css ) ){
                var sedCssCopy = $.extend( true, {} , mainShortcode.attrs.sed_css );
                modulesShortcodesCopy[2].attrs.sed_css = {};
            }

            this.newElementId = modulesShortcodesCopy[2].id;

            index = modulesShortcodesCopy.length + rowIdx;

            api.contentBuilder.addShortcodesToParent( rowSh.id , modulesShortcodesCopy , postId , index );

            api.doShortcodeMode = "normal";
            html = api.contentBuilder.do_shortcode( "sed_row" , modulesShortcodesCopy[0].id , modulesShortcodesCopy[0].id );

            newItem = $(html).insertAfter( $( '[sed_model_id="' + rowSh.id + '"]'  ) );

            api.selectPlugin.select( $( '[sed_model_id="' + this.newElementId + '"]' ) , false );

            /*api.preview.send( "duplicateSettingsSync" , {
                modelsParentId : modulesShortcodesCopy[0].id ,
                place          : api.shortcodeCurrentPlace ,
                ids            : this.Ids
            });*/

            /*if( !_.isUndefined( sedCssCopy ) && !_.isEmpty( sedCssCopy ) && _.isObject( sedCssCopy ) ){
                this.syncStyleEditorPreview( sedCssCopy );
            }*/

        },

        modifyModels : function( modulesShortcodesCopy ){
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


    $( function() {

        api.duplicatePlugin = new api.DuplicatePlugin();

        api.duplicate = function(elementId , callback){
            api.duplicatePlugin.duplicate(elementId , callback);
        };

    });

}(sedApp, jQuery));