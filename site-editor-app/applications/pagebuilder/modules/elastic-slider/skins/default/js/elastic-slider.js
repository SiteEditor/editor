/*
#js_info#(
"handle" => "elastic-slider",
"deps" => array('jquery',"easing" , "eislideshow-elastic-slider"),
"ver" => "1.0.0",
"in_footer" => true
)#
*/

jQuery(document).ready(function($){

        $('.elastic-container').livequery(function(){
            var id_box  = $(this).find(".ei-slider").attr("id"),
                data    = $(this).find(".ei-slider").data() ,
                options = {
              		animation			: data.animation,
              		autoplay			: data.autoplay,
              		slideshow_interval	: data.slideshowInterval,
              		speed		     	: data.speed,
              		easing		    	: data.easing,
              		titlesfactor		: data.titlesfactor/100,
              		titlespeed			: data.titlespeed,
              		titleeasing			: data.titleeasing,
              		thumbmaxwidth		: data.thumbmaxwidth,
                 };
    //console.log(options);
            $('#' + id_box).eislideshow(options);



            var _responsiveElastic = function( el ){
                if( $(el).width() < 550 ){
                    $(el).addClass("elastic-resize-responsive");
                }else{
                    $(el).removeClass("elastic-resize-responsive");
                }
            };

            _responsiveElastic( this );

            $(this).on("sed.moduleResize sed.moduleResizeStop" , function(){
                _responsiveElastic( this );
            });

            /*
            @Site Editor pakage
            Edit By SiteEditor
            for module sortable(darg & drop)
            */
            $(this).on("sed.moduleSortableStop sedAfterRemoveColumns" , function(){
                _responsiveElastic( this );
            });


            $(this).parents(".sed-pb-module-container:first").on( "sedChangeModulesLength", function( e , length ){
                _responsiveElastic( $(this).find(".sed-pb-module-container:first") );
            });

            $(this).parents(".sed-pb-module-container:first").on( "sedChangedSheetWidth", function(){
                if( $(this).parents(".sed-row-boxed").length > 0 ){
                    _responsiveElastic( $(this).find(".sed-pb-module-container:first") );
                }
            });

            $(this).parents(".sed-pb-module-container:first").on( "sedChangedPageLength", function( e , length ){
                if( ($(this).parents(".sed-row-boxed").length == 0 && length == "wide" ) || ($(this).parents(".sed-row-boxed").length == 1 && length == "boxed" ) ){
                    _responsiveElastic( $(this).find(".sed-pb-module-container:first") );
                }
            });

        });

});