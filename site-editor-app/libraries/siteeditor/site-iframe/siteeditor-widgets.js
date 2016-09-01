(function( exports, $ ) {

    var api = sedApp.editor;
    api.shortcodeCreate = api.shortcodeCreate || {} ;
    api.currentWidgetContent = api.currentWidgetContent || "";
    api.currentWidgetIdBase = api.currentWidgetIdBase || "";
    api.widgetInstance = api.widgetInstance || {};

    api.SiteEditorWidgets = api.Class.extend({
        initialize: function( params , options ){
            var self = this;

            $.extend( this, options || {} );

            api.Events.bind( "sedBeforeRemove" , function( elementId ){
                self.removeWidget( elementId );
            });

        },

        removeWidget : function( elementId ){
            var shortcode = api.contentBuilder.getShortcode( elementId ) ,
                postId = api.pageBuilder.getPostId( $("#" + elementId) ) ,
                modulesShortcodes = api.contentBuilder.findAllTreeChildrenShortcode( elementId , postId ) ,
                $thisValue = api( "page_widgets_list" )();

            modulesShortcodes.unshift( shortcode );

            _.each( modulesShortcodes , function( model ){
                if( model.tag == "sed_widget" ){

                    var id_base = model.attrs.id_base,
                        index = $.inArray( id_base , $thisValue );

                    if( index > -1){
                        $thisValue.splice( index , 1 );
                        api( "page_widgets_list" ).set( $thisValue );
                    }

                }
            });

        },

        widgetsHandler: function( element , name , dropItem, direction ){

            var widget = element.attrs["sed-widget-class"] ,
                widgetIdBase = element.attrs["sed-widget-id-base"] ,
                widgetClassName = element.attrs["sed-widget-classname"] ,
                tpl = $("#widget-tpl-" + widgetIdBase );

            $(tpl.html()).appendTo( $("body") );

            api.currentWidgetIdBase = widgetIdBase;

            var instance = $("#sed-app-control-widget-" + widgetIdBase).find('.widget-form').serialize() ,
                newItem = api.pageBuilder.addModuleToPost( "widget" , dropItem, direction ),
                elementId = newItem.find(':first-child > :first-child').attr("id");

            //save all widgets when add its to this page
            var $thisValue = api( "page_widgets_list" )();
            $thisValue.push( widgetIdBase );
            api( "page_widgets_list" ).set( $thisValue );
            //send to parent Iframe , binded in siteeditor/plugins/themeSynchronization/plugin.min.js
            api.preview.send( 'pageWidgetsList' , $thisValue );

            api.contentBuilder.updateShortcodeAttr( 'instance'  , instance      , elementId);

            this.the_widget( widget , instance , "" , widgetIdBase , elementId , widgetClassName );
        },

        the_widget: function( widget , instance , args , widgetIdBase , elementId , widgetClassName ){

            api.contentBuilder.updateShortcodeAttr( 'widget'    , widget        , elementId);
            api.contentBuilder.updateShortcodeAttr( 'args'      , ""            , elementId);
            api.contentBuilder.updateShortcodeAttr( 'id_base'   , widgetIdBase  , elementId);
            api.contentBuilder.updateShortcodeAttr( 'class_name'   , widgetClassName  , elementId);

            api.preview.send( 'moduleForceRefresh' );
            /*var widgetAjaxloader = new api.Ajax({
                data : {
                    widget        : widget ,
                    instance      : instance ,
                    args          : args ,
                    id_base       : widgetIdBase ,
                    class_name    : widgetClassName ,
                    action        : 'widget_load',
                    nonce         : api.addOnSettings.widgets.nonce ,
                    sed_page_ajax : 'sed_widget_loader'
                },

                success : function(){
                    ////api.log( this.response );
                    var html = this.response.data.output || api.I18n.empty_widget;

                    var scripts = this.response.data.scripts ,
                        styles = this.response.data.styles;

                    var _callback = function(){
                        api.currentWidgetContent = html;

                        api.Events.trigger( "syncModuleTmpl" , elementId , "sed_widget" );
                        api.currentWidgetContent = "";
                    };

                    if($.isArray( scripts )  && scripts.length > 0 ){

                        if($.isArray( styles )  && styles.length > 0 )
                            api.pageBuilder.moduleStylesLoad( styles );

                        api.pageBuilder.moduleScriptsLoad( scripts , _callback );

                    }else if($.isArray( styles )  && styles.length > 0 ){

                        api.pageBuilder.moduleStylesLoad( styles , _callback );
                    }else{
                        _callback();
                    }

                },

                error : function(){
                   alert( this.response.data.output );
                }

            },{
                container   : "#" + elementId
            });  */

        },

		/**
		 * Find all inputs in a widget container that should be considered when
		 * comparing the loaded form with the sanitized form, whose fields will
		 * be aligned to copy the sanitized over. The elements returned by this
		 * are passed into this._getInputsSignature(), and they are iterated
		 * over when copying sanitized values over to the the form loaded.
		 *
		 * @param {jQuery} container element in which to look for inputs
		 * @returns {jQuery} inputs
		 * @private
		 */
		_getInputs: function( container ) {
			return $( container ).find( ':input[name]' );
		},

        paramToObject: function( instance , idBase ){
            if(!instance)
                return {};

            var val = decodeURIComponent(instance.replace(/\+/g," "));
            var fvals = val.split("&") , $thisVal , context = {};

            $.each(fvals , function(index , value){
                var param = value.split("=");
                var property = param[0].replace("widget-" + idBase + "[__i__]","");
                property = property.replace(/[\[\]]/g , "");

                context[property] = param[1] || "";
            });
                   
            return context;
        },

        do_widget: function( instance , source , idBase ){
            var self = this , context;

            context = this.paramToObject( instance , idBase );

            var template = Handlebars.compile(source);
            content  = template(context);

            return content;
        }

    });




    /*api.ShortcodeContentBuilder = api.ShortcodeBuilder.extend({


    }); */

    $( function() {
       //api.postsContent = window._sedAppPostsContent ;
       /*  styles = window._sedAppPageBuilderModulesStyles;*/
       api.widgetTpls = window._sedAppEditorWidgetTpls;

        api.widgetBuilder = new api.SiteEditorWidgets({} , {
            preview : api.preview,
            //postsContent : api.postsContent,
            //shortcodes : api.shortcodes
        });

        api.preview.bind( 'current_widget' , function( widget ) {
            api.currentWidget = widget;
		});

        api.Events.bind( "setWidgetInstance" , function( modules , elementId ){
                                                      
            var idBase = api.currentWidget.idBase ,
                fieldName = api.currentWidget.fieldName.replace("widget-" + idBase + "[__i__]","");

            fieldName = fieldName.replace(/[\[\]]/g , "");

            var attr = idBase + "_" + fieldName ,
            instance = modules[elementId][api.currentAttr] ,
            val = decodeURIComponent(instance.replace(/\+/g," "));
            var fvals = val.split("&") , $thisVal;

            $.each(fvals , function(index , value){
                var param = value.split("=");
                if(param[0] == api.currentWidget.fieldName){
                    $thisVal = param[1];
                    return false;
                }
            });

            if(api.widgetInstance[attr])
                api.widgetInstance[attr]( elementId , $thisVal , idBase );
            else if(api.widgetTpls[idBase]){
                api.currentWidgetContent = api.widgetBuilder.do_widget( instance , api.widgetTpls[idBase] , idBase );

                api.Events.trigger( "syncModuleTmpl" , elementId , "sed_widget" );
                api.currentWidgetContent = "";
            }else
                api.widgetBuilder.the_widget( api.currentWidget.phpClass , instance , "" , idBase , elementId , api.currentWidget.className );

            //api.Events.trigger( "syncModuleTmpl" , elementId , api.currentShortcode );

        });


        /*api.widgetInstance.calendar_title = function( elementId , value , idBase ){

            var wTitle = $("#" + elementId).find('.widgettitle');
            if(wTitle.length > 0)
                wTitle.html( value );
            else
                $('<h2 class="widget-title">' + value  + '</h2>').prependTo( $("#" + elementId).find('>.widget_calendar') );

        };*/


        //pageBuilder.render();
    });

}(sedApp, jQuery));