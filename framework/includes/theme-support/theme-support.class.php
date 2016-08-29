<?php
/**
 * SiteEditor Themes Support classes
 *
 * @package SiteEditor
 * @subpackage framework
 * @since 1.0.0
 */

/**
 * SiteEditor Theme Support class.
 *
 * Manage Themes Support
 *
 * @since 1.0.0
 */

class SiteEditorThemeSupport{

    /**
     * An array of current theme features
     *
     * @access private
     * @var array
     */
    private $theme_features = array();

    /**
     * An array of all registered features
     *
     * @access private
     * @var array
     */
    private $features = array();

    /**
     * SiteEditorOptionsManager constructor.
     */
    public function __construct( ) {

        require_once dirname( __FILE__ ) . '/theme-feature.class.php';

    }

    /**
     * Register a SiteEditor theme framework feature.
     *
     * @since 1.0.0
     * @access public
     * @param string $feature_id ID of the feature.
     */
    public function register_feature( $feature_id , $php_class ) {

        if ( !in_array( $feature_id , array_keys( $this->features ) ) ) {
            $this->features[$feature_id] = $php_class ;
        }
    }

    /**
     * Add a feature to current theme.
     *
     * @since 1.0.0
     *
     * @param SiteEditorThemeFeature|string $id   Theme Feature object, or ID.
     * @param array                       $args Feature arguments; passed to SiteEditorThemeFeature
     *                                          constructor.
     * @return object ( instance of SiteEditorThemeFeature or extends )
     */
    public function add_theme_feature( $id , $args ) {

        if( !in_array( $id , array_keys( $this->features ) ) ){
            return new WP_Error( "not_exist_feature" , sprintf( __( "%s feature not found in registered features" , "site-editor" ) , $id ) );
        }

        $class = $this->features[$id];

        $feature = new $class( $this, $id, $args );

        $this->theme_features[ $feature->id ] = $feature;

        return $feature;

    }

    /**
     * Retrieve a theme feature
     *
     * @since 1.0.0
     * @access public
     * @param string $id ID of the feature.
     * @return SiteEditorThemeFeature $feature The feature object.
     */
    public function get_theme_feature( $id ) {

        if ( isset( $this->theme_features[ $id ] ) ) {
            return $this->theme_features[$id];
        }

    }

    /**
     * Remove a theme feature.
     *
     * @since 1.0.0
     * @access public
     * @param string $id ID of the feature.
     */
    public function remove_theme_feature( $id ) {

        if ( isset( $this->theme_features[ $id ] ) ) {
            unset($this->theme_features[$id]);
        }
    }

    /**
     * Checks a theme's support for a given feature
     *
     * @since 1.0.0
     * @access public
     * @param string $id the feature being checked
     * @return bool
     */
    public function current_theme_support( $id ){

        $feature = $this->get_theme_feature( $id );

        if( isset( $feature ) && $feature )
            return true;

        return false;
    }

}

$GLOBALS['sed_theme_support'] = new SiteEditorThemeSupport();

function sed_register_feature( $feature_id , $php_class ){
    global $sed_theme_support;

    $sed_theme_support->register_feature( $feature_id , $php_class );
}