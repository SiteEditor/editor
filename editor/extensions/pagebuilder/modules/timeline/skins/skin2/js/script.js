/*#js_info#(
"handle"    => "module-timeline-skin2",
"deps"      => array("jquery"),
"ver"       => "1.0.0",
"in_footer"     => true
)#*/
(function( exports, $ ) {
    var api = sedApp.editor;

$( document ).ready( function (  ) {
    $('.module-timeline-skin2').livequery(function(){

        $(".module-timeline-skin2 li").each(function(index){
            var $afterObj  = $(this).next(),
                offsetOBJ  = $(this).find('.timeline-badge').offset(),
                afterObjPo = $afterObj.find('.timeline-badge').offset();
            if( typeof( offsetOBJ ) != 'undefined' && typeof afterObjPo != 'undefined' ){
                
                var offsetTopOBJ = offsetOBJ.top + $(this).find('.timeline-badge').outerHeight() ;
                
                var  offsetAfterObj = afterObjPo.top; 

                //console.log( "id = " + $(this).attr('id') + " | top = " + offsetTopOBJ + " | after obj top = " + offsetAfterObj );
                
                //console.log( offsetTopOBJ > offsetAfterObj );

                if( offsetTopOBJ >= offsetAfterObj ){
                    margin_top = offsetTopOBJ - offsetAfterObj;
                    $afterObj.css('margin-top' , margin_top + 'px' );
                }
            }
        });
        $('.module-timeline-skin2 li:odd').addClass('timeline-inverted');
    });
});


}(sedApp, jQuery));