/*jQuery(document).ready(function($) {

    jQuery('.sed-tabs').livequery(function(){
        $(this).tabs();
    });

});*/
(function( exports, $ ) {
    var api = sedApp.editor;

    $( function() {
                       
        $('.module-tab').livequery(function(){

            var id_box = $(this).attr("sed_model_id") , activatedTabs = [ $('#' + id_box + ' a:first').attr("href") ];

            if( !$("body").hasClass("siteeditor-app") && $(this).hasClass("tab-skin9") ){ 
                $('#' + id_box + ' a[data-toggle="tab"]').hover(function (e) {
                	  //e.preventDefault()
                	  $(this).tab('show');
                },function (e) {
                	  //e.preventDefault()
                	  $(this).parent().removeClass("active");
                      $(this).attr("aria-expanded" , false);
                      $($(this).attr("href") ).removeClass("active");
                });

                var tabpanel = $('#' + id_box + ' [role="tabpanel"]');
                tabpanel.hover(function (e) {
                	  //e.preventDefault()
                      $('#' + id_box + ' a[href="#'+ $(this).attr("id") +'"]').tab('show');
                },function (e) {
                	  //e.preventDefault()
                	  $('#' + id_box + ' a[href="#'+ $(this).attr("id") +'"]').parent().removeClass("active");
                      $('#' + id_box + ' a[href="#'+ $(this).attr("id") +'"]').attr("aria-expanded" , false);
                      $( this ).removeClass("active");
                });

                $('#' + id_box + ' a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

                    var cId = $(this).attr("href");
                    if( $.inArray( cId , activatedTabs ) == -1 ){
                        $(cId).find(".sed-row-pb > .sed-pb-module-container").trigger("sedFirstTimeActivatedTabs" , [$(this)]);
                        activatedTabs.push( cId );
                    }

                });
            }else{

                $('#' + id_box + ' aa[data-toggle="tab"]').click(function (e) {
                	  e.preventDefault()
                	  $(this).tab('show')
                });

                $('#' + id_box + ' a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

                    var cId = $(this).attr("href");
                    if( $.inArray( cId , activatedTabs ) == -1 ){
                        $(cId).find(".sed-row-pb > .sed-pb-module-container").trigger("sedFirstTimeActivatedTabs" , [$(this)]);
                        activatedTabs.push( cId );
                    }

                });
            }


            var _fixDragDrop = function( type ){
                $('#' + id_box ).find('[role="tabpanel"]').addClass("sed-sortable-disabled");
                $('#' + id_box ).find('[role="tabpanel"]').removeClass("bp-component");

                if(type == 0){
                    $( '#' + id_box ).find('.active[role="tabpanel"]').removeClass("sed-sortable-disabled");
                    $( '#' + id_box ).find('.active[role="tabpanel"]').addClass("bp-component");
                }else if(type == 1){
                    $( '#' + id_box ).find('[role="tabpanel"]').eq(0).removeClass("sed-sortable-disabled");
                    $( '#' + id_box ).find('[role="tabpanel"]').eq(0).addClass("bp-component");
                }
            };

            $('#' + id_box + ' a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

                _fixDragDrop(0);

            });

            _fixDragDrop(1);

            var _responsiveTab = function( el ){
                if( $(el).width() < 260 ){
                    $(el).addClass("tab-resize-responsive");
                }else{
                    $(el).removeClass("tab-resize-responsive");
                }
            };

            _responsiveTab( this );

            $(this).on("sed.moduleResize sed.moduleResizeStop" , function(){
                _responsiveTab( this );
            });

            /*
            @Site Editor pakage
            Edit By SiteEditor
            for module sortable(darg & drop)
            */
            $(this).on("sed.moduleSortableStop sedAfterRemoveColumns" , function(){
                _responsiveTab( this );
            });


            $(this).parents(".sed-pb-module-container:first").on( "sedChangeModulesLength", function( e , length ){
                _responsiveTab( $(this).find(".sed-pb-module-container:first") );
            });

            $(this).parents(".sed-pb-module-container:first").on( "sedChangedSheetWidth", function(){
                if( $(this).parents(".sed-row-boxed").length > 0 ){
                    _responsiveTab( $(this).find(".sed-pb-module-container:first") );
                }
            });

            $(this).parents(".sed-pb-module-container:first").on( "sedChangedPageLength", function( e , length ){
                if( ($(this).parents(".sed-row-boxed").length == 0 && length == "wide" ) || ($(this).parents(".sed-row-boxed").length == 1 && length == "boxed" ) ){
                    _responsiveTab( $(this).find(".sed-pb-module-container:first") );
                }
            });

        });


    });


}(sedApp, jQuery));