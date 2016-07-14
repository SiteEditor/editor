jQuery( document ).ready( function ( $ ) {

    $('[data-sed-role="masonry"]').livequery(function(){
        var $masonry = $(this) ,
            data = $masonry.data(),
            options = {} ,
            $container = $masonry.parents(".sed-pb-module-container:first");

        for( property in data ){
            if(property != "sedRole")
                options[property] = data[property];
        }

        if( $("body").hasClass("rtl-body") ){
           options.isOriginLeft = false;
        }

        $masonry.imagesLoaded().done( function( instance ) {

            $masonry.masonry( options );

            //FOR FIX BUG IN PAGES WITH MASONRY GALLERY
            //$(window).stellar();

        }).fail( function() {

            console.log('all images loaded, at least one is broken');

        });

        $container.on("sed.moduleResize sed.moduleResizeStop" , function(){
            $masonry.masonry();
        });

        $container.on("sed.moduleSortableStop sedAfterRemoveColumns" , function(){
            $masonry.masonry();
        });

        $container.parents(".sed-pb-module-container:first").on( "sedChangeModulesLength", function( e , length ){
            $masonry.masonry();
        });

        $container.parents(".sed-pb-module-container:first").on( "sedChangedSheetWidth", function(){
            if( $(this).parents(".sed-row-boxed").length > 0 ){
                $masonry.masonry();
            }
        });

        $container.parents(".sed-pb-module-container:first").on( "sedChangedPageLength", function( e , length ){
            if( ($(this).parents(".sed-row-boxed").length == 0 && length == "wide" ) || ($(this).parents(".sed-row-boxed").length == 1 && length == "boxed" ) ){
                $masonry.masonry();
            }
        });

        $container.parents(".sed-pb-module-container:first").on( "sedFirstTimeActivatedTabs", function(){
            $masonry.masonry();
        });

        $container.parents(".sed-pb-module-container:first").on( "sedFirstTimeActivatedAccordionTabs", function(){
            $masonry.masonry();
        });

        $container.parents(".sed-pb-module-container:first").on( "sedFirstTimeMegamenuActivated", function(){
            $masonry.masonry();
        });

    });

});
