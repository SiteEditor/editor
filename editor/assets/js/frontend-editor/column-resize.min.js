(function($) {

    $.fn.sedColumnResize = function(options) {
            var options = $.extend({
                cursor: 'w-resize',
                minWidth: 10,
                column: "sed-column-pb", //column class
                unit : "px" , // % || px
                //columnInner : "sed-column-contents-pb",
                isRtl : false ,
                resizeStart :  function() {},
                resize : function() {},
                stop : function() {}
            }, options);

            var $this = $(this), $handle, targetElm, maxWidth;
            _create();

            function _create(){
                $this.css("position","relative");
                $handle =  $('<div class="sed-resizable-handle sed-resizable-w" style="z-index: 90;"><div class="sed-column-resize-helper"></div></div>').appendTo($this);
                $handle.css("position","absolute");

            }

            function _updateMaxWidth(){
                var thPW = $this.parent().width(),
                cols = $this.parent().children("." + options.column).length,
                //totalMinWidth = cols * options.minWidth,
                totalExtarW = 0;
                $this.parent().children("." + options.column).each(function(e){
                    if($(this)[0] !==  $this[0] && $(this)[0] !==  $this.prev()[0]){
                        totalExtarW += $(this).outerWidth(true);
                    }else{
                        totalExtarW += $(this).outerWidth(true) - $(this).width();
                    }

                });
                maxWidth = thPW - totalExtarW - options.minWidth;

            }

            function _getPadding( element ){
                return parseInt( element.css("padding-left") ) + parseInt( element.css("padding-right") )
            }

            function _getBorder( element ){
                return parseInt( element.css("padding-left") ) + parseInt( element.css("padding-right") )
            }

            function _getMargin( element ){
                return parseInt( element.css("margin-left") ) + parseInt( element.css("margin-right") )
            }


            $handle
                .css('cursor', options.cursor)
                .on('mousedown', function(e) {
                    targetElm = $(this);

                    var x = targetElm.offset().left - e.pageX,
                        y = targetElm.offset().top,
                        z = targetElm.css('z-index'),
                        w = targetElm.offset().left,
                        d, pW = targetElm.parent().width(),
                        resizeStarted = false, ppW = targetElm.parent().parent().width(),
                        ppad = _getPadding( targetElm.parent() ) , pbor = _getBorder( targetElm.parent() ) ,
                        pmar = _getMargin( targetElm.parent() );

                    _updateMaxWidth();
                    ////api.log(maxWidth);

                    $(document.documentElement)
                        .on('mousemove.sedColumnResize', function(e) {

                            if(resizeStarted === false){
                                options.resizeStart(e, targetElm.parent() );
                                resizeStarted = true;
                            }

                            targetElm.css({'z-index': 999, 'cursor' :  options.cursor, 'bottom': 'auto', 'right': 'auto'});

                            d = x + e.pageX - w;

                            if( options.isRtl === true ){
                                if(pW + d >= options.minWidth && pW + d <= maxWidth){

                                    /*targetElm.offset({
                                        left: x + e.pageX,
                                        top: y
                                    });*/
                                         // //api.log(maxWidth + "," + d + "," + pW);
                                    //if(pW - d >= options.minWidth && pW - d <= maxWidth){

                                    if(options.unit == "%")                            //+ ppad + pbor + pmar
                                        targetElm.parent().css("width", ( ( ( (pW + d) + ppad + pbor  ) / ppW) * 100 ) + "%");
                                    else
                                        targetElm.parent().width( pW + d + ppad + pbor );

                                    options.resize(e, d, targetElm.parent(), maxWidth , options );
                                }
                            }else{
                                if(pW - d >= options.minWidth && pW - d <= maxWidth){

                                    /*targetElm.offset({
                                        left: x + e.pageX,
                                        top: y
                                    });*/
                                         // //api.log(maxWidth + "," + d + "," + pW);
                                    //if(pW - d >= options.minWidth && pW - d <= maxWidth){

                                    if(options.unit == "%")                            //+ ppad + pbor + pmar
                                        targetElm.parent().css("width", ( ( ( (pW - d) + ppad + pbor  ) / ppW) * 100 ) + "%");
                                    else
                                        targetElm.parent().width( pW - d + ppad + pbor );

                                    options.resize(e, d, targetElm.parent(), maxWidth , options );
                                }
                            }

                        })
                        .one('mouseup', function(e) {
                            options.stop(e , targetElm.parent());

                            //onMouseUp( e , $(this) );
                            $(this).off('mousemove.sedColumnResize');
                            //targetElm.css('z-index', z);
                        });

                    // disable selection
                    e.preventDefault();
                });
            return this;
    };





//sortable


})(jQuery);



