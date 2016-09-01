+function ($) { "use strict";

    var SEDMultiLevelBox = function(element, options) {

        this.$element = $(element);

        this.options  = $.extend({}, {
            // These are the defaults.
            titleBar: "",
            innerContainer : "",

        }, options);

        if(this.options.innerContainer.length == 0)
            return ;

        this._render();

    };

    SEDMultiLevelBox.prototype = {

        _activeTransition : function( ){
            this.$element.find("[data-multi-level-box=true]").removeClass("level-box-transition-none");
            this.options.titleBar.find(".multi-level-box-title").removeClass("level-box-transition-none");
        },

        _render : function(){
            this._PageBoxItems();
            this._PageBoxItems2();
        },

        _PageBoxItems : function(){

            var self = this ,
                items   = self.$element.find("[data-multi-level-box=true]"),
                page_id       = self.options.innerContainer.attr("id") ,
                title	        = self.options.innerContainer.data("title"),
                title_html    = ' <div class="multi-level-box-title first multi-level-box-current" data-self-level-box="' + page_id + '"><span class="ui-dialog-title">' + title + '</span> <button class="close-page-box ui-button ui-dialog-titlebar-close"><span class="icon-delete"></span><span class="ui-button-text">close</span></button><div> ';

                items.addClass("multi-level-box");
                self.options.innerContainer.attr("data-multi-level-box","true");
                self.options.innerContainer.attr("data-level","0");
                self.options.innerContainer.addClass("multi-level-box-current multi-level-box");
                self.options.titleBar.append(title_html);
                self.options.titleBar.find( ".ui-dialog-title:first" ).css( "visibility", "hidden" );
                self.options.titleBar.find( ".ui-dialog-titlebar-close:first" ).css( "visibility", "hidden" );


            items.each( function( i ) {
                var item	    = $(this),
                page_id       = item.attr("id") ,
                parent_id     = item.parentsUntil(self.$element, "[data-multi-level-box=true]:first" ).attr("id"),
                number_level	= item.parentsUntil(self.$element, "[data-multi-level-box=true]").length,
                title	        = item.data("title"),
                title_html    = ' <div class="multi-level-box-title  multi-level-box-next" data-self-level-box="' + page_id + '" ><span class="icon-close-level-box"><i class="icon-chevron-left"></i></span><span class="ui-dialog-title">' + title + '</span> <button class="close-page-box ui-button ui-dialog-titlebar-close"><span class="icon-delete"></span><span class="ui-button-text">close</span></button><div> ';


                item.data("backLevelBoxId", parent_id );

                if(_.isUndefined( item.data("level") ))
                    item.data("level", number_level );

                item.addClass("multi-level-box-next");
                self.options.titleBar.append(title_html);

            });

        },

        _pageBoxNext : function(el){
            var self = this;
            self._activeTransition();

            var dataRelated  = $(el).data("relatedLevelBox"),
               page_current = $(el).parentsUntil(self.$element,"[data-multi-level-box=true]:first"),
               selfLevelBox  = page_current.attr("id"),
               page_next    = self.$element.find("#" + dataRelated);

            page_current.removeClass("multi-level-box-current");
            page_current.addClass("multi-level-box-prev");

            page_next.removeClass("multi-level-box-next");
            page_next.addClass("multi-level-box-current");


            var nextTitle  =  self.options.titleBar.find('[data-self-level-box='+ dataRelated +']'),
                selfTitle  =  self.options.titleBar.find('[data-self-level-box='+ selfLevelBox +']');

            selfTitle.removeClass("multi-level-box-current");
            selfTitle.addClass("multi-level-box-prev");

            nextTitle.removeClass("multi-level-box-next");
            nextTitle.addClass("multi-level-box-current");

            page_current.one("webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend", function() {

                if($(this).data("level") == page_next.data("level")) {

                   $(this).addClass("level-box-transition-none");
                   selfTitle.addClass("level-box-transition-none");

                   $(this).removeClass("multi-level-box-prev");
                   $(this).addClass("multi-level-box-next");

                   selfTitle.removeClass("multi-level-box-prev");
                   selfTitle.addClass("multi-level-box-next");

                   $(this).removeClass("level-box-transition-none");
                   selfTitle.removeClass("level-box-transition-none");

                }

            });

        },

        _PageBoxItems2 : function(){
            var self = this;
            self.$element.append( self.$element.find( "[data-multi-level-box=true]" ) );
            var related       = self.$element.find("[data-related-level-box]"),
                backPage      = self.options.titleBar.find("[data-self-level-box] .icon-close-level-box"),
                //backPageTitle = self.options.titleBar.find("[data-self-level-box]"),
                item          = self.$element.find("[data-multi-level-box=true]");

            related.filter("form").submit(function(e){
                self._pageBoxNext( this );
            });
            related.not("form").click(function(e){
                self._pageBoxNext( this );
            });

            backPage.click(function(e){

                self._activeTransition();

                var dataSelf  = $(this).parent("[data-self-level-box]").data("selfLevelBox"),
                    page_current = self.$element.find("#" + dataSelf),
                    backPageBox  = $("#" + dataSelf).data("backLevelBoxId"),
                    page_prev    = $( "#" + backPageBox );

                if(page_current.data("level") !== 0 ){
                    page_current.removeClass("multi-level-box-current");
                    page_current.addClass("multi-level-box-next");
                }

                page_prev.removeClass("multi-level-box-prev");
                page_prev.addClass("multi-level-box-current");

                var backTitle  =  self.options.titleBar.find('[data-self-level-box='+ backPageBox +']'),
                    selfTitle  =  self.options.titleBar.find('[data-self-level-box='+ dataSelf +']');

                if(page_current.data("level") !== 0 ){
                    selfTitle.removeClass("multi-level-box-current");
                    selfTitle.addClass("multi-level-box-next");
                }

                backTitle.removeClass("multi-level-box-prev");
                backTitle.addClass("multi-level-box-current");

            });


        },

        _reset : function(){

            var self = this ,
                resetItems   = self.$element.find("[data-multi-level-box=true]"),
                resetTitle   = self.options.titleBar.find(".multi-level-box-title");
                       // alert('resetItems------ : ' , resetItems);

            resetItems.addClass("level-box-transition-none");
            resetTitle.addClass("level-box-transition-none");

            resetItems.each(function( i ) {
                if($(this).data("level") == 0  ){

                     //alert('le 0');

                 if($(this).hasClass("multi-level-box-prev")){
                       //alert('le pr');
                      $(this).removeClass("multi-level-box-prev");
                      $(this).addClass("multi-level-box-current");
                  }

                }else{

                  if($(this).hasClass("multi-level-box-current")){

                      $(this).removeClass("multi-level-box-current");
                      $(this).addClass("multi-level-box-next");

                  }
                  if($(this).hasClass("multi-level-box-prev")){
                                     // alert("test");
                      $(this).removeClass("multi-level-box-prev");
                      $(this).addClass("multi-level-box-next");
                  }

                }
            });
            resetTitle.each(function( i ) {
                var selfLevelBox       = $(this).data("selfLevelBox"),
                    level_page_current = $("#" + selfLevelBox).data("level");

                if(level_page_current == 0  ){

                  if($(this).hasClass("multi-level-box-prev")){
                      // alert('le 0 t');
                      $(this).removeClass("multi-level-box-prev");
                      $(this).addClass("multi-level-box-current");
                  }

                }else{

                  if($(this).hasClass("multi-level-box-current")){

                      $(this).removeClass("multi-level-box-current");
                      $(this).addClass("multi-level-box-next");

                  }

                  if($(this).hasClass("multi-level-box-prev")){
                        //alert("test1");
                      $(this).removeClass("multi-level-box-prev");
                      $(this).addClass("multi-level-box-next");
                  }
                }
            });

        } ,

        //for call from contextmenu
        _callDirectlyLevelBox : function( relatedLevelBox , transition ){

            //transition = _.isUndefined( transition ) ? false : transition ;

            //if( !transition ){
                this.$element.find("[data-multi-level-box=true]").addClass("level-box-transition-none");
                this.options.titleBar.find(".multi-level-box-title").addClass("level-box-transition-none");
            //}else
                //this._activeTransition();


            var self = this ,
                dataRelated  = relatedLevelBox,
                page_current = $("#" + dataRelated),
                backPageBox  = page_current.data("backLevelBoxId"),
                page_prev    = $( "#" + backPageBox );

            if(page_current.data("level") !== 0 ){

                page_current.removeClass("multi-level-box-next");
                page_current.addClass("multi-level-box-current");
            }

            var selfTitle  =  self.options.titleBar.find('[data-self-level-box='+ dataRelated +']'),
                backTitle  =  self.options.titleBar.find('[data-self-level-box='+ backPageBox +']');

            if(page_current.data("level") !== 0 ){
                selfTitle.removeClass("multi-level-box-next");
                selfTitle.addClass("multi-level-box-current");
            }

            while(backPageBox){

                if(page_prev.data("level") == 0 ){

                    page_prev.removeClass("multi-level-box-current");
                    page_prev.addClass("multi-level-box-prev");

                    backTitle.removeClass("multi-level-box-current");
                    backTitle.addClass("multi-level-box-prev");
                }else{
                    page_prev.removeClass("multi-level-box-next");
                    page_prev.addClass("multi-level-box-prev");

                    backTitle.removeClass("multi-level-box-next");
                    backTitle.addClass("multi-level-box-prev");
                }

                var backPageBox = page_prev.data("backLevelBoxId"),
                    page_prev   = $( "#" + backPageBox ),
                    backTitle   = self.options.titleBar.find('[data-self-level-box='+ backPageBox +']');
            }


        }
    };


    $.fn.multiLevelBoxPlugin = function (option) {
      return this.each(function () {
        var $this   = $(this)
        var data    = $this.data('sed.multiLevelBoxPlugin')
        var options = typeof option == 'object' && option

        if (!data) $this.data('sed.multiLevelBoxPlugin', (data = new SEDMultiLevelBox(this, options)))
      })
    };

}(jQuery);