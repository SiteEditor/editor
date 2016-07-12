<?php

//******************[sed_html]***************
class PBContainerShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array (
			"name"		  => "sed_container",
			"module"	  => "module",
            "is_child"    =>  true
		));

	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'module',
            'tag'               => 'div'
        );

        return $atts;
    }
}
new PBContainerShortcode();
