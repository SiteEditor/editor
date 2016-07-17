(function( exports, $ ) {
    var api = sedApp.editor ,
        sprElements = {
            related_posts              : "hide-spr-p-related",
            social_share_box           : "hide-spr-p-share",
            post_nav                   : "hide-spr-p-nav",
            author_info_box            : "hide-spr-p-author",
            show_comments              : "hide-spr-p-comments",
        },
        emptyElements = {
            related_posts              : "sed-empty-content-related",
            social_share_box           : "sed-empty-content-share",
            post_nav                   : "sed-empty-content-nav",
            author_info_box            : "sed-empty-content-author",
            show_comments              : "sed-empty-content-comments",
        };

$( document ).ready( function (  ) {
    $('[data-sed-post-role="post-module-container"]').livequery(function(){
        var self = this;
        $.each( emptyElements , function( module , className ){
            var el = $(self).find("." + className);
            if( el.length > 0 ){
                $(self).addClass( sprElements[module] );
            }
        });
    });
});

}(sedApp, jQuery));