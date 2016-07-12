<?php
/*
Module Name: Column
Module URI: http://www.siteeditor.org/modules/column
Description: Module Column For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/
class PBColumnShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_column",                                //*require
                "title"       => __("Column","site-editor"),
                "description" => __("","site-editor"),      //*require for toolbar
                "module"      =>  "columns",                                    //*require
                "is_child"    =>  true       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
		);
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'columns',
            'width'             => (100/3) . '%' , 
            'placeholder'       => __('Drop A Module Here','site-editor')
        );

        return $atts;
    }

}

new PBColumnShortcode();



