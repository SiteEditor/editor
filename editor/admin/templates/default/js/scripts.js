(function( $ ) {
    $( document ).ready( function() {

    	$('#sed_admin_content_tabs li').hide();
    	$('#sed_admin_tabs li').first().find('a').addClass( "active" );
    	$('#sed_admin_content_tabs li').first().show();

    	$( "#sed_admin_tabs" ).find( "a" ).click( function( e ) {
    		e.preventDefault();
    		$( "#sed_admin_tabs" ).find( "a" ).removeClass( "active" );
    		$(this).addClass( "active" );
    		var tabs_content = $(this).attr('href');
    		if( tabs_content.charAt(0) === "#"){
    			$(tabs_content).siblings().fadeOut(0);
    			$(tabs_content).fadeIn(400);
    		}
       })

    	/*$('.active-module-bt').click(function(event) {
    		var $prent = $(this).parents('.module-list-item'),
    			slug   = $prent.find('.slug-module');

    		$.ajax({
    			url: '/path/to/file',
    			type: 'POST',
    			dataType: 'json',
    			data: { 'slug' : slug },
    			beforeSend: function()
                {
                    $('.icon-loading').css({opacity:0, display:"block", visibility:'visible',position:'absolute', top:'21px', left:'345px'}).animate({opacity:1});
                },
                success: function(response)
                {
                    this.posts = response.posts ;
                    console.log( response );

                },
                error: function( xhr, status, error) {
                    console.log(xhr);
                    console.log(status);
                    console.log(error);
                }
    		});
    	});*/

        $(".install-process-title").click(function(event) {
            $(this).toggleClass( 'open' ,  "close" );
            $(this).find("ul").slideToggle(400);
        });

        // Add Color Picker to all inputs that have 'color-field' class
        $('.color-field').wpColorPicker();

        var _customColorsShow = function( el ){
            if( el.val() == "custom" ){
                el.parents(".sed_admin_item_setting:first").siblings().show();
            }else{
                el.parents(".sed_admin_item_setting:first").siblings().hide();
            }
        };

        _customColorsShow( $('[name="sed-color-palette"]').filter(":checked") );

        $('.sed-color-palettes label').click(function(){
            _customColorsShow( $(this).find('[name="sed-color-palette"]') );
        });


    });
}( jQuery ));