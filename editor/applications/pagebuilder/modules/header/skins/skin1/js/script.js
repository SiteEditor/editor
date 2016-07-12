/*
#js_info#(
"handle" =>"header-skin1-js",
"deps" 	 =>array('jquery'),
"ver"    =>"1.0.0",
"in_footer"  =>true
)#
*/
jQuery( document ).ready( function ( $ ) {

    $('body').livequery(function(){
        var $element =$(this),
            pageContainer =  $element.find(".site-main"),
            headerFixed   =  $element.find(".header-skin1 .header-fixed") ,
            navbarHeader  =  $element.find(".sed-navbar-header"),
            navbarToggle  =  $element.find(".sed-navbar-toggle");

        //navbarToggle.addClass("navbar-inactive");

        navbarHeader.click(function(){

            pageContainer.addClass("site-menu-open");
            headerFixed.addClass("header-fixed-menu-open");

            /*if(pageContainer.hasClass("site-menu-open")){
                pageContainer.removeClass("site-menu-open");
            }else{
                pageContainer.addClass("site-menu-open");
            }

            if(headerFixed.hasClass("header-fixed-menu-open")){
                headerFixed.removeClass("header-fixed-menu-open");
            }else{
                headerFixed.addClass("header-fixed-menu-open");
            }


            if(navbarToggle.hasClass("navbar-inactive")){

                navbarToggle.removeClass("navbar-inactive");
                navbarToggle.addClass("navbar-active");

            }else if(navbarToggle.hasClass("navbar-active")){

                navbarToggle.removeClass("navbar-active");
                navbarToggle.addClass("navbar-inactive");

            } */

        });

        if( $("#wpadminbar").length > 0 ){
            headerFixed.css("top" , $("#wpadminbar").height() + "px")
        }
    });

    $(document).click(function (event) {
        var clickover = $(event.target);   //_opened === true &&
        if ( clickover.parents(".header-fixed").length == 0 && !clickover.hasClass("header-fixed") &&  clickover.parents(".sed-navbar-header").length == 0 &&  !clickover.hasClass("sed-navbar-header") ) {
            $(".site-main").removeClass("site-menu-open");
            $(".header-skin1 .header-fixed").removeClass("header-fixed-menu-open");
        }
    });


});

