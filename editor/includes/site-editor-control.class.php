<?php
/**
 * App Setting Class.
 *
 * Handles saving and sanitizing of settings.
 *
 * @package SiteEditor
 * @subpackage Settings
 * @since 3.4.0
 */
class SedAppControl{
	/**
	 * @access public
	 * @var WP_Customize_Manager
	 */
	public $manager;

	/**
	 * Constructor.
	 *
	 * Any supplied $args override class property defaults.
	 *
	 * @since 3.4.0
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string               $id      An specific ID of the setting. Can be a
	 *                                      theme mod or option name.
	 * @param array                $args    Setting arguments.
	 * @return SedAppSettings $setting
	 */
	public function __construct( $manager, $id, $args = array() ) {

	}


}