(function( exports, $ ){
	var api = sedApp.editor,
		debounce;


	/**
	 * Returns a debounced version of the function.
	 *
	 * @todo Require Underscore.js for this file and retire this.
	 */
	debounce = function( fn, delay, context ) {
		var timeout;
		return function() {
			var args = arguments;

			context = context || this;

			clearTimeout( timeout );
			timeout = setTimeout( function() {
				timeout = null;
				fn.apply( context, args );
			}, delay );
		};
	};

	/**
	 * @constructor
	 * @augments sedApp.editor.customize.Messenger
	 * @augments sedApp.editor.customize.Class
	 * @mixes sedApp.editor.customize.Events
	 */
	api.Preview = api.Messenger.extend({
		/**
		 * @param {object} params  - Parameters to configure the messenger.
		 * @param {object} options - Extend any instance parameter or method with this object.
		 */
		initialize: function( params, options ) {
			var self = this;

			api.Messenger.prototype.initialize.call( this, params, options );

			this.body = $( document.body );
			this.body.on( 'click.preview', 'a', function( event ) {
				var link, isInternalJumpLink;
				link = $( this );
				isInternalJumpLink = ( '#' === link.attr( 'href' ).substr( 0, 1 ) );
				event.preventDefault();

				if ( isInternalJumpLink && '#' !== link.attr( 'href' ) ) {
					$( link.attr( 'href' ) ).each( function() {
						this.scrollIntoView();
					} );
				}

				/*
				 * Note the shift key is checked so shift+click on widgets or
				 * nav menu items can just result on focusing on the corresponding
				 * control instead of also navigating to the URL linked to.
				 * only go to new page if we are in preview mode on
				 */
				if ( event.shiftKey || isInternalJumpLink || api.appPreview.mode == "off" ) {
					return;
				}
				self.send( 'scroll', 0 );
				self.send( 'url', link.prop( 'href' ) );
			});

			// You cannot submit forms.
			// @todo: Allow form submissions by mixing $_POST data with the customize setting $_POST data.
			this.body.on( 'submit.preview', 'form', function( event ) {
				event.preventDefault();
			});

			this.window = $( window );
			this.window.on( 'scroll.preview', debounce( function() {
				self.send( 'scroll', self.window.scrollTop() );
			}, 200 ));

			this.bind( 'scroll', function( distance ) {
				self.window.scrollTop( distance );
			});
		}
	});

	/*var hoverBox = $("#static-module-hover-box" , window.parent.document);
	var iframe = $("#website" , window.parent.document);
	var ifrTop = iframe.offset().top;
	var ifrLeft = iframe.offset().left;

	$('[sed-role="static-template-content"]').hover(function(e){
		var w = $( this ).outerWidth() , h = $( this ).outerHeight() ,
			offset = $( this ).offset() , l = offset.left ,
			t = offset.top;
		////api.log(x +","+ y);
		l = l - $( window ).scrollLeft() + ifrLeft;
		t = t - $( window ).scrollTop() + ifrTop;

		hoverBox.show();

		hoverBox.css({
			width  : w,
			height : h,
			left   : l,
			top    : t ,
			backgroundColor : "blue" ,
			opacity : 0.4
		});

	},function(e){
		hoverBox.css({
			backgroundColor : "transparent" ,
			opacity : 1
		});
		hoverBox.hide();
	});*/

	/**
	 * api.template( id )
	 *
	 * Fetches a template by id.
	 *
	 * @param  {string} id   A string that corresponds to a DOM element with an id prefixed with "tmpl-".
	 *                       For example, "attachment" maps to "tmpl-attachment".
	 * @return {function}    A function that lazily-compiles the template requested.
	 */
	api.template = _.memoize(function ( id ) {
		var compiled,
			options = {
				evaluate:    /<#([\s\S]+?)#>/g,
				interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
				escape:      /\{\{([^\}]+?)\}\}(?!\})/g
			};

		return function ( data ) {
			compiled = compiled || _.template( $( '#' + id ).html(), null, options );
			return compiled( data );
		};
	});

	api.wpAjax = {
		//settings: settings.ajax || {},

		/**
		 * wp.ajax.post( [action], [data] )
		 *
		 * Sends a POST request to WordPress.
		 *
		 * @param  {string} action The slug of the action to fire in WordPress.
		 * @param  {object} data   The data to populate $_POST with.
		 * @return {$.promise}     A jQuery promise that represents the request.
		 */
		post: function( action, data ) {
			return api.wpAjax.send({
				data: _.isObject( action ) ? action : _.extend( data || {}, { action: action })
			});
		},

		/**
		 * wp.ajax.send( [action], [options] )
		 *
		 * Sends a POST request to WordPress.
		 *
		 * @param  {string} action  The slug of the action to fire in WordPress.
		 * @param  {object} options The options passed to jQuery.ajax.
		 * @return {$.promise}      A jQuery promise that represents the request.
		 */
		send: function( action, options ) {

            var promise, deferred;

            if ( _.isObject( action ) ) {
                options = action;
            } else {
                options = options || {};
                options.data = _.extend( options.data || {}, { action: action });
            }

            options = _.defaults( options || {}, {
                type:    'POST',
                url:     SEDAJAX.url,
                context: this
            });

            deferred = $.Deferred( function( deferred ) {
                // Transfer success/error callbacks.
                if ( options.success )
                    deferred.done( options.success );
                if ( options.error )
                    deferred.fail( options.error );

                delete options.success;
                delete options.error;

                // Use with PHP's wp_send_json_success() and wp_send_json_error()
                deferred.jqXHR = $.ajax( options ).done( function( response ) {
                    // Treat a response of `1` as successful for backwards
                    // compatibility with existing handlers.
                    if ( response === '1' || response === 1 )
                        response = { success: true };

                    if ( _.isObject( response ) && ! _.isUndefined( response.success ) )
                        deferred[ response.success ? 'resolveWith' : 'rejectWith' ]( this, [response.data] );
                    else
                        deferred.rejectWith( this, [response] );
                }).fail( function() {
                    deferred.rejectWith( this, arguments );
                });
            });

            promise = deferred.promise();
            promise.abort = function() {
                deferred.jqXHR.abort();
                return this;
            };

            return promise;

		}
	};

})( sedApp, jQuery );
