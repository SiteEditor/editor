/*!
 * jquery.lately.js 0.0.1 - https://github.com/yckart/jquery.lately.js
 * A very lightweight jQuery plugin to lazy load images
 *
 * Copyright (c) 2013 Yannick Albert (http://yckart.com)
 * Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php).
 * 2013/02/09
*/
;(function ($, window) {

    $.fn.lately = function (options) {
        options = $.extend({}, {
            container: window,
            gapX: 0,
            gapY: 0
        }, options);

        var $win = $(options.container),
            self = this;

        this.one("lately", function () {
            var src = this.getAttribute("data-src");
            this.setAttribute("src", src);
            this.removeAttribute("data-src");
        });

        function lately() {
            var inview = self.filter(function () {
                var el = $(this),
                    elW = el.outerWidth() + options.gapX,
                    elH = el.outerHeight() + options.gapY,

                    scroll = {
                        y: $win.scrollTop(),
                        x: $win.scrollLeft()
                    },

                    viewport = {
                        x: $win.width() + options.gapX,
                        y: $win.height() + options.gapY
                    };

                return (
                el.offset().top < (scroll.y + viewport.y) && el.offset().left < (scroll.x + viewport.x) && (el.offset().top + elH) > scroll.y && (el.offset().left + elW) > scroll.x);
            });

            var loaded = inview.trigger("lately");
            self = self.not(loaded);
        }

        $win.on('resize scroll', lately);
        lately();

        return this;
    };

}(jQuery, window));