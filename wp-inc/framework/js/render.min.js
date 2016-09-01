jQuery(document).ready(function ($) {

    //render wow animation in all site page
    var wow = new WOW(
      {
        boxClass:     'wow',      // animated element css class (default is wow)
        animateClass: 'animated', // animation css class (default is animated)
        offset:       0,          // distance to the element when triggering the animation (default is 0)
        mobile:       true,       // trigger animations on mobile devices (default is true)
        live:         true,       // act on asynchronously loaded content (default is true)
      }
    );
    wow.init();

    if( typeof window._sedAppParallaxBackgroundImage != "undefined" ){
        var parallaxBackgrounds = window._sedAppParallaxBackgroundImage;

        $.each( parallaxBackgrounds , function( element , ratio ){  
            $( element ).parallax({
                xpos           : "50%",
                speedFactor    : ratio,
            });
        });
    }

    /*$(window).load(function(){

    });*/

    var _goToByScroll = function ( targetOffset ) {
        $('html, body').animate({scrollTop: targetOffset}, 2000 );
    };

    var _getStickyHeight = function(){
        var stickyHeight = 0;
        $('body').find('.module.module-megamenu').each(function(){
            var sedmegamenu = $(this).data('sed.sedmegamenu');
            if( sedmegamenu && typeof sedmegamenu.options !== undefined && typeof sedmegamenu.options.isSticky !== undefined && sedmegamenu.options.isSticky ){
                stickyHeight = $(this).outerHeight(true);
                return false;
            }
        });
        return stickyHeight;
    };

    $('body').find('a[href*=#]').each(function(){
        if( $(this).parents(".module.module-megamenu").length > 0 || $(this).attr("data-toggle") == "tab" ){
            return true;
        }
                 
        if(location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'')
           && location.hostname == this.hostname
           && this.hash.replace(/#/,'') ) {

            var targetId = $(this.hash),
                targetAnchor = $('[name=' + this.hash.slice(1) + ']') ,
                target = targetId.length ? targetId : targetAnchor.length ? targetAnchor : false ,
                targetOffsetTop;

            if( target ){
                $(this).click(function(e){
                    e.preventDefault();
                    targetOffsetTop = target.offset().top;

                    targetOffsetTop -= _getStickyHeight();

                    if( $('#wpadminbar').length > 0 ) {
                        targetOffsetTop -= $('#wpadminbar').outerHeight();
                    }

                    _goToByScroll(targetOffsetTop);
                });
            }
        }
    });

});
