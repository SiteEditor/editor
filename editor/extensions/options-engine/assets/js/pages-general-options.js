/**
 * SiteEditor Posts JS Plugin
 *
 * Copyright, 2016
 * Released under LGPL License.
 *
 * License: http://www.siteeditor.org/license
 * Contributing: http://www.siteeditor.org/contributing
 */

/*global siteEditor:true */
(function( exports, $ ){

    var api = sedApp.editor;

    api.SiteEditorPosts = api.Class.extend({
        /*
         @pageType : post || term ( || general || authors || post_type )
         */
        initialize: function (options) {

            $.extend( this, options || {} );

        }

    });

    $( function() {



    });

})( sedApp, jQuery );