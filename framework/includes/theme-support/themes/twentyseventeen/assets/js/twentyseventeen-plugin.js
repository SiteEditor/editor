/**
 * app-preset-plugin.js
 *
 * Copyright, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://www.siteeditor.org/license
 * Contributing: http://www.siteeditor.org/contributing
 */

/*global siteEditor:true */
(function( exports, $ ){

	var api = sedApp.editor;

    var last_header_textcolor = '';
    
    api.addFilter( "header_textcolor_set" , function( value , controlId ){

        if( controlId == 'twenty_seventeen_header_display_header_text' ) {
            
            if ( ! value ) {
                last_header_textcolor = api('header_textcolor').get();
            }
            
            value = value ? last_header_textcolor : 'blank';

        }

        return value;

    });

    // Juggle the two controls that use header_textcolor
    api.control( 'twenty_seventeen_header_display_header_text', function( control ) {

        control.update( 'blank' !== control.setting() );

        control.setting.bind( function( to ) {
            control.update( 'blank' !== to );
        });
        
    });

    $( function() {



    });

})( sedApp, jQuery );