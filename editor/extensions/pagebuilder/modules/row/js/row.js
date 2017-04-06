(function( exports, $ ) {
    var api = sedApp.editor;

    $( function() {

         /*var fixSpacingResponsive = function(){
          var $element = $('body') ,
              Browser_w  = $(window).width(),
              sheetWidth = $('body').data("sheetWidth"),
              main = $('body').find("#site-editor-main-part");

          if(Browser_w <= sheetWidth || (main.hasClass("sed-row-boxed") == true)){
            //console.log($element.find('.sed-pb-post-container > .sed-row-wide .sed-row-boxed:first'));

            $element.find('.sed-pb-post-container[data-content-type="post"] > .sed-row-wide .sed-row-boxed').each(function(){
                if( $(this).parentsUntil( $( '.sed-pb-post-container[data-content-type="post"]' ), ".sed-row-boxed" ).length == 0 && $(this).parentsUntil( $( '.sed-pb-post-container[data-content-type="post"]' ), ".sed-column-pb").length == 0 ){
                    $(this).css({
                        "paddingLeft": '30px',
                        "paddingRight":'30px'
                    });
                } 
            });

            $element.find('.sed-pb-post-container[data-content-type="post"] > .sed-row-boxed').css({
                "paddingLeft": '30px',
                "paddingRight":'30px'
            });

           /* $element.find('.sed-site-main-part > .sed-row-wide').each(function(){
              if($(this).hasClass("sed-main-content-row-role") == false){   *
                $element.find('.sed-site-main-part > .sed-row-wide .sed-row-boxed').each(function(){
                    if( $(this).parentsUntil( $( '.sed-site-main-part' ), ".sed-row-boxed" ).length == 0 && $(this).parentsUntil( $( '.sed-site-main-part' ), ".sed-column-pb").length == 0 ){
                        $(this).css({
                            "paddingLeft": '30px',
                            "paddingRight":'30px'
                        });
                    }
                });
           /*   }
           }); *

          }

        };

        var _lazyFix = _.debounce(function(){
            fixSpacingResponsive();
        }, 50);

        fixSpacingResponsive();

        $(window).resize(function(){
            _lazyFix();
        });

       $(".sed-columns-pb").livequery(function(){
            var spacing = $(this).data("responsiveSpacing");
            $(this).find(">td >.sed-column-contents-pb > .sed-row-pb > .sed-pb-module-container").find("")
        });*/



        $(".sed-pb-row-sticky").livequery(function(){

            var $element = $(this) ,
                slideToggle = true ,
                elementOffsetTop = $element.offset().top,
                elementHeight = $element.outerHeight();

            var sticky_top = 0 ,
                wpadminbar = $( '#wpadminbar' ),
                _position = wpadminbar.css("position");

            if( wpadminbar.length > 0 && _position == "fixed" ) {
                sticky_top += wpadminbar.outerHeight();
            }

            var _resetSticky = function(){

                $element.removeClass("sed-active-sticky");

                $element.css({
                    position    : '' ,
                    top         : '' ,
                    zIndex      : ''
                });

            };

            var _lazySticky = _.debounce(function(){

                var wTopPos = $(window).scrollTop();

                if ( wTopPos > elementOffsetTop ) {

                    $element.addClass("sed-active-sticky");

                    if( slideToggle ) {

                        $element.css({
                            position: 'fixed',
                            top: ( sticky_top - elementHeight ) + 'px' ,
                            right: '0px',
                            left: '0px' ,
                            zIndex : 99999
                        });

                        $element.animate({
                            top         : sticky_top + 'px'
                        }, 200 );

                        slideToggle = false;

                    }


                }else{

                    _resetSticky();

                    slideToggle = true;

                }

            }, 10);

            _resetSticky();

            $(window).on("scroll.sedRowticky" , function(){
                _lazySticky();
            });

        });

    });


}(sedApp, jQuery));