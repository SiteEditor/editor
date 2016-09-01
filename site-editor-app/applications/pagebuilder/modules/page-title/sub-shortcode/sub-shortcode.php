<?php
//SUB SHORTCODE FOR MODULE PAGE TITLE [sed_item_page_title]
//=============================================
class PBItemPageTitleShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array (
			"name"		=> "sed_item_page_title",
			"module"	  => "page-title",
            "is_child"    =>  true
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'page-title',
        );

        return $atts;
    }
}

new PBItemPageTitleShortcode;


