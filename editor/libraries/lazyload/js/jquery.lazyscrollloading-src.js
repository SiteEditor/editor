/**
 * @preserve jQuery Lazy Scroll Loading Plugin 0.4.5
 * 
 * http://plugins.jquery.com/jQueryLazyScrollLoading/
 * https://code.google.com/p/jquerylazyscrollloading/
 * 
 * Apache License 2.0 - http://www.apache.org/licenses/LICENSE-2.0
 * 
 * @author Dreamltf
 * @date 2013/12/07
 * 
 * Depends: jquery.js (1.2+)
 */
(function($) {
	/* static variables */
	var PLUGIN_NAMESPACE = "LazyScrollLoading";
	var PLUGIN_LAZYIMAGE_ATTR = "lazy-img";

	/* default options */
	var defaultOptions = {
		isDefaultLazyImageMode : false,
		lazyItemSelector : null,
		delay : 500,
		/* callback function */
		onCreate : null,
		onScroll : null,
		onLazyItemFirstVisible : null,
		onLazyItemVisible : null,
		onLazyItemInvisible : null,
		onScrollVertically : null,
		onScrollHorizontally : null,
		onScrollUp : null,
		onScrollDown : null,
		onScrollLeft : null,
		onScrollRight : null,
		onScrollToTop : null,
		onScrollToBottom : null,
		onScrollToLeftmost : null,
		onScrollToRightmost : null
	};

	/* others variables */
	var userAgentStr = navigator.userAgent.toLowerCase();
	var isIE = /msie/.test(userAgentStr);
	var IEVersion = (isIE ? parseFloat((userAgentStr.match(/.*(?:rv|ie)[\/: ](.+?)([ \);]|$)/) || [])[1]) : -1);
	var scrollBarSize = 16;

	/**
	 * Public Method
	 */
	$.extend($.fn, {
		/**
		 * Public : Main method
		 * 
		 * @param options
		 *            Object
		 * @return jQuery
		 */
		lazyScrollLoading : function(options) {
			options = $.extend({}, defaultOptions, options);
			/* correct options */
			if (options.isDefaultLazyImageMode && options.lazyItemSelector == null) {
				options.lazyItemSelector = "img[" + PLUGIN_LAZYIMAGE_ATTR + "]:not([src])";
			}
			/* starting */
			return this.each(function() {
				var $container = $(this);
				/* destroy */
				$container.destroyLazyScrollLoading();
				/* prepare options */
				$container.data("options." + PLUGIN_NAMESPACE, options);
				/* initialize */
				initializeLazyScrollLoading($container, options);
				/* trigger event */
				if (options.onCreate != null) {
					options.onCreate.apply($container[0]);
				}
			});
		},

		/**
		 * Public : Get container's options
		 * 
		 * @return Object
		 */
		getLazyScrollLoadingOptions : function() {
			return this.data("options." + PLUGIN_NAMESPACE);
		},

		/**
		 * Public : Get container's scroll history
		 * 
		 * @return Object
		 */
		getLazyScrollLoadingScrollHistory : function() {
			return this.data("scrollHistory." + PLUGIN_NAMESPACE);
		},

		/**
		 * Public : Clear container's scroll history
		 * 
		 * @return jQuery
		 */
		clearLazyScrollLoadingScrollHistory : function() {
			return this.removeData("scrollHistory." + PLUGIN_NAMESPACE);
		},

		/**
		 * Public : Get container or lazyItem's viewport
		 * 
		 * @return Object
		 */
		getLazyScrollLoadingViewport : function() {
			var $container = this;
			var container = $container[0];
			var isRoot = isRootContainer(container);
			var $window = $(window);
			var $document = $(document);
			var $body = $(document.body);
			var containerScrollHistory = $container.getLazyScrollLoadingScrollHistory();
			return {
				getOffset : function() {
					return (isRoot ? $body.offset() : $container.offset());
				},
				getScrollLeft : function() {
					return (isRoot ? $window : $container).scrollLeft();
				},
				getScrollTop : function() {
					return (isRoot ? $window : $container).scrollTop();
				},
				getScrollBindTarget : function() {
					return (isRoot ? $document : $container);
				},
				getWidth : function(isOuter) {
					return (isRoot ? $window.width() : (isOuter ? $container.outerWidth() : $container.innerWidth()));
				},
				getHeight : function(isOuter) {
					return (isRoot ? $window.height() : (isOuter ? $container.outerHeight() : $container.innerHeight()));
				},
				getScrollWidth : function(includeScrollBarSize) {
					/* -1: when root container scroll to the rightmost, it's scroll weight not corrected */
					return (isRoot ? $document.width() : container.scrollWidth) + (includeScrollBarSize && this.isVerticalScrollBarVisible() ? scrollBarSize : (isRoot ? -1 : 0));
				},
				getScrollHeight : function(includeScrollBarSize) {
					/* -1: when root container scroll to the bottom, it's scroll height not corrected */
					return (isRoot ? $document.height() : container.scrollHeight) + (includeScrollBarSize && this.isHorizontalScrollBarVisible() ? scrollBarSize : (isRoot ? -1 : 0));
				},
				getLeftPos : function() {
					return (isRoot ? this.getScrollLeft() : this.getOffset().left);
				},
				getTopPos : function() {
					return (isRoot ? this.getScrollTop() : this.getOffset().top);
				},
				getRightPos : function() {
					return this.getLeftPos() + this.getWidth(true);
				},
				getBottomPos : function() {
					return this.getTopPos() + this.getHeight(true);
				},
				isVerticalScrollBarVisible : function() {
					return (this.getHeight(false) < this.getScrollHeight(false));
				},
				isHorizontalScrollBarVisible : function() {
					return (this.getWidth(false) < this.getScrollWidth(false));
				},
				isVerticalScrollBarScrolling : function() {
					if (!this.isVerticalScrollBarVisible()) {
						return false;
					}
					return (containerScrollHistory != null && containerScrollHistory.scrollTop != this.getScrollTop());
				},
				isHorizontalScrollBarScrolling : function() {
					if (!this.isHorizontalScrollBarVisible()) {
						return false;
					}
					return (containerScrollHistory != null && containerScrollHistory.scrollLeft != this.getScrollLeft());
				},
				isScrollUp : function() {
					if (!this.isVerticalScrollBarVisible()) {
						return false;
					}
					return (containerScrollHistory != null && containerScrollHistory.scrollTop > this.getScrollTop());
				},
				isScrollDown : function() {
					if (!this.isVerticalScrollBarVisible()) {
						return false;
					}
					return (containerScrollHistory != null && containerScrollHistory.scrollTop < this.getScrollTop());
				},
				isScrollLeft : function() {
					if (!this.isHorizontalScrollBarVisible()) {
						return false;
					}
					return (containerScrollHistory != null && containerScrollHistory.scrollLeft > this.getScrollLeft());
				},
				isScrollRight : function() {
					if (!this.isHorizontalScrollBarVisible()) {
						return false;
					}
					return (containerScrollHistory != null && containerScrollHistory.scrollLeft < this.getScrollLeft());
				},
				isScrollToTop : function() {
					if (!this.isVerticalScrollBarVisible()) {
						return false;
					}
					return (this.getScrollTop() <= 0);
				},
				isScrollToBottom : function() {
					if (!this.isVerticalScrollBarVisible()) {
						return false;
					}
					return (this.getScrollTop() + this.getHeight(false) >= this.getScrollHeight(true));
				},
				isScrollToLeftmost : function() {
					if (!this.isHorizontalScrollBarVisible()) {
						return false;
					}
					return (this.getScrollLeft() <= 0);
				},
				isScrollToRightmost : function() {
					if (!this.isHorizontalScrollBarVisible()) {
						return false;
					}
					return (this.getScrollLeft() + this.getWidth(false) >= this.getScrollWidth(true));
				}
			};
		},

		/**
		 * Public : Get container's cached lazy items
		 * 
		 * @param selector
		 *            String
		 * @return jQuery
		 */
		getLazyScrollLoadingCachedLazyItems : function(selector) {
			return this.pushStack($.map(this, function(container) {
				var $container = $(container);
				var options = $container.getLazyScrollLoadingOptions();
				var $lazyItems = $container.data("items." + PLUGIN_NAMESPACE);
				if (options != null && options.lazyItemSelector != null && $lazyItems == null) {
					/* cache lazy items if necessary */
					$lazyItems = $(options.lazyItemSelector, (isRootContainer(container) ? undefined : $container));
					$container.data("items." + PLUGIN_NAMESPACE, $lazyItems);
				}
				if ($lazyItems != null && selector != null) {
					$lazyItems = $lazyItems.filter(selector);
				}
				return ($lazyItems != null ? $lazyItems.get() : null);
			}));
		},

		/**
		 * Public : Clear container's cached lazy items
		 * 
		 * @return jQuery
		 */
		clearLazyScrollLoadingCachedLazyItems : function() {
			return this.removeData("items." + PLUGIN_NAMESPACE);
		},

		/**
		 * Public : Destroy LazyScrollLoading
		 * 
		 * @return jQuery
		 */
		destroyLazyScrollLoading : function() {
			/* yield event handler */
			return this.each(function() {
				var $container = $(this);
				/* reset event handler */
				$container.getLazyScrollLoadingViewport().getScrollBindTarget().unbind("scroll." + PLUGIN_NAMESPACE);
				/* clear cache */
				$container.getLazyScrollLoadingCachedLazyItems().removeData("isLoaded." + PLUGIN_NAMESPACE);
				$container.clearLazyScrollLoadingCachedLazyItems().clearLazyScrollLoadingScrollHistory().removeData("options." + PLUGIN_NAMESPACE);
			});
		},

		/**
		 * Public : Is lazy item loaded
		 * 
		 * @return boolean
		 */
		isLazyScrollLoadingLazyItemLoaded : function() {
			return this.data("isLoaded." + PLUGIN_NAMESPACE);
		},

		/**
		 * Public : Is lazy item loading
		 * 
		 * @return boolean
		 */
		isLazyScrollLoadingLazyItemLoading : function() {
			return this.data("isLoading." + PLUGIN_NAMESPACE);
		},

		/**
		 * Public : Is lazy item visible
		 * 
		 * @param $container
		 *            jQuery
		 * @return boolean
		 */
		isLazyScrollLoadingLazyItemVisible : function($container) {
			var lazyItemViewport = this.getLazyScrollLoadingViewport();
			var containerViewport = $container.getLazyScrollLoadingViewport();
			/* calculate isVisible by position */
			return (lazyItemViewport.getBottomPos() > containerViewport.getTopPos() && lazyItemViewport.getLeftPos() < containerViewport.getRightPos() && lazyItemViewport.getTopPos() < containerViewport.getBottomPos() && lazyItemViewport.getRightPos() > containerViewport.getLeftPos());
		}
	});

	/**
	 * Private : Is Root Container
	 */
	function isRootContainer(container) {
		return (container == window || container == document || container == document.body);
	}

	/**
	 * Private : Initialize LazyScrollLoading
	 */
	function initializeLazyScrollLoading($container, options) {
		var containerViewport = $container.getLazyScrollLoadingViewport();
		var $scrollBindTarget = containerViewport.getScrollBindTarget();
		/* starting */
		var isTimerOn = false;
		var timer = null;
		$scrollBindTarget.bind("scroll." + PLUGIN_NAMESPACE, function(e) {
			if (options.delay <= 0) {
				fireOnScrollEvent(e, $container, options);
			} else if (!isTimerOn) {
				isTimerOn = true;
				if (timer != null) {
					clearTimeout(timer);
				}
				timer = setTimeout(function() {
					fireOnScrollEvent(e, $container, options);
					/* clear timer */
					clearTimeout(timer);
					isTimerOn = false;
				}, options.delay);
			}
		});
		/* on first window loaded, for visible element only */
		/* IE version < 9 would not be triggered the onscroll event */
		if ((containerViewport.getScrollTop() <= 0 && containerViewport.getScrollLeft() <= 0) || (isIE && IEVersion < 9)) {
			$scrollBindTarget.trigger("scroll." + PLUGIN_NAMESPACE);
		}
	}

	/**
	 * Private : Fire OnScroll Event
	 */
	function fireOnScrollEvent(e, $container, options) {
		var $lazyItems = $container.getLazyScrollLoadingCachedLazyItems();
		var lazyItemVisibleArray = [];
		var lazyItemFirstVisibleArray = [];
		var lazyItemInvisibleArray = [];
		if (options.lazyItemSelector != null) {
			if (options.isDefaultLazyImageMode || options.onLazyItemFirstVisible != null || options.onLazyItemVisible != null || options.onLazyItemInvisible != null) {
				$lazyItems.each(function() {
					var $lazyItem = $(this);
					/* is lazy item visible */
					if ($lazyItem.isLazyScrollLoadingLazyItemVisible($container)) {
						$lazyItem.data("isLoading." + PLUGIN_NAMESPACE, true);
						lazyItemVisibleArray.push(this);
						if (!$lazyItem.isLazyScrollLoadingLazyItemLoaded()) {
							$lazyItem.data("isLoaded." + PLUGIN_NAMESPACE, true);
							lazyItemFirstVisibleArray.push(this);
						}
					} else if ($lazyItem.isLazyScrollLoadingLazyItemLoading()) {
						$lazyItem.removeData("isLoading." + PLUGIN_NAMESPACE);
						lazyItemInvisibleArray.push(this);
					}
				});
			}
			/* lazy image mode */
			if (options.isDefaultLazyImageMode) {
				for (var i = 0, lazyItemFirstVisibleArraySize = lazyItemFirstVisibleArray.length; i < lazyItemFirstVisibleArraySize; i++) {
					var lazyImageItem = lazyItemFirstVisibleArray[i];
					lazyImageItem.src = lazyImageItem.getAttribute(PLUGIN_LAZYIMAGE_ATTR);
				}
			}
		}
		triggerCallbackFunctions(e, $container, options, $lazyItems, lazyItemVisibleArray, lazyItemFirstVisibleArray, lazyItemInvisibleArray);
	}

	/**
	 * Private : Trigger Customized OnScroll Event
	 */
	function triggerCallbackFunctions(e, $container, options, $lazyItems, lazyItemVisibleArray, lazyItemFirstVisibleArray, lazyItemInvisibleArray) {
		var container = $container[0];
		if (options.onScroll != null) {
			options.onScroll.apply(container, [ e ]);
		}
		/* trigger callback */
		if (options.onScrollVertically != null || options.onScrollUp != null || options.onScrollDown != null || options.onScrollToTop != null || options.onScrollToBottom != null || options.onScrollHorizontally != null || options.onScrollLeft != null || options.onScrollRight != null
				|| options.onScrollToLeftmost != null || options.onScrollToRightmost != null) {
			var containerViewport = $container.getLazyScrollLoadingViewport();
			var scrollTop = containerViewport.getScrollTop();
			var scrollLeft = containerViewport.getScrollLeft();
			/* keep the old scrollTop and scrollLeft */
			var newScrollHistory = {
				scrollTop : scrollTop,
				scrollLeft : scrollLeft
			};
			var defaultApplyParameter = [ e, $lazyItems ];
			if (containerViewport.isVerticalScrollBarScrolling()) {
				if (options.onScrollVertically != null) {
					options.onScrollVertically.apply(container, defaultApplyParameter);
				}
				if (options.onScrollUp != null && containerViewport.isScrollUp()) {
					options.onScrollUp.apply(container, defaultApplyParameter);
				}
				if (options.onScrollDown != null && containerViewport.isScrollDown()) {
					options.onScrollDown.apply(container, defaultApplyParameter);
				}
				if (options.onScrollToTop != null && containerViewport.isScrollToTop()) {
					options.onScrollToTop.apply(container, defaultApplyParameter);
				}
				if (options.onScrollToBottom != null && containerViewport.isScrollToBottom()) {
					options.onScrollToBottom.apply(container, defaultApplyParameter);
				}
			}
			if (containerViewport.isHorizontalScrollBarScrolling()) {
				if (options.onScrollHorizontally != null) {
					options.onScrollHorizontally.apply(container, defaultApplyParameter);
				}
				if (options.onScrollLeft != null && containerViewport.isScrollLeft()) {
					options.onScrollLeft.apply(container, defaultApplyParameter);
				}
				if (options.onScrollRight != null && containerViewport.isScrollRight()) {
					options.onScrollRight.apply(container, defaultApplyParameter);
				}
				if (options.onScrollToLeftmost != null && containerViewport.isScrollToLeftmost()) {
					options.onScrollToLeftmost.apply(container, defaultApplyParameter);
				}
				if (options.onScrollToRightmost != null && containerViewport.isScrollToRightmost()) {
					options.onScrollToRightmost.apply(container, defaultApplyParameter);
				}
			}
			/* reset the scrollbar after event triggered */
			$container.scrollTop(scrollTop);
			$container.scrollLeft(scrollLeft);
			/* reset history */
			$container.data("scrollHistory." + PLUGIN_NAMESPACE, newScrollHistory);
		}
		if (options.onLazyItemFirstVisible != null && lazyItemFirstVisibleArray.length > 0) {
			options.onLazyItemFirstVisible.apply(container, [ e, $lazyItems, $container.pushStack(lazyItemFirstVisibleArray) ]);
		}
		if (options.onLazyItemVisible != null && lazyItemVisibleArray.length > 0) {
			options.onLazyItemVisible.apply(container, [ e, $lazyItems, $container.pushStack(lazyItemVisibleArray) ]);
		}
		if (options.onLazyItemInvisible != null && lazyItemInvisibleArray.length > 0) {
			options.onLazyItemInvisible.apply(container, [ e, $lazyItems, $container.pushStack(lazyItemInvisibleArray) ]);
		}
	}

})(jQuery);