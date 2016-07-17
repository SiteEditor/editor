<?php
//SUB SHORTCODE [sed_accordion_slider_info]
//=================================================================
class PBAccordionSliderTitleShortcode extends PBShortcodeClass{
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_accordion_slider_info",        
                "module"      => "accordion-slider",            
                "is_child"    =>  true
            ) // Args
        );
    }

    function get_atts(){
        $atts = array(
            'parent_module'     => 'accordion-slider',
        );

        return $atts;
    }  

}

//SUB SHORTCODE [sed_accordion_slider_item]
//=================================================================
class PBAccordionSliderItemShortcode extends PBShortcodeClass{
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_accordion_slider_item",        
                "module"      => "accordion-slider",            
                "is_child"    =>  true
            ) // Args
        );  
    }

    function get_atts(){
        $atts = array(
            'parent_module'     => 'accordion-slider',
        );

        return $atts;
    }  

}
new PBAccordionSliderItemShortcode;
new PBAccordionSliderTitleShortcode;