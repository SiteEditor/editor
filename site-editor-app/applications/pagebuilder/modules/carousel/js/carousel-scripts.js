(function( exports, $ ) {
    var api = sedApp.editor;

$( document ).ready( function (  ) {

	$('.sed_module_carousel').livequery(function(){
                                                   
    	var $this = $(this),
    		options = {},
    		data_attr = [
				'slidesToShow','slidesToScroll','arrows','rtl','dots','infinite',
				'autoplay','draggable',
				'autoplaySpeed' ,'pauseOnHover','fade'
			];

        $.each( data_attr , function( key , value ){
            var val =  $this.parent().data( "carousel" + api.fn.ucfirst( value ) );

            if( value == "infinite" && !_.isUndefined( api.appPreview ) && !_.isUndefined( api.appPreview.mode ) && api.appPreview.mode == "off" ){
                val = false;
            }

            options[value] = val;

        });

        if( options['slidesToShow'] != 1 || options['slidesToScroll'] != 1 )
            delete options['fade'];

        if( options['slidesToShow'] < options['slidesToScroll'] )
            options['slidesToScroll'] = options['slidesToShow'];

        if( options['slidesToShow'] >= 3  ){
            var breakpoint_lg  = 3;
        }else{
            var breakpoint_lg  = options['slidesToShow'];
        }

        if( options['slidesToShow'] >= 2  ){
            var breakpoint_md  = 2;
        }else{
            var breakpoint_md  = options['slidesToShow'];
        }

        var slick_options = $.extend({} , {
            variableWidth: true ,
            centerMode: true,
            slidesToShow: 3,
            slidesToScroll: 1,
            prevArrow:'<span class="fa fa-angle-left slick-arrow slick-prev-button" style="display: block;"></span>',
            nextArrow:'<span class="fa fa-angle-right slick-arrow slick-next-button" style="display: block;"></span>',
            swipe      : true ,
            touchMove  : true ,
            responsive: [
              {
                breakpoint: 1024,
                settings: {
                  slidesToShow: breakpoint_lg,
                  slidesToScroll: breakpoint_lg,
                }
              },
              {
                breakpoint: 600,
                settings: {
                  slidesToShow: breakpoint_md,
                  slidesToScroll: breakpoint_md
                }
              },
              {
                breakpoint: 480,
                settings: {
                  slidesToShow: 1,
                  slidesToScroll: 1
                }
              }
            ]
        } , options );


        var $element = $(this);

        $element.parent().on("sed.moduleSortableStop sed.moduleResize sed.moduleResizeStop sedAfterRemoveColumns" , function(){
            $element.slick('unslick');
            $element.slick( slick_options );
        });


        $element.find("img").on( "sed.changeImgSrc", function( event , newSrc ){
            $element.slick('unslick');
            $element.slick( slick_options );
        });

        $element.parent().parents(".sed-pb-module-container:first").on( "sedChangeModulesLength", function( e , length ){
            $element.slick('unslick');
            $element.slick( slick_options );
        });

        $element.parent().parents(".sed-pb-module-container:first").on( "sedChangedSheetWidth", function(){
            if( $(this).parents(".sed-row-boxed").length > 0 ){
                $element.slick('unslick');
                $element.slick( slick_options );
            }
        });

        $element.parent().parents(".sed-pb-module-container:first").on( "sedChangedPageLength", function( e , length ){
            if( ($(this).parents(".sed-row-boxed").length == 0 && length == "wide" ) || ($(this).parents(".sed-row-boxed").length == 1 && length == "boxed" ) ){
                $element.slick('unslick');
                $element.slick( slick_options );
            }
        });

        $element.parent().parents(".sed-pb-module-container:first").on( "sedFirstTimeActivatedTabs", function(){
            $element.slick('unslick');
            $element.slick( slick_options );
        });

        $element.parent().parents(".sed-pb-module-container:first").on( "sedFirstTimeActivatedAccordionTabs", function(){
            $element.slick('unslick');
            $element.slick( slick_options );
        });

        $element.parent().parents(".sed-pb-module-container:first").on( "sedFirstTimeMegamenuActivated", function(){
            $element.slick('unslick');
            $element.slick( slick_options );
        });

        $(this).slick( slick_options );
    });

});

}(sedApp, jQuery));