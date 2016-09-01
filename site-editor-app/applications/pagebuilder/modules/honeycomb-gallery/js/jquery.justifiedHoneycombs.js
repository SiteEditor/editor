(function ($) {

    $.fn.justifiedHoneycombs = function(options) {
        // Establish our default settings
        var settings = $.extend({
            honeycombWidth:       110,
            margin:             5,
            border:             0,
            resizeDelay:        500,
            vertical:           true,
            debug:              false,
        }, options);

        var DEBUG = settings.debug;

        function initialiseVertical(element) {
            var count               = 0;
            var $item               = $(element).find('.item');
            var itemDiagonal        = Math.sqrt(Math.pow(settings.honeycombWidth, 2) * 2);
            var itemWidth           = settings.honeycombWidth;
            var itemHeight          = ( Math.sqrt(3) * itemWidth ) / 1.5;
            var itemMargin          = settings.margin;
            var itemCorner          = (itemHeight / 2) / 2;
            var currTop             = 0;
            var currLeft            = 0;                                  // - itemMargin

            var rowCapacity         = Math.floor( ( $item.parent().width() ) / (itemWidth + itemMargin));

            fixedZeroCapacity();

            var firstRowCapacity    = rowCapacity;
            var rowCapacityFlag     = 0;
            var currColumn          = 1;
            var currRow             = 1;
            var parentHeight        = itemDiagonal;
            var imageUrl;

            function fixedZeroCapacity(){
                if (rowCapacity == 0){
                    rowCapacity  = 1;
                    itemWidth    = $item.parent().width();
                    itemHeight   = ( Math.sqrt(3) * itemWidth ) / 1.5;
                    itemCorner   = (itemHeight / 2) / 2;
                    itemDiagonal = Math.sqrt(Math.pow(itemWidth, 2) * 2);
                }else if( itemWidth !=  settings.honeycombWidth){

                    itemWidth    = Math.sqrt(Math.pow(settings.honeycombWidth, 2) * 2);
                    itemHeight   = ( Math.sqrt(3) * itemWidth ) / 1.5;
                    itemCorner   = (itemHeight / 2) / 2;
                    itemDiagonal = Math.sqrt(Math.pow(itemWidth, 2) * 2);
                    rowCapacity         = Math.floor( ( $item.parent().width() ) / (itemWidth + itemMargin));
                }
            }

            /**
             * positionin items
             */
            function positionVertically() {
                $item.parent().width(((itemWidth + itemMargin) * rowCapacity) - itemMargin);

                $item.each(function(e) {
                    count++;
                    if (DEBUG) {
                        $(this).find('.hex-inner').addClass('debug').html('<span>' + count + '</span>');
                        $(this).css('display', 'table');
                    }

                    imageUrl = $(this).find('img').attr('src');

                    $(this).css({
                        'left':                 currLeft,
                        'top':                  currTop,
                        'width':                itemWidth,
                        'height':               itemHeight,
                        'border-width':         settings.border,
                    }).find('.hex-inner').css({
                        //'width':                itemDiagonal,
                        //'height':               itemDiagonal,
                        //'margin-top':           -itemCorner,
                        //'margin-left':          -itemCorner,
                        'background-image':     'url(' + imageUrl + ')',
                    });

                    $(this).find('img').css('opacity', '0');

                    //console.log(rowCapacity);
                    if (firstRowCapacity == 1) {
                        currLeft += 0;
                        currTop += itemHeight + itemMargin;
                        currRow++;
                        return;
                    } else if (++currColumn <= rowCapacity) {
                        currLeft += itemWidth  + itemMargin;
                    } else {
                        currColumn = 1;
                        currTop += (itemHeight - itemCorner) + itemMargin;

                        if ((currRow % 2) == 0) {  // ===================== Even Row
                            //DEBUG// console.log('row: ' + currRow + ' is Even.');
                            currLeft = 0;
                            rowCapacity++;
                        } else { // ======================================= Odd Row
                            //DEBUG// console.log('row: ' + currRow + ' is Odd.');
                            currLeft = (itemWidth / 2) + (itemMargin / 2);
                            rowCapacity--;
                        }

                        if (DEBUG)
                            $(this).find('.hex-inner').html('<span>row: ' + currRow + '</span>');

                        currRow++;
                    };
                });

                if (firstRowCapacity == 1) {
                    parentHeight = (currRow - 1) * (itemHeight + itemMargin);
                } else if ( currColumn == 1 && count == $item.size() && firstRowCapacity != 1) {
                    parentHeight = ((currRow - 1) * ((itemCorner * 3) + itemMargin)) + itemCorner;
                } else {
                    parentHeight = (currRow * ((itemCorner * 3) + itemMargin)) + itemCorner;
                }
                //parentHeight = ((currRow + 1) * (itemDiagonal / 2) )+ ((currRow - 1) * (itemMargin));
                //if ((currRow % 2) == 0) {  // ===================== Even Row
                //    parentHeight = currRow * ((itemDiagonal / 2) + (itemMargin * 2)) + (itemCorner * 2) - (itemCorner / 2);
                //} else { // ======================================= Odd Row
                //    parentHeight = currRow * ((itemDiagonal / 2) + (itemMargin * 2)) + (itemCorner * 2) - (itemCorner / 2);
                //}

                if (DEBUG){
                    console.log('resizeDelay: ' + settings.resizeDelay);
                    console.log('itemCorner: ' + itemCorner);
                    console.log('itemDiagonal: ' + itemDiagonal);
                    console.log('currRow: ' + currRow);
                    console.log('currColumn: ' + currColumn);
                    console.log('count: ' + count);
                    console.log('rowCapacity: ' + rowCapacity);
                    console.log('firstRowCapacity: ' + firstRowCapacity);
                    console.log('$item.size()' + $item.size());
                    console.log('====================================');
                }

                $item.parent().height(parentHeight);
            }

            var refreshVHoneycombs = function() {
                $item.parent().width('100%');
                count           = 0;
                currTop             = 0;
                currLeft            = 0;
                itemDiagonal        = Math.sqrt(Math.pow(settings.honeycombWidth, 2) * 2);
                itemWidth           = settings.honeycombWidth;
                itemHeight          = ( Math.sqrt(3) * itemWidth ) / 1.5;
                itemCorner          = (itemHeight / 2) / 2;
                rowCapacity         = Math.floor($item.parent().width() / (itemWidth + itemMargin));
                fixedZeroCapacity();

                firstRowCapacity    = rowCapacity;
                currColumn          = 1;
                currRow             = 1;
                parentHeight        = itemDiagonal;
                imageUrl;
                positionVertically();
            };

            var lazyResize = _.debounce(function(){     //alert("test");
                refreshVHoneycombs();
            }, 300);

           // window resize
            $(window).resize(function() {
                lazyResize();
            });


            /*
            @Site Editor pakage
            Edit By SiteEditor
            for resolve change image
            */
            var images = $(element).find("img");
            images.on( "sed.changeImgSrc", function( event , newSrc ){
                refreshVHoneycombs();
            });

            /*
            @Site Editor pakage
            Edit By SiteEditor
            for column resize
            */                                       //
            $(element).parent().on("sed.moduleResizing" , function(){
                lazyResize();
            });

            /*
            @Site Editor pakage
            Edit By SiteEditor
            for module sortable(darg & drop)
            */                                          //sed.moduleResizeStop
            $(element).parent().on("sed.moduleSortableStop" , function(){
                refreshVHoneycombs();
            });

            positionVertically();
        } // initialiseVertical()


        function initialiseHorizontal(element) {
            var count               = 0;
            var $item               = $(element).find('.item');
            var itemDiagonal        = Math.sqrt(Math.pow(settings.honeycombWidth, 2) * 2);
            var itemWidth           = settings.honeycombWidth;
            var itemHeight          = ( Math.sqrt(3) * itemWidth ) / 2;
            var itemMargin          = settings.margin;
            var itemCorner          = (itemWidth / 2) / 2;
            var currTop             = 0;
            var currLeft            = 0;
            var rowCapacity         = Math.floor($item.parent().width() / ((itemWidth + itemMargin) + (itemWidth / 2)));
            fixedZeroCapacity();

            var firstRowCapacity    = rowCapacity;
            var rowCapacityFlag     = 0;
            var currColumn          = 1;
            var currRow             = 1;
            var parentHeight        = itemDiagonal;
            var imageUrl;

            function fixedZeroCapacity(){
                if (rowCapacity == 0){
                    rowCapacity  = 1;
                    itemWidth    = $item.parent().width();
                    itemHeight   = ( Math.sqrt(3) * itemWidth ) / 2;
                    itemCorner   = (itemWidth / 2) / 2;
                    itemDiagonal = Math.sqrt(Math.pow(settings.honeycombWidth, 2) * 2);
                }else if( itemWidth !=  settings.honeycombWidth){

                    itemWidth    = settings.honeycombWidth;
                    itemHeight   = ( Math.sqrt(3) * itemWidth ) / 2;
                    itemCorner   = (itemWidth / 2) / 2;
                    itemDiagonal = Math.sqrt(Math.pow(settings.honeycombWidth, 2) * 2);
                    rowCapacity  = Math.floor($item.parent().width() / ((itemWidth + itemMargin) + (itemWidth / 2)));
                }
            }

            /**
             * positionin items
             */
            function positionHorizontally() {
                //item.parent().width((itemWidth + itemMargin) * rowCapacity);
                $item.parent().width(
                      ((itemWidth + (itemMargin * 2)) * rowCapacity)
                    + ((itemWidth / 2) * (rowCapacity - 1))
                    - (itemMargin * 2)
                );

                $item.each(function(e) {
                    count++;
                    if (DEBUG) {
                        $(this).find('.hex-inner').addClass('debug').html('<span>' + count + '</span>');
                        $(this).css('display', 'table');
                    }

                    imageUrl = $(this).find('img').attr('src');

                    $(this).css({
                        'left':                 currLeft,
                        'top':                  currTop,
                        'width':                itemWidth,
                        'height':               itemHeight,
                        'border-width':         settings.border,
                    }).find('.hex-inner').css({
                        //'width':                itemDiagonal,
                        //'height':               itemDiagonal,
                        //'margin-top':           -itemCorner,
                        //'margin-left':          -itemCorner,
                        'background-image':     'url(' + imageUrl + ')',
                    });

                    $(this).find('img').css('opacity', '0');

                    if (firstRowCapacity == 1) {
                        currLeft = 0;
                        currTop += itemHeight + itemMargin;
                        currRow++;
                        return;
                    } else if (++currColumn <= rowCapacity) {
                        currLeft += (itemWidth + (itemMargin * 2)) + (itemWidth / 2);
                    } else {
                        currColumn = 1;
                        currTop += (itemHeight / 2) + (itemMargin / 2);

                        if ((currRow % 2) == 0) {  // ===================== Even Row
                            //DEBUG// console.log('row: ' + currRow + ' is Even.');
                            currLeft = 0;
                            rowCapacity++;
                        } else { // ======================================= Odd Row
                            //DEBUG// console.log('row: ' + currRow + ' is Odd.');
                            currLeft = (itemWidth - itemCorner) + (itemMargin);
                            rowCapacity--;
                        }

                        if (DEBUG)
                            $(this).find('.hex-inner').html('<span>row: ' + currRow + '</span>');

                        currRow++;
                    };
                });

                if (firstRowCapacity == 1) {
                    parentHeight = (currRow - 1) * (itemHeight + itemMargin);
                } else if ( currColumn == 1 && count == $item.size() && firstRowCapacity != 1) {
                    parentHeight = ((currRow) * ((itemHeight / 2) + (itemMargin / 2)));
                    //parentHeight += (itemHeight / 2);
                } else {
                    parentHeight = ((currRow + 1) * ((itemHeight / 2) + (itemMargin / 2)));
                }
                //parentHeight = ((currRow + 1) * (itemDiagonal / 2) )+ ((currRow - 1) * (itemMargin));
                //if ((currRow % 2) == 0) {  // ===================== Even Row
                //    parentHeight = currRow * ((itemDiagonal / 2) + (itemMargin * 2)) + (itemCorner * 2) - (itemCorner / 2);
                //} else { // ======================================= Odd Row
                //    parentHeight = currRow * ((itemDiagonal / 2) + (itemMargin * 2)) + (itemCorner * 2) - (itemCorner / 2);
                //}

                if (DEBUG){
                    console.log('resizeDelay: ' + settings.resizeDelay);
                    console.log('itemCorner: ' + itemCorner);
                    console.log('itemDiagonal: ' + itemDiagonal);
                    console.log('currRow: ' + currRow);
                    console.log('currColumn: ' + currColumn);
                    console.log('count: ' + count);
                    console.log('rowCapacity: ' + rowCapacity);
                    console.log('firstRowCapacity: ' + firstRowCapacity);
                    console.log('$item.size()' + $item.size());
                    console.log('====================================');
                }

                $item.parent().height(parentHeight);
            }

	     	var refreshHoneycombs = function() {
                $item.parent().width('100%');
                count               = 0;
                currTop             = 0;
                currLeft            = 0;
                itemDiagonal        = Math.sqrt(Math.pow(settings.honeycombWidth, 2) * 2);
                itemWidth           = settings.honeycombWidth;
                itemHeight          = ( Math.sqrt(3) * itemWidth ) / 2;
                itemCorner          = (itemWidth / 2) / 2;
                rowCapacity         = Math.floor($item.parent().width() / ((itemWidth + itemMargin) + (itemWidth / 2)));
                fixedZeroCapacity();

                firstRowCapacity    = rowCapacity;
                currColumn          = 1;
                currRow             = 1;
                parentHeight        = itemDiagonal;
                imageUrl;
                positionHorizontally()//);  setTimeout(settings.resizeDelay,
	     	};

            var lazyResize = _.debounce(function(){     //alert("test");
                refreshHoneycombs();
            }, 300);

           // window resize
            $(window).resize(function() {
                lazyResize();
            });


            /*
            @Site Editor pakage
            Edit By SiteEditor
            for resolve change image
            */
            var images = $(element).find("img");
            images.on( "sed.changeImgSrc", function( event , newSrc ){
              refreshHoneycombs();
            });

            /*
            @Site Editor pakage
            Edit By SiteEditor
            for column resize
            */                                       //
            $(element).parent().on("sed.moduleResizing" , function(){
                lazyResize();
            });

            /*
            @Site Editor pakage
            Edit By SiteEditor
            for module sortable(darg & drop)
            */                                           // sed.moduleResizeStop
            $(element).parent().on("sed.moduleSortableStop sedAfterRemoveColumns" , function(){
                refreshHoneycombs();
            });

            $(element).parent().parents(".sed-pb-module-container:first").on( "sedChangeModulesLength", function( e , length ){
                refreshHoneycombs();
            });

            $(element).parent().parents(".sed-pb-module-container:first").on( "sedChangedSheetWidth", function(){
                if( $(this).parents(".sed-row-boxed").length > 0 ){
                    refreshHoneycombs();
                }
            });

            $(element).parent().parents(".sed-pb-module-container:first").on( "sedChangedPageLength", function( e , length ){
                if( ($(this).parents(".sed-row-boxed").length == 0 && length == "wide" ) || ($(this).parents(".sed-row-boxed").length == 1 && length == "boxed" ) ){
                    refreshHoneycombs();
                }
            });

            $(element).parent().parents(".sed-pb-module-container:first").on( "sedFirstTimeActivatedTabs", function(){
                refreshHoneycombs();
            });

            $(element).parent().parents(".sed-pb-module-container:first").on( "sedFirstTimeActivatedAccordionTabs", function(){
                refreshHoneycombs();
            });

            $(element).parent().parents(".sed-pb-module-container:first").on( "sedFirstTimeMegamenuActivated", function(){
                refreshHoneycombs();
            });


            positionHorizontally();
        } // initialiseVertical()

        return this.each(function() {
            if (settings.vertical) {
                initialiseVertical(this);
            } else {
                initialiseHorizontal(this);
            }
        });
    }  



}(jQuery));