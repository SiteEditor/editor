(function( exports, $ ) {
    var api         = sedApp.editor;
    function SEDAjaxLoadPosts( element , options ){

        this.element  = element;

        this.options = $.extend({
            pagination_type     : "infinite_scroll", //"scroll//button",
            container           : window ,    //container for scroll lazy load posts
            current_url         : "" ,
            btn_more            : "" ,
            offset              : 20 ,
            max_pages           : 1 ,
            success             : function(){}
        }, options );

        this.paged = 1;
        this.flag = true;
        this._loadMore = true;

        this.init();
    }

    SEDAjaxLoadPosts.prototype = {
        init : function(){
            switch( this.options.pagination_type ){
                case "button" :
                    this.init_button();
                break;
                default:
                    this.init_scroll();
                break;

            }
        },

        options_is_set : function( key ){
            return ( typeof this.options[key] != "undefined" && this.options[key] != '' );
        },

        init_button : function(){
            var self = this;
            $( this.options.btn_more ).bind( "click" , function(e) {
                e.preventDefault();
                self.load_more();
            });
        },

        init_scroll : function(){
            var self = this ,
            	_debounce = function( fn, delay, context ) {
            		var timeout;
            		return function() {
            			var args = arguments;

            			context = context || this;

            			clearTimeout( timeout );
            			timeout = setTimeout( function() {
            				timeout = null;
            				fn.apply( context, args );
            			}, delay );
            		};
            	};
            // WHENE SCROLL IS BOTTOM GO TO LOAD MORE USER AND ADD TO REPOSITORY LIST
            // ======================================================================
           var _lazyload = _debounce(function(){
                if( $( self.options.container ).scrollTop() + $( self.options.container ).height() > $( self.element ).height() - self.options.offset ){
                    //console.log('scroll is bottom');
                    self.load_more();
                }
            } , 10);

            $( self.options.container ).bind( "scroll" , function(){
                _lazyload( );
            });

        },

        load_more : function(){

            if( this._loadMore !== true ){
                //console.log('no more post');
                return false;
            }else{
                this.load_posts();
            }

        },
        load_posts   : function( args ){
            var self    = this ,
                loadingContainer;

            if( this.options.pagination_type == "button"){
                loadingContainer = $( this.options.btn_more );
            }else{
                loadingContainer = $( this.options.btn_more ).next(); //".load-more-posts-infinite-scroll"
            }
                     //console.log( $(this.element) );
            args = {
                //paged           : this.paged + 1 ,
                ajax_archive    : true
            };

            if( this.flag ){
                this.paged++;
                args.paged = this.paged;

                if( this.paged > this.options.max_pages){
                    this._loadMore = false;
                    return ;
                }

                $.ajax({
                    type: "GET",
                    url: this.current_url ,
                    data: args,
                    beforeSend: function()
                    {
                        self.flag = false;

                        loadingContainer.addClass("loading");
                    },

                    success: function( data )
                    {
                        loadingContainer.removeClass("loading");
                        self.flag = true;
                        elements = $( $( data ).find('#ajax-update-posts').html() ).appendTo( $( self.element ) );

                        if(typeof self.options.success == "function"){
                            self.options.success( elements );
                        }

                        if(self.options.max_pages == self.paged && self.options.pagination_type == "button" ){
                            $(self.options.btn_more ).addClass("hide");
                        }

                    },
                    error: function(xhr, status, error) {
                         loadingContainer.removeClass("loading");
                        //console.log(xhr);
                        //console.log(status);
                        //console.log(error);
                    }
                });
            }

        },

        destroy     : function(){
            switch( this.options.pagination_type ){
                case "button" :
                    $( this.options.btn_more ).unbind('click');
                break;
                default:
                    $( this.options.container ).unbind('scroll');
                break;
            }
            $( this.element ).off('.sedAjaxLoadPosts').removeData( 'sedAjaxLoadPosts' );
        }

    }



    $.fn.sedAjaxLoadPosts = function( option ) {


        return this.each( function( ) {
            var $this = $(this);

              var $this   = $(this);
              var data    = $this.data('sedAjaxLoadPosts') ;

              var options = typeof option == 'object' && option;
              if (!data) $this.data('sedAjaxLoadPosts', (data = new SEDAjaxLoadPosts( this , options )));

        });
    };

}( sedApp , jQuery ));