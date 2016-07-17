<?php
//SUB SHORTCODE FOR MODULE ELASTIC SLIDER [sed_parallax_slider_wrapper]
//=================================================================
class PBParallaxSliderWrapperShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_parallax_slider_wrapper",  //*require
			"module"      => "parallax-slider" ,            //*require
			"is_child"	  => true
		));
	}
}


//SUB SHORTCODE FOR MODULE ELASTIC SLIDER [sed_parallax_slider_items]
//=================================================================
class PBParallaxSliderItemsShortcode extends PBShortcodeClass{
	function __construct() {
        parent::__construct( array(
                "name"        => "sed_parallax_slider_items",                                //*require
                "module"      => "parallax-slider",                                    //*require
                "is_child"    =>  true
            ) // Args
        );
    }

    function get_atts(){
        $atts = array(
            'parent_module'     => 'parallax-slider',
        );

        return $atts;
    }

}
//SUB SHORTCODE FOR MODULE ELASTIC SLIDER [sed_parallax_slider_thumbs]
//=================================================================
class PBParallaxSliderThumbsShortcode extends PBShortcodeClass{
	function __construct() {
        parent::__construct( array(
                "name"        => "sed_parallax_slider_thumbs",                                //*require
                "module"      => "parallax-slider",                                    //*require
                "is_child"    =>  true
            ) // Args
        );
    }

    function get_atts(){
        $atts = array(
            'parent_module'     => 'parallax-slider',
        );

        return $atts;
    }

}
//SUB SHORTCODE FOR MODULE ELASTIC SLIDER [sed_parallax_slider_item]
//=================================================================
class PBParallaxSliderItemShortcode extends PBShortcodeClass{
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_parallax_slider_item",                                //*require
                "module"      => "parallax-slider",                                    //*require
                "is_child"    =>  true
            ) // Args
        );
    }

    function get_atts(){
        $atts = array(
            'parent_module'     => 'parallax-slider',
        );

        return $atts;
    }

}
//SUB SHORTCODE FOR MODULE ELASTIC SLIDER [sed_parallax_slider_thumb]
//=================================================================
class PBParallaxSliderThumbShortcode extends PBShortcodeClass{
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_parallax_slider_thumb",                                //*require
                "module"      => "parallax-slider",                                    //*require
                "is_child"    =>  true
            ) // Args
        );
    }

    function get_atts(){
        $atts = array(
            'parent_module'     => 'parallax-slider',
        );

        return $atts;
    }

}
new PBParallaxSliderWrapperShortcode;
new PBParallaxSliderItemsShortcode;
new PBParallaxSliderThumbsShortcode;
new PBParallaxSliderItemShortcode;
new PBParallaxSliderThumbShortcode;