<?php
/******************[sed_accordion_header]***********************************/

class PBAccordionHeaderShortcode extends PBShortcodeClass{

	function __construct() {
		parent::__construct( array(
                "name"        => "sed_accordion_header",                                //*require
                "title"       => __("Accordion Header","site-editor"),
                "description" => __("Add Accordion Header To Page","site-editor"),      //*require for toolbar
                "module"      =>  "accordion",                                    //*require
                "is_child"    =>  true       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
		);
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'accordion',
        );
        return $atts;
    }


}

new PBAccordionHeaderShortcode();

/******************[sed_accordion_contents]***********************************/

class PBAccordionContentsShortcode extends PBShortcodeClass{

	function __construct() {
		parent::__construct( array(
                "name"        => "sed_accordion_contents",                                //*require
                "title"       => __("Accordion Contents","site-editor"),
                "description" => __("Add Accordion Contents To Page","site-editor"),      //*require for toolbar
                "module"      =>  "accordion",                                    //*require
                "is_child"    =>  true       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
		);
	}

    function get_atts(){
        $atts = array(
            'parent_module'     => 'accordion',
        );
        return $atts;
    }

}

new PBAccordionContentsShortcode();