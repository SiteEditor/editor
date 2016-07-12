jQuery(document).ready(function($){


    $(".module-separator").livequery(function(){
        var spr = $(this);
        var separatorSkin = function(spr) {
           var flexNowrap = spr.find(".flex-nowrap"),
              flexNowrapW   = flexNowrap.outerWidth(),
              sprW          = spr.parents(".sed-pb-module-container:first").width();

              if( flexNowrap.length == 0 )
                return ;

              //console.log("flexNowrapW-----" , flexNowrapW );
              //console.log( "sprW-----" , sprW );
              //console.log("(flexNowrapW + 4) >= sprW-----" , (flexNowrapW + 4) >= sprW  );
              //console.log(" (flexNowrapW + 4) < sprW-----" , (flexNowrapW + 4) < sprW );
              if( (flexNowrapW ) >= sprW ){
                  flexNowrap.css({
                      whiteSpace: 'normal',
                  });
                  spr.find(".spr-container").css({
                      display: 'none'
                  });
              }else if( (flexNowrapW) < sprW ){
                  flexNowrap.css({
                      whiteSpace: '',
                  });
                  spr.find(".spr-container").css({
                      display: ''
                  });
              }
        };

        separatorSkin(spr);

        var lazyChange = _.debounce(function(){
            separatorSkin(spr);
        }, 100);

        $(window).resize(function(){
          lazyChange();
        });

        spr.on("sed.moduleResize sed.moduleResizeStop" , function(){
          separatorSkin(spr);
        });

        spr.on("sed.moduleSortableStop sedAfterRemoveColumns" , function(){
          separatorSkin(spr);
        });

        spr.parents(".sed-pb-module-container:first").on( "sedChangeModulesLength", function( e , length ){
            separatorSkin(spr);
        });

        spr.parents(".sed-pb-module-container:first").on( "sedChangedSheetWidth", function(){
            if( $(this).parents(".sed-row-boxed").length > 0 ){
                separatorSkin(spr);
            }
        });

        spr.parents(".sed-pb-module-container:first").on( "sedChangedPageLength", function( e , length ){
            if( ($(this).parents(".sed-row-boxed").length == 0 && length == "wide" ) || ($(this).parents(".sed-row-boxed").length == 1 && length == "boxed" ) ){
                separatorSkin(spr);
            }
        });

        /*spr.find(".hi-icon").on( "sed.changeIconSize", function( event , size ){
           separatorSkin(spr);
        });

        var lazyChange = _.debounce(function(){
            separatorSkin(spr);
        }, 50);
           */
        spr.find(".flex-nowrap-text").on( "sed.changeMCEContent", function( event , content ){
            separatorSkin(spr);
        });


    });

});