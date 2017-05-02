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
       });

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

		$( "#sed_user_tracking_allow" ).dialog({
			resizable	: false,
            dialogClass: "sed-feedback-dialog-wrapper sed-admin-dialog-wrapper",
			height		: 400,
			width		: 500,
			modal		: true
		});

		$(".sed_user_tracking_allow_action").on("click" , function(){

			var value = $(this).data("value");

			$("#sed_user_tracking_allow_from_admin").val( value );

			$(this).parents(".sed_user_tracking_allow_form:first").submit();

		});


		var deactivateLinkEl = $( '#the-list' ).find( '[data-slug="site-editor"] span.deactivate a' ),
			feedBackDialogEl = $( "#sed-deactivate-feedback-dialog-wrapper" );

		feedBackDialogEl.dialog({
			resizable	: false,
            dialogClass : "sed-feedback-dialog-wrapper sed-admin-dialog-wrapper",
			height		: 400,
			width		: 500,
			modal		: true ,
			autoOpen	: false
		});

		deactivateLinkEl.on("click" , function( event ){

			event.preventDefault();

			feedBackDialogEl.dialog("open");

		});


		var _deactivate = function(){

			location.href = deactivateLinkEl.attr( 'href' );

		};


		var _sendFeedback = function() {

			var formData = $("#sed-deactivate-feedback-dialog-form").serialize();

			feedBackDialogEl.find(".sed-deactivate-feedback-send").addClass( 'sed-ajax-loading' );

			$.post( ajaxurl, formData, function( data ) {

				_deactivate();

			} );

		};


		feedBackDialogEl.find(".sed-deactivate-feedback-send").on("click" , function(){

			_sendFeedback();

		});


		feedBackDialogEl.find(".sed-deactivate-feedback-skip").on("click" , function(){

			_deactivate();

		});
		
    });

}( jQuery ));