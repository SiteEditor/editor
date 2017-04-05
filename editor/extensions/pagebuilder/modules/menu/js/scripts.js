
(function($) {

 
    var sedResponsiveMenu = function() {
        var mediaType = 'desktop';

        $(window).on('load resize', function() {
            var currentMediaType = 'desktop';

            if ( matchMedia( 'only screen and (max-width: 910px)' ).matches ) {
                currentMediaType = 'mobile';
            }

            if ( currentMediaType !== mediaType ) {
                mediaType = currentMediaType;

                if ( currentMediaType === 'mobile' ) {
                    var MobileMenu = $('.navbar-wrap').attr('class', 'navbar-wrap-mobile').hide();
                    var hasChildrenMenu = $('.navbar-wrap-mobile').find('li:has(ul)');

                    hasChildrenMenu.children('ul').hide();
                    hasChildrenMenu.children('a').after('<span class="submenu-toggle-arrow"></span>');
                    $('.navbar-toggle-wrap').removeClass('active');  
                } else {
                    var DesktopMenu = $('.navbar-wrap-mobile').attr('class', 'navbar-wrap').removeAttr('style');

                    DesktopMenu.find('.submenu').removeAttr('style');
                    $('.submenu-toggle-arrow').remove(); 
                }
            }
        });

        $('.navbar-toggle-wrap').on('click', function() {
            $('.navbar-wrap-mobile').slideToggle(300);
            $(this).toggleClass('active');
        });

        $(document).on('click', '.navbar-wrap-mobile li .submenu-toggle-arrow', function(e) {
            $(this).toggleClass('active').next('ul').slideToggle(300);
            e.stopImmediatePropagation()
        });
    }

    sedResponsiveMenu();

})(jQuery);

