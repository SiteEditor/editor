(function( exports, $ ) {
    var api = sedApp.editor;

    $( function() {

    	$('.parallax-slider').livequery(function(){
              var id_box = $(this).attr("id"),
                    data = $(this).data();

            	$('#' + id_box).parallaxSlider({
                      auto              :data.parallaxAuto,
                      speed             :data.parallaxSpeed,
                      easing            :data.parallaxEasing,
                      easingBg          :data.parallaxEasingBg,
                      circular          :data.parallaxCircular,
                      thumbRotation     :data.parallaxThumbRotation,
            	});
        });

    });


}(sedApp, jQuery));
