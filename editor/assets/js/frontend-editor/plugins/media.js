(function( exports, $ ) {

    var api = sedApp.editor ;

    api.MediaPlugin = api.Class.extend({

        initialize: function( params , options ){
            var self = this;

            $.extend( this, options || {} );

            this.ready();

            this.ready;
        },

        ready : function( data ){
            var self = this;

            api.preview.bind( "syncMediaAttachments" , function( data ) {
                self.syncMediaAttachments( data );
            });

            /*api.Events.bind( "mediaGroupUsingSize" , function( modules , elementId , shortcode_tag ,attr ){
                self.updateGroupUsingSize( modules , elementId , shortcode_tag ,attr );
            });*/


        },

        /*updateGroupUsingSize : function( modules , elementId , shortcode_tag ,attr ){
                         
            var postId = api.pageBuilder.getPostId( $( '[sed_model_id="' + elementId + '"]' ) ) ,
                sizeType = attr.replace("_using_size" , "") ,
                shortcode = api.contentBuilder.getShortcode( elementId ) ,
                currentSize = modules[elementId][attr];

            if( !_.isUndefined(shortcode.attrs) && !_.isUndefined(shortcode.attrs.sed_role) && shortcode.attrs.sed_role == "media-list"
                && !_.isUndefined(shortcode.attrs.sed_list_type) && shortcode.attrs.sed_list_type == sizeType ){

                var listModel = shortcode;

            }else{
                var modulesShortcodes = api.contentBuilder.findAllTreeChildrenShortcode( elementId , postId );

                var listModel = _.find( modulesShortcodes , function( shortcode ){
                    return !_.isUndefined(shortcode.attrs) && !_.isUndefined(shortcode.attrs.sed_role) && shortcode.attrs.sed_role == "media-list"
                            && !_.isUndefined(shortcode.attrs.sed_list_type) && shortcode.attrs.sed_list_type == sizeType ;
                });
            }

            var listShortcodes = api.contentBuilder.findAllTreeChildrenShortcode( listModel.id , postId );

            var list = _.filter( listShortcodes , function( shortcode ){
                return !_.isUndefined(shortcode.attrs) && !_.isUndefined(shortcode.attrs.sed_main_media) && shortcode.attrs.sed_main_media ;
            });
               //console.log( list );
            _.each( list  , function( shortcode ){
                api.contentBuilder.updateShortcodeAttr( 'using_size'  , currentSize , shortcode.id);
                if(!_.isUndefined(shortcode.attrs) && !_.isUndefined(shortcode.attrs.post_id) && shortcode.attrs.post_id > 0)
                    api.Events.trigger( "sed_image_using_size" , modules , shortcode.id , currentSize );
            });

            api.contentBuilder.refreshModule( elementId );

        }, */

        syncMediaAttachments : function( data ){
            //attachment , elementId
            var attachment = data.attachment ,
                targetElement = data.targetElement ,
                shortcodeName = data.shortcode;

            api.attachmentsSettings = _.filter( api.attachmentsSettings , function( attach ){
                return attach.id != attachment.id;
            });

            api.attachmentsSettings.push( attachment );

            /*if(shortcodeName == "sed_image" && attachment.type == "image"){
                var shortcode = api.shortcodes[shortcodeName] ,
                    currAttrs = api.contentBuilder.getAttrs( targetElement ) ,
                    attachAttrs = {
                        alt         : attachment['title']  ,
                        title       : attachment['title']  ,
                        description : attachment['description']
                    };
                                
                                //api.log( shortcode.attrs );
                _.each( attachAttrs , function( value , attr ){
                    if( (_.isUndefined( currAttrs[attr] ) || currAttrs[attr] == shortcode.attrs[attr] ) && value ){
                        api.contentBuilder.updateShortcodeAttr( attr  , value , targetElement);
                        currAttrs[ attr ] = value;
                    }
                });

                api.preview.send( 'shortcodeControlsUpdate' , {
                    shortcode   : shortcodeName,
                    attrs       : currAttrs ,
                    targetAttrs : _.keys( attachAttrs )
                });

                api.contentBuilder.updateShortcodeAttr( "post_id"  , attachment.id , targetElement);

            }else if( (shortcodeName == "sed_video" || shortcodeName == "sed_audio") && (  attachment.type == "audio" || attachment.type == "video" ) ){
                var shortcode = api.shortcodes[shortcodeName] ,
                    currAttrs = api.contentBuilder.getAttrs( targetElement ) ,
                    attachAttrs = {
                        artist              : attachment.meta.artist || api.I18n.unknown_artist  ,
                        setting_title       : attachment.title  ,
                        desc                : attachment.description   ,
                        setting_width       : attachment.width  ,
                        setting_height      : attachment.height
                    };

                    if( !_.isUndefined( attachment.thumb ) && !_.isUndefined( attachment.thumb.src ) && attachment.thumb.src != attachment.icon ){
                        attachAttrs.setting_poster = attachment.thumb.src;
                    }

                                //api.log( currAttrs );
                                //api.log( shortcode.attrs );
                _.each( attachAttrs , function( value , attr ){  //(_.isUndefined( currAttrs[attr] ) || currAttrs[attr] == shortcode.attrs[attr] )&& 
                    if(  value ){
                        api.contentBuilder.updateShortcodeAttr( attr  , value , targetElement);
                        currAttrs[ attr ] = value;
                    }
                });

                api.preview.send( 'shortcodeControlsUpdate' , {
                    shortcode   : shortcodeName ,
                    attrs       : currAttrs ,
                    targetAttrs : _.keys( attachAttrs )
                });

            }  */


        },



     });

    $( function() {

        api.mediaPlugin = new api.MediaPlugin();

    });

}(sedApp, jQuery));