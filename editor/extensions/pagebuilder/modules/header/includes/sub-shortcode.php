<?php
//******************[sed_item_header]***************
class PBItemHeaderShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array (
			"name"		=> "sed_item_header",
			"module"	  => "header",
            "is_child"    =>  true
		));

	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'header',
        );

        return $atts;
    }
}


new PBItemHeaderShortcode();
?>