<?php

class PBItemCheckListShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array (
			"name"		=> "sed_item_checklist",
			"module"	  => "checklist",
            "is_child"    =>  true
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'checklist',
            "icon"              => "fa fa-angle-double-right" ,
        );

        return $atts;
    }
}
new PBItemCheckListShortcode;
