<?php
//******************[sed_item_tagline_box]***************
class PBItemTaglineBoxShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array (
			"name"		=> "sed_item_tagline_box",
			"module"	  => "tagline-box",
            "is_child"    =>  true
		));

	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'tagline-box',
        );

        return $atts;
    }
}

new PBItemTaglineBoxShortcode();