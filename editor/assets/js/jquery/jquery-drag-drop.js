(function($) {

    $.fn.sedDraggable = function(options) {
            var options = $.extend({
                handle: null,
                cursor: 'move',
                draggingClass: 'dragging',
                helper: "clone",
                revert: true,
                axis : false,
                revertDuration: 500,
                //this option only for iframes
                dropInSortable: false, // false or selector
                cancelSortable: false,
                items : false ,
                iframeSortable : "parent",
                tolerance : "pointer",
                placeholder: false,    //html template if not false
                scroll: true,
                scrollSensitivity : 20,
                scrollSpeed : 20,
                dragStart :  function() {},
                drag : function() {},
                stop : function() {},
                sortStop : function() {} ,
                out : function() {} ,
                over : function() {}
            }, options);

            var $handle = this;

            if( options.handle != null) {
                $handle = $(options.handle);
            }

            var iframe = {};
            if(options.iframeSortable != "parent"){
                iframe[options.iframeSortable] = {
                  offset : {
                      top : $("#" + options.iframeSortable).offset().top,
                      left : $("#" + options.iframeSortable).offset().left
                  }
                };
                /*
                iframe[options.iframeSortable].proportions = {
                    width : ,
                    height :
                }*/
            }

        	var mouseDown = false, targetElm ,originalPosition,
            margins, cssPosition;
        	var helper = null;
        	var xOffset = 0, yOffset = 0 , offsetParent, sortablesPosition = [],
            helperProportions,positionAbs, dragStarted = false , offset , droppableElm = null , dropItem;

         	function _createHelper(event , options)
        	{
        	    var element = targetElm;
        	    //helper :: support only clone
                if(options.helper != "clone")
                    return ;

        		mouseDown = true;

                //var offset = element.offset();
      			//clone the element

      			helper = element.clone()
                            .removeAttr("id")
                            .offset({
                                left: originalPosition.left,
                                top: originalPosition.top
                             })
                            .appendTo($("body"));



        		var tmpOffset = helper.offset();
        		xOffset = tmpOffset.left - event.pageX;
        		yOffset = tmpOffset.top - event.pageY;
                helper.css("position", "absolute");

                _cacheHelperProportions();

                positionAbs = {
                    left : originalPosition.left,
                    top :  originalPosition.top
                };

        		//$(helper);

        		//var width = element.width();
        		//var height = element.height();

        		//$(helper).width(width);
        		//$(helper).height(height);

        		return false;
        	}

            function _cacheHelperProportions(){
        		helperProportions = {
        			width: helper.outerWidth(),
        			height: helper.outerHeight()
        		};
        	}


        	function onMouseUp (event, el)
        	{
        	    var ui;
        	    //helper = el;
        		//check if object is dropped on droppable element
                //if(options.dropInSortable !== false){
                    if(currentContainer != null){
                        currentItem.attr("current-sortable-item" , "yes");
                        ui = {
                            helper : helper,
                            item : currentItem,
                            direction : direction,
                            placeHolder : placeHolderElement,
                            sortable : currentContainer ,
                            handle : $handle
                        };
                        _clear();
                        options.sortStop(event, ui);

                    }else if(droppableElm !== null){
                        //cancelDrop(event);
                    //}
                //}else{
                        performDrop(event);

            		}else{
                        cancelDrop(event);
            		}
                //}

                _mouseStop();

            }

            function checkExistDropArea( event ){
                $.SiteEditorDroppable.currentIframe = $.SiteEditorDroppable.targetIframe(event.pageX, event.pageY);

        		var m = $.SiteEditorDroppable.droppables[$.SiteEditorDroppable.currentIframe];

        		if (m.length == 0)
        		{
        			return false;
        		}
        		else
        		{
                    var currentDroppableElm = null;
        			for (var i = m.length-1; i >= 0; i--)
        			{
        			   var elm = (m[i].targetWindow == "parent") ? $(m[i].selector) : m[i].targetWindow.jQuery(m[i].selector);
                      
                        elm.each(function(index,elment){

            				if (isLocationInElement($(this), event.pageX, event.pageY, $.SiteEditorDroppable.currentIframe))
            				{
            					currentDroppableElm = $(this);
                                dropItem = m[i];
            					//return false;
            				}
                        });

                        if(currentDroppableElm != null)
                            break;
        			}

        			if (currentDroppableElm === null)
        			    return false
                    else{
                        if(droppableElm !== null && droppableElm.length > 0)
                            droppableElm.removeClass("sed-row-drop-area");
                        droppableElm = currentDroppableElm;
                    }

        		}
            }

        	function cancelDrop (event)
        	{

        		if (helper == null)
        			return;

                if(options.revert === true) {
        			helper.animate(originalPosition, parseInt(options.revertDuration, 10), function() {
        				_clear();
        			});
        		}else{
                  _clear();
        		}


        	}

            function _mouseStop(){
        		mouseDown = false;
        		xOffset = 0;
        		yOffset = 0;
            }

            function _clear(){
        		if (helper == null)
        			return;

                if(placeHolderElement != null)
                    placeHolderElement.remove();

                sortableItems = [] ;
                containers = [];
                currentContainer = null;
                placeHolderElement = null;
                dragStarted = false;

        		helper.remove();
        		helper = null;
            }

        	function performDrop (event)
        	{
                droppableElm.attr("current-droppable-item" , "yes");
   
                var ui = {
                    helper : helper,
                    droppable : droppableElm ,
                    handle : $handle ,
                    offset : getHelperDropOffset()
                };

        		dropItem.options.drop(event, ui);

                droppableElm.removeClass("sed-row-drop-area");
                droppableElm = null;

                if(placeHolderElement != null)
                    placeHolderElement.remove();

                sortableItems = [] ;
                containers = [];
                currentContainer = null;
                placeHolderElement = null;
                dragStarted = false;

        	    helper.remove();
        		helper = null;
        	}

            function getHelperDropOffset(){
                var offset = helper.offset() ,
                    targetWindow = $.SiteEditorDroppable.currentIframe || "parent";
        	    if(targetWindow == "parent"){

                    return {
                        left :  offset.left,
                        top  :  offset.top
                    };

        		}else{

                    if(typeof $.SiteEditorDroppable.scope[targetWindow] !== undefined){
                        var offsetTop = $.SiteEditorDroppable.scope[targetWindow].top;
                        var offsetLeft = $.SiteEditorDroppable.scope[targetWindow].left;
                    }else{
                        var offsetTop = $(targetWindow).offset().top;
                        var offsetLeft = $(targetWindow).offset().left;
                    }

                    return {
                		top : offset.top + $("#" + targetWindow).contents().scrollTop() - offsetTop,
                        left : offset.left + $("#" + targetWindow).contents().scrollLeft() - offsetLeft
                    };
        		}
            }

        	function isLocationInElement (element, x, y , targetWindow)
        	{
        	    if(targetWindow == "parent"){

            		var elmOffsetY = element.offset().top - $( window ).scrollTop();
                    var elmOffsetX = element.offset().left - $( window ).scrollLeft();

            		if (x >= elmOffsetX && x <= (elmOffsetX + element.width()) &&
            			y >= elmOffsetY && y <= (elmOffsetY + element.height()))
            			return true;
                    else
                        return false
        		}else{
                    if(typeof $.SiteEditorDroppable.scope[targetWindow] !== undefined){
                        var offsetTop = $.SiteEditorDroppable.scope[targetWindow].top;
                        var offsetLeft = $.SiteEditorDroppable.scope[targetWindow].left;
                    }else{
                        var offsetTop = $("#" + targetWindow).offset().top;
                        var offsetLeft = $("#" + targetWindow).offset().left;
                    }
            		var elmOffsetY = (element.offset().top - $("#" + targetWindow).contents().scrollTop()) + ( offsetTop - $( window ).scrollTop() );
                    var elmOffsetX = (element.offset().left - $("#" + targetWindow).contents().scrollLeft()) + ( offsetLeft - $( window ).scrollLeft() );
                        

            		if (x >= elmOffsetX && x <= (elmOffsetX + element.width()) &&
            			y >= elmOffsetY && y <= (elmOffsetY + element.height()))
            			return true;
                    else
                        return false
        		}
        	}

            function scrollDrag(e , options)
            {
                var target,dif, currentIframe = $.SiteEditorDroppable.currentIframe = $.SiteEditorDroppable.targetIframe(e.pageX, e.pageY)
                ,maxScrollTop , targetOffsetHeight;

                if(currentIframe == "parent"){
                    target = $(document);
                    dif = 0;
                    targetHeight = $(window).height();
                }else{
                    target = $("#" + currentIframe).contents();
                    dif = $.SiteEditorDroppable.scope[currentIframe].top - $( window ).scrollTop();
                    targetHeight = $("#" + currentIframe).height();
                }

                targetOffsetHeight = target.height();
                maxScrollTop = targetOffsetHeight - targetHeight;

                if(maxScrollTop > 0){
                    if(targetHeight - (e.pageY - dif) < options.scrollSensitivity && target.scrollTop() < maxScrollTop) {
                        target.scrollTop(target.scrollTop() + options.scrollSpeed);
                    }else if((e.pageY - dif) < options.scrollSensitivity && target.scrollTop() > 0){
                        target.scrollTop(target.scrollTop() - options.scrollSpeed);
                    }

                    if(target.scrollTop() < 0 ){
                        target.scrollTop(0);
                    }

                    if(target.scrollTop() > maxScrollTop ){
                        target.scrollTop(maxScrollTop);
                    }
                }
            }

            function _getParentOffset(){

          		//Get the offsetParent and cache its position
          		var po = offsetParent.offset();

          		// This is a special case where we need to modify a offset calculated on start, since the following happened:
          		// 1. The position of the helper is absolute, so it's position is calculated based on the next positioned parent
          		// 2. The actual offset parent is a child of the scroll parent, and the scroll parent isn't the document, which means that
          		//    the scroll is included in the initial calculation of the offset of the parent, and never recalculated upon drag
          		/*if(this.cssPosition === "absolute" && this.scrollParent[0] !== document && $.contains(this.scrollParent[0], offsetParent[0])) {
          			po.left += this.scrollParent.scrollLeft();
          			po.top += this.scrollParent.scrollTop();
          		}*/

          		//This needs to be actually done for all browsers, since pageX/pageY includes this information
          		//Ugly IE fix
          		if((offsetParent[0] === document.body) ||
          			(offsetParent[0].tagName && offsetParent[0].tagName.toLowerCase() === "html" && $.ui.ie)) {
          			po = { top: 0, left: 0 };
          		}

          		return {
          			top: po.top + (parseInt(offsetParent.css("borderTopWidth"),10) || 0),
          			left: po.left + (parseInt(offsetParent.css("borderLeftWidth"),10) || 0)
          		};

          	}


            function _getRelativeOffset(){

        		if(cssPosition === "relative") {
        			var p = targetElm.position();
        			return {
        				top: p.top - (parseInt(helper.css("top"),10) || 0) ,
        				left: p.left - (parseInt(helper.css("left"),10) || 0)
        			};
        		} else {
        			return { top: 0, left: 0 };
        		}

        	}

        	function _cacheMargins(){
        		margins = {
        			left: (parseInt(targetElm.css("marginLeft"),10) || 0),
        			top: (parseInt(targetElm.css("marginTop"),10) || 0),
        			right: (parseInt(targetElm.css("marginRight"),10) || 0),
        			bottom: (parseInt(targetElm.css("marginBottom"),10) || 0)
        		};
        	}

        	function _convertToAbs() {

        		return {
        			top: (
        				targetElm.position().top - _getParentOffset().top - _getRelativeOffset().top
        			),
        			left: (
        				targetElm.position().left - _getParentOffset().left - _getRelativeOffset().left
        			)
        		};

        	}

             //start sortable ...
             var sortableItems = [] ,containers = [],currentContainer = null,currentContainerId,currentItem = null,placeHolderElement = null,
             floating = false, lastPositionAbs, direction,_counter;
             function _sortable(event, x, y){
          		//Rearrange
            
                if(currentContainer == null)
                    return ;

                var $itemsC;
                if( options.cancelSortable !== false){
                    if( options.items !== false )
                        $itemsC = currentContainer.children( options.items ).not(options.cancelSortable);
                    else
                        $itemsC = currentContainer.children().not(options.cancelSortable);
                }else{
                    if( options.items !== false )
                        $itemsC = currentContainer.children( options.items );
                    else
                        $itemsC = currentContainer.children();
                }

                if( $itemsC.length == 0){
                    placeHolderElement.appendTo( currentContainer );
                    currentItem = currentContainer;
                    direction = "none";
                    return ;
                }


          		for (i = sortableItems.length - 1; i >= 0; i--) {

          			//Cache variables and intersection, continue if no intersection
          			item = sortableItems[i];
          			//itemElement = item.item[0];
          			intersection = _intersectsWithPointer(item , event);

          			if (!intersection) {
          				continue;
          			}             

          			// Only put the placeholder inside the current Container, skip all
          			// items from other containers. This works because when moving
          			// an item from one container to another the
          			// currentContainer is switched before the placeholder is moved.
          			//
          			// Without this, moving items in "sub-sortables" can cause
          			// the placeholder to jitter beetween the outer and inner container.

          			if (item.instance[0] !== currentContainer[0]) {
          				continue;
          			}

                    /*positionAbs = {
                        top: y + event.pageY - margins.top,
                        left: x + event.pageX - margins.left
                    };*/
                    currentItem = item.item;
      				direction = intersection === 1 ? "down" : "up";
                    _rearrange(event, item);
      				break;
          		}



    			    lastPositionAbs = positionAbs;

             }

        	function _rearrange(event, i, a, hardRefresh) {

        		i.item[0].parentNode.insertBefore(placeHolderElement[0], (direction === "down" ? i.item[0] : i.item[0].nextSibling));

        		//Various things done here to improve the performance:
        		// 1. we create a setTimeout, that calls refreshPositions
        		// 2. on the instance, we have a counter variable, that get's higher after every append
        		// 3. on the local scope, we copy the counter variable, and check in the timeout, if it's still the same
        		// 4. this lets only the last addition to the timeout stack through
        		_counter = _counter ? ++_counter : 1;
        		var counter = _counter;

        		_delay(function() {
        			if(counter === _counter) {
   
        				refreshPositions(!hardRefresh); //Precompute after each DOM insertion, NOT on mousemove
        			}
        		});

        	}

           function _delay( handler, delay ) {
        		function handlerProxy() {
        			return ( typeof handler === "string" ? instance[ handler ] : handler )
        				.apply( instance, arguments );
        		}
        		var instance = this;
        		return setTimeout( handlerProxy, delay || 0 );
        	}

           function _createPlaceholder(){
                var elementHtml,element;
                if(options.placeholder !== false && typeof options.placeholder == "string"){
                    elementHtml = options.placeholder;
                }else{
                    elementHtml = "<div class='sortable-placeholder'></div>";
                }

                element = $( elementHtml )
                .addClass("sed-sortable-placeholder ui-sortable-placeholder");
                placeHolderElement = element;

           }

            function isOverAxis( x, reference, size ) {
            	return ( x > reference ) && ( x < ( reference + size ) );
            }

            function isFloating(item) {
            	return (/left|right/).test(item.css("float")) || (/inline|table-cell/).test(item.css("display"));
            }

        	function _intersectsWithPointer(item) {
        	    var targetWindow = options.iframeSortable;
                if(targetWindow == "parent"){
    			    var l = item.left,
                    t = item.top,
                    isOverElementHeight = ( options.axis === "x" ) || isOverAxis(positionAbs.top + offset.click.top, t, item.height),
            		isOverElementWidth = ( options.axis === "y" ) || isOverAxis(positionAbs.left + offset.click.left, l, item.width);
                }else{
    			    var l = item.left + iframe[targetWindow].offset.left,
                    t = item.top + iframe[targetWindow].offset.top,
                    isOverElementHeight = ( options.axis === "x" ) || isOverAxis(positionAbs.top + $("#" + targetWindow).contents().scrollTop() + offset.click.top, t, item.height),
            		isOverElementWidth = ( options.axis === "y" ) || isOverAxis(positionAbs.left + $("#" + targetWindow).contents().scrollLeft() + offset.click.left, l, item.width);

                }


        		var isOverElement = isOverElementHeight && isOverElementWidth,//_intersectsWith(item),
        		verticalDirection = _getDragVerticalDirection(),
        		horizontalDirection = _getDragHorizontalDirection();

        		if (!isOverElement) {
        			return false;
        		}

        	    return floating ?
        			( ((horizontalDirection && horizontalDirection === "right") || verticalDirection === "down") ? 2 : 1 )
        			: ( verticalDirection && (verticalDirection === "down" ? 2 : 1) );

        	}


        	function _getDragVerticalDirection() {
        		var delta = positionAbs.top - lastPositionAbs.top;
        		return delta !== 0 && (delta > 0 ? "down" : "up");
        	}

        	function _getDragHorizontalDirection() {
        		var delta = positionAbs.left - lastPositionAbs.left;
        		return delta !== 0 && (delta > 0 ? "right" : "left");
        	}

           function refreshPositions(fast){

          		var i, item, p,slen = sortableItems.length, clen = containers.length;

          		for (i = slen - 1; i >= 0; i--){
          			item = sortableItems[i];

          			//We ignore calculating positions of all connected containers when we're not over them
          			if(item.instance !== currentContainer && currentContainer) {
          				continue;
          			}

                    t = item.item;

          			if (!fast) {
          				item.width = t.outerWidth();
          				item.height = t.outerHeight();
          			}

          			p = t.offset();
          			item.left = p.left;
          			item.top = p.top;
          		}

    			for (i = clen - 1; i >= 0; i--){
    				p = containers[i].element.offset();
    				containers[i].containerCache.left = p.left;
    				containers[i].containerCache.top = p.top;
    				containers[i].containerCache.width	= containers[i].element.outerWidth();
    				containers[i].containerCache.height = containers[i].element.outerHeight();
    			}

           }

           function _refreshItems(){

              if(options.iframeSortable == "parent"){

                  $( options.dropInSortable ).each(function(){
                      containers.push({
                          element : $(this),
                          containerCache : {
                              left: 0,
                              top: 0,
                              width: 0,
                              height: 0
                          }
                      });
                      var that = $(this);

                      if( options.items !== false ){

                          $(this).children(options.items).not(options.cancelSortable).each(function(){
                              sortableItems.push({
                                  top : 0,
                                  left : 0,
                                  height : 0,
                                  width : 0,
                                  item : $(this),
                                  instance : that
                              })
                          });

                      }else{

                          $(this).children().not(options.cancelSortable).each(function(){
                              sortableItems.push({
                                  top : 0,
                                  left : 0,
                                  height : 0,
                                  width : 0,
                                  item : $(this),
                                  instance : that
                              })
                          });
                      }
                  });
              }else{
                  var iframe = "#" + options.iframeSortable;

                  $(iframe)[0].contentWindow.jQuery( options.dropInSortable ).each(function(){
                      containers.push({
                          element : $(iframe)[0].contentWindow.jQuery(this),
                          containerCache : {
                              left: 0,
                              top: 0,
                              width: 0,
                              height: 0
                          }
                      });
                      var that = $(iframe)[0].contentWindow.jQuery(this);

                      if( options.items !== false ){

                          $(iframe)[0].contentWindow.jQuery(this).children( options.items ).not(options.cancelSortable).each(function(){
                              sortableItems.push({
                                  top : 0,
                                  left : 0,
                                  height : 0,
                                  width : 0,
                                  item : $(iframe)[0].contentWindow.jQuery(this),
                                  instance : that
                              })
                          });

                      }else{

                          $(iframe)[0].contentWindow.jQuery(this).children().not(options.cancelSortable).each(function(){
                              sortableItems.push({
                                  top : 0,
                                  left : 0,
                                  height : 0,
                                  width : 0,
                                  item : $(iframe)[0].contentWindow.jQuery(this),
                                  instance : that
                              })
                          });

                      }
                  });

              }
           }

           function _refreshSortable(){
               _refreshItems();
               refreshPositions();

           }

           function getCurrentContainer(event){

              var oldContainer = currentContainer ,
                  oldContainerId = currentContainerId;

               var i;
               currentContainer = null;
               currentContainerId = -2;

               if( checkExistDropArea(event) === false ){
                  if(droppableElm !== null){
                      droppableElm.removeClass("sed-row-drop-area");
                      droppableElm = null;
                  }

                  for (i = containers.length - 1; i >= 0; i--){

                     if( containers[i].element.hasClass("sed-sortable-disabled") )
                        continue;

                     if(_intersectsWith(containers[i].containerCache)){

                        currentContainer = containers[i].element; //
                        currentContainerId = i;

                        if(oldContainer !== null && oldContainerId != currentContainerId)
                            options.out( event , oldContainer , options );


                        options.over( event , currentContainer , options );

                        break;
                     }
                  }
               }else{
                    droppableElm.addClass("sed-row-drop-area");
               }


               if(currentContainer == null){
                    placeHolderElement = placeHolderElement.detach();

               }

           }



        	function _intersectsWith(item) {

        		var x1 = positionAbs.left,
                    y1 = positionAbs.top,targetWindow = options.iframeSortable;
                    if(targetWindow == "parent"){
        			    var l = item.left,
                        t = item.top;
                    }else{
        			    var l = item.left + iframe[targetWindow].offset.left,
                        t = item.top + iframe[targetWindow].offset.top;
                        x1 += $("#" + targetWindow).contents().scrollLeft();
                        y1 += $("#" + targetWindow).contents().scrollTop();
                    }
        			var x2 = x1 + helperProportions.width,
        			y2 = y1 + helperProportions.height,
        			r = l + item.width,
        			b = t + item.height,
        			dyClick = offset.click.top,
        			dxClick = offset.click.left,
        			isOverElementHeight = ( options.axis === "x" ) || ( ( y1 + dyClick ) > t && ( y1 + dyClick ) < b ),
        			isOverElementWidth = ( options.axis === "y" ) || ( ( x1 + dxClick ) > l && ( x1 + dxClick ) < r ),
        			isOverElement = isOverElementHeight && isOverElementWidth;

        		//if ( options.tolerance === "pointer" ) {
        			return isOverElement;
        		/*} else {

        			return (l < x1 + (helperProportions.width / 2) && // Right Half
        				x2 - (helperProportions.width / 2) < r && // Left Half
        				t < y1 + (helperProportions.height / 2) && // Bottom Half
        				y2 - (helperProportions.height / 2) < b ); // Top Half

        		}
                */

        	}

           //end sortable

           function _create(e){
              cssPosition = targetElm.css("position");
              _cacheMargins();
              offsetParent = targetElm.offsetParent();

              //The element's absolute position on the page minus margins
              offset = {
                  top: targetElm.offset().top - margins.top,
                  left: targetElm.offset().left - margins.left
              };

              $.extend(offset, {
                  click: { //Where the click happened, relative to the element
                      left: e.pageX - offset.left,
                      top: e.pageY - offset.top
                  }
                  //parent: this._getParentOffset(),
                  //relative: this._getRelativeOffset() //This is a relative to absolute position minus the actual position calculation - only used for relative positioned helper
              });

              originalPosition = {
                  top : offset.top,
                  left : offset.left
              };
              /*
              originalPosition = {
                  top : offset.top -  _getParentOffset().top,
                  left : offset.left - _getParentOffset().left
              };
              */

           }

            var that = this;
            $.fn.sedDraggable.destroy = function() {

                that.unbind('mousedown');
                that.unbind('mousemove');
                that.unbind('mouseup');

            }

            $handle
                .css('cursor', options.cursor)
                .on('mousedown', function(e) {
                    targetElm = $(this);
                    _create(e);

                    if(options.dropInSortable !== false){
                        _refreshSortable();
                        _createPlaceholder();
                        floating = sortableItems.length ? options.axis === "x" || isFloating(sortableItems[0].item) : false;
                    }

                    var x = targetElm.offset().left - e.pageX,
                        y = targetElm.offset().top - e.pageY,
                        z = targetElm.css('z-index');


                    if(options.helper != "original")
                        _createHelper(e , options);

                    helper.css({'z-index': 999, 'bottom': 'auto', 'right': 'auto'});

                    $(document.documentElement)
                        .on('mousemove.sedDraggable', function(e) {
                          if(helper != null){

                            if(dragStarted === false){


                                options.dragStart(e, targetElm ,helper);
                                dragStarted = true;
                            }

                            helper.offset({
                                left: x + e.pageX,
                                top: y + e.pageY
                            });



                            positionAbs = {
                                top: y + e.pageY - margins.top  ,
                                left: x + e.pageX - margins.left
                            };

                     		if (!lastPositionAbs) {
                			    lastPositionAbs = positionAbs;
                		    }

                            options.drag(e, targetElm ,helper);

                             if(options.scroll === true){
                                scrollDrag(e , options);
                             }


                              if(options.dropInSortable !== false){
                                  getCurrentContainer(e);
                                  _sortable(e, x, y);
                              }
                           }
                        })
                        .one('mouseup', function(e) {
                            options.stop(e, targetElm ,helper);

                            onMouseUp( e , $(this) );
                            $(this).off('mousemove.sedDraggable');
                            targetElm.css('z-index', z);
                        });

                    // disable selection
                    e.preventDefault();
                });
            return this;
    };

    $.SiteEditorDroppable = {
        droppables : { "parent": [] },
        currentIframe : "parent",
        scope : {"parent": {top: 0 , left:0}},
        create: function(element) {
            var iframeSelector , $iframe , $iframeId,$scope, options = element.options;

            if(options.iframe !== false){
                $iframeId = options.iframe;
                iframeSelector = "#" + $iframeId;
                $iframe = $(iframeSelector);
                if(typeof $iframe === undefined) return; //test

                element.targetWindow = $(iframeSelector)[0].contentWindow;
                $scope = $iframeId;

                if($.inArray($scope,$.SiteEditorDroppable.scope) == -1)
                    $.SiteEditorDroppable.scope[$scope] = {top : $iframe.offset().top, left: $iframe.offset().left};

                /*$(iframeSelector).load(function(){
                    (options.addClasses && element.targetWindow.jQuery(element.selector).addClass("sed-iframe-droppable"));
                });*/
            }else{
                //this.element = $(selector);
                $scope = "parent";
                element.targetWindow = "parent";
                (options.addClasses && this.element.$(element.selector).addClass("sed-iframe-droppable"));
            }

            if(!$.isArray($.SiteEditorDroppable.droppables[$scope]))
                $.SiteEditorDroppable.droppables[$scope] = [];

            //this.element.offset = this.element.offset();
            $.SiteEditorDroppable.droppables[$scope].push(element);


        },

        destroy : function( selector , scope ){
            var currIdx = -1;

            $.each( $.SiteEditorDroppable.droppables , function( $scope , elements ){
                if( $scope == scope ){
                    $.each( elements , function( index , $element ){
                        if( selector == $element.selector )
                            currIdx = index;
                    });

                    if( currIdx != -1 )
                        elements.splice( currIdx , 1 );

                }
            });

        },

        fullDestroy : function(){
            $.SiteEditorDroppable.scope =  { "parent": [] };
            $.SiteEditorDroppable.droppables =  {"parent": {top: 0 , left:0}};
            $.SiteEditorDroppable.currentIframe = "parent";
        },

        targetIframe: function(x,y) {
            var $iframe = null;
            for(prop in $.SiteEditorDroppable.scope){
                if(prop != "parent"){
                    if($.SiteEditorDroppable.scope[prop].top <= y && $.SiteEditorDroppable.scope[prop].left <= x){
                        $iframe = prop;
                        break;
                    }
                }
            }
            $iframe = ($iframe == null) ? "parent": $iframe;
            return $iframe;
        }

    };

    $.sedDroppable = function(selector ,options) {
        var options = $.extend({
    		accept: "*",
            iframe: false,    // false | iframe id
    		activeClass: false,
    		addClasses: true,
    		hoverClass: false,

    		// callbacks
    		activate: null,
    		deactivate: null,
    		drop: function() {},
        }, options);

        var element = {};
        element.options = options;
        element.selector = selector;
        //(options.addClasses && element.target.addClass("sed-iframe-droppable"));
        $.SiteEditorDroppable.create(element);

    };



//sortable


})(jQuery);


