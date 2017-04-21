<?php

class PBContentLayoutColumnShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"                  => "sed_content_layout_column",                                //*require
                "title"                 => __("Content Layout Column","site-editor"),
                "description"           => __("","site-editor"),      //*require for toolbar
                "module"                =>  "content-layout",                                    //*require
                //"remove_wpautop"        => true ,
                "is_child"              =>  true       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
		);
	}

    function get_atts(){
        $atts = array(
            'parent_module'     => 'content-layout',
            'width'             => (100/3) . '%' , 
            'placeholder'       => __('Drop A Module Here','site-editor') ,
            'sed_main_content'  => "no"
        );

        return $atts;
    }

}

new PBContentLayoutColumnShortcode();



