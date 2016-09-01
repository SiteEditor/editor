/**
 * plugin.js
 *
 *
 * License: http://www.siteeditor.org/license
 * Contributing: http://www.siteeditor.org/contributing
 */

/*global siteEditor:true */
siteEditor.PluginManager.add('themeSynchronization', function(siteEditor) {

  var api = siteEditor.sedAppClass.editor , $ = siteEditor.dom.Sizzle;
  //previewer = siteEditor.siteEditorControls;
  ////api.log( siteEditor );
  ////api.log( sedApp.editor === api );
  ////api.log( siteEditor.dom.Sizzle === jQuery );
  ////api.log( sedApp );
  ////api.log( jQuery );
  ////api.log( siteEditor );

  $( function() {

      api.previewer.bind( 'theme_synchronization', function( value ) {
          var syncSetting = api.instance("theme_synchronization");
          syncSetting.set( value );
      });


      api.previewer.bind( 'pageWidgetsList', function( value ) {
          var setting = api.instance("page_widgets_list");
          setting.set( value );
      });

  });

});