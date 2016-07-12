<?php
//SUB SHORTCODE FOR MODULE TAB [SED_TITLE_TABS]
//=============================================
class PBTitleTabsShortcode extends PBShortcodeClass {
	function __construct() {
		parent::__construct( array(
			"name"     => "sed_title_tabs",
			"module"   => "tab",
			"is_child" => true
		) );
	}

	function get_atts() {
		$atts = array(
			'parent_module' => 'tab',
		);

		return $atts;
	}
}

//SUB SHORTCODE FOR MODULE TAB [SED_TITLE_TAB]
//============================================
class PBTitleTabShortcode extends PBShortcodeClass {
	function __construct() {
		parent::__construct( array(
			"name"     => "sed_title_tab",
			"module"   => "tab",
			"is_child" => true
		) );
	}

	function get_atts() {
		$atts = array(
			'parent_module' => 'tab',
			'href'          => ''
		);

		return $atts;
	}
}

//SUB SHORTCODE FOR MODULE TAB [SED_CONTENT_TABS]
//==============================================
class PBContentTabsShortcode extends PBShortcodeClass {
	function __construct() {
		parent::__construct( array(
			"name"     => "sed_content_tabs",
			"module"   => "tab",
			"is_child" => true
		) );
	}

	function get_atts() {
		$atts = array(
			'parent_module' => 'tab',
		);

		return $atts;
	}
}

//SUB SHORTCODE FOR MODULE TAB [SED_CONTENT_TAB]
//==============================================
class PBContentTabShortcode extends PBShortcodeClass {

	function __construct() {
		parent::__construct( array(
			"name"     => "sed_content_tab",
			"module"   => "tab",
			"is_child" => true
		) );
	}

	function get_atts() {
		$this->modules_accepted = 'title,paragraph,image,icons,widget,columns,row,button,separator,google-map,contact-form-7,craousel,social-bar,search,checklist,blockquote,tagline-box,image-content-box,icon-content-box,testimonial,woocommerce-best-selling,woocommerce-categories,woocommerce-product,woocommerce-product-attribute,woocommerce-product-category,woocommerce-products,woocommerce-recent-products,woocommerce-sale-products,woocommerce-top-rated,recent-works,diamond-gallery,masonry-gallery,video,audio';
		$atts                   = array(
			'parent_module'          => 'tab',
			'modules_accepted'       => $this->modules_accepted,
			'modules_accepted_error' => sprintf( __( "Only using from %s modules for this draggable area", "site-editor" ), $this->modules_accepted )
		);

		return $atts;
	}

}


new PBTitleTabsShortcode;
new PBTitleTabShortcode;
new PBContentTabsShortcode;
new PBContentTabShortcode;


