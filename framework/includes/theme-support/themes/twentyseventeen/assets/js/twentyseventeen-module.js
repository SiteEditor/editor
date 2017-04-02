(function( exports, $ ) {

    var api = sedApp.editor ;

    $( function() {

        api.twentySeventeenDynamicCssSettings = window._sedTwentySeventeenDynamicCssSettings;

        api.colorSchemeSettings = window._sedColorSchemeSettings;

        api( "sed_disable_header" , function( value ) {

            value.bind( function( to ) {

                if( !to ){
                    $( "#masthead" ).removeClass("hide");
                }else{
                    $( "#masthead" ).addClass("hide");
                }

            });

        });

        api( "sed_disable_footer" , function( value ) {

            value.bind( function( to ) {

                if( !to ){
                    $( "#colophon" ).removeClass("hide");
                }else{
                    $( "#colophon" ).addClass("hide");
                }

            });

        });

        api( "sed_footer_columns" , function( value ) {

            value.bind( function( to ) {

                to = parseInt( to );

                to = to < 2 || to > 4 ? 2 : to;

                $( "#colophon .widget-area" ).addClass( "footer-widget-area-" + to );

                var prevCols = parseInt( $( "#colophon .widget-area" ).data("numColumns") );

                $( "#colophon .widget-area" ).removeClass( "footer-widget-area-" + prevCols );

                $( "#colophon .widget-area" ).data("numColumns" , to );

                if( to > 2 ){
                    $( "#colophon .widget-area .footer-widget-3" ).removeClass("hide");
                    $( "#colophon .widget-area .footer-widget-3" ).prev().removeClass("hide");
                }else{
                    $( "#colophon .widget-area .footer-widget-3" ).addClass("hide");
                    $( "#colophon .widget-area .footer-widget-3" ).prev().addClass("hide");
                }

                if( to == 4 ){
                    $( "#colophon .widget-area .footer-widget-4" ).removeClass("hide");
                    $( "#colophon .widget-area .footer-widget-4" ).prev().removeClass("hide");
                }else{
                    $( "#colophon .widget-area .footer-widget-4" ).addClass("hide");
                    $( "#colophon .widget-area .footer-widget-4" ).prev().addClass("hide");
                }

            });

        });


        api( "sed_copyright_text" , function( value ) {

            value.bind( function( to ) {

                $( "#colophon .site-info a" ).text( to );

            });

        });


        api( "sed_header_title_type" , function( value ) {

            value.bind( function( to ) {

                if( to == "default" ) {

                    $(".site-title a").text( api( 'blogname' )() );

                    $( ".site-description" ).text( api( 'blogdescription' )() );

                }else{

                    $( "#masthead .site-branding .site-title a" ).text( api( 'sed_custom_header_title' )() );

                    $( "#masthead .site-branding .site-description" ).text( api( 'sed_custom_header_sub_title' )() );

                }

            });

        });


        api( "blogname" , function( value ) {

            value.bind( function( to ) {

                $( ".site-title a" ).text( to );

            });

        });

        api( "blogdescription" , function( value ) {

            value.bind( function( to ) {

                $( ".site-description" ).text( to );

            });

        });

        api( "sed_custom_header_title" , function( value ) {

            value.bind( function( to ) {

                $( "#masthead .site-branding .site-title a" ).text( to );

            });

        });

        api( "sed_custom_header_sub_title" , function( value ) {

            value.bind( function( to ) {

                $( "#masthead .site-branding .site-description" ).text( to );

            });

        });

        // Whether a header image is available.
        function hasHeaderImage() {
            var image = api( 'header_image' )();
            return '' !== image && 'remove-header' !== image;
        }

        // Whether a header video is available.
        function hasHeaderVideo() {
            var externalVideo = api( 'external_header_video' )(),
                video = api( 'header_video' )();

            return '' !== externalVideo || ( 0 !== video && '' !== video );
        }

        // Toggle a body class if a custom header exists.
        $.each( [ 'external_header_video', 'header_image', 'header_video' ], function( index, settingId ) {
            api( settingId, function( setting ) {
                setting.bind(function() {
                    if ( hasHeaderImage() ) {
                        $( document.body ).addClass( 'has-header-image' );
                    } else {
                        $( document.body ).removeClass( 'has-header-image' );
                    }

                    if ( ! hasHeaderVideo() ) {
                        $( document.body ).removeClass( 'has-header-video' );
                    }
                } );
            } );
        } );


        // Page layouts.
        api( 'page_layout', function( value ) {
            value.bind( function( to ) {
                if ( 'one-column' === to ) {
                    $( 'body' ).addClass( 'page-one-column' ).removeClass( 'page-two-column' );
                } else {
                    $( 'body' ).removeClass( 'page-one-column' ).addClass( 'page-two-column' );
                }
            } );
        } );

        // Header text color.
        api( 'header_textcolor', function( value ) {
            value.bind( function( to ) {
                if ( 'blank' === to ) {
                    $( '.site-title, .site-description' ).css({
                        clip: 'rect(1px, 1px, 1px, 1px)',
                        position: 'absolute'
                    });
                    // Add class for different logo styles if title and description are hidden.
                    $( 'body' ).addClass( 'title-tagline-hidden' );
                } else {

                    // Check if the text color has been removed and use default colors in theme stylesheet.
                    if ( ! to.length ) {
                        $( '#twentyseventeen-custom-header-styles' ).remove();
                    }
                    $( '.site-title, .site-description' ).css({
                        clip: 'auto',
                        position: 'relative'
                    });
                    $( '.site-branding, .site-branding a, .site-description, .site-description a' ).css({
                        color: to
                    });
                    // Add class for different logo styles if title and description are visible.
                    $( 'body' ).removeClass( 'title-tagline-hidden' );
                }
            });
        });

        /**
         * Dynamic Css Preview
         * Override Default Site Editor Color Scheme Preview
         */

        console.log( "-----api.colorSchemeSettings----" , api.colorSchemeSettings );

        $('<style type="text/css" id="sed_twenty_seventeen_dynamic_css"></style>').insertAfter( $('#sed_color_scheme_css_code') );

        var _getColorSchemeType = function(){

            return api.has( 'sed_color_scheme_type' ) ? api( 'sed_color_scheme_type' )() : api.colorSchemeSettings.type;

        };

        var _getColorSchemeSkin = function(){

            return api.has( 'sed_color_scheme_skin' ) ? api( 'sed_color_scheme_skin' )() : api.colorSchemeSettings.currentSkin;

        };

        var _getCssVars = function(){

            var _varsTpl = {};

            $.each( api.twentySeventeenDynamicCssSettings.variables , function( _key , option ){
                _varsTpl[_key] = api.has( option.settingId ) ? api( option.settingId )() : option.value;
            });

            $i = 0;

            var skin = _getColorSchemeSkin(); 

            //Add Color Scheme Vars
            $.each( api.colorSchemeSettings.customize , function( _key , _settingId ){

                if( _getColorSchemeType() == "customize" ) {

                    _varsTpl[_key] = api.has(_settingId) ? api(_settingId)() : api.colorSchemeSettings.currents[_key];

                }else{

                    if( skin == "default" || _.isUndefined( api.colorSchemeSettings.skin[skin] ) ){

                        _varsTpl[_key] = api.colorSchemeSettings.defaults[_key];

                    }else{

                        _varsTpl[_key] = api.colorSchemeSettings.skin[skin][$i] || '';

                        $i++;

                    }

                }


            });

            //Add Sheet Width && Page Length vars
            _varsTpl['sheet_width'] = api( 'sheet_width' )();

            //_varsTpl['page_length'] = api.pagesOptionsPreview.getPageSetting( 'page_length' );

            return _varsTpl;

        };

        var _setDynamicCss = function(){

            var _varsTpl = _getCssVars();

            var template = api.template("tmpl-sed-twentyseventeen-dynamic-css"),
                html = template( _varsTpl );

            $("#sed_twenty_seventeen_dynamic_css").html( html );

        };

        $.each( api.twentySeventeenDynamicCssSettings.variables , function( key , option ){

            api( option.settingId , function( setting ) {

                setting.bind( function( to ) {

                    _setDynamicCss();

                });

            });

        });

        api( 'sed_color_scheme_type', function( value ) {
            value.bind( function( to ) {

                _setDynamicCss();

            });
        });

        api( 'sed_color_scheme_skin', function( value ) {
            value.bind( function( to ) {

                if( _getColorSchemeType() == "skin" ){

                    _setDynamicCss();

                }

            });
        });

        $.each( api.colorSchemeSettings.customize , function( key , settingId ){

            api( settingId , function( value ) {

                value.bind( function( to ) {

                    _setDynamicCss();

                });

            });

        });

    });

}(sedApp, jQuery));