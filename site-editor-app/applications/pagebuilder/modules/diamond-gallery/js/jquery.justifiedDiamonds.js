(function ($) {

    var DiamondsGallery = function(element, options) {

        this.$element = $(element);

        // Establish our default settings
        this.options = $.extend({
            diamondWidth:       110,
            margin:             5,
            border:             0,
            resizeDelay:        500,
            debug:              false,
        }, options);

        this.DEBUG = this.options.debug;

        this.initialize();

    };

    DiamondsGallery.prototype = {

        initialize : function(){
            this.count               = 0;
            this.$item               = this.$element.find('.item');
            this.itemDiagonal        = Math.sqrt(Math.pow(this.options.diamondWidth, 2) * 2);
            this.itemWidth           = this.options.diamondWidth;
            this.itemHeight          = this.options.diamondWidth;
            this.itemMargin          = this.options.margin;
            this.itemCorner          = (this.itemDiagonal - this.itemWidth) / 2;
            this.currTop             = this.itemCorner;
            this.currLeft            = this.itemCorner;
            this.rowCapacity         = Math.floor((this.$item.parent().width()) / (this.itemDiagonal + this.itemMargin));
            this.firstRowCapacity    = this.rowCapacity;
            this.rowCapacityFlag     = 0;
            this.currColumn          = 1;
            this.currRow             = 1;
            this.parentHeight        = this.itemDiagonal;
            this.imageUrl;

            this.setBGImage();
            this.positionItem();
        },

        fixedLargWidth : function(){
            var parentW = this.$item.parent().width() ,
                diagonal = Math.sqrt(Math.pow(this.options.diamondWidth, 2) * 2) + this.itemMargin;

            if( parentW < diagonal ){

                this.itemDiagonal        = parentW;
                this.itemWidth           = this.itemDiagonal/(Math.sqrt(2));
                this.itemHeight          = this.itemWidth;
                this.itemCorner          = (this.itemDiagonal - this.itemWidth) / 2;
                this.$item.css({
                    'width':                this.itemWidth,
                    'height':               this.itemHeight,
                }).find('.inner').css({
                    'width':                this.itemDiagonal,
                    'height':               this.itemDiagonal,
                    'margin-top':           -this.itemCorner,
                    'margin-left':          -this.itemCorner,
                });
                this.rowCapacity         = 1;
                this.firstRowCapacity    = 1;
                this.currTop         = this.itemCorner;
                this.currLeft        = this.itemCorner;
                this.parentHeight    = this.itemDiagonal;
            }else if( this.itemWidth != this.options.diamondWidth ){
                this.itemDiagonal        = Math.sqrt(Math.pow(this.options.diamondWidth, 2) * 2);
                this.itemWidth           = this.options.diamondWidth;
                this.itemHeight          = this.options.diamondWidth;

                this.itemCorner          = (this.itemDiagonal - this.itemWidth) / 2;
                this.currTop             = this.itemCorner;
                this.currLeft            = this.itemCorner;

                this.rowCapacity         = Math.floor((this.$item.parent().width()) / (this.itemDiagonal + this.itemMargin));
                this.firstRowCapacity    = this.rowCapacity;

                this.rowCapacityFlag     = 0;
                this.currColumn          = 1;
                this.currRow             = 1;
                this.parentHeight        = this.itemDiagonal;

                this.$item.css({
                    'width':                this.itemWidth,
                    'height':               this.itemHeight,
                }).find('.inner').css({
                    'width':                this.itemDiagonal,
                    'height':               this.itemDiagonal,
                    'margin-top':           -this.itemCorner,
                    'margin-left':          -this.itemCorner,
                });

            }
        },

        setBGImage : function(){
            var self = this;

            self.$item.each(function(e) {

                self.imageUrl = $(this).find('img').attr('src');

                $(this).css({
                    //'left':                 self.currLeft,
                    //'top':                  self.currTop,
                    'width':                self.itemWidth,
                    'height':               self.itemHeight,
                    'border-width':         self.options.border,
                }).find('.inner').css({
                    'width':                self.itemDiagonal,
                    'height':               self.itemDiagonal,
                    'margin-top':           -self.itemCorner,
                    'margin-left':          -self.itemCorner,
                    'background-image':     'url(' + self.imageUrl + ')',
                });

                $(this).find('img').css('opacity', '0');
            });
        },

        positionItem : function() {
            var self = this;
            //var startTime = new Date();
            this.fixedLargWidth();

            self.$item.parent().width((self.itemDiagonal + self.itemMargin) * self.rowCapacity);

            self.$item.each(function(e) {
                self.count++;
                if (self.DEBUG) {
                    $(this).addClass('debug').html('<span>' + self.count + '</span>');
                    $(this).css('display', 'table');
                }

                //self.imageUrl = $(this).find('img').attr('src');

                $(this).css({
                    'left':                 self.currLeft,
                    'top':                  self.currTop,
                    //'width':                self.itemWidth,
                    //'height':               self.itemHeight,
                    //'border-width':         self.options.border,
                });/*.find('.inner').css({
                    'width':                self.itemDiagonal,
                    'height':               self.itemDiagonal,
                    'margin-top':           -self.itemCorner,
                    'margin-left':          -self.itemCorner,
                    'background-image':     'url(' + self.imageUrl + ')',
                });

                $(this).find('img').css('opacity', '0');*/


                //console.log(self.currRow);
                if (self.firstRowCapacity == 1) {
                    self.currLeft += 0;
                    self.currTop += self.itemDiagonal + self.itemMargin;
                    self.currRow++;
                    return;
                } else if (++self.currColumn <= self.rowCapacity) {
                    self.currLeft += self.itemDiagonal + self.itemMargin;
                } else {
                    self.currColumn = 1;
                    self.currTop += (self.itemDiagonal / 2) + self.itemMargin;

                    if ((self.currRow % 2) == 0) {  // ===================== Even Row
                        //DEBUG// console.log('row: ' + self.currRow + ' is Even.');
                        self.currLeft = self.itemCorner;
                        self.rowCapacity++;
                    } else { // ======================================= Odd Row
                        //DEBUG// console.log('row: ' + self.currRow + ' is Odd.');
                        self.currLeft = (self.itemDiagonal / 2) + self.itemCorner + (self.itemMargin / 2);
                        self.rowCapacity--;
                    }

                    if (self.DEBUG)
                        $(this).html('<span>row: ' + self.currRow + '</span>');

                    self.currRow++;
                };
            });



            if (self.firstRowCapacity == 1) {
                self.parentHeight = (self.currRow - 1) * (self.itemDiagonal + self.itemMargin);
            } else if ( self.currColumn == 1 && self.count == self.$item.size() && self.firstRowCapacity != 1) {
                self.parentHeight = ((self.currRow) * ((self.itemDiagonal / 2) + self.itemMargin));
            } else {
                self.parentHeight = ((self.currRow + 1) * (self.itemDiagonal / 2)) + ((self.currRow - 1) * (self.itemMargin))
            }

            //self.parentHeight = ((self.currRow + 1) * (self.itemDiagonal / 2) )+ ((self.currRow - 1) * (self.itemMargin));
            //if ((self.currRow % 2) == 0) {  // ===================== Even Row
            //    self.parentHeight = self.currRow * ((self.itemDiagonal / 2) + (self.itemMargin * 2)) + (self.itemCorner * 2) - (self.itemCorner / 2);
            //} else { // ======================================= Odd Row
            //    self.parentHeight = self.currRow * ((self.itemDiagonal / 2) + (self.itemMargin * 2)) + (self.itemCorner * 2) - (self.itemCorner / 2);
            //}

            if (self.DEBUG){
                console.log('resizeDelay: ' + self.options.resizeDelay);
                console.log('self.itemCorner: ' + self.itemCorner);
                console.log('self.itemDiagonal: ' + self.itemDiagonal);
                console.log('self.currRow: ' + self.currRow);
                console.log('self.currColumn: ' + self.currColumn);
                console.log('self.count: ' + self.count);
                console.log('self.rowCapacity: ' + self.rowCapacity);
                console.log('self.firstRowCapacity: ' + self.firstRowCapacity);
                console.log('self.$item.size()' + self.$item.size());
                console.log('====================================');
            }

            self.$item.parent().height(self.parentHeight);
            //console.log( new Date() - startTime );
        },

      	refresh : function() {
            this.$item.parent().width('100%');
            this.count           = 0;
            this.currTop         = this.itemCorner;
            this.currLeft        = this.itemCorner;
            this.rowCapacity     = Math.floor((this.$item.parent().width()) / (this.itemDiagonal + this.itemMargin));
            this.firstRowCapacity    = this.rowCapacity;
            this.rowCapacityFlag = 0;
            this.currColumn      = 1;
            this.currRow         = 1;
            this.parentHeight    = this.itemDiagonal;
         
            //$item.parent().width(rowCapacity * (itemDiagonal + itemMargin));
            this.positionItem();
      	}


    };

    $.fn.justifiedDiamonds = function(option) {

        return this.each(function () {
            var $this   = $(this);
            var data    = $this.data('sed.justifiedDiamonds') ;

            var options = typeof option == 'object' && option;
            if (!data) $this.data('sed.justifiedDiamonds', (data = new DiamondsGallery(this, options)));
            //if (typeof option == 'string') data[option].apply(data , args );
        });

    };



}(jQuery));