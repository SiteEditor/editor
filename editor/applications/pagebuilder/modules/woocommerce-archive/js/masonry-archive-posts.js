jQuery( document ).ready( function ( $ ) {
    $('.sed-archive-masonry').livequery(function(){
        var $container = $(this);
        $container.masonry({
           itemSelector: '.sed-archive-masonry > div',
        });
       $container.parents(".sed-pb-module-container:first").on("sed.moduleResize sed.moduleResizeStop" , function(){
           $container.masonry();
        });

        $container.parents(".sed-pb-module-container:first").on("sed.moduleSortableStop" , function(){
           $container.masonry();
        });
    });
});