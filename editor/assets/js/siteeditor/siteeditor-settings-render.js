(function( exports, $ ) {
    var api = sedApp.editor;


    $( function() {

	   $.each( api.settings.settings, function( id, data ) {

			api.create( id, id, data.value, {
				transport   : data.transport,
				previewer   : api.previewer,
                stype       : data.type || "general" ,  //settings type :: general type not support object values
                dirty       : !! data.dirty
			} );

		});

        console.log( "---------------api.settings-----------" , api.settings );

        api.styleEditorSettings = api.styleEditorSettings || [];
  	    $.each( api.settings.settings, function( id, data ) {
             if( !_.isUndefined( data.type ) && data.type == "style-editor" ){
                api.styleEditorSettings.push( id );
             }
  	    });

        api.Events.bind( "renderSettingsControls" , function(id, data , extra){

            api.Events.trigger( "beforerCreateSettingsControls" , id, data , extra);

			var constructor = api.controlConstructor[ data.type ] || api.Control,
				control;

			control = api.control.add( id, new constructor( id, {
				params: data,
				previewer: api.previewer ,
                mediaSettings : api.mediaSettings ,
                extraOpt : extra || {}
			} ) );


        });

        /*var modulesControls = _.filter( api.settings.controls , function( data , id){
            return !_.isUndefined(data.category) && data.category == 'module-settings';
        });

        api.modulesSettingsControls = _.groupBy( modulesControls , function(data , id){ return data.shortcode; });*/

        var allControls = _.map( api.settings.controls ,function( data , id ){
            var newData = _.clone( data );
            newData.control_id = id;
            return newData;
        });

        var allControlsWithGroup = _.filter( allControls , function( data , key ){
            return !_.isUndefined(data.sub_category) && data.sub_category;
        });

        api.sedGroupControls = _.groupBy( allControlsWithGroup , function(data , id){ return data.sub_category; });

        var stylesControls = _.filter( api.settings.controls , function( data , id){
            return !_.isUndefined(data.category) && data.category == 'style-editor';
        });

        api.stylesSettingsControls = _.groupBy( stylesControls , function(data , id){ return data.option_group; });

        var startTime = new Date();
		$.each( api.settings.controls, function( id, data ) {

            if( _.isUndefined( data.category )  ||  $.inArray( data.category , ['module-settings','style-editor' , 'layout'] ) == -1 || ( !_.isUndefined( data.sub_category ) &&  data.sub_category == "general_settings" && data.category == 'style-editor')  ){
                api.Events.trigger( "renderSettingsControls" , id, data);
            }

		});

        var AjaxLoadDefPatterns = new api.Ajax({
            url  :  SED_BASE_URL + "extensions/pagebuilder/includes/ajax_shortcode_pattern.php" ,
            //dataType : "jsonp" ,
            data : {
                action        : 'shortcodes_default_pattern',
                //nonce         : api.settings.nonce.module.load_patterns ,
                sed_page_ajax : 'sed_ajax_default_patterns'
            },
            loadingType : "small" ,
            success : function(){

                var response = this.response.data.output;
                //api.log( response );

                //$.when( api.previewerActive )
                api.previewer.bind( 'previewerActive' ,function(){
                    $.extend( api.defaultPatterns , response || {} );
                    var patterns = api.applyFilters( 'sedDefaultPatternsFilter' , $.extend( true , {} , api.defaultPatterns ) );
                    api.previewer.send( "defaultModulePatterns" , patterns );
                });
            },

            error : function(){

            }

        }, {
            container   : "body" ,
            repeatRequest : false
        });

        /*var AjaxLoadStyleEditorSettings = new api.Ajax({
            url  :  SED_BASE_URL + "/extensions/pagebuilder/includes/ajax_style_editor_settings.php" ,
            //dataType : "jsonp" ,
            data : {
                action        : 'shortcodes_default_pattern2',
                nonce         :  api.settings.nonce.module.load_patterns ,
                sed_page_ajax : 'sed_ajax_default_patterns2'
            },
            loadingType : "small" ,
            success : function(){

                var response = this.response.data.output;
                //api.log( response );
                $("body").append( response );
            },

            error : function(){

            }

        }, {
            container   : "body" ,
            repeatRequest : false
        }); */

		// Check if preview url is valid and load the preview frame.
		if ( api.previewer.previewUrl() ) { 
            api.previewer.refresh();
        }else {
            api.previewer.previewUrl(api.settings.url.home);
        }

		// Control visibility for default controls
        /*
		$.each({
			'background_image': {
				controls: [ 'background_repeat', 'background_position_x', 'background_attachment' ],
				callback: function( to ) { return !! to; }
			},
			'show_on_front': {
				controls: [ 'page_on_front', 'page_for_posts' ],
				callback: function( to ) { return 'page' === to; }
			},
			'header_textcolor': {
				controls: [ 'header_textcolor' ],
				callback: function( to ) { return 'blank' !== to; }
			}
		}, function( settingId, o ) {
			api( settingId, function( setting ) {
				$.each( o.controls, function( i, controlId ) {
					api.control( controlId, function( control ) {
						var visibility = function( to ) {
							control.container.toggle( o.callback( to ) );
						};

						visibility( setting.get() );
						setting.bind( visibility );
					});
				});
			});
		});

		// Juggle the two controls that use header_textcolor
		api.control( 'display_header_text', function( control ) {
			var last = '';

			control.elements[0].unsync( api( 'header_textcolor' ) );

			control.element = new api.Element( control.container.find('input') );
			control.element.set( 'blank' !== control.setting() );

			control.element.bind( function( to ) {
				if ( ! to )
					last = api( 'header_textcolor' ).get();

				control.setting.set( to ? last : 'blank' );
			});

			control.setting.bind( function( to ) {
				control.element.set( 'blank' !== to );
			});
		});  */

		api.trigger( 'ready' );

    });

}(sedApp, jQuery));