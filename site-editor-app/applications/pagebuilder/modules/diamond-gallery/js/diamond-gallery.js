(function( exports, $ ) {
    var api = sedApp.editor;

    $( function() {

        $('.module-diamond-gallery > .items-container').livequery(function(){
              var $this           = $(this) ,
                  $parent_id      = $(this).parent().attr("id"),
                //  id_box          = $(this).attr("id"),
                  data            = $(this).data();

              $this.justifiedDiamonds({
                  diamondWidth       : data.diamondWidth,
                  margin             : data.margin,
                  border             : data.border,
                  resizeDelay        : 500,
                  debug              : false,
              });


              var lazyResize = _.debounce(function(){     //alert("test");
                  $("#" + $parent_id).find("> .items-container").data('sed.justifiedDiamonds').refresh();
              }, 300);

             // window resize
              $(window).resize(function() {
                 lazyResize();
              });


              /*
              @Site Editor pakage
              Edit By SiteEditor
              for resolve change image
              */
              var images = $this.find("img");
              images.on( "sed.changeImgSrc", function( event , newSrc ){
                  $this.data('sed.justifiedDiamonds').refresh();
              });

              /*
              @Site Editor pakage
              Edit By SiteEditor
              for column resize
              */                 //

              $this.parent().on("sed.moduleResizing" , function(){
                  lazyResize();
              });

              /*
              @Site Editor pakage
              Edit By SiteEditor
              for module sortable(darg & drop)
              */                                       // sed.moduleResizeStop
              $this.parent().on("sed.moduleSortableStop sedAfterRemoveColumns" , function(){
                  $this.data('sed.justifiedDiamonds').refresh();
              });


              $this.parent().parents(".sed-pb-module-container:first").on( "sedChangeModulesLength", function( e , length ){
                  $this.data('sed.justifiedDiamonds').refresh();
              });

              $this.parent().parents(".sed-pb-module-container:first").on( "sedChangedSheetWidth", function(){
                  if( $(this).parents(".sed-row-boxed").length > 0 ){
                      $this.data('sed.justifiedDiamonds').refresh();
                  }
              });

              $this.parent().parents(".sed-pb-module-container:first").on( "sedChangedPageLength", function( e , length ){
                  if( ($(this).parents(".sed-row-boxed").length == 0 && length == "wide" ) || ($(this).parents(".sed-row-boxed").length == 1 && length == "boxed" ) ){
                      $this.data('sed.justifiedDiamonds').refresh();
                  }
              });

              $this.parent().parents(".sed-pb-module-container:first").on( "sedFirstTimeActivatedTabs", function(){
                  $this.data('sed.justifiedDiamonds').refresh();
              });


              $this.parent().parents(".sed-pb-module-container:first").on( "sedFirstTimeActivatedAccordionTabs", function(){
                  $this.data('sed.justifiedDiamonds').refresh();
              });


              $this.parent().parents(".sed-pb-module-container:first").on( "sedFirstTimeMegamenuActivated", function(){
                  $this.data('sed.justifiedDiamonds').refresh();
              });

        });

    });


}(sedApp, jQuery));
