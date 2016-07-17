jQuery( document ).ready( function ( $ ) {

	$('.module-accordion').livequery(function(){
        var id_box = $(this).find(".accordion-container").attr("id"),
            data = $(this).data(),
            activatedTabs = [] ,
            icons = {
                header:         "ui-icon-free-plus",
                activeHeader:   "ui-icon-free-minus"
            };                 //console.log(id_box);console.log(data );

        var _fixDragDrop = function(){
            $( '#' + id_box ).find(".ui-accordion-content").addClass("sed-sortable-disabled");
            $( '#' + id_box ).find(".ui-accordion-content").removeClass("bp-component");
            $( '#' + id_box ).find(".ui-accordion-content-active").removeClass("sed-sortable-disabled");
            $( '#' + id_box ).find(".ui-accordion-content-active").addClass("bp-component");
        };

        $( '#' + id_box ).accordion({
            icons:          icons,
            active:         data.active,
            collapsible:    data.collapsible,
            event:          data.event,
            heightStyle:    data.heightStyle,
            activate: function( event, ui ) {
                _fixDragDrop();

                var cId = ui.newPanel.attr("id");
                if( $.inArray( cId , activatedTabs ) == -1 ){
                    ui.newPanel.find(".sed-row-pb > .sed-pb-module-container").trigger("sedFirstTimeActivatedAccordionTabs" , [ui.newPanel]);
                    activatedTabs.push( cId );
                }

            },
            create: function( event, ui ) {
                _fixDragDrop();

                activatedTabs.push( ui.panel.attr("id") );

            },
        });

    });
});
