<?php
//******************[sed_th_table]***************
class PBThTableShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array (
			"name"		=> "sed_th_table",
			"module"	  => "table",
            "is_child"    =>  true
		));

	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'table',
        );

        return $atts;
    }
}
//******************[sed_td_table]***************
class PBTdTableShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array (
			"name"		=> "sed_td_table",
			"module"	  => "table",
            "is_child"    =>  true
		));

	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'table',
        );

        return $atts;
    }
}
//******************[sed_tr_table]***************
class PBTrTableShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array (
			"name"		=> "sed_tr_table",
			"module"	  => "table",
            "is_child"    =>  true
		));

	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'table',
        );

        return $atts;
    }
}
//******************[sed_thead_table]***************
class PBTheadTableShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array (
			"name"		=> "sed_thead_table",
			"module"	  => "table",
            "is_child"    =>  true
		));

	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'table',
        );

        return $atts;
    }
}
//******************[sed_tfoot_table]***************
class PBTfootTableShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array (
			"name"		=> "sed_tfoot_table",
			"module"	  => "table",
            "is_child"    =>  true
		));

	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'table',
        );

        return $atts;
    }
}
//******************[sed_tbody_table]***************
class PBTbodyTableShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array (
			"name"		  => "sed_tbody_table",
			"module"	  => "table",
            "is_child"    =>  true
		));

	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'table',
        );

        return $atts;
    }
}
new PBThTableShortcode();
new PBTdTableShortcode();
new PBTrTableShortcode();
new PBTheadTableShortcode();
new PBTfootTableShortcode();
new PBTbodyTableShortcode();
