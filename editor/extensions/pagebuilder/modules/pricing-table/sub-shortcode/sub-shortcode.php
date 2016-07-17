<?php
/* THIS SUB SHORTCODE FOR PRICING TABLE MODULE
=============================================*/
class PBPricingTableColumn extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array (
			"name"		  => "sed_pricing_table_column",
			"module"	  => "pricing-table",
            "is_child"    =>  true
		));
	}

	function get_atts(){

		$atts = array(
			'featured'      	=> false,
		);

		return $atts;
  	}
}

/* NEXT SUB SHORTCODE
====================*/

class PBPricingTablePriceColumn extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array (
			"name"		=> "sed_pricing_table_price_column",
			"module"	  => "pricing-table",
            "is_child"    =>  true
		));
		
	}
}
/* NEXT SUB SHORTCODE
====================*/
class PBPricingTableListColumn extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array (
			"name"		=> "sed_pricing_table_list_column",
			"module"	  => "pricing-table",
            "is_child"    =>  true
		));

	}
}


class PBPricingTableFeaturesListItem extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array (
			"name"		=> "sed_pt_features_list_item",
			"module"	  => "pricing-table",
            "is_child"    =>  true
		));

	}
}
/* NEXT SUB SHORTCODE
====================*/
class PBPricingTableFooterColumn extends PBShortcodeClass{
	function __construct(){
		parent::__construct( array (
			"name"		=> "sed_footer_table_column",
			"module"	  => "pricing-table",
            "is_child"    =>  true
		));

	}
}



new PBPricingTableColumn;
new PBPricingTablePriceColumn;
new PBPricingTableListColumn;
new PBPricingTableFeaturesListItem;
new PBPricingTableFooterColumn;

