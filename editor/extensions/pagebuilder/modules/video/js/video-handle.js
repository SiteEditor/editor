(function( exports, $ ) {
    var api = sedApp.editor;

$( document ).ready( function (  ) {

	$('.video-module').livequery(function(){
    	var $this            = $(this),
            track_options    = {},
            //this_id          = $this.attr("sed_model_id") ,    
            container_id     = $this.find('.sed_jp_container').attr('id'), 
            player_id        = $this.find('.sed_jp_jplayer').attr('id'),  
    		item_data_attr   = [
        	    "title","m4v","ogv","flv","webmv","poster"
			],
            $this_item = $this.find('.sed_playlist_item_info');

        $.each( item_data_attr , function( key , value ){
            var val =  $this_item.data( "video" + api.fn.ucfirst( value ) );

            track_options[value] = val;

        });

        options  = {
            cssSelectorAncestor : "#" + container_id ,
            swfPath: "js",
            wmode: "window",
            smoothPlayBar: true,
            keyEnabled: true,
            remainingDuration: true,
            toggleDuration: true,
            verticalVolume: true,
            preload  : $this_item.data("videoPreload"),
            loop  : $this_item.data("videoLoop"),
            supplied: "m4v , ogv , webmv , flv , rtmpv",
            size: {
                width: $this_item.data("videoWidth") + "px",
                height: $this_item.data("videoHeight") + "px",
                cssClass: "jp-video-" + $this_item.data("videoHeight") + "p"
            },
            ready: function (event) {
                $(this).jPlayer( "setMedia", track_options );

                if( $this_item.data( "videoAutoplay" ) )
                    $( "#" + player_id ).jPlayer("play");
            },

            setmedia: function(e) {
                var jp       = $('#' + player_id ), jpData = jp.data('jPlayer');
                var jpNum    = jpData.status.media.num;
                var jpTitle  = jpData.status.media.title;
                var jpPoster = jpData.status.media.poster;

                $this.find('.jp-playing-poster > .img').html('<img src="' + jpPoster + '" alt="' + jpTitle + '">');
                $this.find('.jp-playing-title').html(jpTitle);
                $this.find('.jp-playing-num').html(jpNum);

                //alert( $this_item.parents('[data-sed-role="masonry"]').length );
                //$this_item.parents('[data-sed-role="masonry"]').masonry();
            }

        };

        $('#' + player_id ).jPlayer(options);

        $('#' + player_id ).bind($.jPlayer.event.loadeddata, function() {
            if( $this_item.parents('[data-sed-role="masonry"]').length > 0 )
                $this_item.parents('[data-sed-role="masonry"]').masonry();
        });

        $('#' + player_id ).bind($.jPlayer.event.click, function(event) {
            //console.log(event.jPlayer.status);//event.jPlayer.status.currentTime>0 &&
            if (event.jPlayer.status.paused === false) {
                // Its playing right now
                $(this).jPlayer("pause");
                $(this).find('a.jp-play > i').toggleClass('fa-pause fa-play');
            } else {
                $(this).jPlayer("play");
                $(this).find('a.jp-play > i').toggleClass('fa-play fa-pause');
            }
        });

        /*
        @Site Editor pakage
        Edit By SiteEditor
        for column resize
        */

        var _responsiveVideo = function( el ){
            if( $(el).width() < 550 || $this_item.data("videoWidth") < 550 ){
                $(el).addClass("video-resize-responsive");
            }else{
                $(el).removeClass("video-resize-responsive");
            }
        };

        _responsiveVideo( this );

        $(this).on("sed.moduleResize sed.moduleResizeStop" , function(){
            _responsiveVideo( this );
        });

        /*
        @Site Editor pakage
        Edit By SiteEditor
        for module sortable(darg & drop)
        */
        $(this).on("sed.moduleSortableStop sedAfterRemoveColumns" , function(){
            _responsiveVideo( this );
        });


        $(this).parents(".sed-pb-module-container:first").on( "sedChangeModulesLength", function( e , length ){
            _responsiveVideo( $(this).find(".sed-pb-module-container:first") );
        });

        $(this).parents(".sed-pb-module-container:first").on( "sedChangedSheetWidth", function(){
            if( $(this).parents(".sed-row-boxed").length > 0 ){
                _responsiveVideo( $(this).find(".sed-pb-module-container:first") );
            }
        });

        $(this).parents(".sed-pb-module-container:first").on( "sedChangedPageLength", function( e , length ){
            if( ($(this).parents(".sed-row-boxed").length == 0 && length == "wide" ) || ($(this).parents(".sed-row-boxed").length == 1 && length == "boxed" ) ){
                _responsiveVideo( $(this).find(".sed-pb-module-container:first") );
            }
        });


    }); // END LIVEQUERY
});

}(sedApp, jQuery));
/*item_valueAttr       = $this_item.attr(item_Attr);

if( typeof(item_valueAttr) !== 'undefined' ){
    switch(item_valueAttr){
        case "true":
            track_options[value] = true;
        break;
        case "false":
            track_options[value] = false;
        break;
        default:
            if( $.isNumeric( item_valueAttr ) ){
                track_options[value] = parseInt(item_valueAttr);
            }else{
                track_options[value] = item_valueAttr;
            }
        break;
    }
} */
