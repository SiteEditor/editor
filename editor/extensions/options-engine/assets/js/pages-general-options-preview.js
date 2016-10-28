(function( exports, $ ){

    var api = sedApp.editor;

    $( function() {

        var _generalOptionsData = window._sedAppPreviewPagesGeneralSettings;

        api.preview.bind( 'active', function() {

            var settings = {};

            _.extend( api.previewPosts.data, window._sedAppPreviewPostsData );

            $.each( api.settings.values , function( id , value ) {

                var settingProperties = _generalOptionsData.settings[ id ];

                if ( ! api.has( id ) || ! settingProperties ) {
                    return;
                }

                settings[ id ] = {
                    value: value,
                    //dirty: Boolean( api.settings._dirty[ setting.id ] ),
                    type: settingProperties.type || "general",
                    transport: settingProperties.transport ,
                    option_type : settingProperties.option_type
                };

            } );

            api.preview.send( 'syncGeneralOptionsData', {
                settings: settings
            } );

        });

    });


})( sedApp, jQuery );