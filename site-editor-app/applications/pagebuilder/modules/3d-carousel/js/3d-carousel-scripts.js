(function( exports, $ ) {
    var api = sedApp.editor;

$( document ).ready( function (  ) {

	$('.module-3d-carousel').livequery(function(){
                                                   
    	var $element = $(this).find(".sed_3d_carousel"),
    		options = {
				right_to_left   : $(this).data("trdCarouselRighToLeft"),
            	container_width : $(this).data("trdCarouselContainerWidth"),
            	front_img_width : $(this).data("trdCarouselFrontImgWidth"),
            	front_img_height: $(this).data("trdCarouselFrontImgHeight"),
                lightbox_support: true
			};

            $element.find('.fancy').fancybox();

            $element.boutique(options);

    });

});

}(sedApp, jQuery));