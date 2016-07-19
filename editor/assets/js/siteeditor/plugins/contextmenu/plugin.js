/**
 * plugin.js
 *
 *
 * License: http://www.siteeditor.org/license
 * Contributing: http://www.siteeditor.org/contributing
 */

/*global siteEditor:true */
(function( exports, $ ){

  var api = sedApp.editor ,
      _directlyTransition;

  api.currentCssSelector = api.currentCssSelector || "";
  //handels of all loaded scripts in siteeditor app
  api.sedAppLoadedScripts = api.sedAppLoadedScripts || [];
  //previewer = siteEditor.siteEditorControls;
  ////api.log( siteEditor );
  ////api.log( sedApp.editor === api );
  ////api.log( siteEditor.dom.Sizzle === jQuery );
  ////api.log( sedApp );
  ////api.log( jQuery );
  ////api.log( siteEditor );

  $( function() {
		api.settings = window._sedAppEditorSettings;
        api.wpScripts = window._wpRegisteredScripts;
        api.widgetScripts = window._sedAppWidgetScripts;
		api.l10n = window._sedAppEditorControlsL10n;
        //api.paramsSettingsValid = window._paramsSettingsValid;

		// Check if we can run the customizer.
		if ( ! api.settings )
			return;


		// Redirect to the fallback preview if any incompatibilities are found.
		if ( ! $.support.postMessage || (  api.settings.isCrossDomain ) )  //! $.support.cors &&
			return window.location = api.settings.url.fallback;

        /*api.previewer.bind( 'contextmenu-ready', function() {  //html : $("#tmpl-contextmenu").html() ,
            api.previewer.send( 'contextmenu', { settingsValid: api.paramsSettingsValid } );
        });*/

        api.Events.bind("sedDialogWebAddress" , function(data){

        });


        var dialogCtxtLoaded = [];

        var _elementOpenDialog = function( sedDialog ){

            var tmplType = (typeof sedDialog.data.dialogTmplType != "undefined") ? sedDialog.data.dialogTmplType : "static";

            if($.inArray( sedDialog.selector , dialogCtxtLoaded) == -1 ){

                dialogCtxtLoaded.push( sedDialog.selector );

                $( sedDialog.selector ).dialog( sedDialog.options.dialog_options  );

                if( tmplType == "static" ){
                    $( sedDialog.selector ).html( $("#" + sedDialog.tmpl).html() );
                    if(typeof sedDialog.options.dialog_options.title == "undefined")
                        $( sedDialog.selector ).dialog( "option" , "title", $("#" + sedDialog.tmpl).attr("data-dialog-title"));
                }

            }

            if( tmplType == "dynamic" ){
                $( sedDialog.selector ).find(".tmpl-dialog-module-settings").hide();
                //$( sedDialog.selector ).html( $("#" + sedDialog.tmpl).html() );
                $("#" + sedDialog.tmpl).show();
                if(typeof sedDialog.options.dialog_options.title == "undefined" && sedDialog.id != "sedDialogSettings" )
                    $( sedDialog.selector ).dialog( "option" , "title", $("#" + sedDialog.tmpl).attr("data-dialog-title"));
            }


            $( sedDialog.selector ).dialog( "open" );

            var extra = $.extend({} , sedDialog.extra || {});     

            api.Events.trigger(sedDialog.id , [sedDialog.data] , extra , sedDialog);
        };

        api.Events.bind( 'element_open_dialog', function( sedDialog ) {
            _elementOpenDialog( sedDialog );
        });

        api.previewer.bind( 'element_open_dialog', function( sedDialog ) {
            _elementOpenDialog( sedDialog );
        });

	});

})( sedApp, jQuery );