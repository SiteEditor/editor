// jQuery Plugin Boilerplate
// A boilerplate for jumpstarting jQuery plugins development
// version 1.1, May 14th, 2011
// by Stefan Gabos

(function($) {
    //"use strict";
    $.sedMegaMenu = function(element, options) {

        var defaults = {
            foo: 'bar',
            onFoo: function() {}
        }

        var plugin = this;

        plugin.settings = {}

        var $element = $(element),
             element = element;

        plugin.init = function() {

            var $this = $(this),
                $parent = $this.find('[data-hover="dropdown"]').parent(),
                defaults = {
                    stickyMenu: true,
                    trigger   : 'hover', // click || hover
                    delay: 500,
                    instantlyCloseOthers: true
                },
                data = {
                    delay: $(this).data('delay'),
                    instantlyCloseOthers: $(this).data('close-others')
                },
                showEvent = 'show.bs.dropdown',
                hideEvent = 'hide.bs.dropdown',
                // shownEvent = 'shown.bs.dropdown',
                // hiddenEvent = 'hidden.bs.dropdown',
                settings = $.extend(true, {}, defaults, options, data),
                timeout;

               if(settings.stickyMenu) {
                   sticky_private_method();
                }
               if(settings.trigger === 'hover') {
                   hover_private_method();
                }
               submenu_private_method();


        }


        var hover_private_method = function() {
            $parent.hover(function (event) {
                // so a neighbor can't open the dropdown
                if(!$parent.hasClass('open') && !$this.is(event.target)) {
                    // stop this event, stop executing any code
                    // in this callback but continue to propagate
                    return true;
                }

                $allDropdowns.find(':focus').blur();

                if(settings.instantlyCloseOthers === true)
                    $allDropdowns.removeClass('open');

                window.clearTimeout(timeout);
                $parent.addClass('open');
                $this.trigger(showEvent);
            }, function () {
                timeout = window.setTimeout(function () {
                    $parent.removeClass('open');
                    $this.trigger(hideEvent);
                }, settings.delay);
            });

            // this helps with button groups!
            $this.hover(function () {
                $allDropdowns.find(':focus').blur();

                if(settings.instantlyCloseOthers === true)
                    $allDropdowns.removeClass('open');

                window.clearTimeout(timeout);
                $parent.addClass('open');
                $this.trigger(showEvent);
            });
        },

        var submenu_private_method = function() {
             $parent.find('.dropdown-submenu').each(function (){
                var $this = $(this);
                var subTimeout;
                $this.hover(function () {
                    window.clearTimeout(subTimeout);
                    $this.children('.dropdown-menu').show();

                    var $window = $(window).width(),
                        left = $this.children('.dropdown-menu').offset().left,
                        sub_right = $window -left ,
                        sub_w = $this.children('.dropdown-menu').width();
                     if((sub_right < sub_w) || (left < sub_w) ){
                      $this.children('.dropdown-menu').addClass("dropdown-menu-left-ct");
                     }
                    // always close submenu siblings instantly
                    $this.siblings().children('.dropdown-menu').hide();
                }, function () {
                    var $submenu = $this.children('.dropdown-menu');
                    subTimeout = window.setTimeout(function () {
                        $submenu.hide();
                    }, settings.delay);
                });
            });
        },

        var sticky_private_method = function() {

            var menu = document.querySelector('.navbar-wrap')
            var menuPosition = menu.getBoundingClientRect().top;
            window.addEventListener('scroll', function() {
                if (window.pageYOffset >= menuPosition) {
                    menu.style.position = 'fixed';
                    menu.style.top = '0px';
                    menu.style.right = '0px';
                    menu.style.left = '0px';
                } else {
                    menu.style.position = '';
                    menu.style.top = '';
                }
            });

        }

        plugin.init();

    }

    $.fn.sedMegaMenu = function(options) {

        return this.each(function() {
            if (undefined == $(this).data('sedMegaMenu')) {
                var plugin = new $.sedMegaMenu(this, options);
                $(this).data('sedMegaMenu', plugin);
            }
        });

    }

    $("nav#primary-navigation").sedMegaMenu();

}(jQuery));