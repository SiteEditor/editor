jQuery( document ).ready( function ( $ ) {

    var _getZoomOptions = function( $element ){

        var data = $element.data(),
            options = {} ,
            breakpoint = 768 ,
            zoomOptions;

        for( var property in data ){
            if(property != "sedRole" && data.hasOwnProperty( property ) )
                options[property] = data[property];
        }

        if( $(window).width() <= breakpoint ){
            options.zoomType = "inner";
        }

        zoomOptions = $.extend({} , {
            responsive          : true ,
            zoomType            : "window"//"inner", Lens, Window, Inner
        } , options );

        return zoomOptions;

    };

    $('[data-sed-role="image-zoom"]').livequery(function(){

        var containerEl = $(this) ,
            imageEl = containerEl.find('.sed-zoom');

        imageEl.elevateZoom( _getZoomOptions( containerEl ) );

    });

});