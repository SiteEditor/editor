/*
 * Title: Boutique carousel jQuery plugin
 * Author: Berend de Jong, Frique
 * Author URI: http://www.frique.me/
 * Version: 1.5.1 (20121008.1)
 */

;(function($){
	$.fn.boutique = function(options){

		// OPTION DEFAULTS
		var o = $.extend({
			container_width:			'auto',				// Total carousel width (value in pixels or "auto" (auto is 100%/fluid width))
			front_img_width:			280,				// Width of the frontal image (value in pixels)
			front_img_height:			'auto',				// Height of the frontal image (value in pixels / 'auto')
			variable_container_height:	true,				// If your images have different heights, this will make the container height adapt to the visible frames (true/false)
			frames:						5,					// Amount of visible frames (3 or 5)
			starter:					1,					// Number of the frame that starts in front (number)
			speed:						600,				// Overall animation speed (time in ms)
			front_topmargin:			40,					// Vertical align of the front frame (distance from the container top in pixels)
			behind_topmargin:			20,					// Vertical align of the behind frames (2nd and 4th) (distance from the container top in pixels)
			back_topmargin:				0,					// Vertical align of the furthest back frames (distance from the container top in pixels)
			behind_opacity:				0.4,				// Opacity of the furter back items (between or equal to 0-1)
			back_opacity:				0.15,				// Opacity of the furthest back items (between or equal to 0-1)
			behind_size:				0.7,				// Size of the further back images (between or equal to 0-1)
			back_size:					0.4,				// Size of the furthest back images (between or equal to 0-1)
			behind_distance:			'auto',				// Option to manually set the horizontal position (distance from the container edge) of the behind frames (number 2 and 4) ('auto' or pixel value)
			autoplay:					false,				// Autoplay on/off (true/false)
			autoplay_interval:			3000,				// Autoplay time between slides (time in ms)
			stop_autoplay_on_click:		false,				// If autoplay if on, it will switch off when a frame is clicked (true/false)
			freescroll:					true,				// Whether you can still navigate while animating (true/false)
			hovergrowth:				0.08,				// How much the front item will enlarge on mouse-over (between or equal to 0-1)
			easing:						'easeIOBoutique',	// Easing type for moving the frames 1 step ('easing type title')
			move_more_easein:			'easeInBoutique',	// Easing type for the first part of moving multiple steps ('easing type title')
			move_more_easebetween:		'linear',			// Easing type for moving multiple steps but not first and last ('easing type title')
			move_more_easeout:			'easeOutBoutique',	// Easing type for the end of moving multiple steps ('easing type title')
			move_more_directly:			false,				// Will not bring the in-between frames forward when moving more than once (true/false)
			never_move_twice:			false,				// Prevents moving 2 steps when frame 1 or 5 is clicked (true/false)
			text_front_only:			false,				// Show the title/description only in the front frame (true/false)
			text_opacity:				0.7,				// Opacity of the title/description layer (value between or equal to 0-1)
			keyboard:					true,				// Enable/disable keyboard functionality (true/false)
			move_on_hover:				false,				// Navigating with mouse-over instead of clicking (true/false)
			right_to_left:				false,				// Adds right-to-left language support (text & animation direction) (true/false)
			lightbox_support:			false				// [Experimental] If true, Boutique will try to make your lightbox link work (see fancybox example) (true/false)
		}, options);

		$.each(this, function(){
			var $window = $(window),
				$container = $(this).addClass('boutique').show(),
				$parent = $container.parent(),
				$lis = $container.children('li');

			// Set constants
			var $newitem1, $newitem2, $newitem3, $newitem4, $newitem5, $item1, $item2, $item3, $item4, $item5,
				autotimer, container_width, container_height, front_img_height, next,
				containerid = $container.attr('id'),
				busy = false,
				hovering = false,
				current = o.starter,
				items = $lis.length,
				ie = false,
				ie6 = false,
				lteie8 = false,
				container_100 = false,
				hoverspeed = (o.speed/4),
				var_heights = false;
			if(typeof $.browser==='object' && $.browser.msie){
				ie = true;
				if($.browser.version < 9){
					lteie8 = true;
					if($.browser.version < 7){
						ie6 = true;
					}
				}
			}
			if(o.starter > items){
				o.starter = items;
			}
			if(o.right_to_left){
				$container.addClass('rtl');
			}
			if(!containerid){
				containerid = 'no_id';
			}

			// Option synonyms
			if('behind_opac' in o){ o.behind_opacity = o.behind_opac; }
			if('back_opac' in o){ o.back_opacity = o.back_opac; }
			if('autointerval' in o){ o.autoplay_interval = o.autointerval; }
			if('hoverspeed' in o){ hoverspeed = o.hoverspeed; }

			// Simulate 3 frames
			if(o.frames === 3 || o.frames === '3'){
				if(o.behind_distance === 'auto'){
					o.behind_distance = 0;
				}
				o.back_opacity = 0;
			}

			// For each list item...
			var $li = [],
				imgheights = [];
			$.each($lis, function(i){
				i += 1;
				var $this = $(this).addClass('li'+i),
					$a = $this.children('a'),
					$img = $this.find('img'),
					ratio = 0,
					imgwidth = 0,
					imgheight = 0,
					displayheight = 0,
				// Set headers from alt tags
					header = $img.attr('alt'),
					$span = $this.find('span');
				if(o.front_img_height === 'auto'){
					imgwidth = $img.attr('width');
					imgheight = parseInt($img.attr('height'), 10);
					if(!imgwidth){ imgwidth = $img.width(); }
					if(!imgheight){ imgheight = $img.height(); }
					imgheights.push(imgheight);
					// Calculate the display height
					if(imgwidth && (o.front_img_width !== imgwidth)){
						ratio = o.front_img_width / imgwidth;
						displayheight = Math.floor(ratio * imgheight);
					}
					$this.data('displayheight', displayheight);
				}else{
					$this.data('displayheight', o.front_img_height);
				}
				if(!$span.length){
					if(!header){
						$span = $('<span class="dummy" />');
					}else{
						$span = $('<span />');
					}
					if($a.length){
						$span.appendTo($a);
					}else{
						$span.appendTo($this);
					}
				}else{
					$span = $this.find('span');
				}
				if(!header){
					$('<h6 class="dummy" />').prependTo($span);
				}else{
					$('<h6>'+header+'</h6>').prependTo($span);
				}
				// Cache the element:
				$li[i] = $this;
			});

			// Prepare variable image heights
			if(o.front_img_height === 'auto'){
				var_heights = true;
				front_img_height = 0;
			}else{
				front_img_height = o.front_img_height;
			}

			// Line up first 5 items
			if(items === 1){
				$item1 = $li[1].clone().addClass('frame1').prependTo($container).data('framesource', 1);
				$item2 = $li[1].clone().addClass('frame2').prependTo($container).data('framesource', 1);
			}
			else if(o.starter === 2){
				$item2 = $li[1].clone().addClass('frame2').prependTo($container).data('framesource', 1);
				$item1 = $li[items].clone().addClass('frame1').prependTo($container).data('framesource', items);
			}
			else if(o.starter === 1){
				$item1 = $li[items-1].clone().addClass('frame1').prependTo($container).data('framesource', items-1);
				$item2 = $li[items].clone().addClass('frame2').prependTo($container).data('framesource', items);
			}
			else{
				$item2 = $li[o.starter-1].clone().addClass('frame2').prependTo($container).data('framesource', o.starter-1);
				$item1 = $li[o.starter-2].clone().addClass('frame1').prependTo($container).data('framesource', o.starter-2);
			}
			$item3 = $li[o.starter].clone().addClass('frame3').prependTo($container).data('framesource', o.starter);
			if(items===1){
				$item4 = $li[1].clone().addClass('frame4').prependTo($container).data('framesource', 1);
				$item5 = $li[1].clone().addClass('frame5').prependTo($container).data('framesource', 1);
			}
			else if(o.starter === (items-1)){
				$item4 = $li[items].clone().addClass('frame4').prependTo($container).data('framesource', items);
				$item5 = $li[1].clone().addClass('frame5').prependTo($container).data('framesource', 1);
			}
			else if(o.starter === items){
				$item4 = $li[1].clone().addClass('frame4').prependTo($container).data('framesource', 1);
				$item5 = $li[2].clone().addClass('frame5').prependTo($container).data('framesource', 2);
			}
			else{
				$item4 = $li[o.starter+1].clone().addClass('frame4').prependTo($container).data('framesource', o.starter+1);
				$item5 = $li[o.starter+2].clone().addClass('frame5').prependTo($container).data('framesource', o.starter+2);
			}

			// Remove rel attribute
			$item1.add($item2).add($item3).add($item4).add($item5).children('a').removeAttr('rel');

			// Set CSS classes
			var $back = $item1.add($item5).show().css({ opacity:0 }).addClass('back'),
				$behind = $item2.add($item4).show().css({ opacity:0 }).addClass('behind'),
				$front = $item3.show().css({ opacity:0 }).addClass('front');

			// Get container width
			if(o.container_width === 'auto' || o.container_width === '100%'){
				container_100 = true;
				container_width = parseInt($parent.width(), 10);
			}else{
				container_width = o.container_width;
			}

			// Get vertical frame margins
			var front_top = parseInt($front.css('marginTop'), 10),
				behind_top = parseInt($behind.css('marginTop'), 10),
				back_top = parseInt($back.css('marginTop'), 10);
			if(!front_top){ front_top = parseInt(o.front_topmargin, 10); }
			if(!behind_top){ behind_top = parseInt(o.behind_topmargin, 10); }
			if(!back_top){ back_top = parseInt(o.back_topmargin, 10); }

			var li_border			= parseInt($lis.css('borderLeftWidth'), 10),
				li_padding			= parseInt($lis.css('paddingLeft'), 10),
				front_header		= $front.find('h6').css('font-size'),
				front_span			= $front.find('span').css('font-size'),
				$front_img			= $front.find('img'),
				front_margin		= {
										top: parseInt($front_img.css('marginTop'), 10),
										right: parseInt($front_img.css('marginRight'), 10),
										bottom: parseInt($front_img.css('marginBottom'), 10),
										left: parseInt($front_img.css('marginLeft'), 10)
									},
				front_width			= Math.round(o.front_img_width + front_margin.left + front_margin.right + (li_padding*2) + (li_border*2)),
				front_height		= Math.round(front_img_height + front_margin.top + front_margin.bottom + (li_padding*2) + (li_border*2)),
				behind_img_width	= Math.round(o.front_img_width * o.behind_size),
				behind_img_height	= Math.round(front_img_height * o.behind_size),
				behind_header		= $behind.find('h6').css('font-size'),
				behind_span			= $behind.find('span').css('font-size'),
				$behind_img			= $behind.find('img'),
				behind_margin		= {
										top: parseInt($behind_img.css('marginTop'), 10),
										right: parseInt($behind_img.css('marginRight'), 10),
										bottom: parseInt($behind_img.css('marginBottom'), 10),
										left: parseInt($behind_img.css('marginLeft'), 10)
									},
				behind_width		= Math.round(behind_img_width + behind_margin.left + behind_margin.right + (li_padding*2) + (li_border*2)),
				behind_height		= Math.round(behind_img_height + behind_margin.top + behind_margin.bottom + (li_padding*2) + (li_border*2)),
				back_img_width		= Math.round(o.front_img_width * o.back_size),
				back_img_height		= Math.round(front_img_height * o.back_size),
				back_header			= $back.find('h6').css('font-size'),
				back_span			= $back.find('span').css('font-size'),
				$back_img			= $back.find('img'),
				back_margin			= {
										top: parseInt($back_img.css('marginTop'), 10),
										right: parseInt($back_img.css('marginRight'), 10),
										bottom: parseInt($back_img.css('marginBottom'), 10),
										left: parseInt($back_img.css('marginLeft'), 10)
									},
				back_width			= Math.round(back_img_width + back_margin.left + back_margin.right + (li_padding*2) + (li_border*2)),
				back_height			= Math.round(back_img_height + back_margin.top + back_margin.bottom + (li_padding*2) + (li_border*2)),
				item3_pos			= Math.round((container_width/2)-(front_width/2)),
				item5_pos			= (container_width - back_width),
				item2_pos;

			// Variable image heights
			if(var_heights){
				front_img_height = 'auto';
				behind_img_height = 'auto';
				back_img_height = 'auto';
			}

			// Optional custom behind frame distance
			if(o.behind_distance !== 'auto'){
				item2_pos = parseInt(o.behind_distance, 10);
			}else{
				item2_pos = Math.round( (container_width/4)-(behind_width/2) );
			}
			var item4_pos = (container_width - item2_pos - behind_width);

			// Remove CSS classes & dummy elements
			$back.removeClass('back');
			$behind.removeClass('behind');
			$front.removeClass('front');

			// Deal with the text container <span> padding for future animation
			var front_span_paddingTop = $('span', $lis).css('padding-top'),
				front_span_paddingRight = $('span', $lis).css('padding-right'),
				front_span_paddingBottom = $('span', $lis).css('padding-bottom'),
				front_span_paddingLeft = $('span', $lis).css('padding-left'),
				front_span_animate = {
					opacity: o.text_opacity,
					fontSize: front_span,
					paddingTop: front_span_paddingTop,
					paddingRight: front_span_paddingRight,
					paddingBottom: front_span_paddingBottom,
					paddingLeft: front_span_paddingLeft
				},
				behind_span_animate = {
					opacity: o.text_opacity,
					fontSize: behind_span,
					paddingTop: Math.round( parseInt(front_span_paddingTop, 10)*0.8 ),
					paddingRight: Math.round( parseInt(front_span_paddingRight, 10)*0.8 ),
					paddingBottom: Math.round( parseInt(front_span_paddingBottom, 10)*0.8 ),
					paddingLeft: Math.round( parseInt(front_span_paddingLeft, 10)*0.8 )
				},
				back_span_animate = {
					opacity: o.text_opacity,
					fontSize: back_span,
					paddingTop: Math.round( parseInt(front_span_paddingTop, 10)*0.6 ),
					paddingRight: Math.round( parseInt(front_span_paddingRight, 10)*0.6 ),
					paddingBottom: Math.round( parseInt(front_span_paddingBottom, 10)*0.6 ),
					paddingLeft: Math.round( parseInt(front_span_paddingLeft, 10)*0.6 )
				};
			if(o.text_front_only){
				behind_span_animate.opacity = 0;
				back_span_animate = $.extend(back_span_animate, behind_span_animate);
			}
			if(ie6){
				var front_span_margin = (parseInt($front.find('span').css('margin-left'), 10) + parseInt($front.find('span').css('margin-right'), 10)),
					behind_span_margin = (parseInt($behind.find('span').css('margin-left'), 10) + parseInt($behind.find('span').css('margin-right'), 10)),
					back_span_margin = (parseInt($back.find('span').css('margin-left'), 10) + parseInt($back.find('span').css('margin-right'), 10));
				front_span_animate.width = front_width-parseInt(front_span_paddingRight, 10)-parseInt(front_span_paddingLeft, 10)-front_span_margin-(li_border*2);
				behind_span_animate.width = behind_width-behind_span_animate.paddingRight-behind_span_animate.paddingLeft-behind_span_margin-(li_border*2);
				back_span_animate.width = back_width-back_span_animate.paddingRight-back_span_animate.paddingLeft-back_span_margin-(li_border*2);
			}

			// Now safe to remove dummy elements
			$container.find('.dummy').remove();

			// Get total container height
			var framespaces = [],
				resetContainerHeight = function(animate){
					framespaces = [];
					framespaces.push(Math.floor(parseInt($li[$item1.data('framesource')].data('displayheight'), 10) * o.back_size) + back_height + parseInt(back_top, 10));
					framespaces.push(Math.floor(parseInt($li[$item2.data('framesource')].data('displayheight'), 10) * o.behind_size) + behind_height + parseInt(behind_top, 10));
					framespaces.push(parseInt($li[current].data('displayheight'), 10) + front_height + parseInt(front_top, 10));
					framespaces.push(Math.floor(parseInt($li[$item4.data('framesource')].data('displayheight'), 10) * o.behind_size) + behind_height + parseInt(behind_top, 10));
					framespaces.push(Math.floor(parseInt($li[$item5.data('framesource')].data('displayheight'), 10) * o.back_size) + back_height + parseInt(back_top, 10));
					if(animate){
						framespaces.sort(function(a,b){ return b-a; });
						if(container_height !== framespaces[0]){
							container_height = framespaces[0];
							$container.stop().animate({ height:container_height }, o.speed, o.easing).css({ overflow:'visible' });
						}
					}
			};
			if(o.variable_container_height && var_heights){
				resetContainerHeight(false);
			}else if(var_heights){
				// Find the largest img height
				imgheights.sort(function(a,b){ return b-a; });
				var tallestimage = parseInt(imgheights[0], 10);
				// If not available, set it to 300
				if(!tallestimage){
					tallestimage = 300;
				}
				framespaces.push(tallestimage + front_height + parseInt(front_top, 10));
				framespaces.push(tallestimage + behind_height + parseInt(behind_top, 10));
				framespaces.push(tallestimage + back_height + parseInt(back_top, 10));
			}else{
				framespaces.push(front_height + parseInt(front_top, 10));
				framespaces.push(behind_height + parseInt(behind_top, 10));
				framespaces.push(back_height + parseInt(back_top, 10));
			}
			framespaces.sort(function(a,b){ return b-a; });
			container_height = framespaces[0];

			// Starting positions
			$container
				.height(container_height)
				.width(container_width);
			$item1.css({ left:0, top:back_top }).css({ opacity:o.back_opacity })
				.find('img').css({ width:back_img_width, height:back_img_height, marginTop:back_margin.top, marginRight:back_margin.right, marginBottom:back_margin.bottom, marginLeft:back_margin.left, opacity:1 })
				.siblings('span').css(back_span_animate)
				.children('h6').css({ fontSize:back_header });
			$item2.css({ left:item2_pos, top:behind_top, zIndex:2 }).css({ opacity:o.behind_opacity })
				.find('img').css({ width:behind_img_width, height:behind_img_height, marginTop:behind_margin.top, marginRight:behind_margin.right, marginBottom:behind_margin.bottom, marginLeft:behind_margin.left, opacity:1 })
				.siblings('span').css(behind_span_animate)
				.children('h6').css({ fontSize:behind_header });
			$item3.css({ left:item3_pos, top:front_top, zIndex:3 }).css({ opacity:1 })
				.find('a *').css({ cursor:'pointer' }).end()
				.find('img').css({ width:o.front_img_width, height:front_img_height, marginTop:front_margin.top, marginRight:front_margin.right, marginBottom:front_margin.bottom, marginLeft:front_margin.left, opacity:1 })
				.siblings('span').css(front_span_animate)
				.children('h6').css({fontSize:front_header});
			$item4.css({ left:item4_pos, top:behind_top, zIndex:2 }).css({ opacity:o.behind_opacity })
				.find('img').css({ width:behind_img_width, height:behind_img_height, marginTop:behind_margin.top, marginRight:behind_margin.right, marginBottom:behind_margin.bottom, marginLeft:behind_margin.left, opacity:1 })
				.siblings('span').css(behind_span_animate)
				.children('h6').css({fontSize:behind_header});
			$item5.css({ left:item5_pos, top:back_top }).css({ opacity:o.back_opacity })
				.find('img').css({ width:back_img_width, height:back_img_height, marginTop:back_margin.top, marginRight:back_margin.right, marginBottom:back_margin.bottom, marginLeft:back_margin.left, opacity:1 })
				.siblings('span').css(back_span_animate)
				.children('h6').css({fontSize:back_header});
			if(o.text_front_only){
				$item1.add($item2).add($item4).add($item5).find('span').hide();
			}
			// Fix ie6 not understanding how to give an 'auto' height
			if(ie6 && var_heights){
				$item1.find('img').css({ height: Math.floor(($li[ $item1.data('framesource') ].data('displayheight'))*o.back_size) });
				$item2.find('img').css({ height: Math.floor(($li[ $item2.data('framesource') ].data('displayheight'))*o.behind_size) });
				$item3.find('img').css({ height: Math.floor($li[ $item3.data('framesource') ].data('displayheight')) });
				$item4.find('img').css({ height: Math.floor(($li[ $item4.data('framesource') ].data('displayheight'))*o.behind_size) });
				$item5.find('img').css({ height: Math.floor(($li[ $item5.data('framesource') ].data('displayheight'))*o.back_size) });
			}

			// Prepare image animations
			var backimganimate = { width:back_img_width, marginTop:back_margin.top, marginRight:back_margin.right, marginBottom:back_margin.bottom, marginLeft:back_margin.left, opacity:1 };
			if(!var_heights){ backimganimate.height = back_img_height; }
			var newbackimganimate = $.extend({}, backimganimate);
			if(var_heights){ newbackimganimate.height = 'auto'; }
			var behindimganimate = { width:behind_img_width, marginTop:behind_margin.top, marginRight:behind_margin.right, marginBottom:behind_margin.bottom, marginLeft:behind_margin.left, opacity:1 };
			if(!var_heights){ behindimganimate.height = behind_img_height; }
			var frontimganimate = { width:o.front_img_width, marginTop:front_margin.top, marginRight:front_margin.right, marginBottom:front_margin.bottom, marginLeft:front_margin.left, opacity:1 };
			if(!var_heights){ frontimganimate.height = front_img_height; }

			// Define easing types
			if(typeof $.easing.easeIOBoutique==='undefined' || !$.easing.easeIOBoutique){
				$.extend($.easing, {
					easeInBoutique: function(t, millisecondsSince, startValue, endValue, totalDuration){
						var rest = millisecondsSince/totalDuration;
						return rest*rest;
					},
					easeOutBoutique: function(t, millisecondsSince, startValue, endValue, totalDuration){
						var rest = millisecondsSince/totalDuration;
						return -1*rest*(rest-2);
					},
					easeIOBoutique: function(t, millisecondsSince, startValue, endValue, totalDuration){
						var rest = (millisecondsSince/(totalDuration/2));
						if(t<=0.5){ return ((rest/2) * rest); }
						else{ return -0.5 * ((--rest)*(rest-2) - 1); }
					}
				});
			}

// FUNCTIONS

			// Set container width
			function setContainerWidth(){
				// Container
				var width = parseInt($parent.width(), 10);
				$container.width( width );
				// Position frames
				if(o.behind_distance === 'auto'){
					item2_pos = Math.round( (width/4)-(behind_width/2) );
					item3_pos = Math.round( (width/2)-(front_width/2) );
					$item2.css({ left:item2_pos });
					$item4.css({ left:item4_pos });
				}
				item4_pos = (width - item2_pos - behind_width);
				item5_pos = (width - back_width);
				$item3.css({ left:item3_pos });
				$item5.css({ left:item5_pos });
			};
			if(container_100){
				setContainerWidth();
			}

			// Autoplay functions
			function stopInterval(){
				if(autotimer){
					clearInterval(autotimer);
					autotimer = false;
				}
			};
			function startInterval(){
				if(autotimer){
					stopInterval();
				}
				autotimer = setInterval(function(){
					if(o.right_to_left){
						$item2.click();
					}else{
						$item4.click();
					}
				}, o.autoplay_interval);
			};

			// Move right
			function moveRight(times, state){
				var $itemimg, eazing, zpeed = o.speed;
				busy = true;
				state = state || false;
				if(!state){
					if(times > 1){ state = 'first'; }
					else{ state = 'normal'; }
				}

				// Set easing type and easing speed
				if(times > 1 && state==='first'){
					eazing = o.move_more_easein;
					zpeed = Math.round(o.speed * 0.7);
				}else if(times > 1){
					eazing = o.move_more_easebetween;
					zpeed = Math.round(o.speed * 0.4);
				}else if(state==='last'){
					eazing = o.move_more_easeout;
				}else{
					eazing = o.easing;
				}

				// Pause autoplay
				if(o.autoplay){
					stopInterval();
				}

				// Set next item number
				if(current === (items-2)){
					next = 1;
				}else if(current === (items-1)){
					next = 2;
					if(next > items){ next = 1; }
				}else if(current === items){
					next = 3;
					if(next > items){ next = 1; }
				}
				else{
					next = (current+3);
				}

				// Pre-move callback
				if(typeof pre_move_callback === 'function'){
					var href = $item4.children('a').attr('href');
					if(!href){
						href = 'no_anchor';
					}
					pre_move_callback(href, containerid, $item4.data('framesource'));
				}

				if(lteie8){
					// Container height should not animate at the same time as slides in ie
					$container.stop();
					// Hide caption for text_front_only
					if(o.text_front_only){ $item3.find('span').hide(); }
				}

				// Remove item 1
				$item1.removeClass('frame1').addClass('remove').css('z-index', -1);

				// Item 2 becomes item 1
				$newitem1 = $item2.removeClass('frame2').addClass('frame1').stop().animate({ opacity:o.back_opacity, left:0, top:back_top }, zpeed, eazing);
				$itemimg = $newitem1.find('img').stop();
				if(ie6 && var_heights){ $itemimg.css({ height:'auto' }); }
				$itemimg.animate(backimganimate, zpeed, eazing);
				if(!o.text_front_only){
					$newitem1.find('h6').stop().animate({ fontSize:back_header }, zpeed, eazing)
						.end().find('span').stop().animate(back_span_animate, zpeed, eazing);
				}
				setTimeout(function(){ $newitem1.css('z-index', 1); }, (zpeed/4));

				// Item 3 becomes item 2
				$newitem2 = $item3.removeClass('frame3').addClass('frame2').stop().animate({ opacity:o.behind_opacity, left:item2_pos, top:behind_top }, zpeed, eazing);
				$newitem2.find('h6').stop().animate({ fontSize:behind_header }, zpeed, eazing)
					.end().find('span').stop().animate(behind_span_animate, zpeed, eazing);
				$itemimg = $newitem2.find('img').stop();
				if(ie6 && var_heights){ $itemimg.css({ height:'auto' }); }
				$itemimg.animate(behindimganimate, zpeed, eazing);
				setTimeout(function(){ $newitem2.css('z-index', 2); }, (zpeed/4));

				// Item 4 becomes item 3
				$newitem3 = $item4.removeClass('frame4').addClass('frame3').stop().animate({ opacity:1, left:item3_pos, top:front_top }, zpeed, eazing, function(){
					if(lteie8 && o.text_front_only){
						$(this).find('h6').stop().css({ fontSize:front_header })
							.end().find('span').stop().css(front_span_animate).fadeIn(200);
					}
					if(lteie8 && var_heights && o.variable_container_height){
						resetContainerHeight(true);
					}
				});
				$itemimg = $newitem3.find('img').stop();
				if(ie6 && var_heights){ $itemimg.css({ height:'auto' }); }
				$itemimg.animate(frontimganimate, zpeed, eazing);
				if(!lteie8 || !o.text_front_only){
					$newitem3.find('h6').stop().animate({ fontSize:front_header }, zpeed, eazing)
						.end().find('span').stop().show().animate(front_span_animate, zpeed, eazing);
				}
				setTimeout(function(){ $newitem3.css('z-index', 3); }, (zpeed/4));

				// Item 5 becomes item 4
				$newitem4 = $item5.removeClass('frame5').addClass('frame4').stop().animate({ opacity:o.behind_opacity, left:item4_pos, top:behind_top }, zpeed, eazing);
				$itemimg = $newitem4.find('img').stop();
				if(ie6 && var_heights){ $itemimg.css({ height:'auto' }); }
				$itemimg.animate(behindimganimate, zpeed, eazing);
				if(!o.text_front_only){
					$newitem4.find('h6').stop().animate({ fontSize:behind_header }, zpeed, eazing)
						.end().find('span').stop().animate(behind_span_animate, zpeed, eazing);
				}
				setTimeout(function(){ $newitem4.css('z-index', 2); }, (zpeed/4));

				// Add a new item 5
				$item5 = $li[next].clone();
				$item5.addClass('frame5')
					.prependTo($container)
					.data('framesource', next)
					.show()
					.css({ opacity:0, left:item5_pos, top:back_top })
					.animate({ opacity:o.back_opacity }, zpeed, function(){
						// When done animating:
						// Remove pointer cursor from previous item
						$newitem2.find('a *').css({ cursor:'default' });
						// Continue autoplay
						if(o.autoplay){
							startInterval();
						}
						// Move 2nd time if requested
						if(times === 2){
							moveRight(1, 'last');
						}
						// Move more
						else if(times > 1){
							moveRight(times-1, 'normal');
						}
						// Add pointer cursor if front frame has a link
						else{
							$newitem3.find('a *').css({ cursor:'pointer' });
						}
						if(!$newitem3.is(':animated')){
							// Reenable click events
							busy = false;
							// Make sure old items are removed
							$container.children('.remove').stop().fadeOut(zpeed, function(){ $(this).remove(); });
							// Callback: item with this anchor moved forward
							if(typeof move_callback === 'function'){
								var href = $newitem3.children('a').attr('href');
								if(!href){
									href = 'no_anchor';
								}
								move_callback(href, containerid, $item3.data('framesource'));
							}
						}
					})
					.find('img').css(newbackimganimate)
					.end().children('a').removeAttr('rel');
				// New frame description
				if(o.text_front_only){
					$item5.find('h6').css({ fontSize:behind_header })
						.end().find('span').css(behind_span_animate).hide();
				}else{
					$item5.find('h6').css({ fontSize:back_header })
						.end().find('span').css(back_span_animate);
				}
				// Recache
				$item1 = $newitem1;
				$item2 = $newitem2;
				$item3 = $newitem3;
				$item4 = $newitem4;
				// Remove the out of range item
				$container.children('.remove').fadeOut(zpeed, function(){ $(this).remove(); });
				// Set new current
				if(current === items){
					current = 1;
				}else{
					current = (current+1);
				}
				// Animate container height
				if(!lteie8 && var_heights && o.variable_container_height){
					resetContainerHeight(true);
				}
			};

			// Move left
			function moveLeft(times, state){
				var $itemimg, eazing, zpeed = o.speed;
				busy = true;
				state = state || false;
				if(!state){
					if(times > 1){ state = 'first'; }
					else{ state = 'normal'; }
				}

				// Set easing type and easing speed
				if(times > 1 && state==='first'){
					eazing = o.move_more_easein;
					zpeed = Math.round(o.speed * 0.7);
				}else if(times > 1){
					eazing = o.move_more_easebetween;
					zpeed = Math.round(o.speed * 0.4);
				}else if(state === 'last'){
					eazing = o.move_more_easeout;
				}else{
					eazing = o.easing;
				}

				// Pause autoplay
				if(o.autoplay){
					stopInterval();
				}

				// Set next item number
				if(current === 3){
					next = items;
				}else if(current === 2){
					next = (items-1);
					if(next < 1){ next = items; }
				}else if(current === 1){
					next = (items-2);
					if(next < 1){ next = items; }
				}
				else{
					next = (current-3);
				}

				// Pre-move callback
				if(typeof pre_move_callback === 'function'){
					var href = $item2.children('a').attr('href');
					if(!href){
						href = 'no_anchor';
					}
					pre_move_callback(href, containerid, $item2.data('framesource'));
				}

				if(lteie8){
					// Container height should not animate at the same time as slides in ie
					$container.stop();
					// Hide caption for text_front_only
					if(o.text_front_only){ $item3.find('span').hide(); }
				}

				// Remove item 5
				$item5.removeClass('frame5').addClass('remove').css('z-index', -1);

				// Item 4 becomes item 5
				$newitem5 = $item4.removeClass('frame4').addClass('frame5').stop().animate({ opacity:o.back_opacity, left:item5_pos, top:back_top }, zpeed, eazing);
				$itemimg = $newitem5.find('img').stop();
				if(ie6 && var_heights){ $itemimg.css({ height:'auto' }); }
				$itemimg.animate(backimganimate, zpeed, eazing);
				if(!o.text_front_only){
					$newitem5.find('h6').stop().animate({ fontSize:back_header }, zpeed, eazing)
						.end().find('span').stop().animate(back_span_animate, zpeed, eazing);
				}
				setTimeout(function(){ $newitem5.css('z-index', 1); }, (zpeed/4));

				// Item 3 becomes item 4
				$newitem4 = $item3.removeClass('frame3').addClass('frame4').stop().animate({ opacity:o.behind_opacity, left:item4_pos, top:behind_top }, zpeed, eazing);
				$newitem4.find('h6').stop().animate({ fontSize:behind_header }, zpeed, eazing)
					.end().find('span').stop().animate(behind_span_animate, zpeed, eazing);
				$itemimg = $newitem4.find('img').stop();
				if(ie6 && var_heights){ $itemimg.css({ height:'auto' }); }
				$itemimg.animate(behindimganimate, zpeed, eazing);
				setTimeout(function(){ $newitem4.css('z-index', 2); }, (zpeed/4));

				// Item 2 becomes item 3
				$newitem3 = $item2.removeClass('frame2').addClass('frame3').stop().animate({ opacity:1, left:item3_pos, top:front_top }, zpeed, eazing, function(){
					if(lteie8 && o.text_front_only){
						$(this).find('h6').stop().css({ fontSize:front_header })
							.end().find('span').stop().css(front_span_animate).fadeIn(200);
					}
					if(lteie8 && var_heights && o.variable_container_height){
						resetContainerHeight(true);
					}
				});
				$itemimg = $newitem3.find('img').stop();
				if(ie6 && var_heights){ $itemimg.css({ height:'auto' }); }
				$itemimg.animate(frontimganimate, zpeed, eazing);
				if(!lteie8 || !o.text_front_only){
					$newitem3.find('h6').stop().animate({ fontSize:front_header }, zpeed, eazing)
						.end().find('span').stop().show().animate(front_span_animate, zpeed, eazing);
				}
				setTimeout(function(){ $newitem3.css('z-index', 3); }, (zpeed/4));

				// Item 1 becomes item 2
				$newitem2 = $item1.removeClass('frame1').addClass('frame2').stop().animate({ opacity:o.behind_opacity, left:item2_pos, top:behind_top }, zpeed, eazing);
				$itemimg = $newitem2.find('img').stop();
				if(ie6 && var_heights){ $itemimg.css({ height:'auto' }); }
				$itemimg.animate(behindimganimate, zpeed, eazing);
				if(!o.text_front_only){
					$newitem2.find('h6').stop().animate({ fontSize:behind_header }, zpeed, eazing)
						.end().find('span').stop().animate(behind_span_animate, zpeed, eazing);
				}
				setTimeout(function(){ $newitem2.css('z-index', 2); }, (zpeed/4));

				// Add a new item 1
				$item1 = $li[next].clone();
				$item1.addClass('frame1')
					.prependTo($container)
					.data('framesource', next)
					.show()
					.css({ opacity:0, left:0, top:back_top })
					.animate({ opacity:o.back_opacity }, zpeed, function(){
						// When done animating:
						// Remove pointer cursor from previous item
						$newitem4.find('a *').css({ cursor:'default' });
						// Continue autoplay
						if(o.autoplay){
							startInterval();
						}
						// Move 2nd time if requested
						if(times === 2){
							moveLeft(1, 'last');
						}
						// Move more
						else if(times > 1){
							moveLeft(times-1, 'normal');
						}
						// Add pointer cursor if front frame has a link
						else{
							$newitem3.find('a *').css({ cursor:'pointer' });
						}
						if(!$newitem3.is(':animated')){
							// Reenable click events
							busy = false;
							// Make sure old items are removed
							$container.children('.remove').stop().fadeOut(zpeed, function(){ $(this).remove(); });
							// Callback: item with this anchor moved forward
							if(typeof move_callback === 'function'){
								var href = $newitem3.children('a').attr('href');
								if(!href){
									href = 'no_anchor';
								}
								move_callback(href, containerid, $item3.data('framesource'));
							}
						}
					})
					.find('img').css(newbackimganimate)
					.end().children('a').removeAttr('rel');
				// New frame description
				if(o.text_front_only){
					$item1.find('h6').css({ fontSize:behind_header })
						.end().find('span').css(behind_span_animate).hide();
				}else{
					$item1.find('h6').css({ fontSize:back_header })
						.end().find('span').css(back_span_animate);
				}
				// Recache
				$item2 = $newitem2;
				$item3 = $newitem3;
				$item4 = $newitem4;
				$item5 = $newitem5;
				// Remove the out of range item
				$container.children('.remove').fadeOut(zpeed, function(){ $(this).remove(); });
				// Set new current
				if(current === 1){
					current = items;
				}else{
					current = (current-1);
				}
				// Animate container height
				if(!lteie8 && var_heights && o.variable_container_height){
					resetContainerHeight(true);
				}
			};

// PUBLIC/EXTERNAL FUNCTIONS

			// Previous frame
			window[containerid+'_ext_prev'] =
			window[containerid+'_prev'] =
			window[containerid+'_previous'] =
			function(times){
				times = times || 1;
				if(o.right_to_left){
					moveRight(times);
				}else{
					moveLeft(times);
				}
			};

			// Next frame
			window[containerid+'_ext_next'] =
			window[containerid+'_next'] =
			function(times){
				times = times || 1;
				if(o.right_to_left){
					moveLeft(times);
				}else{
					moveRight(times);
				}
			};

			// Stop autoplay
			window[containerid+'_stopautoplay'] = function(){
				if(o.autoplay){
					stopInterval();
				}
				o.autoplay = false;
			};

			// Start autoplay
			window[containerid+'_startautoplay'] = function(){
				if(!o.autoplay){
					startInterval();
				}
				o.autoplay = true;
			};

			// Go to specific frame
			window[containerid+'_goto'] = function(frame){
				frame = frame || false;
				if(frame){
					frame = parseInt(frame, 10);
					// If it's a number and not the active frame and the frame exists
					if(parseFloat(frame)===frame && current!==frame && items>=frame){
						var steps = 0,
							direction;
						if(current < frame){
							if(frame-current <= (current+(items-frame))){
								direction = 'right';
								steps = frame - current;
							}else{
								direction = 'left';
								steps = current+(items-frame);
							}
						}else{
							if(((items-current)+frame) <= current-frame){
								direction = 'right';
								steps = (items-current)+frame;
							}else{
								direction = 'left';
								steps = current-frame;
							}
						}
						if(direction==='right'){
							moveRight(steps);
						}else{
							moveLeft(steps);
						}
					}
				}
			};

// ACTIONS

			// Frame 1 click (move 2 steps left)
			$container.delegate('.frame1', 'click', function(e){
				if(o.freescroll || !busy){
					if(o.never_move_twice){
						moveLeft(1);
					}else if(o.move_more_directly){
						moveLeft(1);
						moveLeft(1);
					}else{
						moveLeft(2);
					}
					e.preventDefault();
				}
			});
			// Frame 2 click (move 1 step left)
			$container.delegate('.frame2', 'click', function(e){
				if(o.freescroll || !busy){
					moveLeft(1);
					e.preventDefault();
				}
			});
			// Frame 4 click (move 1 step right)
			$container.delegate('.frame4', 'click', function(e){
				if(o.freescroll || !busy){
					moveRight(1);
					e.preventDefault();
				}
			});
			// Frame 5 click (move 2 steps right)
			$container.delegate('.frame5', 'click', function(e){
				if(o.freescroll || !busy){
					if(o.never_move_twice){
						moveRight(1);
					}else if(o.move_more_directly){
						moveRight(1);
						moveRight(1);
					}else{
						moveRight(2);
					}
					e.preventDefault();
				}
			});
			if(o.move_on_hover){
				// Frame 1 hover (move 2 steps left)
				$container.delegate('.frame1', 'mousemove', function(){
					$item1.click();
				});
				// Frame 2 hover (move 1 step left)
				$container.delegate('.frame2', 'mousemove', function(){
					$item2.click();
				});
				// Frame 4 hover (move 1 step right)
				$container.delegate('.frame4', 'mousemove', function(){
					$item4.click();
				});
				// Frame 5 hover (move 2 steps right)
				$container.delegate('.frame5', 'mousemove', function(){
					$item5.click();
				});
			}

			// Mouse-over center frame: zoom in
			var hover_animateframe = { left:'-='+Math.floor(o.front_img_width*(o.hovergrowth/2)), top:'-='+Math.floor((o.front_img_width*(o.hovergrowth))/2) };
			if(!var_heights){
				hover_animateframe.top = '-='+Math.floor(front_img_height*o.hovergrowth);
			}
			var hover_frame2animate = { left:'-='+Math.floor(behind_img_width*o.hovergrowth) };
			var hover_frame4animate = { left:'+='+Math.floor(behind_img_width*o.hovergrowth) };
			var hover_animateimage = { width:Math.floor(o.front_img_width*(1+o.hovergrowth)) };
			var hover_iespananimate = { width:'+='+Math.floor(o.hovergrowth*o.front_img_width) };
			if(!var_heights){
				hover_animateimage.height = Math.floor(front_img_height*(1+o.hovergrowth));
			}
			var hoverout_animateimage = { width:o.front_img_width };
			if(!var_heights){
				hoverout_animateimage.height = front_img_height;
			}
			$container.delegate('.frame3', 'mouseenter mouseleave', function(e){
				if(e.type === 'mouseenter' && !busy && !hovering){
					// Prevent double effect
					hovering = true;
					if(o.autoplay){
						stopInterval();
					}
					// Prepare ie6
					if(var_heights && ie6){
						hover_animateimage.height = Math.floor($li[$item3.data('framesource')].data('displayheight')*(1+o.hovergrowth));
					}
					// Animate
					$item3.addClass('zoomed')
						.stop(true,true).animate(hover_animateframe, hoverspeed)
						.find('img').stop().animate(hover_animateimage, hoverspeed);
					$item2.stop(true,true).animate(hover_frame2animate, hoverspeed);
					$item4.stop(true,true).animate(hover_frame4animate, hoverspeed);
					// Set span width for IE6
					if(ie6){
						$item3.find('span').stop().animate(hover_iespananimate, hoverspeed);
					}
				}
				// Mouse-out center frame: zoom out
				else if(!busy){
					hovering = false;
					if(o.autoplay){
						startInterval();
					}
					// Prepare ie6
					if(var_heights && ie6){
						hoverout_animateimage.height = $li[$item3.data('framesource')].data('displayheight');
					}
					// Animate
					$item3.stop().animate({ left:item3_pos, top:front_top }, hoverspeed)
						.find('img').stop().animate(hoverout_animateimage, hoverspeed, function(){
							$item3.removeClass('zoomed');
						});
					$item2.stop().animate({ left:item2_pos }, hoverspeed);
					$item4.stop().animate({ left:item4_pos }, hoverspeed);
					// Set span width for IE6
					if(ie6){
						$container.find('.zoomed span').stop().animate(front_span_animate, hoverspeed);
					}
				}
			});

			// Also zoom in if center frame is slided in under your cursor (Excluding IE)
			if(!ie){
				$container.delegate('.frame3:not(.zoomed)', 'mousemove', function(){
					if(!hovering){
						$item3.trigger('mouseenter');
					}
				});
			}

			// Callback: when a front-frame link is clicked
			$container.delegate('.frame3 a', 'click', function(e){
				if(typeof link_callback === 'function'){
					link_callback($(this).attr('href'), containerid);
				}
				// Stop autoplay
				if(o.autoplay && o.stop_autoplay_on_click){
					stopInterval();
					o.autoplay = false;
				}
				// Trigger lightbox link
				if(o.lightbox_support){
					// Find original frame and click that
					$lis.eq(current-1).children('a').click();
					e.preventDefault();
				}
			});

			// Keystrokes
			if(o.keyboard){
				$(document).keydown(function(e){
					// Right arrow key = move right (also Enter key / Space key when language is left-to-right)
					if(e.keyCode===39 || (!o.right_to_left && (e.keyCode===13 || e.keyCode===32))){
						moveRight(1);
					}
					// Left arrow key = move left (also Enter key / Space key when language is right-to-left)
					if(e.keyCode===37 || (o.right_to_left && (e.keyCode===13 || e.keyCode===32))){
						moveLeft(1);
					}
				});
			}

			// Initiate first autoplay
			if(o.autoplay){
				startInterval();
			}

			// Window resize (100% container width)
			if(container_100){
				$window.resize(function(){
					setContainerWidth();
				});
			}

		// End plugin wrap
		});
		return this;
	};
})(jQuery);