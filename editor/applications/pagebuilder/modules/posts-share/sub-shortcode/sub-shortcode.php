<?php
/******************[sed_post_share_continer]***********************************/

class PBPostShareContiner extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_post_share_continer",                 //*require
			"title"       => __("","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "posts-share",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'posts-share',
        );

        return $atts;
    }
}
new PBPostShareContiner;
/******************[sed_post_share_item]***********************************/

class PBPostShareItem extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_post_share_item",                 //*require
			"title"       => __("","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "posts-share",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'posts-share',
            'type'              =>'',
            'share_src'         => ''
        );

        return $atts;
    }
}
new PBPostShareItem;
