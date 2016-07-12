function SEDAjaxLoadPosts( selector ){
    var $ = jQuery;
    this.selector = selector;

    
}
SEDAjaxLoadPosts.prototype = {
    init : function(){
        this.max   = $(this.selector).find('.sed-max-page');
        this.nonce = 
        $(this.selector).find('.sed-bt-load-more').click(function(event) {

            this.load_more();
        });
    },
    load_more : function(){

        var widgetAjaxloader = new api.Ajax({
                data : {
                    action        : 'load_more_posts',
                    nonce         : api.addOnSettings.archive.load_more ,
                    sed_page_ajax : 'sed_widget_loader'
                },

                success : function(){
                    //console.log( this.response );
                    var html = this.response.data.output || api.I18n.empty_widget;

                    var scripts = this.response.data.scripts ,
                        styles = this.response.data.styles;

                    var _callback = function(){
                        api.currentWidgetContent = html;

                        api.Events.trigger( "syncModuleTmpl" , elementId , "sed_widget" );
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
                container   : "body"
            });
    }
}