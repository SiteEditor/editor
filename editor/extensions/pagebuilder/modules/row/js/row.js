(function( exports, $ ) {
    var api = sedApp.editor;

    $( function() {

         /*var fixSpacingResponsive = function(){
          var $element = $('body') ,
              Browser_w  = $(window).width(),
              sheetWidth = $('body').data("sheetWidth"),
              main = $('body').find("#site-editor-main-part");

          if(Browser_w <= sheetWidth || (main.hasClass("sed-row-boxed") == true)){
            //console.log($element.find('.sed-pb-post-container > .sed-row-wide .sed-row-boxed:first'));

            $element.find('.sed-pb-post-container[data-content-type="post"] > .sed-row-wide .sed-row-boxed').each(function(){
                if( $(this).parentsUntil( $( '.sed-pb-post-container[data-content-type="post"]' ), ".sed-row-boxed" ).length == 0 && $(this).parentsUntil( $( '.sed-pb-post-container[data-content-type="post"]' ), ".sed-column-pb").length == 0 ){
                    $(this).css({
                        "paddingLeft": '30px',
                        "paddingRight":'30px'
                    });
                } 
            });

            $element.find('.sed-pb-post-container[data-content-type="post"] > .sed-row-boxed').css({
                "paddingLeft": '30px',
                "paddingRight":'30px'
            });

           /* $element.find('.sed-site-main-part > .sed-row-wide').each(function(){
              if($(this).hasClass("sed-main-content-row-role") == false){   *
                $element.find('.sed-site-main-part > .sed-row-wide .sed-row-boxed').each(function(){
                    if( $(this).parentsUntil( $( '.sed-site-main-part' ), ".sed-row-boxed" ).length == 0 && $(this).parentsUntil( $( '.sed-site-main-part' ), ".sed-column-pb").length == 0 ){
                        $(this).css({
                            "paddingLeft": '30px',
                            "paddingRight":'30px'
                        });
                    }
                });
           /*   }
           }); *

          }

        };

        var _lazyFix = _.debounce(function(){
            fixSpacingResponsive();
        }, 50);

        fixSpacingResponsive();

        $(window).resize(function(){
            _lazyFix();
        });

       $(".sed-columns-pb").livequery(function(){
            var spacing = $(this).data("responsiveSpacing");
            $(this).find(">td >.sed-column-contents-pb > .sed-row-pb > .sed-pb-module-container").find("")
        });*/

    });


}(sedApp, jQuery));