
(function($) {

 
    var responsiveMenu = function() {
        var menuType = 'desktop';

        $(window).on('load resize', function() {
            var currMenuType = 'desktop';

            if ( matchMedia( 'only screen and (max-width: 1024px)' ).matches ) {
                currMenuType = 'mobile';
            }

            if ( currMenuType !== menuType ) {
                menuType = currMenuType;

                if ( currMenuType === 'mobile' ) {
                    var $mobileMenu = $('.navbar-wrap').attr('class', 'navbar-wrap-mobi').hide();
                    var hasChildMenu = $('.navbar-wrap-mobi').find('li:has(ul)');

                    //$('.navigation-wrapper').after($mobileMenu);
                    hasChildMenu.children('ul').hide();
                    hasChildMenu.children('a').after('<span class="submenu-toggle-arrow"></span>');
                    $('.navbar-toggle-wrap').removeClass('active');  
                } else {
                    var $desktopMenu = $('.navbar-wrap-mobi').attr('class', 'navbar-wrap').removeAttr('style');

                    $desktopMenu.find('.submenu').removeAttr('style');
                    //$('.navigation-wrapper').append($desktopMenu);
                    $('.submenu-toggle-arrow').remove(); 
                }
            }
        });

        $('.navbar-toggle-wrap').on('click', function() {
            $('.navbar-wrap-mobi').slideToggle(300);
            $(this).toggleClass('active');
        });

        $(document).on('click', '.navbar-wrap-mobi li .submenu-toggle-arrow', function(e) {
            $(this).toggleClass('active').next('ul').slideToggle(300);
            e.stopImmediatePropagation()
        });
    }

    responsiveMenu();

})(jQuery);

