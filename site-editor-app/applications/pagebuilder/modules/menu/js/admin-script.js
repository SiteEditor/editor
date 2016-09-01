jQuery( document ).ready( function( $ ) {
    /* SCRIPT FOR UPLOAD FILD OPTIONE
    ===============================*/
    $(".uploadbt,.uploadbgbtn").click(function( event ) {
        event.preventDefault();

        try{
            var idObj  = $( this ).attr("data-id"),
                title  = $( this ).attr("data-title")  != '' ? $( this ).attr("data-title")  : 'Upload File',
                textBt = $( this ).attr("data-textbt") != '' ? $( this ).attr("data-textbt") : 'Insert File',
                $parent = $( "#" + idObj ).parents( ".field-lib-img" ),
                custom_uploader = wp.media.frames.file_frame = wp.media({
                    title  : title ,
                    button : {
                        text: textBt
                    },
                    multiple: false
                });
          
            //When a file is selected, grab the URL and set it as the text field's value
            custom_uploader.on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                var $url = ( attachment.sizes && attachment.sizes.thumbnail && attachment.sizes.thumbnail.url ) ? attachment.sizes.thumbnail.url : attachment.url;

                $( "#" + idObj ).val( attachment.url );
                var html = '<img src="' + $url +'">';
                //console.log( html );
                //console.log( $parent );
                //console.log( $parent.find(".sed-image-show") );
                $parent.find(".sed-image-show").html( html );
            });
            //Open the uploader dialog
            custom_uploader.open();
        }catch( e ){
            console.log( e.message() );
        }

        
   });

   $(".sed-remove-image").click(function(){
       var id = $(this).data("id");
       $( "#" + id ).val( "" );
       $(this).prev().html("");
   });

});