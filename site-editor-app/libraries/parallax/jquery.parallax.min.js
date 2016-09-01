/*
Plugin: jQuery Parallax
Version 1.0.0
*/

(function( $ ){
	var $window = $(window);
	var windowHeight = $window.height();

	$window.resize(function () {     
		windowHeight = $window.height();
	});

    var SEDParallax = function(element, options) {

        this.$element = $(element);   

        this.options  = $.extend({}, {
            // These are the defaults.
            xpos               : "50%",
            speedFactor        : 0.1, // click || hover
            outerHeight        : true,

        }, options);

        this.initialize();

    };

    SEDParallax.prototype = {

        initialize : function(){
            var self = this;
            this.initParallax();

            this.updateParallax = function(){
                self.update();
    		};

    		$window.bind('scroll.parallax', this.updateParallax );

            $window.bind('resize.parallax', this.updateParallax );

            this.updateInit = function(){
                self.initParallax();
    		};

            $window.bind('scrollstart.parallax', this.updateInit );

    		this.update();
        },

        initParallax : function(){
            this.firstTop = this.$element.offset().top;
        },

        getHeight : function( $element ){
            var getHeight;
    		if (this.options.outerHeight) {
    			getHeight = $element.outerHeight(true);
    		} else {
    			getHeight = $element.height();
    		}
            return getHeight;
        },

        update : function(){
			var pos = $window.scrollTop();

            var top = this.$element.offset().top;
            var height = this.getHeight( this.$element );

            // Check if totally above or totally below viewport
            if (top + height < pos || top > pos + windowHeight) {
            	return;
            }

            this.$element.css('backgroundPosition', this.options.xpos + " " + Math.round((this.firstTop - pos) * this.options.speedFactor) + "px");
		},

        destroy : function(){
            this.$element.css({
                'background-position' : ''
            });

            $window.unbind( 'scrollstart.parallax' , this.updateInit );
            $window.unbind( 'scroll.parallax' , this.updateParallax );
            $window.unbind( 'resize.parallax' , this.updateParallax );
            this.$element.removeData('sed.parallaxbg'); //.off('.parallax')
        }

    };

	$.fn.parallax = function( option ) {

        var slice = Array.prototype.slice ,
            args = slice.call( arguments, 1 );

        if (typeof option == 'string' && option == "option" && args.length == 1 ){
            var data = this.data('sed.parallaxbg');
            if(typeof data == "undefined" || typeof data[option] == "undefined" )
                return ;

            return data[option].apply(data , args );
        }

        return this.each(function() {
            var $this   = $(this);
            var data    = $this.data('sed.parallaxbg') ;

            if (!data && option == 'destroy') return ;

            if(option == "destroy"){
                $this.data('sed.parallaxbg').destroy();
                return ;
            }

            var options = typeof option == 'object' && option;
            if (!data) $this.data('sed.parallaxbg', (data = new SEDParallax(this, options)));
            if (typeof option == 'string') data[option].apply(data , args );

        });
	};

})(jQuery);
