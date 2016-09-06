<?php
/**
 * Installation related functions and actions.
 *
 * @author   Site Editor Team
 * @category Admin
 * @package  SiteEditor/Includes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SiteEditorInstall Class.
 */
class SiteEditorInstall {

	/**
	 * SiteEditorInstall constructor.
	 */
	public function __construct( ){

		add_action( 'init' , array( $this , 'options_init' ) );

	}

	public function options_init(){

        if ( get_option( 'page_options_scope' ) === false ) {

            //The option hasn't been added yet. We'll add it with $autoload set to 'no'.
            $deprecated = null;
            $autoload = 'yes';

            $scopes = array();

            add_option( 'page_options_scope' , $scopes , $deprecated, $autoload );
        }

	}

	/**
	 * Install WC.
	 */
	public static function install() {

	}
}
