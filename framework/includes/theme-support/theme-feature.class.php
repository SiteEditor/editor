<?php
/**
 * Theme Feature Class.
 *
 * Handles all features of themes
 *
 * @package SiteEditor
 * @subpackage framework
 * @since 1.0.0
 */
class SiteEditorThemeFeature{

    /**
     * @access public
     * @var SiteEditorThemeSupport
     */
    public $theme_support;

    /**
     * @access public
     * @var string
     */
    public $id;

    /**
     * Constructor.
     *
     * Any supplied $args override class property defaults.
     *
     * @since 1.0.0
     *
     * @param SiteEditorThemeSupport $theme_support
     * @param string               $id      An specific ID of the feature. Can be a
     *                                      theme mod or option name.
     * @param array                $args    Feature arguments.
     * @return SiteEditorThemeFeature $feature
     */
    public function __construct( $theme_support , $id, $args = array() ) {
        $keys = array_keys( get_object_vars( $this ) );
        foreach ( $keys as $key ) {
            if ( isset( $args[ $key ] ) )
                $this->$key = $args[ $key ];
        }

        $this->theme_support = $theme_support;
        $this->id = $id;

        $this->render_init();
    }

    /**
     * For initialize custom class
     *
     * @since 1.0.0
     * @access public
     */
    public function render_init() {}

    /**
     * Get the data to export to the client via JSON.
     * Using data in preview
     *
     * @since 1.0.0
     * @access public
     * @return array Array of parameters passed to the JavaScript.
     */
    public function json() {

        $json_array = array();

        $json_array['feature_id'] = $this->id;

        return $json_array;
    }

}