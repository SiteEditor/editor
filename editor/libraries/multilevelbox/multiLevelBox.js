(function ($) {

$.fn.multiLevelBox = function(options) {

    var obj = this,
        o = settings = $.extend({
        // These are the defaults.
        titleBar: "",
        innerContainer : "",

        }, options );


    _PageBoxItems = function(){
      var items   = obj.find("[data-dialog-page-box=true]"),
          page_id       = o.innerContainer.attr("id") ,
          title	        = o.innerContainer.data("title"),
          title_html    = ' <div class="dialog-page-box-title first dialog-page-box-current" data-self-page-box="' + page_id + '"><span class="ui-dialog-title">' + title + '</span> <button class="close-page-box ui-button ui-dialog-titlebar-close"><span class="icon-delete"></span><span class="ui-button-text">close</span></button><div> ';

          items.addClass("dialog-page-box");
          o.innerContainer.attr("data-dialog-page-box","true");
          o.innerContainer.attr("data-level","0");
          o.innerContainer.addClass("dialog-page-box-current dialog-page-box");
          o.titleBar.append(title_html);
          o.titleBar.find( ".ui-dialog-title:first" ).css( "visibility", "hidden" );
          o.titleBar.find( ".ui-dialog-titlebar-close:first" ).css( "visibility", "hidden" );


      items.each( function( i ) {
          var item	    = $(this),
          page_id       = item.attr("id") ,
          parent_id     = item.parentsUntil(obj, "[data-dialog-page-box=true]:first" ).attr("id"),
          number_level	= item.parentsUntil(obj, "[data-dialog-page-box=true]").length,
          title	        = item.data("title"),
          title_html    = ' <div class="dialog-page-box-title  dialog-page-box-next" data-self-page-box="' + page_id + '" ><span class="icon-close-page-box"><i class="icon-chevron-left"></i></span><span class="ui-dialog-title">' + title + '</span> <button class="close-page-box ui-button ui-dialog-titlebar-close"><span class="icon-delete"></span><span class="ui-button-text">close</span></button><div> ';

             // console.log(page_id);
              //console.log(parent_id);
          item.data("backPageBoxId", parent_id );
         // item.attr("data-back-page-box-id", parent_id );
          item.data("level", number_level );
          item.addClass("dialog-page-box-next");
          o.titleBar.append(title_html);

      });

    }
    _PageBoxItems();



    _PageBoxItems2 = function(){
          obj.append( $( "[data-dialog-page-box=true]" ) );
          var related       = obj.find("[data-related-page-box]"),
              backPage      = o.titleBar.find("[data-self-page-box] .icon-close-page-box"),
              //backPageTitle = o.titleBar.find("[data-self-page-box]"),
              item          = obj.find("[data-dialog-page-box=true]");
               //console.log(related);
        related.click(function(e){

         var dataRelated  = $(this).data("relatedPageBox"),
             page_current = $(this).parentsUntil(obj,"[data-dialog-page-box=true]:first"),
             selfPageBox  = page_current.attr("id"),
             page_next    = obj.find("#" + dataRelated);    //console.log(page_current);


         page_current.removeClass("dialog-page-box-current");
         page_current.addClass("dialog-page-box-prev");

         page_next.removeClass("dialog-page-box-next");
         page_next.addClass("dialog-page-box-current");


          var nextTitle  =  o.titleBar.find('[data-self-page-box='+ dataRelated +']'),
              selfTitle  =  o.titleBar.find('[data-self-page-box='+ selfPageBox +']');

         selfTitle.removeClass("dialog-page-box-current");
         selfTitle.addClass("dialog-page-box-prev");

         nextTitle.removeClass("dialog-page-box-next");
         nextTitle.addClass("dialog-page-box-current");


        });

       backPage.click(function(e){

          var dataSelf  = $(this).parent("[data-self-page-box]").data("selfPageBox"),
              page_current = obj.find("#" + dataSelf),
              backPageBox  = $("#" + dataSelf).data("backPageBoxId"),
              page_prev    = $( "#" + backPageBox );

          if(page_current.data("level") !== 0 ){
          page_current.removeClass("dialog-page-box-current");
          page_current.addClass("dialog-page-box-next");
          }
          page_prev.removeClass("dialog-page-box-prev");
          page_prev.addClass("dialog-page-box-current");

          var backTitle  =  o.titleBar.find('[data-self-page-box='+ backPageBox +']'),
              selfTitle  =  o.titleBar.find('[data-self-page-box='+ dataSelf +']');

          if(page_current.data("level") !== 0 ){
          selfTitle.removeClass("dialog-page-box-current");
          selfTitle.addClass("dialog-page-box-next");
          }
          backTitle.removeClass("dialog-page-box-prev");
          backTitle.addClass("dialog-page-box-current");

        });


    };
    _PageBoxItems2();


};
}(jQuery));
