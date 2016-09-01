<?php
/*
Module Name: Column
Module URI: http://www.siteeditor.org/modules/column
Description: Module Column For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/
class PBMegaMenuDragAreaShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_megamenu_drag_area",         //*require
                "title"       => __("Megamenu Drag Area","site-editor"),
                "description" => __("","site-editor"),      //*require for toolbar
                "module"      =>  "menu",                                    //*require
                "is_child"    =>  true       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
		);
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'menu',
        );

        return $atts;
    }

}

new PBMegaMenuDragAreaShortcode();

class PBMenuDragAreaShortcode extends PBShortcodeClass{

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_menu_drag_area",         //*require
                "title"       => __("Menu Drag Area","site-editor"),
                "description" => __("","site-editor"),      //*require for toolbar
                "module"      =>  "menu",                                    //*require
                "is_child"    =>  true       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
		);
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'menu',
        );

        return $atts;
    }

}

new PBMenuDragAreaShortcode();

