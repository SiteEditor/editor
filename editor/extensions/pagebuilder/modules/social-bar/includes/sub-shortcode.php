<?php
//******************[sed_social_bar_item]***************
class PBItemHSocialBarShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array (
			"name"		=> "sed_social_bar_item",
			"module"	  => "social-bar",
            "is_child"    =>  true
		));

	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'social-bar', 
        );

        return $atts;
    }
}


new PBItemHSocialBarShortcode();
?>