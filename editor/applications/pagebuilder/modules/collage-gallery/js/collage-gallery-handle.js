jQuery( document ).ready( function ( $ ) {

	$('.collage-gallery-container').livequery(function(){
        var data = $(this).parent().data(),
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
        $(this).justifiedGallery(options);
   });
   
})