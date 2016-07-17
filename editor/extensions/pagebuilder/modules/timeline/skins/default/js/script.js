/*#js_info#(
"handle"    => "module-timeline-skin1",
"deps"      => array("jquery"),
"ver"       => "1.0.0",
"in_footer"     => true
)#*/
(function( exports, $ ) {
    var api = sedApp.editor;

    $( document ).ready( function (  ) {
        $(".module-timeline-default").livequery(function(){
            $('.module-timeline-default .timeline > li:odd').addClass('timeline-inverted');
        });
    });
             
}(sedApp, jQuery));