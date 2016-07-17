<?php
/**
 * SiteEditor Website Framework Rendering Class
 *
 * @class     SiteEditorFramework
 * @version   1.0.0
 * @package   framework/index
 * @category  Class
 * @author    Site Editor Team
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'SiteEditorFramework' ) ) :

final Class SiteEditorFramework {

    /**
     * using for all dynamic css
     *
     * @since 0.9
     * @var_type array
     */
    public $dynamic_css_data = array();

    public $sed_page_id;

    public $sed_page_type;

    public $typography;


    public function __construct(){

        $this->init_hooks();

        $this->includes(); 

    }

    /**
     * Hook into actions and filters.
     * @since  0.9
     */
    private function init_hooks() {
        add_action( 'wp_footer'         , array( $this , 'print_parallax_data' ) );
        add_action( 'wp_footer'         , array( $this , 'sed_add_dynamic_css_file' ) , 10000 );
        add_filter( 'body_class'        , array( $this , 'add_rtl_body_class' ) );
        add_filter( 'template_include'  , array( $this , 'template_chooser') , 1 );
        add_action( 'wp'                , array( $this , 'set_page_info') , -10000  );
    }

    /**
     * Include required core files used in frontend.
     */
    public function includes() {

        require_once SED_INC_FRAMEWORK_DIR . DS . 'typography.class.php';
        $this->typography = new SiteeditorTypography();

        require_once SED_INC_FRAMEWORK_DIR . DS . 'framework-assets.class.php';
        require_once SED_INC_FRAMEWORK_DIR . DS . 'theme-integration.class.php';
        require_once SED_INC_FRAMEWORK_DIR . DS . 'theme-framework.class.php';
        require_once SED_INC_FRAMEWORK_DIR . DS . 'site-editor-css.class.php';


    }

    function get_sed_page_info_uniqe(){
        $sed_page_id = null;
        $sed_page_type = null;
        if(is_category() || is_tag() || is_tax()){
            $sed_tax_id = get_queried_object()->term_id;
            $sed_page_id = "term_" . $sed_tax_id;
            $sed_page_type = "tax";
        } elseif( is_home() === true && is_front_page() === true ){
            $sed_page_general = "home";
            $sed_page_id = "general_" . $sed_page_general;
            $sed_page_type = "general";
        } elseif( is_home() === false && is_front_page() === true ){
            $sed_post_id = get_queried_object()->ID;
            $sed_page_id = $sed_post_id;
            $sed_page_type = "post";
        } elseif( is_home() === true && is_front_page() === false  ){
            $sed_page_general = "index_blog_page";
            $sed_page_id = "general_" . $sed_page_general;
            $sed_page_type = "general";
        } elseif ( is_search() ) {
            $sed_page_general = "search";
            $sed_page_id = "general_" . $sed_page_general;
            $sed_page_type = "general";
        } elseif ( is_404() ) {
            $sed_page_general = "error_404";
            $sed_page_id = "general_" . $sed_page_general;
            $sed_page_type = "general";
        } elseif( is_singular() ){
            $sed_post_id = get_queried_object()->ID;
            $sed_page_id = $sed_post_id;
            $sed_page_type = "post";
        } elseif ( is_post_type_archive() ) {
            $sed_post_type = get_queried_object()->name;
            $sed_page_id = "post_type_" . $sed_post_type;
            $sed_page_type = "post_type";
        } elseif ( is_author() ) {
            $sed_page_general = "author";
            $sed_page_id = "general_" . $sed_page_general;
            $sed_page_type = "general";
        } elseif ( is_date() || is_day() || is_month() || is_year() || is_time() ) {
            $sed_page_general = "date_archive";
            $sed_page_id = "general_" . $sed_page_general;
            $sed_page_type = "general";
        }

        //is_comments_popup() && is_date(archive by date And year And ...)

        return apply_filters( "sed_page_info_filter" , array( "id" => $sed_page_id, "type" => $sed_page_type) );
    }

    function set_page_info( ) {
        $info_u = $this->get_sed_page_info_uniqe();

        $this->sed_page_id = $info_u['id'];
        $this->sed_page_type = $info_u['type'];

        $settings = sed_get_page_options( $this->sed_page_id  , $this->sed_page_type );

        $GLOBALS['sed_data'] = $settings;

        global $sed_data;

        $sed_data['page_id']  = $this->sed_page_id;
        $sed_data['page_type'] = $this->sed_page_type;

    }

    function template_chooser( $template ) {

        $upload_dir = wp_upload_dir();

        $css_filename = "default_page";

        switch ($this->sed_page_type) {
            case "tax":
            case "general":
            case "post_type":
            case "author":
                $css_filename = 'sed_'. $this->sed_page_id ;
                break;
            case "post":
                $css_filename = 'sed_post'. $this->sed_page_id;
                break;
        }

        global $sed_data;

        //$this->sed_add_dynamic_css_file( $this->sed_page_id , $this->sed_page_type , $sed_data );

        if(  !empty( $css_filename ) && file_exists( $upload_dir['basedir'] . DS . 'siteeditor' . DS . $css_filename . '.css' ) )
            wp_enqueue_style( $this->sed_page_id .'_css' ,  $upload_dir['baseurl'] . '/siteeditor/' . $css_filename . '.css');

        return $template;
    }

    function add_rtl_body_class( $classes ) {
        if( is_rtl() ){
            $classes[] = 'rtl-body';
        }

        return $classes;
    }

    function sed_add_dynamic_css_file( ) {

        $sed_page_id = $this->sed_page_id;
        $sed_page_type = $this->sed_page_type;

        $css_filename = "default_page";

        switch ($sed_page_type) {
            case "tax":
            case "general":
            case "post_type":
                //case "author":
                $css_filename = 'sed_'. $sed_page_id ;
                break;
            case "post":
                $css_filename = 'sed_post'. $sed_page_id;
                break;
        }

        if( empty( $css_filename ) )
            return false;

        $upload_dir = wp_upload_dir();

        if (!file_exists(trailingslashit($upload_dir['basedir']) . "siteeditor")) {
            mkdir(trailingslashit($upload_dir['basedir']) . "siteeditor", 0777, true);
        }

        $filename = trailingslashit($upload_dir['basedir']) . "siteeditor/" . $css_filename . '.css';

        ob_start();
        include SED_INC_FRAMEWORK_DIR . DS . 'dynamic-css.php';
        $dynamic_css = ob_get_contents();
        ob_get_clean();

        global $wp_filesystem;
        if( empty( $wp_filesystem ) ) {
            require_once( ABSPATH .'/wp-admin/includes/file.php' );
            WP_Filesystem();
        }

        if( $wp_filesystem ) {
            $wp_filesystem->put_contents(
                $filename,
                $dynamic_css,
                FS_CHMOD_FILE // predefined mode settings for WP files
            );
        }

    }

    public function print_parallax_data(){
        $css_data = $this->dynamic_css_data;
        $parallax_backgrounds = array();

        if(!empty($css_data)){
            foreach( $css_data AS $selector => $styles ){
                foreach( $styles AS $property => $value){

                    if( $property == "parallax_background_image" ){
                        if( $value === true ){
                            if( isset( $css_data[$selector]['parallax_background_ratio'] ) )
                                $ratio = $css_data[$selector]['parallax_background_ratio'];
                            else
                                $ratio = 0.5;

                            $parallax_backgrounds[$selector] = $ratio;
                        }
                    }

                }

            }
        }

        ?>
        <script>
            var _sedAppParallaxBackgroundImage = <?php echo wp_json_encode( $parallax_backgrounds );?>;
        </script>
        <?php

    }
    
}

endif;