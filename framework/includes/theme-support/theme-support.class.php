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
 * Extend theme features for support in 3d-party themes
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

        $this->register_features( );

        //TODO : Recovery in next versions
        //$this->register_current_theme();

        add_action( "wp_footer" , array( $this , "export_features_data" ) );

    }

    public function register_features( ) {

        $features_path = dirname( __FILE__ ) . DS . "features" . DS . "*feature.class.php" ;

        foreach ( glob( $features_path ) as $php_file ) {
            require_once $php_file;
        }

    }

    public function register_current_theme(){

        $theme = wp_get_theme( isset( $_REQUEST['theme'] ) ? $_REQUEST['theme'] : null );

        if( $theme->get_stylesheet() == "twentysixteen" ){
            require_once dirname( __FILE__ ) . "/themes/twentysixteen/twentysixteen-sync.class.php" ;
            new SiteEditorTwentysixteenThemeSync( $this );
        }else if( in_array( $theme->get_stylesheet() , array( "twentyseventeen" , "twentyseventeen-plus" , "twentyseventeen-plus-lite" ) ) ){
            require_once dirname( __FILE__ ) . "/themes/twentyseventeen/twentyseventeen-sync.class.php" ;
            new SiteEditorTwentyseventeenThemeSync( $this );
        }

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
     * @return bool
     */
    public function remove_theme_feature( $id ) {

        do_action( "sed_before_remove_theme_feature" , $id );

        if ( isset( $this->theme_features[ $id ] ) ) {

            unset($this->theme_features[$id]);

            return true;

        }else{

            return false;
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

    /**
     * Export features data
     *
     * @since 1.0.0
     * @access public
     */
    public function export_features_data(){

        $settings = array();

        foreach ( $this->theme_features AS $feature_id => $feature ){
            $settings[$feature_id] = $feature->json();
        }

        ?>
        <script type="text/javascript">
            var _sedThemeFeaturesSettings = <?php echo wp_json_encode( $settings ); ?>;
        </script>
        <?php

    }

}