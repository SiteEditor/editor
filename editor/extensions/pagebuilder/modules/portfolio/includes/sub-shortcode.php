<?php

/*******************[sed_portfolio_items]******************************/

class PBPortfolioItems extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_portfolio_items",                 //*require
			"title"       => __("Portfolio Items","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "portfolio" ,                        //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
}

new PBPortfolioItems;
