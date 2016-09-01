(function( exports, $ ) {
    var api = sedApp.editor;

$( document ).ready( function (  ) {

	$('.audio-module').livequery(function(){
    	var $this            = $(this),
            track_options    = {},
            //this_id          = $this.attr("sed_model_id") ,    
            container_id     = $this.find('.sed_jp_container').attr('id'), 
            player_id        = $this.find('.sed_jp_jplayer').attr('id'),  
    		item_data_attr   = [
        	    "title","mp3" , "oga" , "webma" , "poster"
			],
            $this_item = $this.find('.sed_playlist_item_info'),
            autoplay = $this_item.data( "audioAutoplay" );

        $.each( item_data_attr , function( key , value ){
            var val =  $this_item.data( "audio" + api.fn.ucfirst( value ) );

            track_options[value] = val;

        });

        options  = {
            cssSelectorAncestor : "#" + container_id ,
            swfPath: "js",
            supplied: "mp3 , oga , webma ",
            wmode: "window",
            smoothPlayBar: true,
            keyEnabled: true,
            remainingDuration: true,
            toggleDuration: true,
            verticalVolume: true,
            useStateClassSkin: true,
            autoBlur: false,
            preload  : $this_item.data("audioPreload"),
            loop  : $this_item.data("audioLoop"),
            size: {
                width: $this_item.data("audioWidth") + "px",
                height: $this_item.data("audioHeight") + "px",
                cssClass: "jp-audio-" + $this_item.data("audioHeight") + "p"
            },

            ready: function (event) {
                $(this).jPlayer( "setMedia", track_options );

                if( autoplay )
                    $(this).jPlayer("play");
            },

            setmedia: function(e) {
                var jp       = $('#' + player_id ), jpData = jp.data('jPlayer');
                var jpNum    = jpData.status.media.num;
                var jpTitle  = jpData.status.media.title;
                var jpPoster = jpData.status.media.poster;

                //console.log(jpData);
                
                $this.find('.jp-playing-poster > .img').html('<img src="' + jpPoster + '" alt="' + jpTitle + '">');
                $this.find('.jp-playing-title').html(jpTitle);
                $this.find('.jp-playing-num').html(jpNum);
            }
        };

        $('#' + player_id ).jPlayer(options);

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


        var _responsiveAudio = function( el ){
            if( $(el).width() < 550 || $this_item.data("audioWidth") < 550  ){
                $(el).addClass("audio-resize-responsive");
            }else{
                $(el).removeClass("audio-resize-responsive");
            }
        };

        _responsiveAudio( this );

        $(this).on("sed.moduleResize sed.moduleResizeStop" , function(){
            _responsiveAudio( this );
        });

        /*
        @Site Editor pakage
        Edit By SiteEditor
        for module sortable(darg & drop)
        */
        $(this).on("sed.moduleSortableStop sedAfterRemoveColumns" , function(){
            _responsiveAudio( this );
        });


        $(this).parents(".sed-pb-module-container:first").on( "sedChangeModulesLength", function( e , length ){
            _responsiveAudio( $(this).find(".sed-pb-module-container:first") );
        });

        $(this).parents(".sed-pb-module-container:first").on( "sedChangedSheetWidth", function(){
            if( $(this).parents(".sed-row-boxed").length > 0 ){
                _responsiveAudio( $(this).find(".sed-pb-module-container:first") );
            }
        });

        $(this).parents(".sed-pb-module-container:first").on( "sedChangedPageLength", function( e , length ){
            if( ($(this).parents(".sed-row-boxed").length == 0 && length == "wide" ) || ($(this).parents(".sed-row-boxed").length == 1 && length == "boxed" ) ){
                _responsiveAudio( $(this).find(".sed-pb-module-container:first") );
            }
        });



    }); // END LIVEQUERY
});

}(sedApp, jQuery));
