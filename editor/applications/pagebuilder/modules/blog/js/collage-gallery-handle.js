jQuery( document ).ready( function ( $ ) {

	$('.blog-collage-gallery-container').livequery(function(){
                var  id_box = $(this).attr("id"),
                    data = $(this).parent().data(),
                      options = {
                          rowHeight                :data.rowHeight,
                          maxRowHeight             :data.maxRowHeight,
                          lastRow                  :data.lastRow,
                          fixedHeight              :data.fixedHeight,
                          randomize                :data.randomize,
                          waitThumbnailsLoad       :data.waitThumbnailsLoad,
                          imagesAnimationDuration  :data.imagesAnimationDuration,
                          //cssAnimation             :data.cssAnimation,
                          margins                  :data.margins,
                          justifyThreshold: 0.75,
                          captions : false,
                          captionSettings : { //ignored with css animations
                            animationDuration : 500,
                            visibleOpacity : 0.7,
                            nonVisibleOpacity : 0.0
                          },
                          rel : null,
                          target : null,
                          refreshTime : 100,
                          extension : /\.[^.\\/]+$/,
                      };
		//console.log(options);
        $('#' + id_box).justifiedGallery(options);

        });
})