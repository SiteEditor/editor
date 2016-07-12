(function( exports, $ ) {
    var api = sedApp.editor;

    $( function() {  

        $('.module-accordion-slider').livequery(function(){
                var id_box = $(this).find(".slider").attr("id"),
                      data = $(this).data();       //console.log(data);

                $('#' + id_box).zAccordion({
                    width:              data.width + '%',
                    height:             data.height + 'px',
                    slideWidth:         data.slideWidth + '%',
                  //  tabWidth:           data.tabWidth ,
                    timeout:            data.timeout,
                    speed:              data.speed,
                    startingSlide:      data.startingSlide,
                    slideClass:         data.slideClass,
                    easing:             data.easing,
                    auto:               data.auto,
                    trigger:            data.trigger,
                    pause:              data.pause,
                    invert:             data.invert,
              //      errors:             data.errors,
                });

        });


    });


}(sedApp, jQuery));