var $ = jQuery;


function SEDLoadMoreProducts( options ){
    this.options = $.extend({
        handle      : "scroll",//"scroll",
        repository  : ".woocommerce-archive",
        skin        : "default",
        container   : window ,
        btnMore     : "" ,
        paged       : 1 ,
        per_page    : 4 ,
        max_pages   : 5 ,
        offset      : 20
    }, options );

    this.flag = true;

    this.init();
}
SEDLoadMoreProducts.prototype = {
    init : function(){

        if( !this.options_is_set( 'skin' ) )
            this.options.skin = $("#sed-archive-skin").val();

        switch( this.options.handle ){
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
        $("#sed-load-more-posts-btn").click(function(e) {
            e.preventDefault();
            self.load_more();
        });
    },
    init_scroll : function(){
        var self = this;
        
        // WHENE SCROLL IS BOTTOM GO TO LOAD MORE USER AND ADD TO REPOSITORY LIST
        // ======================================================================
        //console.log( $ );
        $( this.options.container ).scroll( function(){
            if( $(window).scrollTop() + $(window).height() > $(document).height() - self.options.offset ){
                self.load_more();
            }
        });
    },
    load_more : function(){
        if( this.options.paged >= this.options.max_pages ){
            //console.log('no more post');
            return false;
        }else{
            this.load_products();
        }
    },
    load_products   : function(){
        var self = this;
        if( this.flag ){
            this.options.paged++;
            $.ajax({
                type: "POST",
                url: WPURL + "/wp-admin/admin-ajax.php" ,
                data:
                {
                    action  : "wooPagination",
                    page    : self.options.paged,
                    per_page: self.options.per_page,
                },
                beforeSend: function()
                {
                    self.flag = false;
                    //console.log( 'Ajax is send request' );

                    //$('.icon-loading').css({opacity:0, display:"block", visibility:'visible',position:'absolute', top:'21px', left:'345px'}).animate({opacity:1});
                },
                success: function( data )
                {
                    //console.log( data );
                    self.flag = true;
                    try{
                        $(self.options.repository).append( data );
                    }catch( e ){
                        console.log( e.message );
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr);
                    console.log(status);
                    console.log(error);
                }
            });
        }
            
    }
}
new SEDLoadMoreProducts({});