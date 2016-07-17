<?php
//SUB SHORTCODE FOR MODULE ELASTIC SLIDER [sed_elastic_slider_items]
//=================================================================
class PBElasticSliderItemsShortcode extends PBShortcodeClass{
	function __construct() {
        parent::__construct( array(
                "name"        => "sed_elastic_slider_items",                                //*require
                "module"      => "elastic-slider",                                    //*require
                "is_child"    =>  true
            ) // Args
        );
    }

    function get_atts(){
        $atts = array(
            'parent_module'     => 'elastic-slider',
        );

        return $atts;
    }

}
//SUB SHORTCODE FOR MODULE ELASTIC SLIDER [sed_elastic_slider_thumbs]
//=================================================================
class PBElasticSliderThumbsShortcode extends PBShortcodeClass{
	function __construct() {
        parent::__construct( array(
                "name"        => "sed_elastic_slider_thumbs",                                //*require
                "module"      => "elastic-slider",                                    //*require
                "is_child"    =>  true
            ) // Args
        );
    }

    function get_atts(){
        $atts = array(
            'parent_module'     => 'elastic-slider',
        );

        return $atts;
    }

}
//SUB SHORTCODE FOR MODULE ELASTIC SLIDER [sed_elastic_slider_item]
//=================================================================
class PBElasticSliderItemShortcode extends PBShortcodeClass{
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_elastic_slider_item",                                //*require
                "module"      => "elastic-slider",                                    //*require
                "is_child"    =>  true
            ) // Args
        );
    }

    function get_atts(){
        $atts = array(
            'parent_module'     => 'elastic-slider',
        );

        return $atts;
    }

}
//SUB SHORTCODE FOR MODULE ELASTIC SLIDER [sed_elastic_slider_thumb]
//=================================================================
class PBElasticSliderThumbShortcode extends PBShortcodeClass{
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_elastic_slider_thumb",                                //*require
                "module"      => "elastic-slider",                                    //*require
                "is_child"    =>  true
            ) // Args
        );
    }

    function get_atts(){
        $atts = array(
            'parent_module'     => 'elastic-slider',
        );

        return $atts;
    }

}
//SUB SHORTCODE FOR MODULE ELASTIC SLIDER [sed_elastic_slider_title]
//=================================================================
class PBElasticSliderTitleShortcode extends PBShortcodeClass{
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_elastic_slider_title",                                //*require
                "module"      => "elastic-slider",                                    //*require
                "is_child"    =>  true
            ) // Args
        );
    }

    function get_atts(){
        $atts = array(
            'parent_module'     => 'elastic-slider',
        );

        return $atts;
    }  

}
new PBElasticSliderItemsShortcode;
new PBElasticSliderThumbsShortcode;
new PBElasticSliderItemShortcode;
new PBElasticSliderThumbShortcode;
new PBElasticSliderTitleShortcode;