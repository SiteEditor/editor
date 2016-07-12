/**
 * siteEditorControls.js
 *
 *
 * License: http://www.siteeditor.org/license
 * Contributing: http://www.siteeditor.org/contributing
 */

/*global diagram:true */
(function( exports, $ ){
	var api = sedApp.editor;

    api.control( 'style_editor_parallax_background_image' , function( control ) {

        control.setting.bind( function( to ) {
            if( to ){
                var control = api.control.instance( 'style_editor_background_attachment' );
                control.update( "fixed" );
                control.refresh( "fixed" );
            }
        });

    });

    /*var defaultBGColors = {};
    api.fn.gradientGetStartEndColor = function( control , color ){

        if( _.isUndefined( color ) ){
            if( !_.isUndefined( api.instance("background_color").get()[control.targetElement] ) )
                color = api.instance("background_color").get()[control.targetElement];
            else{
                if( !_.isUndefined( defaultBGColors[control.targetElement ] ) ){
                    color = defaultBGColors[control.targetElement ];
                }else{
                    color = $("#website")[0].contentWindow.jQuery( control.targetElement ).css("background-color");
                    defaultBGColors[control.targetElement ] = color;
                }
            }
        }

        var tColor = tinycolor( color ) ,
            endColor = siteEditorCss.gradientEndColor( tColor.toRgb() ) ,
            startColor = tColor.toHexString();

        startColor =  siteEditorCss.hexToRgb( startColor );
        startColor = 'rgb(' + startColor.r + ',' + startColor.g + ',' + startColor.b + ')';

        return {
            start   : startColor,
            end     : endColor
        };
    };*/

    //sync && update gradient after change background color
    /*_.each(['general_style_editor_background_color','style_editor_background_color'] , function( sControl ){
        api.control( sControl , function( control ) {

        	control.setting.bind( function( to ) {

        	    if( !_.isUndefined( api.instance("background_gradient").get()[control.targetElement] ) ){

                    var gColor = api.fn.gradientGetStartEndColor( control , to[control.targetElement] ) ,
                        startColor  =  gColor.start,
                        endColor    =  gColor.end ,
                        $thisValue  =  _.clone( api.instance("background_gradient").get() );

                    if( $thisValue[control.targetElement].start == startColor && $thisValue[control.targetElement].end == endColor )
                        return ;

                    $thisValue[control.targetElement].start = startColor;
                    $thisValue[control.targetElement].end = endColor;

     api( "background_gradient" ).set( $thisValue );

                    /*    gId         =  "style_editor_gradient" ,
                        gControl = api.control.instance( gId );


                    if( $.inArray(gId , api.appModulesSettings.initControls) == -1 ){

                        api.Events.trigger( "renderSettingsControls" , gId, api.settings.controls[gId] );
                        api.appModulesSettings.initControls.push( id );

                        var control = api.control.instance( id );
                    }

                    $thisValue[control.targetElement].start = startColor;
                    $thisValue[control.targetElement].end = endColor;

                    gControl.currentValue = $thisValue[control.targetElement];

                    gControl.previewer.currentSelector = control.targetElement;

                    gControl.setting.set( $thisValue );

                    if( !_.isUndefined( gControl.params.shortcode ) && !_.isUndefined( gControl.params.attr_name ) )
        	            api.Events.trigger("moduleControlRefresh" , gControl.params.shortcode , gControl.params.attr_name , gControl.currentValue );
                    */   /*
                }

        	});

        });
    }); */

	/* =====================================================================
	 * Ready.
	 * ===================================================================== */

})( sedApp, jQuery );
