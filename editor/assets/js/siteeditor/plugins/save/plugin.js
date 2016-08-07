/**
 * plugin.js
 *
 *
 * License: http://www.siteeditor.org/license
 * Contributing: http://www.siteeditor.org/contributing
 */

/*global siteEditor:true */
(function( exports, $ ){

    var api = sedApp.editor;

    api.postsContent = api.postsContent || {};

    $( function() {

        var body = $( document.body );

		$.extend( api.previewer , {
            save : function() {
                //for sub_theme module-----
                api.Events.trigger( "beforeSave" );

                var dirtyCustomized = {};
                api.each( function ( value, key ) {
                    if ( value._dirty ) {
                        dirtyCustomized[ key ] = value();
                    }
                } );

                var self  = this,
                query = {
                    //sed_app_editor          : 'on',
                    action                  : 'customize_save',
                    sed_app_editor          : 'save' ,
                    theme                   : api.settings.theme.stylesheet,
                    sed_page_customized     : JSON.stringify( dirtyCustomized ),
                    sed_posts_content       : JSON.stringify( api.postsContent || {} ) ,
                    nonce                   : this.nonce.save
                    //sed_pages_theme_content : JSON.stringify( api.pagesThemeContent ) ,
                },
                processing = api.state( 'processing' ),
                submitWhenDoneProcessing,
                submit;

                body.addClass( 'saving' );
                $('#save').find("span.el_txt").text( api.l10n.saving );

                submit = function () {


                    query = api.applyFilters( "sedSaveQueryFilter" , query );

                    var request = $.post( SEDAJAX.url , query );

                    api.trigger( 'save', request );

                    request.always( function () {
                        body.removeClass( 'saving' );
                    } );

                    request.done( function( response ) {  //alert(response);
                        // Check if the user is logged out.
                        if ( '0' === response ) {
                            self.preview.iframe.hide();
                            self.login().done( function() {
                                self.save();
                                self.preview.iframe.show();
                            } );
                            return;
                        }

                        // Check for cheaters.
                        if ( '-1' === response ) {
                            self.cheatin();
                            return;
                        }

                        // Clear setting dirty states
                        api.each( function ( value ) {
                            value._dirty = false;
                        } );

                        api.previewer.send( 'saved', response );

                        api.trigger( 'saved', response );

                    } );
                };

                if ( 0 === processing() ) {
                    submit();
                } else {
                    submitWhenDoneProcessing = function () {
                        if ( 0 === processing() ) {
                            api.state.unbind( 'change', submitWhenDoneProcessing );
                            submit();
                        }
                    };

                    api.state.bind( 'change', submitWhenDoneProcessing );
                }

            }
        });

		// Save and activated states
		(function() {
			var state = new api.Values(),
				saved = state.create( 'saved' ),
				//activated = state.create( 'activated' ),
				processing = state.create( 'processing' );

			state.bind( 'change', function() {
				var save = $('#save');
					//back = $('.back'),

				/*if ( ! activated() ) {
					save.val( api.l10n.activate ).prop( 'disabled', false );
					back.text( api.l10n.cancel );

				} else */

                if ( saved() ) {
					save.prop( 'disabled', true );
                    save.find("span.el_txt").text( api.l10n.saved );
					//back.text( api.l10n.close );

				} else {
					save.prop( 'disabled', false );
                    save.find("span.el_txt").text( api.l10n.save );
					//back.text( api.l10n.cancel );
				}
			});

			// Set default states.
			saved( true );
			//activated( api.settings.theme.active );
			processing( 0 );

			api.bind( 'change', function() {
				state('saved').set( false );
			});

			api.bind( 'saved', function() {
				state('saved').set( true );
				//state('activated').set( true );
			});

            /*
			activated.bind( function( to ) {
				if ( to )
					api.trigger( 'activated' );
			});*/

			// Expose states to the API.
		    api.state = state;
		}());

		// Button bindings.
		$('#save').click( function( event ) {
			api.previewer.save();
			event.preventDefault();
		}).keydown( function( event ) {
			if ( 9 === event.which ) // tab
				return;
			if ( 13 === event.which ) // enter
				api.previewer.save();
			event.preventDefault();
		});

		// Prompt user with AYS dialog if leaving the Customizer with unsaved changes
		$( window ).on( 'beforeunload', function () {
			if ( ! api.state( 'saved' )() ) {
				/*setTimeout( function() {
					overlay.removeClass( 'customize-loading' );
				}, 1 );*/
				return api.l10n.saveAlert;
			}
		} );

		/*$('.back').keydown( function( event ) {
			if ( 9 === event.which ) // tab
				return;
			if ( 13 === event.which ) // enter
				this.click();
			event.preventDefault();
		});

		//api.trigger( 'ready' );

		// Make sure left column gets focus
		topFocus = $('.back');
		topFocus.focus();
		setTimeout(function () {
			topFocus.focus();
		}, 200);*/

	});

})( sedApp, jQuery );