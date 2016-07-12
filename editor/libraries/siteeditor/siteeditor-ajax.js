(function( exports, $ ) {

    var api = sedApp.editor , ajaxCache = [], ajaxCacheOption = [] ;
    api.currentAjax = api.currentAjax || {};

    api.Ajax = api.Class.extend({

		initialize: function(  options , params ) {

            this.options = $.extend( true , {
                type        : "POST",
                url         :  SEDAJAX.url,
                dataType    :  "html" ,
                data        : {},
                success     : function(){} ,
                error       : function(){} ,
                always      : function(){} ,
                beforeSend  : function(){}
            }, options || {} );

            this.success = this.options.success;
            this.error   = this.options.error;

            delete this.options['success'];
            delete this.options['error'];

            if( this.options.hasOwnProperty('done') )
                delete this.options['done'];

            if( this.options.hasOwnProperty('fail') )
                delete this.options['fail'];

            if( _.isUndefined( params.container ) || ( _.isString( params.container ) && !params.container ) || params.container.length == 0 )
                params.container = "body";

            this.container  = params.container;

            if( _.isUndefined(this.options.loading) ){
                var tplId = ( $("#tmpl-sed-ajax-loading").length == 0 ) ? "sed-ajax-loading-tpl": "sed-ajax-loading";
                var tpl = api.template( tplId ) , data , html;

                if(!_.isUndefined(this.options.loadingType)){
                    data = { type : this.options.loadingType};  // loadingType : "small" || "medium" || ""
                    delete this.options.loadingType;
                }else
                    data = {type : ""};

                 html = tpl( data );

                this.loading  = $(html).appendTo( $(this.container) );

            }else{
               this.loading = this.options.loading;
               delete this.options.loading;
            }

            this.errorElm   = $('.sed-ajax-error' , this.container);
            this.successElm = $('.sed-ajax-success' , this.container);
            this.overlayElm = $('.sed-ajax-overlay' , this.container);

            $.extend( this, params );

			this.render();

        },

        render: function() {

            var data = $.extend( this.options.data , {url :  this.options.url} ) ,
                optStr = JSON.stringify( data ) ,
                cache = false , cacheIndex , self = this;

            $.each( ajaxCacheOption , function(index , value){
                if( optStr === value ){
                    cache = true;
                    cacheIndex = index;
                    return false;
                }
            });

            if( cache === true && ( _.isUndefined(this.repeatRequest) || !this.repeatRequest ) ){
                this.response = ajaxCache[cacheIndex];

                if( this.response.success === true){
                    this.success();
                }else{
                    this.error();
                }

            }else{
                this.loading.show();
                this.overlayElm.show();

                this.request = $.ajax( this.options );

                this.request.done( function() {
                    ajaxCacheOption.push( optStr );
                    self.requestDone.apply(self, arguments);
                });

                this.request.fail( function() {
                    self.requestFail.apply(self, arguments);
                });

            }

        },

		requestDone: function( response , textStatus, jqXHR ) {
		    var self = this;
                           
            this.loading.hide();
            this.overlayElm.hide();
            // Check if the user is logged out.
            if ( '0' == response ) {
                if( api.hasOwnProperty('previewer') ){
                    api.previewer.preview.iframe.hide();
                    api.previewer.login().done( function() {
                        self.render();
                        api.previewer.preview.iframe.show();
                    } );
                    return;
                }else{
                    api.currentAjax = self;
                    api.preview.send("check_user_logged_out");
                    return;
                }

            }

            // Check for cheaters.
            if ( '-1' == response ) {
                if( api.hasOwnProperty('previewer') ){
                    api.previewer.cheatin();
                    return;
                }else{
                    api.preview.send("check_cheaters");
                    return;
                }
            }

            if ( '-2' == response ) {
                self.errorElm.show();
                self.errorElm.html( api.I18n.invalid_data);
                return;
            }

            self.errorElm.hide();   //alert( response );

            self.response = $.parseJSON( response );
            ajaxCache.push( self.response );

            if( self.response.success === true){
                self.success();
            }else{
                self.error();
            }
        },

		requestFail: function( jqXHR, textStatus, errorThrown ) {
            var self = this;

		    this.loading.hide();
            this.overlayElm.hide();

            var _try = '<p>' + api.I18n.please + '<a href="#" class="sed-ajax-try-again"><b>' + api.I18n.try_again + '</b></a></p>';

            if (jqXHR.textStatus === 0) {
                self.errorElm.html( api.I18n.disconnect );
            } else if (jqXHR.textStatus == 404) {
                self.errorElm.html( api.I18n.not_found );
            } else if (jqXHR.textStatus == 500) {
                self.errorElm.html( api.I18n.internal_error );
            } else if (errorThrown === 'parsererror') {
                self.errorElm.html( api.I18n.parser_error );
            } else if (errorThrown === 'timeout') {
                self.errorElm.html( api.I18n.timeout );
            } else if (errorThrown === 'abort') {
                self.errorElm.html( api.I18n.abort );
            } else {
                self.errorElm.html( api.I18n.uncaught + jqXHR.responseText );
            }

            var tryElm = $( _try ).appendTo( self.errorElm );
            tryElm.on("click" , function(){
                self.tryAgain();
            });
        },

        tryAgain: function() {
            this.render();
        }

    });


}(sedApp, jQuery));