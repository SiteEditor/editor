<?php
/**
 * Customize Posts Support class.
 *
 * @package WordPress
 * @subpackage Customize
 */

/**
 * Class SiteEditorCustomizePostsSupport
 */
abstract class SiteEditorCustomizePostsSupport {

	/**
	 * Plugin/Theme slug.
	 *
	 * @access public
	 * @var string
	 */
	public $slug;

	/**
	 * Posts component.
	 *
	 * @access public
	 * @var SiteEditorCustomizePosts
	 */
	public $posts_component;

	/**
	 * Initial loader.
	 *
	 * @access public
	 *
	 * @param SiteEditorCustomizePosts $posts_component Component.
	 * @throws Exception If the Posts component is not instantiated.
	 */
	public function __construct( SiteEditorCustomizePosts $posts_component ) {
		if ( empty( $posts_component ) || ! ( $posts_component instanceof SiteEditorCustomizePosts ) ) {
			throw new Exception( 'Posts component not instantiated.' );
		}
		$this->posts_component = $posts_component;
	}

	/**
	 * Initialize support.
	 *
	 * @access public
	 */
	public function init() {
		if ( true === $this->is_support_needed() ) {
			$this->add_support();
		}
	}

	/**
	 * Is support needed.
	 *
	 * @return bool
	 */
	abstract public function is_support_needed();

	/**
	 * Add support.
	 *
	 * This would be where hooks are added.
	 */
	public function add_support() {}
}
