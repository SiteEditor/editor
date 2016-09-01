<?php
/******************[sed_posts_wrapper]***********************************/

class PBPortfolioSingleWrapper extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array(
			"name"        => "sed_portfolio_single_wrapper",                 //*require
			"title"       => __("","site-editor"),   //*require for toolbar
			"description" => __("","site-editor"),
			"module"      =>  "portfolio-single",                         //*require
			"is_child"    =>  true                         //for childe shortcodes like sed_tr , sed_td for table module
		));
	}
    function get_atts(){
        $atts = array(
            'parent_module'     => 'portfolio-single',
        );

        return $atts;
    }
}
new PBPortfolioSingleWrapper;