jQuery(document).ready(function($){
    $(".module-search").livequery(function(){
        $(".search-skin-default .search-button,.search-skin2 .search-button,.search-skin4 .search-button,.search-skin5 .search-button").click(function(e){
            var searchForm = $("#" + $(this).data("searchId"));
            searchForm.submit();
        });
    });
});