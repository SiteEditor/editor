/*
* sedmegamenu jquery plugin
* requirement : jquery and underscore
* created By SiteEditor Team
* http://www.siteeditor.org
* @SiteEditor Pakage
*/
(function($) {

    var SEDMegaMenu = function(element, options) {

        this.$element = $(element);
        this.showEvent = 'show.bs.dropdown';
        this.hideEvent = 'hide.bs.dropdown';
        this.timeout;
        this.touchenabled = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0) || (navigator.msMaxTouchPoints > 0);

        this.orginalTrigger = options.trigger || "hover";
        this.orginalSticky = !_.isUndefined( options.isSticky ) ? options.isSticky : false;

        this.options  = $.extend({}, {
            // These are the defaults.
            isSticky                : false,
            trigger                 : 'hover', // click || hover
            delay                   : 500,
            instantlyCloseOthers    : true ,
            orientation             : "horizontal" ,
            //scroll animate for anchor (scroll To anchor with easing animate like #intro)
            scrollAnimate           : "easeInOutQuint" ,
            scrollAnimateDuration   : 2000 ,
            isVerticalFixed         : false ,
            breakpoint              : 768
        }, options);

         this.activeDragArea = false;

		if( this.touchenabled ){
			this.$element.addClass( 'sed-megamenu-touch' );
            this.options.trigger = "click";
		}else
            this.$element.addClass( 'sed-megamenu-notouch' );

      	var deviceAgent = navigator.userAgent.toLowerCase();

      	//For iOS, set to hover for consistency
      	var is_iOS = deviceAgent.match(/(iphone|ipod|ipad)/);
      	if( is_iOS )
      		this.options.trigger = 'hover';

        this.responsive = $(window).width() < this.options.breakpoint ? true : false;

        if( this.responsive )
            this.options.isSticky = false;

        if( this.responsive && !is_iOS )
            this.options.trigger = 'click';

        this.initialize();

    };

    if (!$.fn.dropdown) throw new Error('SEDMegaMenu requires dropdown.js');

    SEDMegaMenu.prototype = {

        initialize : function(){
            var self = this;
            if(this.options.trigger == "hover"){
                this.hoverDropdown();
                this.hoverDropdownToggle();
                this.$element.addClass("hover-menu").removeClass("click-menu");
                this.$element.find("[data-toggle='dropdown']").addClass("disabled");

            }else{
                this.$element.find("[data-toggle='dropdown']").removeClass("disabled");
                this.$element.addClass("click-menu").removeClass("hover-menu");
            }

            if( $("#megamenu-overlay").length == 0 )
                $('<div id="megamenu-overlay"></div>').appendTo( "body" );

            if( this.$element.find(".megamenu-container-cover").length == 0 )
                $('<div class="megamenu-container-cover"></div>').appendTo( this.$element );

            this.$element.find(".megamenu-drag-area").click(function(){
                self.activeMegamenuDragArea( $(this) );
            });

            this.renderSubmenu();

            this.stickyMenu();

            this.scrollAnimateToAnchor();

            if( !this.responsive )
                this.equalToHeight();

            /*if( self.options.orientation == "vertical" ){
                var $dropdowns = this.$element.find(".dropdown-menu[data-depth='0']");
                $dropdowns.each(function(){
                    if( !_.isUndefined( $(this).data("submenuWidth") ) ){
                        $(this).width( $(this).data("submenuWidth") );
                    }
                });
            } */

            this.responsiveMenuTest();

        },

        activeMegamenuDragArea : function( element ){
            var self = this;
            if( !element.hasClass("active") ){
                element.addClass("active");
                $("#megamenu-overlay").show();
                self.$element.find(".megamenu-container-cover").show();

                $("body").addClass("drag-area-overlay-mode");

                $("#megamenu-overlay").height( $("body").height() );
                element.parents(".dropdown-menu:first").addClass("megamenu-drag-area-mode");

                $(".bp-component").each(function(){

                    if( !$(this).hasClass("sed-sortable-disabled") && !$(this).hasClass("megamenu-module-widget-area") && $(this).parents(".megamenu-module-widget-area").length == 0 ){
                        $(this).addClass("sed-sortable-disabled megamenu-mode");
                        $(this).removeClass("bp-component");
                    }

                });

                self.activeDragArea = true;

            }else{
                element.removeClass("active");
                $("#megamenu-overlay").hide();
                self.$element.find(".megamenu-container-cover").hide();

                element.parents(".dropdown-menu:first").removeClass("megamenu-drag-area-mode");

                $(".sed-sortable-disabled.megamenu-mode").each(function(){
                    $(this).removeClass("sed-sortable-disabled megamenu-mode");
                    $(this).addClass("bp-component");
                });

                $("body").removeClass("drag-area-overlay-mode");

                self.activeDragArea = false;

            }

            element.trigger("afterActivateMegamenuDragArea" , [self.$element , element]);
        },

        responsiveMenuTest : function(){
            var self = this , _lazyTest , count = 0;

            _lazyTest = _.debounce(function(){

                self.$element.find("li.menu-item").removeData("menuHeightInit");
                self.$element.find("li.menu-item > .dropdown-menu").css({
                    top : "",
                    bottom : "" ,
                    overflow : "" ,
                    height : "" ,
                    //marginTop : -($this.find(">a").outerHeight()) + "px"
                });
                self.$element.find(".dropdown").removeData("sedDropdownActivated");

                if( $(window).width() < self.options.breakpoint ){

                    self.responsive = true;

                    if(self.options.trigger == "hover"){
                        self.$element.find(".dropdown").unbind('mouseenter mouseleave');
                        self.$element.find("[data-toggle='dropdown']").unbind('mouseenter mouseleave');
                        self.$element.find(".dropdown .dropdown-submenu").unbind('mouseenter mouseleave');
                        self.$element.find("[data-toggle='dropdown']").removeClass("disabled");
                        self.$element.addClass("click-menu").removeClass("hover-menu");

                        self.$element.find(".dropdown").removeClass("open");
                        self.$element.find(".dropdown .dropdown-submenu").addClass("closed-submenu").removeClass("opened-submenu");
                        self.$element.find(".dropdown .dropdown-submenu > .dropdown-menu").hide();

                        self.options.trigger = "click";

                        self.renderSubmenu();
                    }

                    self.$element.find( "ul:first >.menu-item > a" ).css("height" , "");
                    self.$element.find( "ul:first >.menu-item > a" ).css("line-height" , "");

                    if(self.options.orientation == "vertical")
                        self.$element.find( "ul:first >.menu-item > ul" ).css("marginTop" , "" );


                    self.options.isSticky = false;
                }else{

                    self.responsive = false;

                    if(self.options.trigger == "click" && self.orginalTrigger == "hover"){


                        self.$element.find(".dropdown").unbind('click');
                        //self.$element.find("[data-toggle='dropdown']").unbind('click');
                        //self.$element.find("[data-toggle='dropdown']").addClass("disabled");
                        self.$element.find(".dropdown .dropdown-submenu").unbind('click');

                        self.$element.find(".dropdown").removeClass("open");
                        self.$element.find(".dropdown .dropdown-submenu").addClass("closed-submenu").removeClass("opened-submenu");
                        self.$element.find(".dropdown .dropdown-submenu > .dropdown-menu").hide();

                        self.options.trigger =  "hover";
                        self.hoverDropdown();
                        self.hoverDropdownToggle();
                        self.$element.addClass("hover-menu").removeClass("click-menu");
                        self.$element.find("[data-toggle='dropdown']").addClass("disabled");

                        self.renderSubmenu();
                    }//else
                        //self.options.trigger = lastTrigger;

                    self.equalToHeight();

                    self.options.isSticky = self.orginalSticky;

                }

            }, 10);

            $(window).on("resize.megamenu" , function(){
                _lazyTest();
            });
        },

        openDropdown : function( $this , event ){
            var self = this ,
                $dropdowns = this.$element.find(".dropdown");

            if(self.options.trigger == "click")
                return ;

            if( !self.responsive )
                self.repositionSubmenu( $this , true );

            if(self.options.orientation == "horizontal" && !self.responsive  )
                self.repositionMegamenu( $this );

            var $thisDdt = $this.find(">[data-toggle='dropdown']");
            // so a neighbor can't open the dropdown
            if(!$this.hasClass('open') && typeof event != "undefined" && !$thisDdt.is(event.target)) {
                // stop this event, stop executing any code
                // in this callback but continue to propagate
                return true;
            }

            $dropdowns.find(':focus').blur();

            if(self.options.instantlyCloseOthers === true)
                $dropdowns.removeClass('open');

            window.clearTimeout(self.timeout);
            $this.addClass('open');

            $thisDdt.trigger(self.showEvent);

        },

        hoverDropdown : function(){
            var self= this ,
                $dropdowns = this.$element.find(".dropdown");

            $dropdowns.hover(function (event) {
                self.openDropdown( $(this) , event );

            }, function () {


                if( self.activeDragArea === true )
                    return false;

                if(self.options.trigger == "click")
                    return ;

                var $thisDdt = $(this).find(">[data-toggle='dropdown']") ,
                    $this = $(this);

                self.timeout = window.setTimeout(function () {
                    $this.removeClass('open');
                    $thisDdt.trigger(self.hideEvent);
                }, self.options.delay);

            });
        } ,


        hoverDropdownToggle : function(){
            var self= this ,
                dropdownsToggles = this.$element.find("[data-toggle='dropdown']") ,
                $dropdowns = this.$element.find(".dropdown");

            dropdownsToggles.hover(function () {

                if(self.options.trigger == "click" )
                    return ;

                $dropdowns.find(':focus').blur();

                if(self.options.instantlyCloseOthers === true)
                    $dropdowns.removeClass('open');

                window.clearTimeout(self.timeout);
                $(this).parent().addClass('open');
                $(this).trigger(self.showEvent);
            });
        } ,

        repositionMegamenu : function( item ){
            var self= this;
               ////megamenu-half-special
            if( $(item).hasClass('megamenu-half') ){
                var megamenuW = $(item).find(">.dropdown-menu").outerWidth( ) ,
                    menuW = self.$element.outerWidth( ),
                    left = $(item).offset().left - self.$element.offset().left,     //.find(">.dropdown-menu")
                    sub_right;    //console.log( menuW ); console.log( left );
                                               //console.log( megamenuW );

                if( $("body").hasClass("rtl-body") ){

                    left += $(item).outerWidth( );

                    if( megamenuW > left ){
                        $(item).addClass("megamenu-half-special");
                    }else{
                        $(item).removeClass("megamenu-half-special");
                    }

                }else{

                    sub_right = menuW -left;

                    if( megamenuW > sub_right ){
                        $(item).addClass("megamenu-half-special");
                    }else{
                        $(item).removeClass("megamenu-half-special");
                    }

                }


            }


        },

        verticalFixedSubmenu :  function( $this , type ){
            var self = this;

            if( this.options.isVerticalFixed && !$this.data("menuHeightInit") ){
                $this.children('.dropdown-menu').css("height" , "auto");

                var type = _.isUndefined( type ) ? "top_level" : type ,
                    $windowH = $(window).height() ,
                    top = $this.children('.dropdown-menu').offset().top ,
                    bottom = $windowH - top,
                    submenuH = $this.children('.dropdown-menu').height() ,
                    //botLi = $windowH - $this.offset().top - $this.height() ,
                    containerTop = (type == "top_level") ? self.$element.offset().top : top ,
                    containerbot = (type == "top_level") ? $windowH - containerTop - self.$element.height() : bottom - $this.outerHeight() ;

                if( submenuH > $windowH ){
                    //$this.addClass("vsubmenu-full-height");
                    $this.children('.dropdown-menu').css({
                        top : -containerTop + "px",
                        bottom : -containerbot + "px" ,
                        overflow : "auto" ,
                        height : $windowH + "px" ,
                        marginTop : ""
                    });
                }else if( submenuH > bottom ){
                    $this.children('.dropdown-menu').css({
                        top : (type == "top_level") ? "" : -( submenuH - containerbot - $this.outerHeight() ) + "px" ,
                        bottom : -containerbot + "px" ,
                        overflow : "" ,
                        height : "" ,
                        marginTop : ""
                    });
                    //$this.addClass("vsubmenu-bottom-side");
                }else{
                    $this.children('.dropdown-menu').css({
                        bottom : "" ,
                        overflow : "" ,
                        height : "" ,
                        marginTop :  (type == "top_level") ? -($this.find(">a").outerHeight()) + "px" : "" ,
                        top :  (type == "top_level") ? "" : 0,
                    });

                }

                $this.data("menuHeightInit" , true);
            }

        },

        //for all "submenus" in --Horizontal-- mode And for all "submenus" && "megamenus" in --Vertical-- mode
        repositionSubmenu : function( $element , isTopLevel ){
            var self= this ,
                $this = $element ,
                $window = $(window).width() ,
                left,
                sub_right,
                sub_w = $this.children('.dropdown-menu').outerWidth(),
                className = ( self.options.orientation == "vertical" && isTopLevel ) ? "dropdown-vertical-submenu-right" : "dropdown-submenu-left";

            if( $("body").hasClass("rtl-body") ){

                left = ( isTopLevel && self.options.orientation != "vertical" ) ? $this.offset().left + $this.outerWidth() : $this.offset().left ;

                if( sub_w > left ){
                    $this.removeClass( className );
                }else{
                    $this.addClass( className );
                }

            }else{

                left =  ( isTopLevel && self.options.orientation != "vertical" ) ? $this.offset().left : $this.offset().left + $this.outerWidth();
                sub_right = $window -left ;

                if( (sub_right < sub_w)  ){
                    $this.addClass( className );
                }else{
                    $this.removeClass( className );
                }

            }

            if( self.options.orientation == "vertical" && isTopLevel )
                self.verticalFixedSubmenu( $this );

        },

        renderSubmenu : function(){
            var self= this ,
                $dropdowns = this.$element.find(".dropdown");

            this._openSubmenu = function( $this , subTimeout ){
                window.clearTimeout(subTimeout);
                $this.children('.dropdown-menu').show();


                if( !self.responsive ){
                    self.repositionSubmenu( $this );

                    self.verticalFixedSubmenu( $this , "submenu" );
                }

                // always close submenu siblings instantly
                $this.siblings().children('.dropdown-menu').hide();
                $this.siblings().addClass("closed-submenu").removeClass("opened-submenu");
                $this.addClass("opened-submenu").removeClass("closed-submenu");
            };

            this._closeSubmenu = function( $this , subTimeout ){
                var $submenu = $this.children('.dropdown-menu');

                if( self.options.trigger == "hover" ){
                    subTimeout = window.setTimeout(function () {
                        $submenu.find(".dropdown-submenu").addClass("closed-submenu").removeClass("opened-submenu");
                        $this.addClass("closed-submenu").removeClass("opened-submenu");
                        $submenu.hide();
                        $submenu.find(".dropdown-submenu > .dropdown-menu").hide();
                    }, self.options.delay);
                }else{
                    $submenu.find(".dropdown-submenu").addClass("closed-submenu").removeClass("opened-submenu");
                    $this.addClass("closed-submenu").removeClass("opened-submenu");
                    $submenu.hide();
                    $submenu.find(".dropdown-submenu > .dropdown-menu").hide();
                }
            };

            // handle submenus
            $dropdowns.find('.dropdown-submenu').each(function (){
                var $this = $(this);
                var subTimeout;

                $this.addClass("closed-submenu");

                if(self.options.trigger == "hover"){

                    $this.hover(function () {
                        self._openSubmenu( $this , subTimeout );
                    }, function () {
                        self._closeSubmenu( $this , subTimeout );
                    });

                }else{

                    $this.click(function (e) {
                        e.stopPropagation();

                        if( $("body").hasClass("sed-app-preview") ){
                            e.preventDefault();
                        }

                        if( $(this).hasClass("closed-submenu") ){
                            self._openSubmenu( $this , subTimeout );
                        }else{
                            self._closeSubmenu( $this , subTimeout );
                        }
                    });

                }

            });

        } ,

        //all main(top level) level menu items height equal to max all items height
        equalToHeight : function(){
            var heights = [] , maxH;

            this._equalHeightSelector = function( selector ){

                this.$element.find( selector ).css("height" , "auto");
                this.$element.find( selector ).css({
                    "lineHeight"    :  ""
                });

                this.$element.find( selector ).each(function(){
                    heights.push( $(this).height() );
                });

                maxH = Math.max.apply(null, heights);

                this.$element.find( selector ).height( maxH );

                this.$element.find( selector ).css({
                    "lineHeight"    :   maxH + "px"
                });

            };

            this._equalHeightSelector( "ul:first >.menu-item > a" );

            if(this.options.orientation == "vertical"){
                this.$element.find( "ul:first >.menu-item > ul" ).css("marginTop" , -(this.$element.find( "ul:first >.menu-item > a" ).outerHeight()) + "px" );
            }else{
                this.$element.find( "ul:first >.menu-item > ul" ).css("marginTop" , '' );
            }

        },

        /*
        *sticky menu only apply in site and siteeditor "preview mode"
        *sticky menu only apply last created menu(with active sticky not other menus) in one page and not apply on
        *--several menu in one page( sticky apply only on one menu in each page )
        */
        stickyMenu : function(){
            var sticky_top = 0;
            if( $('#wpadminbar').length > 0 ) {
                sticky_top = $('#wpadminbar').outerHeight();
            }

            var self = this ,
                menu = this.$element ,
                menuTopPos = menu.offset().top ,
                wTopPos ,
                mHeight = menu.outerHeight(true) ,
                _resetSticky = function(){
                    menu.removeClass("sticky-menu");
                    menu.css({
                        position    : '' ,
                        top         : '' ,
                    });
                    menu.parent().css({
                        position    : '' ,
                        height      : ''
                    });
                },

                _lazySticky = _.debounce(function(){    // console.log( "self.options.isSticky ------ : " , self.options.isSticky );
                    wTopPos = $(window).scrollTop();
                    if( self.options.orientation == "horizontal" && self.options.isSticky === true){
                        if (wTopPos > menuTopPos) {
                            menu.addClass("sticky-menu");
                            menu.css({
                                position    : 'fixed' ,
                                top         : sticky_top + 'px' ,
                                right       : '0px' ,
                                left        : '0px'
                            });
                            menu.parent().css({
                                position    : 'static' ,
                                height      : mHeight
                            });
                        } else {
                            _resetSticky();
                        }
                    }else{
                        _resetSticky();
                    }
                }, 10);

            _resetSticky();

            $(window).on("scroll.sticky" , function(){
                _lazySticky();
            });

        } ,

        scrollAnimateToAnchor : function(){
            var self = this;

            this.$element.find('a[href*=#]').each(function(){
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

                            if( self.options.isSticky ){
                                targetOffsetTop -= self.$element.outerHeight(true);
                            }

                            if( $('#wpadminbar').length > 0 ) {
                                targetOffsetTop -= $('#wpadminbar').outerHeight();
                            }

                            self.goToByScroll(targetOffsetTop);
                        });
                    }
                }
            });

        },

        goToByScroll : function ( targetOffset ) {

            if( _.isUndefined( jQuery.easing ) || !this.options.scrollAnimate  )
                $('html, body').animate({scrollTop: targetOffset}, this.options.scrollAnimateDuration );
            else{
                var scrollAnimate = this.options.scrollAnimate ? this.options.scrollAnimate : 'easeInOutQuint';
                $('html, body').animate({
                    scrollTop: targetOffset
                }, this.options.scrollAnimateDuration , scrollAnimate);
            }

        },

        option : function( option , newValue ){
            if(typeof option == 'undefined' || (arguments.length == 1 && typeof this.options[option] == 'undefined' ) )
                return ;

            if( arguments.length == 1 )
                return this.options[option];

            if( option == "trigger" )
                this.orginalTrigger = newValue;

            if( option == "isSticky" )
                this.orginalSticky = newValue;

            this.options[option] = newValue;

        },

        destroy : function(){
            this.$element.find(".megamenu-drag-area").unbind('click');
            this.$element.find(".dropdown").unbind('mouseenter mouseleave click');
            this.$element.find(".dropdown").removeData("sedDropdownActivated");
            this.$element.find("[data-toggle='dropdown']").unbind('mouseenter mouseleave click');
            this.$element.find("[data-toggle='dropdown']").addClass("disabled");
            this.$element.find(".dropdown .dropdown-submenu").unbind('mouseenter mouseleave click');
            $(window).unbind('scroll.sticky');
            $(window).unbind('resize.megamenu');
            this.$element.off('.sedmegamenu').removeData('sed.sedmegamenu');
        }

    };

    $.fn.sedmegamenu = function (option) {

          var slice = Array.prototype.slice ,
              args = slice.call( arguments, 1 );

          if (typeof option == 'string' && option == "option" && args.length == 1 ){
              var data = this.data('sed.sedmegamenu');
              if(typeof data == "undefined" || typeof data[option] == "undefined" )
                  return ;

              return data[option].apply(data , args );
          }

          return this.each(function () {
              var $this   = $(this);
              var data    = $this.data('sed.sedmegamenu') ;

              if (!data && option == 'destroy') return ;

              if(option == "destroy"){
                  $this.data('sed.sedmegamenu').destroy();
                  return ;
              }

              var options = typeof option == 'object' && option;
              if (!data) $this.data('sed.sedmegamenu', (data = new SEDMegaMenu(this, options)));
              if (typeof option == 'string') data[option].apply(data , args );
          });

    };

})(jQuery);



/**
 * jQuery.browser.mobile (http://detectmobilebrowser.com/)
 *
 * jQuery.browser.mobile will be true if the browser is a mobile device
 *
 **/
(function(a){jQuery.sedMobile=/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))})(navigator.userAgent||navigator.vendor||window.opera);
