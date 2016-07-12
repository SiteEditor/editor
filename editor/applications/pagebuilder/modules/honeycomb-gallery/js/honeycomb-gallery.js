(function( exports, $ ) {
    var api = sedApp.editor;

    $( function() {


          $('.module-honeycomb-gallery > .items-container').livequery(function(){

              var $this           = $(this) ,
                  data            = $(this).data();

              $this.justifiedHoneycombs({
                      honeycombWidth:         data.honeycombWidth,
                      margin:                 data.margin,
                      border:                 data.border,
                      vertical:               data.type,
                      resizeDelay:            2500,
                      debug:                  false,
              });
          });


    });


}(sedApp, jQuery));