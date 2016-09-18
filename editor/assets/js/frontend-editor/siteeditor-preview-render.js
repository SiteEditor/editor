(function( exports, $ ){
	var api = sedApp.editor,
        siteEditorCss = new sedApp.css,
        styleIdElements = {} ,
		rowTmpl = {} , colTmpl = {};

    api.currentPageCustomCss = api.currentPageCustomCss || {};

    api.siteCustomCss = api.siteCustomCss || {};

    api.currentCssSelector = "";

    api.currentCssSettingType = "";

    api.currentAttr = "";

    var _saveCustomCss = function( setting , value ) {

        if( _.isUndefined( api.currentCssSelector ) || !api.currentCssSelector )
            return ;

        if( _.isUndefined( api.currentCssSettingType ) || !api.currentCssSettingType )
            return ;
        
        var sedCss = {};
        
        switch ( api.currentCssSettingType ){
            
            case "module" :

                if( _.isUndefined( api.currentSedElementId ) || !api.currentSedElementId )
                    return ;

                var id = api.currentSedElementId ,
                    attrs = api.contentBuilder.getAttrs( id ) , attrValue;

                sedCss = ( !_.isUndefined( attrs ) && !_.isUndefined( attrs["sed_css"] ) && _.isObject( attrs["sed_css"] ) ) ? attrs["sed_css"] : {};

                if( _.isUndefined( sedCss[api.currentCssSelector] ) )
                    sedCss[api.currentCssSelector] = {};

                sedCss[api.currentCssSelector][setting] = value;

                api.contentBuilder.updateShortcodeAttr( "sed_css" , sedCss , id );

                break;

            case "page" :

                sedCss = api.currentPageCustomCss;

                if( _.isUndefined( sedCss[api.currentCssSelector] ) )
                    sedCss[api.currentCssSelector] = {};

                sedCss[api.currentCssSelector][setting] = value;

                api.preview.send( 'page_custom_design_settings' , sedCss );

                break;

            case "site" :

                sedCss = api.siteCustomCss;

                if( _.isUndefined( sedCss[api.currentCssSelector] ) )
                    sedCss[api.currentCssSelector] = {};

                sedCss[api.currentCssSelector][setting] = value;

                api.preview.send( 'site_custom_design_settings' , sedCss );

                break;

        }

    };

	$( function() {
        api.isRTL = window.IS_RTL;
		api.settings = window._sedAppEditorSettings;
        //api.templateSettings = window._sedAppTemplateOptions;
        api.I18n = window._sedAppEditorI18n;
        api.addOnSettings = window._sedAppEditorAddOnSettings;

		if ( ! api.settings )
			return;

		var bg, parallax  , font , bgElements , textProp ,stylesProp , radius , paddings , margins , borders
        , columnRowWidth = $('[sed-role="main-content"] > .columns-row-inner').width();

		api.preview = new api.Preview({
			url: window.location.href,
			channel: api.settings.channel
		});

        /**
         * Create/update a setting value.
         *
         * @param {string}  id            - Setting ID.
         * @param {*}       value         - Setting value.
         * @param {boolean} [createDirty] - Whether to create a setting as dirty. Defaults to false.
         */
        var setValue = function( id, value, createDirty ) {

            var setting = api( id );

            if ( setting ) {
                setting.set( value );
            } else {
                createDirty = createDirty || false;
                setting = api.create( id, value, {
                    id: id ,
                    stype : api.settings.types[id] || "general"
                } );

                // Mark dynamically-created settings as dirty so they will get posted.
                if ( createDirty ) {
                    setting._dirty = true;
                }
            }
        };

        api.preview.bind( 'settings', function( values ) {
            $.each( values, setValue );
        });

        console.log( "--------------api.settings.values----------------" , api.settings.values );

        api.preview.trigger( 'settings', api.settings.values );


        $.each( api.settings._dirty, function( i, id ) {
            var setting = api( id );
            if ( setting ) {
                setting._dirty = true;
            }
        } );

        api.preview.bind( 'setting', function( args ) {
            var createDirty = true;
            setValue.apply( null, args.concat( createDirty ) );
        });

        // Display a loading indicator when preview is reloading, and remove on failure.
        api.preview.bind( 'loading-initiated', function () {
            //$( 'body' ).addClass( 'wp-customizer-unloading' );
        });
        api.preview.bind( 'loading-failed', function () {
            //$( 'body' ).removeClass( 'wp-customizer-unloading' );
        });

        api.preview.bind( 'current_element', function( elementId ) {
            api.currentSedElementId = elementId;
        });

        api.preview.bind( 'current_css_setting_type', function( type ) {
            api.currentCssSettingType = type;
        });

        //for style editor settings
        api.preview.bind( 'current_css_selector', function( selector ) {
            api.currentCssSelector = selector;
        });

		api.preview.bind( 'sync', function( events ) {
			$.each( events, function( event, args ) {
				api.preview.trigger( event, args );
			});
			api.preview.send( 'synced' );
		});

        api.preview.bind( 'active', function() {

            api.Events.trigger("startActivePreview");

            if ( api.settings.nonce )
                api.preview.send( 'nonce', api.settings.nonce );

            api.preview.send( 'currentPostId' , api.settings.post.id );

            api.preview.send( 'contextmenu-ready' );

            api.preview.send( 'syncPreLoadSettings' , api.settings.preLoadSettings );

        });

        api.preview.bind( 'saved', function( response ) {
            api.trigger( 'saved', response );
        } );

        api.bind( 'saved', function() {
            api.each( function( setting ) {
                setting._dirty = false;
            } );
        } );

        api.preview.bind( 'nonce-refresh', function( nonce ) {
            $.extend( api.settings.nonce, nonce );
        } );

		api.preview.send( 'ready' );

        //for api.Ajax :: than user login ajax render
        api.preview.bind("user_login_done" , function(){
            if(api.currentAjax)
                api.currentAjax.render();
        });

        api.sedCustomStyle = {};
        api.sedCustomStyleString = {
            customCssString   :   ""
        };

        api.sedPageCustomStyle = {};
        api.sedPageCustomStyleString = {
            customCssString   :   ""
        };

        api.sedSiteCustomStyle = {};
        api.sedSiteCustomStyleString = {
            customCssString   :   ""
        };

        api.preview.bind( 'sedCustomStyleUpdate', function( newStyle ) {

            if( _.isUndefined( newStyle ) || _.isUndefined( newStyle.css ) || _.isUndefined( newStyle.setting ) || !api.currentCssSelector )
                return ;

            var css         = newStyle.css ,
                setting     = newStyle.setting ,
                //prop      = newStyle.prop ,
                value       = newStyle.value ,
                needToSave  = newStyle.save ,
                output      = {};

            switch ( api.currentCssSettingType ){

                case "module" :

                    var customCss = api.sedCustomStyle;
                    output = api.sedCustomStyleString;

                    break;

                case "page" :

                    var customCss = api.sedPageCustomStyle;
                    output = api.sedPageCustomStyleString;

                    break;

                case "site" :

                    var customCss = api.sedSiteCustomStyle;
                    output = api.sedSiteCustomStyleString;

                    break;

            }

            if( _.isUndefined( customCss[api.currentCssSelector] ) || _.isUndefined( customCss[api.currentCssSelector][setting] ) ){

                if( _.isUndefined( customCss[api.currentCssSelector] ) )
                    customCss[api.currentCssSelector] = {};

                customCss[api.currentCssSelector][setting] = css;
                output.customCssString += css;

            }else{

                output.customCssString = output.customCssString.replace( customCss[api.currentCssSelector][setting] , css );
                customCss[api.currentCssSelector][setting] = css;
            }

            if( needToSave === true )
                _saveCustomCss( setting , value );

            switch ( api.currentCssSettingType ){

                case "page" :

                    $( "#sed_page_css_editor_generate" ).remove();

                    if( $( "#sed_custom_css_editor_generate" ).length > 0 ){
                        $('<style type="text/css" id="sed_page_css_editor_generate">' + output.customCssString + '</style>').insertBefore( $( "#sed_custom_css_editor_generate" ) );
                    }else{
                        $('<style type="text/css" id="sed_page_css_editor_generate">' + output.customCssString + '</style>').appendTo( $('head') );
                    }

                    break;

                case "site" :

                    $( "#sed_site_css_editor_generate" ).remove();

                    if( $( "#sed_page_css_editor_generate" ).length > 0 ){
                        $('<style type="text/css" id="sed_site_css_editor_generate">' + output.customCssString + '</style>').insertBefore( $( "#sed_page_css_editor_generate" ) );
                    }else if( $( "#sed_custom_css_editor_generate" ).length > 0 ){
                        $('<style type="text/css" id="sed_site_css_editor_generate">' + output.customCssString + '</style>').insertBefore( $( "#sed_custom_css_editor_generate" ) );
                    }else{
                        $('<style type="text/css" id="sed_site_css_editor_generate">' + output.customCssString + '</style>').appendTo( $('head') );
                    }

                    break;
                default:

                    // Refresh the stylesheet by removing and recreating it.
                    $( "#sed_custom_css_editor_generate" ).remove();
                    $('<style type="text/css" id="sed_custom_css_editor_generate">' + output.customCssString + '</style>').appendTo( $('head') );

            }

        });


        var _styleSettingsMap = {
            "padding_top"               : "padding-top" ,
            "padding_right"             : "padding-right" ,
            "padding_left"              : "padding-left" ,
            "padding_bottom"            : "padding-bottom" ,
            "margin_top"                : "margin-top" ,
            "margin_right"              : "margin-right" ,
            "margin_left"               : "margin-left" ,
            "margin_bottom"             : "margin-bottom" ,
            "trancparency"              : "opacity" ,
            "border_top_color"          : "border-top-color" ,
            "border_top_width"          : "border-top-width"  ,
            "border_top_style"          : "border-top-style" ,
            "border_left_color"         : "border-left-color" ,
            "border_left_width"         : "border-left-width"  ,
            "border_left_style"         : "border-left-style" ,
            "border_bottom_color"       : "border-bottom-color" ,
            "border_bottom_width"       : "border-bottom-width"  ,
            "border_bottom_style"       : "border-bottom-style" ,
            "border_right_color"        : "border-right-color" ,
            "border_right_width"        : "border-right-width"  ,
            "border_right_style"        : "border-right-style" ,
            "border_radius_tr"          : "border-top-right-radius" ,
            "border_radius_tl"          : "border-top-left-radius" ,
            "border_radius_br"          : "border-bottom-right-radius" ,
            "border_radius_bl"          : "border-bottom-left-radius" ,
            "font_family"               : "font-family"    ,
            "font_size"                 : "font-size"      ,
            "font_weight"               : "font-weight"    ,
            "font_style"                : "font-style"     ,
            "text_decoration"           : "text-decoration",
            "text_align"                : "text-align"     ,
            "font_color"                : "color"          ,
            "line_height"               : "line-height"    ,
            "position"                  : "position"       ,
            "shadow_color"              : "box-shadow"     ,
            "shadow"                    : "box-shadow"     ,
            "text_shadow_color"         : "text-shadow"    ,
            "text_shadow"               : "text-shadow"    ,
            "parallax_background_image" : "" ,
            "parallax_background_ratio" : "" ,
            "background_attachment"     : "background-attachment" ,
            "background_color"          : "background-color" ,
            "background_position"       : "background-position" ,
            "background_image"          : "background-image" ,
            "external_background_image" : "background-image" ,
            "background_gradient"       : "background-image" ,
            "background_repeat"         : "background-repeat"  ,
            "background_size"           : "background-size" 
        };

        _.each( _styleSettingsMap , function( prop , setting ){
            api( setting , function( value ) {
        		value.bind( function( to ) {

                    if( !api.currentCssSelector )
                        return ;

                    var sedCss = _getCurrentSedCss(),
                        needToSave = true ,
                        styleSetting = setting;

                    switch ( setting ) {
                      case "trancparency":

                          var newVal = ( !to ) ? "initial" : to ;
                          var css =  api.currentCssSelector + "{" + siteEditorCss.transparency( newVal ) + "}";

                      break;
                      case "border_radius_tr":

                          var newVal = ( !to ) ? "initial" : to ;
                          var css =  api.currentCssSelector + "{" + siteEditorCss.sedBorderRadius( newVal , "tr" ) + "}";

                      break;
                      case "border_radius_tl":

                          var newVal = ( !to ) ? "initial" : to ;
                          var css =  api.currentCssSelector + "{" + siteEditorCss.sedBorderRadius( newVal , "tl" ) + "}";

                      break;
                      case "border_radius_br":

                          var newVal = ( !to ) ? "initial" : to ;
                          var css =  api.currentCssSelector + "{" + siteEditorCss.sedBorderRadius( newVal , "br" ) + "}";

                      break;
                      case "border_radius_bl":

                          var newVal = ( !to ) ? "initial" : to ;
                          var css =  api.currentCssSelector + "{" + siteEditorCss.sedBorderRadius( newVal , "bl" ) + "}";

                      break;
                      case "border_top_color":
                      case "border_top_style":
                      case "border_right_color":
                      case "border_right_style":
                      case "border_left_color":
                      case "border_left_style":
                      case "border_bottom_color":
                      case "border_bottom_style":
                      case "font_family":
                      case "font_weight":
                      case "font_style":
                      case "text_decoration":
                      case "text_align":
                      case "font_color":
                      case "position":
                      case "background_color" :
                      case "background_attachment" :

                		  var newVal = ( !to ) ? "initial" : to + " !important" ;
                          var css = api.currentCssSelector + "{" + prop + " : " + newVal + ";}";

                      break;
                      case "background_position" :

                		  var newVal = ( !to ) ? "initial" : to ;
                          var css = api.currentCssSelector + "{" + prop + " : " + newVal + ";}";

                      break;
                      case "border_top_width" :
                      case "border_right_width" :
                      case "border_bottom_width" :
                      case "border_left_width" :
                      case "padding_top":
                      case "padding_right":
                      case "padding_bottom":
                      case "padding_left":
                      case "margin_top":
                      case "margin_right":
                      case "margin_bottom":
                      case "margin_left":
                      case "font_size" :
                      case "line_height" :

                          var newVal = ( !to && to != 0 ) ? "initial" : to + "px !important" ;
                          var css = api.currentCssSelector + "{" + prop + " : " + newVal + ";}";

                      break;
                      case "shadow" :

                          var shColor = ( !_.isUndefined( sedCss[api.currentCssSelector] ) && !_.isUndefined( sedCss[api.currentCssSelector]["shadow_color"] ) ) ? sedCss[api.currentCssSelector]["shadow_color"] : "#000000";
                          var strSh = to;
                          var css = api.currentCssSelector + "{" + _getShadow( strSh , shColor ) + "}";

                      break;
                      case "shadow_color" :

                          if( ( !_.isUndefined( sedCss[api.currentCssSelector] ) && !_.isUndefined( sedCss[api.currentCssSelector]["shadow"] ) ) ){
                              var shColor = to;
                              _saveCustomCss( setting , to );

                              var strSh = sedCss[api.currentCssSelector]["shadow"];
                              var css = _getShadow( strSh , shColor );
                              styleSetting = "shadow";
                              needToSave = false;
                          }else{
                              _saveCustomCss( setting , to );
                              return ;
                          }

                      break;
                      case "text_shadow" :

                          var shColor = ( !_.isUndefined( sedCss[api.currentCssSelector] ) && !_.isUndefined( sedCss[api.currentCssSelector]["text_shadow_color"] ) ) ? sedCss[api.currentCssSelector]["text_shadow_color"] : "#000000";
                          var strSh = to;
                          var css = api.currentCssSelector + '{text-shadow: ' + strSh + " " + shColor + ' !important;}';

                      break;
                      case "text_shadow_color" :

                          if( ( !_.isUndefined( sedCss[api.currentCssSelector] ) && !_.isUndefined( sedCss[api.currentCssSelector]["text_shadow"] ) ) ){
                              var shColor = to;
                              _saveCustomCss( setting , to );

                              var strSh = sedCss[api.currentCssSelector]["text_shadow"];
                              var css =  api.currentCssSelector + '{text-shadow: ' + strSh + " " + shColor + ' !important;}';
                              styleSetting = "text_shadow";
                              needToSave = false;
                          }else{
                              _saveCustomCss( setting , to );
                              return ;
                          }

                      break;
                      case "parallax_background_image" :

                          var ratioNum = ( !_.isUndefined( sedCss[api.currentCssSelector] ) && !_.isUndefined( sedCss[api.currentCssSelector]["parallax_background_ratio"] ) ) ? sedCss[api.currentCssSelector]["parallax_background_ratio"] : 0.5;

                          _saveCustomCss( setting , to );

                          _setParallax( to , ratioNum );

                          return ;

                      break;
                      case "parallax_background_ratio" :

                          if( ( !_.isUndefined( sedCss[api.currentCssSelector] ) && !_.isUndefined( sedCss[api.currentCssSelector]["parallax_background_image"] ) ) ){
                              var ratioNum = to;
                              var isPlx = sedCss[api.currentCssSelector]["parallax_background_image"];
                              _setParallax( isPlx , to );
                          }

                          _saveCustomCss( setting , to );
                          return ;

                      break;
                      case "background_size" :
                          var css = api.currentCssSelector + "{" + siteEditorCss.backgroundSize( to ) + "}";
                      break;
                      case "background_repeat" :
                          var css = api.currentCssSelector + "{background-repeat: " + to + " !important;}";
                      break;
                      case "background_image" :
                      case "external_background_image" :

                          _saveCustomCss( setting , to );

                          var sGradient = !_.isUndefined( sedCss[api.currentCssSelector] ) && !_.isUndefined( sedCss[api.currentCssSelector]["background_gradient"] ) && !_.isEmpty( sedCss[api.currentCssSelector]["background_gradient"] ) && _.isObject( sedCss[api.currentCssSelector]["background_gradient"] ),
                              bgImage = _getBackgroundImage() ,
                              isBgImage = bgImage && bgImage != "none"; alert( bgImage );

                          var css = api.currentCssSelector + "{" + _getCssBackgroundImage( bgImage , isBgImage , sGradient ) + "}";

                          styleSetting = "background_image";
                          needToSave = false;

                      break;
                      case "background_gradient" :

                          var sGradient = !_.isEmpty( to ) && to && _.isObject( to ),
                              bgImage = _getBackgroundImage() ,
                              isBgImage = bgImage && bgImage != "none";

                          _saveCustomCss( setting , to );

                          var css = api.currentCssSelector + "{" + _getCssBackgroundImage( bgImage , isBgImage , sGradient ) + "}";

                          styleSetting = "background_image";
                          needToSave = false;
                      break;

                    }

                    api.preview.trigger( 'sedCustomStyleUpdate' , {
                        css     : css ,
                        setting : styleSetting ,
                        //prop    : prop ,
                        value   : to ,
                        save    : needToSave
                    });

        		});
            });
        });

        var _getCurrentSedCss = function(){

            var sedCss = {};

            switch ( api.currentCssSettingType ){

                case "module" :

                    var id = api.currentSedElementId,
                        attrs = api.contentBuilder.getAttrs(id);

                    sedCss = ( !_.isUndefined(attrs) && !_.isUndefined(attrs["sed_css"]) && _.isObject(attrs["sed_css"]) ) ? attrs["sed_css"] : {};

                    break;

                case "page" :

                    sedCss = api.currentPageCustomCss;

                    break;

                case "site" :

                    sedCss = api.siteCustomCss;

                    break;

            }


            return sedCss;
        };

        var _getShadow = function( strSh , shColor ){
            if( strSh.indexOf( "inset" ) > -1 ){
                strSh = strSh.replace( "inset" , "" );
                strSh = $.trim( strSh );
                strSh += " " + shColor;
                strSh += " inset";
            }else{
                strSh += " " + shColor;
            }

            var css = siteEditorCss.boxShadow( strSh );
            return css;
        };

        var initParallax = {},
            parallaxRatio = {};
        var _setParallax = function( isParallax , ratio , selector ){

            selector = ( !_.isUndefined( selector ) && selector ) ? selector : api.currentCssSelector;

            if( !selector )
                return ;

    		if ( isParallax ){

                var ratioNum = ( ratio ) ? ratio : 0.5;

                if( !_.isUndefined( initParallax[selector] ) && initParallax[selector] === true ){
                    $( selector ).parallax("destroy");
                }

                $( selector ).parallax({
                    xpos           : "50%",
                    speedFactor    : ratioNum,
                });

                initParallax[selector] = true;

            }else{
                $( selector ).parallax("destroy");
                initParallax[selector] = false;
            }

            parallaxRatio[selector] = ratio || 0.5;
        };

        var _refreshParallaxElements = function(){
            $.each( initParallax , function( selector , status ){
                if( status === true && !_.isUndefined( parallaxRatio[selector] ) ){
                    _setParallax( status , parallaxRatio[selector] , selector );
                }
            });
        };

        api.Events.bind( "changePreviewMode" , function( mode ){
            _refreshParallaxElements();
        });

        api.Events.bind( "moduleSortableStopEvent" , function( ui ){
            _refreshParallaxElements();
        });


        var _getGradient = function(){

            var sedCss = _getCurrentSedCss(),
                gradient = ( !_.isUndefined( sedCss[api.currentCssSelector] ) && !_.isUndefined( sedCss[api.currentCssSelector]["background_gradient"] ) ) ? sedCss[api.currentCssSelector]["background_gradient"] : "";

            if( !_.isEmpty( gradient ) && _.isObject( gradient ) ){
                var bgColor = ( !_.isUndefined( sedCss[api.currentCssSelector] ) && !_.isUndefined( sedCss[api.currentCssSelector]["background_color"] ) ) ? sedCss[api.currentCssSelector]["background_color"] : $(api.currentCssSelector).css("background-color");

                bgColor = bgColor || "#000000";

                var tColor = tinycolor( bgColor ) ,
                    endColor = siteEditorCss.gradientEndColor( tColor.toRgb() ) ,
                    startColor = tColor.toHexString();

                startColor =  siteEditorCss.hexToRgb( startColor );
                startColor = 'rgb(' + startColor.r + ',' + startColor.g + ',' + startColor.b + ')';

                gradient.start = startColor;
                gradient.end = endColor;
            }

            return gradient;
        };

        var _getCssBackgroundImage = function( bgImg , isBgImg , isGradient ){

            if( !isBgImg && !isGradient ){
                var css = 'background-image: none !important;';
            }else if( !isBgImg && isGradient ){
                var gradient = _getGradient();    
                var css = siteEditorCss.gradient( gradient.start , gradient.end , gradient );
            }else if( isBgImg && isGradient ){
                var gradient = _getGradient();
                var css = siteEditorCss.gradient( gradient.start , gradient.end , gradient , bgImg ) ;
            }else{
                /*var img = api.fn.getAttachmentImage( bgImg , "full" ),
                    src = !_.isUndefined( img ) && !_.isUndefined( img.src ) ? img.src : "" ; */

                var css = 'background-image: url("' + bgImg + '") !important;';
            }

            return css;
        };

        var _getBackgroundImage = function( ){
            var sedCss = _getCurrentSedCss(),
                bgImage = ( !_.isUndefined( sedCss[api.currentCssSelector] ) && !_.isUndefined( sedCss[api.currentCssSelector]["background_image"] ) ) ? sedCss[api.currentCssSelector]["background_image"] : "";

            if( !bgImage || bgImage == "none" ){
                bgImage = ( !_.isUndefined( sedCss[api.currentCssSelector] ) && !_.isUndefined( sedCss[api.currentCssSelector]["external_background_image"] ) ) ? sedCss[api.currentCssSelector]["external_background_image"] : "";
            }

            return bgImage;
        };


        /* Custom Paddings */
        /*paddings = $.map(['top' , 'right' , 'bottom' , 'left' , 'lock'], function( prop ) {
        	return 'padding_' + prop;
        });
        api.when.apply( api, paddings ).done( function( top , right , bottom , left , lock ) {
        	var update , head = $('head') ,
                lastValue , width;
            update = function(index) {

                if( !api.currentCssSelector )
                    return ;

        	    var css = '', styleId,
                      //elementClass = 'sed-custom-' + api.currentCssSelector,
                    element = $( api.currentCssSelector ) , propStr;

                styleId = _getStyleId( element , 'sed_padding' , 'padding');

        		if ( !_.isUndefined( top() ) )
                    css += "padding-top : " + top()+ "px !important;";

        		if ( !_.isUndefined( right() ) ){ alert( right() );
        		    propStr = ( api.isRTL ) ? "padding-left : " : "padding-right : ";
                    css += propStr + right()+ "px !important;";
                }

        		if ( !_.isUndefined( bottom() ) )
                    css += "padding-bottom : " + bottom()+ "px !important;";
                                              alert( left() );
        		if ( !_.isUndefined( left() ) ){
        		    propStr = ( api.isRTL ) ? "padding-right : " : "padding-left : ";
                    css += propStr + left()+ "px !important;";
                }
                     alert( api.currentCssSelector );

        		// Refresh the stylesheet by removing and recreating it.
        		$( "#" + styleId ).remove();
        		style = $('<style type="text/css" id="' + styleId + '">' + api.currentCssSelector + '{ ' + css + ' }</style>').appendTo( head );

            };

        	$.each( arguments, function(index , value) {
        		this.bind( function(){
                    update( index );
        		});
        	});
        });*/

        /* Custom Radius */
        /*radius = $.map(['tl' , 'tr' , 'br' , 'bl' , 'lock'], function( prop ) {
        	return 'border_radius_' + prop;
        });

        api.when.apply( api, radius ).done( function( tl , tr , br , bl , lock ) {
        	var update , head = $('head') ,
                lastValue;
            update = function(index) {
                if( !api.currentCssSelector )
                    return ;

        	    var css = '', styleId ,
                      //elementClass = 'sed-custom-' + api.currentCssSelector,
                    element = $( api.currentCssSelector ),
                    side;
                        //widthEl = element.outerWidth() ,
                        //borderSides = currentBorderSides

                styleId = _getStyleId( element , 'sed_radius' , 'radius');

        		if ( !_.isUndefined( tl() ) ){

                    if( api.isRTL )
                        side = "tr";
                    else
                        side = "tl";

                    css += siteEditorCss.sedBorderRadius( tl() , side );
                }

        		if ( !_.isUndefined( tr() ) ){

                    if( api.isRTL )
                        side = "tl";
                    else
                        side = "tr";

                    css += siteEditorCss.sedBorderRadius( tr() , side );
                }

        		if ( !_.isUndefined( br() ) ){

                    if( api.isRTL )
                        side = "bl";
                    else
                        side = "br";

                    css += siteEditorCss.sedBorderRadius( br() , side );
                }

        		if ( !_.isUndefined( bl() ) ){

                    if( api.isRTL )
                        side = "br";
                    else
                        side = "bl";

                    css += siteEditorCss.sedBorderRadius( bl() , side );
                }

        		// Refresh the stylesheet by removing and recreating it.
        		$( "#" + styleId ).remove();
        		style = $('<style type="text/css" id="' + styleId + '">' + api.currentCssSelector + '{ ' + css + ' }</style>').appendTo( head );
            };

        	$.each( arguments, function(index , value) {
        		this.bind( function(){
                    update( index );
        		});
        	});
        });*/

        /*stylesProp = ['shadow_color' , 'shadow'];
        api.when.apply( api, stylesProp ).done( function( shadowColor , shadow ) {
        	var update , head = $('head');

            update = function() {
                if( !api.currentCssSelector )
                    return ;

        	    var css = '', styleId ,
                      //elementClass = 'sed-custom-' + api.currentCssSelector,
                    element = $( api.currentCssSelector );

                styleId = _getStyleId( element , 'sed_styles' , 'styles');

                if ( !_.isUndefined( shadowColor() ) ){
                    _saveCustomCss( "shadow_color" , shadowColor() );
                }

        		if ( !_.isUndefined( shadow() ) ){
        		    if( !shadow() || shadow() == "none" ){
                        var strSh = "none";
                        _saveCustomCss( "shadow" , strSh );
        		    }else{
            		    var shColor = shadowColor() || "#000000";
            		    var strSh = shadow();
                        if( strSh.indexOf( "inset" ) > -1 ){
                            strSh = strSh.replace( "inset" , "" );
                            strSh = $.trim( strSh );
                            strSh += " " + shColor;
                            strSh += " inset";
                        }else{
                            strSh += " " + shColor;
                        }
                        _saveCustomCss( "shadow" , strSh );
                        _saveCustomCss( "shadow_color" , strSh );
                    }

                    css += siteEditorCss.boxShadow( strSh );

        		}

        		// Refresh the stylesheet by removing and recreating it.
        		$( "#" + styleId ).remove();
        		style = $('<style type="text/css" id="' + styleId + '">' + api.currentCssSelector + '{ ' + css + ' }</style>').appendTo( head );

            };

        	$.each( arguments, function() {
        		this.bind( update );
        	});
        }); */

        /* font */     //
        /*font = $.map(['color','style', 'weight', 'size', 'family'], function( prop ) {
        	return 'font_' + prop;
        });

        api.when.apply( api, font ).done( function( color, style, weight, size, family ) {
        	var update , head = $('head');


        	update = function() {
                if( !api.currentCssSelector )
                    return ;
                               alert( api.currentCssSelector );
                      //alert(api.currentCssSelector.attr("class"));
        	    var css = '', styleId ,
                      //elementClass = 'sed-custom-' + api.currentCssSelector,
                      element = $( api.currentCssSelector );

                styleId = _getStyleId( element , 'sed_font' , 'font');

        		if ( !_.isUndefined( color() ) )
                    css += 'color: ' + color() + ' !important;';

        		if ( !_.isUndefined( style() ) )
                    css += 'font-style: ' + style() + ' !important;';

        		if ( !_.isUndefined( size() ) )
                    css += 'font-size: ' + size() + 'px !important;';

        		if ( !_.isUndefined( weight() ) )
                    css += 'font-weight: ' + weight() + ' !important;';

        		if ( !_.isUndefined( family() ) ){
        		    api.typography.loadFont( family() );
                    css += 'font-family: ' + family() + ' ';
                }

        		// Refresh the stylesheet by removing and recreating it.
        		$( "#" + styleId ).remove();
        		styleEl = $('<style type="text/css" id="' + styleId + '">' + api.currentCssSelector + '{ ' + css + ' }</style>').appendTo( head );

        	};

        	$.each( arguments, function() {
        		this.bind( update );
        	});
        });*/

        /*textProp = $.map(['shadow', 'shadow_color'], function( prop ) {
        	return 'text_' + prop;
        });
                                                                                           //
        api.when.apply( api, textProp ).done( function( shadow, shadow_color ) {
        	var update , head = $('head') ;


        	update = function() {
                if( !api.currentCssSelector )
                    return ;

                      //alert(api.currentCssSelector.attr("class"));
        	    var css = '', styleId ,
                      //elementClass = 'sed-custom-' + api.currentCssSelector,
                      element = $( api.currentCssSelector ) ,
                      text_shadow , alignVal , newVal;

                styleId = _getStyleId( element , 'sed_text' , 'text');

        		if ( !_.isUndefined( shadow() ) ){
                    text_shadow = shadow();
                    _saveCustomCss( "text_shadow" , shadow() );
                }

                if( !_.isUndefined( shadow_color() ) ){
                    _saveCustomCss( "text_shadow_color" , shadow_color() );
                }

                if ( !_.isUndefined( shadow_color() ) && !_.isUndefined( shadow() ) && shadow() != "none" )
                    text_shadow += " " + shadow_color();

                if ( !_.isUndefined( text_shadow ) )
                    css += 'text-shadow: ' + text_shadow + ' !important;';

        		// Refresh the stylesheet by removing and recreating it.
        		$( "#" + styleId ).remove();
        		styleEl = $('<style type="text/css" id="' + styleId + '">' + api.currentCssSelector + '{ ' + css + ' }</style>').appendTo( head );

        	};

        	$.each( arguments, function() {
        		this.bind( update );
        	});
        }); */


        /*sedApp.editor( 'background_color', function( value ) {
    		value.bind( function( to ) {
    			api.preview.send( 'update_gradient' , api.currentCssSelector , to );
    		});
        });*/

        /*parallax = $.map(['ratio' , 'image'], function( prop ) {
        	return 'parallax_background_' + prop;
        });

        var initParallax = {};
        api.when.apply( api, parallax ).done( function( ratio, image ) {
        	var update , ratioNum;


        	update = function() {
                if( !api.currentCssSelector )
                    return ;

        		if ( !_.isUndefined( image() ) && image()  ){
                    _saveCustomCss( "parallax_background_image" , image() );
                    if( !_.isUndefined( ratio() ) && ratio() )
                        ratioNum = ratio();
                    else
                        ratioNum = 0.5;

                    if( !_.isUndefined( initParallax[api.currentCssSelector] ) && initParallax[api.currentCssSelector] === true )
                        $( api.currentCssSelector ).parallax.destroy();

                    $( api.currentCssSelector ).parallax("50%", ratioNum );
                    _saveCustomCss( "background_attachment" , attachment() );

                    initParallax[api.currentCssSelector] = true;
                }else{
                    $( api.currentCssSelector ).parallax.destroy();
                    initParallax[api.currentCssSelector] = false;
                }

                if( !_.isUndefined( ratio() ) && ratio() ){
                    _saveCustomCss( "parallax_background_ratio" , ratio() );
                }

            };

          	$.each( arguments, function() {
          		this.bind( update );
          	});
        });*/

        /* Custom Backgrounds */
        /*bg = $.map(['color', 'image', 'position', 'image_scaling', 'attachment', 'gradient'], function( prop ) {
        	return 'background_' + prop;
        });

        api.when.apply( api, bg ).done( function( color, image, position, image_scaling, attachment, gradient ) {
        	var update ,
                bgSize = "auto",
                bgRepeat = "no-repeat", bgImg,
                head = $('head'), bgColor;


        	update = function() {
                if( !api.currentCssSelector )
                    return ;

        	    var css = '',
                      styleId ,
                      //elementClass = 'sed-custom-' + api.currentCssSelector,
                      element = $( api.currentCssSelector ) ,
                      sGradient = !_.isUndefined( gradient() ) && gradient() ,
                      bgImage  = !_.isUndefined( image() ) && image() && image() != "none";

                styleId = _getStyleId( element , 'sed_background' , 'background');

                      //element.toggleClass( elementClass , !! ( color() || image() ) )

        		// The body will support custom backgrounds if either
        		// the color or image are set.

        		if ( !_.isUndefined( color() ) ){
                    css += 'background-color: ' + color() + ' !important;';
                    _saveCustomCss( "background_color" , color() );
                }

                if( !_.isUndefined( image() ) )
                    _saveCustomCss( "background_image" , image() );

        		if ( !_.isUndefined( image() ) ){ //&& !sGradient

        		    if( !image() || image() == "none" )
        			    css += 'background-image: none !important;';
                    else{
                        var img = api.fn.getAttachmentImage( image() , "full" ),
                            src = !_.isUndefined( img ) && !_.isUndefined( img.src ) ? img.src : "" ;

                        css += 'background-image: url("' + src + '") !important;';
                    }

                }else if ( _.isUndefined( image() ) && !_.isUndefined( gradient() ) && !gradient() )
                    css += 'background-image: none !important;';


                /*else if( !bgImage && sGradient )
                    css += siteEditorCss.gradient( gradient().start , gradient().end , gradient() );
                else if( bgImage &&  sGradient )
                    css += siteEditorCss.gradient( gradient().start , gradient().end , gradient() , image() ) ;
                */
                /*if( gradient() ) {
                    api.preview.send( 'update_gradient' , api.currentCssSelector , color() );
                }*/ /*

        		if ( bgImage ) {

                    if ( !_.isUndefined( position() ) ){
        			    css += 'background-position: ' + position() + ';';
                    }

                    if ( !_.isUndefined( attachment() ) ){
                        css += 'background-attachment: ' + attachment() + ' !important;';
                    }

                    if( image_scaling() ) {
                        switch ( image_scaling() ) {
                           case "fullscreen":
                                bgSize = "100% 100%";
                           break;
                           case "fit":
                                bgSize = "100% auto";
                                bgRepeat = "repeat-y";
                           break;
                           case "tile":
                                bgSize = "auto";
                                bgRepeat = "repeat";
                           break;
                           case "tile-horizontally":
                                bgSize = "auto";
                                bgRepeat = "repeat-x";
                           break;
                           case "tile-vertically":
                                bgSize = "auto";
                                bgRepeat = "repeat-y";
                           break;
                           case "normal":
                                bgSize = "auto";
                                bgRepeat = "no-repeat";
                           break;
                           case "cover":
                                bgSize = "cover";
                                //bgRepeat = "no-repeat";
                           break;
                        }

                        css += 'background-repeat: ' + bgRepeat + ' !important;';
                        css += siteEditorCss.backgroundSize( bgSize );
                    }


        		}

                if( gradient() || image_scaling())
                    css += "behavior: url(" + _sedAssetsUrls.base.js + "/PIE/PIE.htc" + ");";

                if( image_scaling() )
                    _saveCustomCss( "background_image_scaling" , image_scaling() );

                if ( !_.isUndefined( attachment() ) )
                    _saveCustomCss( "background_attachment" , attachment() );

                if ( !_.isUndefined( position() ) )
                    _saveCustomCss( "background_position" , position() );

        		// Refresh the stylesheet by removing and recreating it.
        		$( "#" + styleId ).remove();
        		style = $('<style type="text/css" id="' + styleId + '">' + api.currentCssSelector + '{ ' + css + ' }</style>').appendTo( head );

                if( !_.isUndefined( image() )  && api('parallax_background_image')() ){
                    //$(window).stellar('refresh');
                }

        	};

        	$.each( arguments, function() {
        		this.bind( update );
        	});
        }); */

        /* Custom Borders */
        /*$.each( [ "top" , "left" , "right" , "bottom" ], function(idx , side){
            var bordersArr = $.map(['style' , 'width' , 'color' ], function( prop ) {
            	return 'border_'+ side + '_' + prop;
            });

            api.when.apply( api, bordersArr ).done( function( style , width , color ) {
            	var update , head = $('head') ;
                update = function(index) {
                    if( !api.currentCssSelector )
                        return ;

            	    var css = '',
                        styleId ,
                        element = $( api.currentCssSelector ),
                        newSide = side;
                        //widthEl = element.outerWidth() ,
                        //borderSides = currentBorderSides

                    if( api.isRTL && side == "left" )
                        newSide = "right";
                    else if( api.isRTL && side == "right" )
                        newSide = "left";


                    styleId = _getStyleId( element , 'sed_border_' + side , 'border_' + side );

                    if( !_.isUndefined( width() ) ){
                        css += "border-" + newSide + "-width :" + width() + "px !important;";
                        //if(val == "right" || val == "left")
                            //widthEl -= width();
                    }

                    if( !_.isUndefined( style() ) )
                        css += "border-" + newSide + "-style :" + style() + " !important;";

                    if( !_.isUndefined( color() ) )
                        css += "border-" + newSide + "-color :" + color() + " !important;";


                    //css += "max-width : " + widthEl + "px !important;";

            		// Refresh the stylesheet by removing and recreating it.
            		$( "#" + styleId ).remove();
            		style2 = $('<style type="text/css" id="' + styleId + '">' + api.currentCssSelector + '{ ' + css + ' }</style>').appendTo( head );

                    if($(api.currentCssSelector).attr("sed-layout") == "row"){
                        $(document).trigger("rowCheckBorder" , [api.currentCssSelector]);
                    }
                };

            	$.each( arguments, function(index , value) {
            		this.bind( function(){
                        update( index );
            		});
            	});
            });
        });*/

        /* Custom Margins */
        /*margins = $.map(['top' , 'right' , 'bottom' , 'left' , 'lock'], function( prop ) {
        	return 'margin_' + prop;
        });

        api.when.apply( api, margins ).done( function( top , right , bottom , left , lock ) {
        	var update , head = $('head') ,
                lastValue , width;
            update = function(index) {
                if( !api.currentCssSelector )
                    return ;

        	    var css = '', styleId ,
                      //elementClass = 'sed-custom-' + api.currentCssSelector,
                    element = $( api.currentCssSelector ) , propStr;

                styleId = _getStyleId( element , 'sed_margin' , 'margin');

        		if ( !_.isUndefined( top() ) )
                    css += "margin-top : " + top()+ "px !important;";

        		if ( !_.isUndefined( right() ) ){
        		    propStr = ( api.isRTL ) ? "margin-left : " : "margin-right : ";
                    css += propStr + right()+ "px !important;";
                }

        		if ( !_.isUndefined( bottom() ) )
                    css += "margin-bottom : " + bottom()+ "px !important;";

        		if ( !_.isUndefined( left() ) ){
        		    propStr = ( api.isRTL ) ? "margin-right : " : "margin-left : ";
                    css += propStr + left()+ "px !important;";
                }

        		$( "#" + styleId ).remove();
        		style = $('<style type="text/css" id="' + styleId + '">' + api.currentCssSelector + '{ ' + css + ' }</style>').appendTo( head );

            };

        	$.each( arguments, function(index , value) {
        		this.bind( function(){
                    update( index );
        		});
        	});
        });*/

        /*$.each(api.templateSettings.patterns.rows , function(index , val){
            rowTmpl[val.role] = val;
        });*/

        //draggable modules in modules tab and drop in page content
        /*parent.jQuery( ".sed-draggable" ).sedDraggable({
            scrollSensitivity : 20,
            scrollSpeed : 20,
            dropInSortable: ".sed-drop-area-layout",
            iframeSortable: "website",
            placeholder:"<div class='sed-state-highlight-row'>test...</div>",
            dragStart : function(event , element, helper){
                parent.jQuery("#iframe_cover").show();
            },
            stop : function(event , element, helper){
                parent.jQuery("#iframe_cover").hide();
            },
            sortStop : function(event,ui) {
                var $numRows, $topArea, $botArea, $topMcArea, $botMcArea,
                    $area, $rowElement, $rowId, $tmpl, rowIndex = 0 , $start , $rowDrag;

                $topArea = $('#sed-header-drop-area');
                $botArea = $('#sed-footer-drop-area');
                $topMcArea = $('#sed-top-mcontent-drop-area');
                $botMcArea = $('#sed-bot-mcontent-drop-area');

                $numRows = $topArea.children().length + $botArea.children().length + $topMcArea.children().length + $botMcArea.children().length;
                switch (ui.sortable.attr("id")) {
                  case 'sed-header-drop-area':
                    $area = "top";
                  break;
                  case 'sed-footer-drop-area':
                    $area = "bottom";
                  break;
                  case 'sed-top-mcontent-drop-area':
                     $area = "top-mc";
                  break;
                  case 'sed-bot-mcontent-drop-area':
                     $area = "bot-mc";
                  break;
                  default :
                     $area = "";
                }

                //$tmpl = rowTmpl["custom"].start + rowTmpl["custom"].end;
                                //alert( $(ui.handle).attr("sed-draggable-element") );
                $rowDrag = $(ui.handle).attr("sed-draggable-element") || "false";
                $rowId = "sed-custom-main-row" + $numRows;
                $tmpl = $tmpl.replace("{{id}}" , $rowId);
                $tmpl = $tmpl.replace("{{area}}" , $area);
                $tmpl = $tmpl.replace("{{draggable}}" , $rowDrag);

                if(ui.direction == "down"){
                    $rowElement = $( $tmpl ).insertBefore( ui.item );
                }else{
                    $rowElement = $( $tmpl ).insertAfter( ui.item );
                }

                ui.sortable.children().each(function(index ,element){
                    if($rowElement[0] == $(this)[0]){
                        rowIndex = index;
                    }
                });

                /*
                $start = rowTmpl["custom"].start
                $start = $start.replace("{{id}}" , $rowId);
                $start = $start.replace("{{area}}" , $area);
                $start = $start.replace("{{draggable}}" , $rowDrag);

                $rowElement.attr("sed-row-area", $area);
                $rowElement.attr("id" , $rowId);
                api.preview.send( 'create_main_row' , {
                    id        : $rowId ,
                    dropArea  : $area,
                    index     : rowIndex,
                    content   : '' ,
                    role      : "custom" ,
                    start     :  $start,
                    end       :  rowTmpl["custom"].end,
                    sync      : true ,
                    type      : "custom" ,
                    attr      : {}
                });

                $(document).trigger("update_sort_row");*/ /*
            }
        }); */

        //parent.jQuery( ".sed-draggable" ).sedDraggable({

        //});
           //data-type-row="draggable-element"
        /*$.each(api.templateSettings.patterns.cols , function(index , val){
            colTmpl[val.role] = val;
        });*/

        /*parent.jQuery( ".sed-col-draggable" ).sedDraggable({
            scrollSensitivity : 20,
            scrollSpeed : 20,
            axis : 'x',
            dropInSortable: '[sed-role="main-content"] > .columns-row-inner',
            iframeSortable: "website",
            placeholder:"<div class='sed-state-highlight-col'>test...</div>",
            dragStart : function(event , element, helper){
                parent.jQuery("#iframe_cover").show();
            },
            stop : function(event , element, helper){
                parent.jQuery("#iframe_cover").hide();
            },
            sortStop : function(event,ui) {
                var $numCols, $colElement, $colId, $tmpl, colIndex = 0 , $start , $colDrag;

                //$tmpl = colTmpl["custom"].start + colTmpl["custom"].end;
                                //alert( $(ui.handle).attr("sed-draggable-element") );
                $numCols = $('[sed-layout="column"]').length;
                $colId = "sed-custom-main-col" + $numCols;
                $tmpl = $tmpl.replace("{{id}}" , $colId);

                var t = Math.floor( 100 / $numCols );
                var ncw = 100 - ($numCols * t);
                $('[sed-layout="column"]').each( function (i,el) {
                    var newW = $(this).width() - t;
                    $(this).css("width", newW + "px");
                    api.preview.send( 'update_col_width' , {id : $(this).attr("sed_model_id"),width : newW/columnRowWidth});
                });

                if(ui.direction == "down"){
                    $colElement = $( $tmpl ).insertBefore( ui.item );
                }else{
                    $colElement = $( $tmpl ).insertAfter( ui.item );
                }

                $($colElement).css("width", (100 + ncw) + "px");

                ui.sortable.children().each(function(index ,element){
                    if($colElement[0] == $(this)[0]){
                        colIndex = index;
                    }
                });

                /*$start = colTmpl["custom"].start
                $start = $start.replace("{{id}}" , $colId);

                $colElement.attr("id" , $colId);
                api.preview.send( 'create_main_col' , {
                    id        : $colId ,
                    index     : colIndex,
                    content   : '' ,
                    role      : "custom" ,
                    start     :  $start,
                    end       :  colTmpl["custom"].end,
                    sync      : true ,
                    type      : "custom" ,
                    attr      : {},
                    settings  : {width : (100 + ncw)/columnRowWidth}
                });

                $(document).trigger("update_sort_col"); */ /*
            }
        });  */

    /*var _getStyleId = function( element , type , prop ){

        var styleId;

        if( _.isUndefined( styleIdElements[api.currentCssSelector] ) || _.isUndefined( styleIdElements[api.currentCssSelector][prop] ) ){
            styleId = _.uniqueId( type + '_') + '_css';

            if( _.isUndefined( styleIdElements[api.currentCssSelector] ) )
                styleIdElements[api.currentCssSelector] = {} ;

            styleIdElements[api.currentCssSelector][prop] = styleId;
        }else
            styleId = styleIdElements[api.currentCssSelector][prop] ;

        return styleId;

       /* if( !_.isString( type ) || element.length == 0 )
            return ;

        if( _.isUndefined( element.data( "styleId" ) ) || _.isUndefined( element.data( "styleId" )[prop] ) ){
            styleId = _.uniqueId( type + '_') + '_css';

            if( _.isUndefined( element.data( "styleId" ) ) )
                element.data( "styleId" , {} );

            element.data( "styleId" )[prop] = styleId;
        }else
            styleId = element.data( "styleId" )[prop];

        return styleId; */ /*

    }; */

        api.trigger( 'preview-ready' );

	});

})( sedApp, jQuery );
