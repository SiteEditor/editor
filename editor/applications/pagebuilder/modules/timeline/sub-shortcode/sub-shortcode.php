<?php
/* THIS SUB SHORTCODE FOR TIMELINE MODULE
=======================================*/
class PBTimeLineItemShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_timeline_item",                 //*require
			"title"       => __("TimeLine Item","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "timeline" ,                        //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
}
class PBTimeLineItemHeadShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_timeline_item_head",                 //*require
			"title"       => __("TimeLine Item","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "timeline" ,                        //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
}
class PBTimeLineItemBodyShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_timeline_item_body",                 //*require
			"title"       => __("TimeLine Item","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "timeline" ,                        //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
}
class PBTimeLineItemContentShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_timeline_item_content",                 //*require
			"title"       => __("TimeLine Item","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "timeline" ,                        //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
}
class PBTimeLineItemFootShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_timeline_item_foot",                 //*require
			"title"       => __("TimeLine Item","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "timeline" ,                        //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
}
class PBTimeLineItemThumbShortcode extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_timeline_item_thumb",                 //*require
			"title"       => __("TimeLine Item","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "timeline" ,                        //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
}
new PBTimeLineItemThumbShortcode;
new PBTimeLineItemContentShortcode;
new PBTimeLineItemFootShortcode;
new PBTimeLineItemHeadShortcode;
new PBTimeLineItemBodyShortcode;
new PBTimeLineItemShortcode;