<?php
/**
 * Site Width & Site Layout Feature Class.
 *
 * Handles all features of themes
 *
 * @package SiteEditor
 * @subpackage framework
 * @since 1.0.0
 */
class SiteEditorSiteLayoutWidthFeature extends SiteEditorThemeFeature{

    /**
     * @access public
     * @var string
     */
    public $id = 'site_layout_feature';

    /**
     * Page default Length use from "wide" or "boxed"
     *
     * @access public
     * @var string
     */
    public $page_length = 'wide';

    /**
     * Sheet default width use from valid css value
     *
     * @access public
     * @var string
     */
    public $sheet_width = '1100px';

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
     * Initialize class
     *
     * @since 1.0.0
     * @access public
     */
    public function feature_output(){
        global $sed_dynamic_css_string , $sed_data;

        $selector = $this->selector;

        ob_start();
        ?>
        <?php echo $selector; ?>,
        .sed-row-boxed{
            max-width : <?php echo $sed_data['sheet_width'];?> !important;
        }
        <?php
        $css = ob_get_clean();
        $sed_dynamic_css_string .= $css;
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
        $json_array['default_page_length']      = $this->page_length;
        $json_array['default_sheet_width']      = $this->sheet_width;

        return $json_array;
    }

}

sed_register_feature( 'site_layout_feature' , 'SiteEditorSiteLayoutWidthFeature' );