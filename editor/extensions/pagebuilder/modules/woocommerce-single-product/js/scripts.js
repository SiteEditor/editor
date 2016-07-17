/*#js_info#(
"handle"    => "woocomerce-single-product-scripts",
"deps"      => array("jquery","elevatezoom-jquery"),
"ver"       => "1.0.0",
"in_footer" => true
)#*/
jQuery(document).ready(function( $ ) {

    //if( $(window).width() >= 1024 ){
        var _zoomOptions = {
            responsive : true ,
            gallery : "product_images_gallery",
            galleryActiveClass: "active",
            zoomWindowFadeIn: 500,
            zoomWindowFadeOut: 750 ,
            //zoomWindowOffetx : -700 ,
            zoomType : "window",//"inner", Lens, Window, Inner
            easing : true ,
            zoomWindowWidth : 400 ,
            zoomWindowHeight : 400 ,
        };

        $.extend( _zoomOptions , window._sedProductZoom);

        if( $(window).width() < 768 ){
            _zoomOptions.zoomType = "inner";
        }



        $(".product-popup-gallery").dialog({
            resizable: false,
            maxHeight:600,
            width: 900 ,
            modal: true,
            autoOpen : false
        });

        $(".product-slider-nav .slick-slide > a").livequery(function(){
            $(this).click(function(){
                var $full_src = $(this).data("zoomImage");
                $("#zoom_product_images").data("fullSrc" , $full_src );
                $("#zoom_product_images").attr("data-full-src" , $full_src );
            });
        });

        $(".expand-open-popup").click(function(){
            $(".product-popup-gallery").dialog("open");
            var $src = $("#zoom_product_images").data("fullSrc"),
                $alt = $("#zoom_product_images").attr("alt");
                   // alert( $src )
            $("#popup-product-slider-thumbnails .slider-item").removeClass("active");
            $('#popup-product-slider-thumbnails .slider-item > a[href="'+$src+'"]').parent().addClass("active");

            var $img = "<img src='"+ $src +"' alt='"+ $alt +"' />";
            $(".product-popup-gallery-inner .popup-main-image > .image-item ").html( $img );

        });

        $("#popup-product-slider-thumbnails").mCustomScrollbar({
            //theme:"dark" ,
            autoHideScrollbar:true ,
            advanced:{
                updateOnBrowserResize:true, /*update scrollbars on browser resize (for layouts based on percentages): boolean*/
                updateOnContentResize:true,
            },
        });


        $("#popup-product-slider-thumbnails .slider-item:first").addClass("active");
        $("#popup-product-slider-thumbnails .slider-item > a").click(function(e){
            e.preventDefault();

            $("#popup-product-slider-thumbnails .slider-item").removeClass("active");
            $(this).parent().addClass("active");
            var $img = "<img src='"+ $(this).attr("href") +"' alt='"+ $(this).find(">img").attr("alt") +"' />";
            $(".product-popup-gallery-inner .popup-main-image > .image-item ").html( $img );
        });

    //}

    var slickOptions = {
        arrows: true,
        slidesToShow: 4,
        slidesToScroll: 1,
        prevArrow : '<span class="slide-nav-bt slide-prev"><i class="fa fa-angle-up"></i></span>',
        nextArrow : '<span class="slide-nav-bt slide-next"><i class="fa fa-angle-down"></i></span>',
        dots: false,
        centerMode: false,
        swipe      : true ,
        touchMove  : true ,
        responsive: [
          {
            breakpoint: 1024,
            settings: {
              slidesToShow: 4,
              slidesToScroll: 1,
            }
          },
          {
            breakpoint: 600,
            settings: {
              slidesToShow: 4,
              slidesToScroll: 2,
              //arrows: false,
            }
          },
          {
            breakpoint: 480,
            settings: {
              slidesToShow:4,
              slidesToScroll: 2 ,
              //arrows: false,
            }
          }
        ]
    };

    if( $("body").hasClass("mobile") ){
        slickOptions.arrows = false;
    }

    if( !$("body").hasClass("mobile") ){
        slickOptions.vertical = true;
    }

    if( $("body").hasClass("rtl-body") && $("body").hasClass("mobile") ){
        slickOptions.rtl = true;
    }

    //$('.product-slider-nav').slick( slickOptions );


    var first_zoom_call = false;
    if( $("body").hasClass("mobile") ){

        $("#sed_product_image_gallery_mobile_carousel .sed-go-mobile-btn").on("SedMobileOpenDialogPage" , function( e , dialogId ){

            if(first_zoom_call === false ){
                $('.product-slider-nav').slick( slickOptions );
                $('#zoom_product_images').elevateZoom( _zoomOptions );
                //product thumb gallery carousel
                first_zoom_call = true;
            }

            var thumbId = $(this).data("thumbId"),
            $index = $('.product-slider-nav').find('.slick-slide[data-thumb-id="'+thumbId+'"]').data("slickIndex");
            $('.product-slider-nav').slick('slickGoTo',$index);
            var $full_src = $('.product-slider-nav').find('.slick-slide[data-thumb-id="'+thumbId+'"] > a').data("zoomImage");
            $("#zoom_product_images").data("zoomImage" , $full_src );  //alert( $full_src );
            $("#zoom_product_images").attr("data-zoom-image" , $full_src );
            $("#zoom_product_images").attr("src" , $('.product-slider-nav').find('.slick-slide[data-thumb-id="'+thumbId+'"] > a').data("image") );
        });

    }else{
        $('.product-slider-nav').slick( slickOptions );
        $('#zoom_product_images').elevateZoom( _zoomOptions );
    }

    var $rtl = ( $("body").hasClass("rtl-body") ) ? true : false;
    $("#sed_product_image_gallery_mobile_carousel").slick({
        mobileFirst : true ,
        arrows: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: true,
        centerMode: false,
        rtl : $rtl,
        swipe      : true ,
        touchMove  : true ,
        //lazyLoad : "progressive"
    });

    //review
    $('.stars a').click(function(e) {
        e.preventDefault();
        var star = $(this).data("star"),
            $current_option = $("#rating").find('option[value="' + star + '"]');

        $( this ).addClass('selected');
        $( this ).siblings().removeClass('selected');

        $current_option.attr('selected', 'selected');
        $current_option.siblings().removeAttr('selected');
    });


    var shareBox = $("#product-social-share-container").html();
    $("#product-social-share-container").hide();
    $('.product-social-share.popover').popover({
        html: true,
	    content: function() {
          return shareBox;
        }
    });

    if( $(".product-excerpt").height() < 260 )
        $(".product-excerpt").find(".excerpt-show-more").hide();
  
    $(".excerpt-show-more").click(function(){
        var $excerpt = $(this).parents(".product-excerpt:first");
        if( $excerpt.hasClass("show-more-info") )
            $excerpt.removeClass("show-more-info");
        else
            $excerpt.addClass("show-more-info");
    });


});