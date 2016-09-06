<?php
/**
 * Site Editor Custom Background Feature Class.
 *
 * Create Custom Background for pages & site
 *
 * @package SiteEditor
 * @subpackage framework
 * @since 1.0.0
 */
class SiteEditorCustomBackgroundFeature extends SiteEditorThemeFeature{

    /**
     * @access public
     * @var string
     */
    public $id = 'sed_custom_background';

    /**
     * Page default Length use from "wide" or "boxed"
     *
     * @access public
     * @var string
     */
    public $default_color = '';

    /**
     * Sheet default width use from valid css value
     *
     * @access public
     * @var string
     */
    public $default_image = '';

    /**
     * Sheet default width use from valid css value
     *
     * @access public
     * @var string
     */
    public $default_repeat = 'no-repeat';


    /**
     * Sheet default width use from valid css value
     *
     * @access public
     * @var string
     */
    public $default_position = 'center center';

    /**
     * Sheet default width use from valid css value
     *
     * @access public
     * @var string
     */
    public $default_size = '';

    /**
     * Sheet default width use from valid css value
     *
     * @access public
     * @var string
     */
    public $default_attachment = 'scroll';

    /**
     * Selector For target element it's can change page length and sheet width
     *
     * @access public
     * @var string
     */
    public $selector = 'body';

    /**
     * Initialize class
     *
     * @since 1.0.0
     * @access public
     */
    public function render_init() {

        add_action( "sed_before_dynamic_css_output" , array( $this , 'feature_output' ) );

    }

    /**
     * Add To Dynamic Css
     *
     * @since 1.0.0
     * @access public
     */
    public function feature_output(){

    }

    /**
     * Get the data to export to the client via JSON.
     * Using data in preview
     *
     * @since 1.0.0
     * @access public
     * @return array Array of parameters passed to the JavaScript.
     */
    public function json() {

        $json_array = parent::json();

        $json_array['selector']                 = $this->selector;

        return $json_array;
    }

}

$this->register_feature( 'sed_custom_background' , 'SiteEditorCustomBackgroundFeature' );