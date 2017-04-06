<?php

/**
 * General Page Options Class
 *
 * Implements General Options management in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 *
 * @Class SiteEditorPageOptions
 * @description : Create settings for all site pages
 * @general pages : like 404error , archives , ... (Using From @Options)
 * @Posts Page : include all default posts pages and custom post pages (Using From @PostMeta)
 */
class SiteEditorPageOptions {

    /**
     * All page options fields.
     *
     * @var string
     */
    private $fields = array();

    /**
     * All page options panels.
     *
     * @var string
     */
    private $panels = array();

    /**
     * All page options
     *
     * @var string
     */
    private $settings = array();

    /**
     * Capability required to edit this field.
     *
     * @var string
     */
    public $capability = 'manage_options';

    /**
     * this field group use :
     *  "general" || "style-editor" || "module" || "post"
     *
     * @access protected
     * @var array
     */
    private $option_group = 'sed_page_options';

    /**
     * This group title
     *
     * @access protected
     * @var array
     */
    public $title = '';

    /**
     * this group description
     *
     * @access protected
     * @var array
     */
    public $description = '';

    /**
     * prefix for controls ids for prevent conflict
     *
     * @var string
     * @access public
     */
    public $control_prefix = 'sed_page_options';

    /**
     * current group has styles settings (options) ?
     *
     * @var string
     * @access public
     */
    public $has_styles_settings = false;

    /**
     * Css Setting Type , Using "module" , "page" , "site"
     *
     * @var string
     * @access public
     */
    public $css_setting_type = "page";

    /**
     * SiteEditorPageOptions constructor.
     */
    public function __construct(){

        $this->title = __("Page Settings" , "site-editor");

        $this->description = __("Page general settings" , "site-editor");

        add_filter( "{$this->option_group}_fields_filter"           , array( $this , 'add_design_field' ) );

        add_action( "sed_editor_init"                               , array( $this , 'add_toolbar_elements' ) , 100 );

        add_action( "init"                                          , array( $this , 'register_options' ) , 80  );

        add_action( 'sed_app_register_general_options'              , array( $this , 'register_private_settings' ) );

        add_action( "sed_register_{$this->option_group}_options"    , array( $this , 'register_pages_options' ) );

        add_action( "sed_register_{$this->option_group}_options"    , array( $this , 'register_page_options_group' ) , -9999 );

        add_action( 'sed_app_preview_init'                          , array( $this , 'sed_app_preview_init' ) );

        add_action( 'wp_default_scripts'			                , array( $this , 'register_scripts' ), 11 );

        add_filter( 'sed_control_sub_category'                      , array( $this , 'set_sub_category' ) , 10 , 2 );

    }

    public function set_config(){

        $keys = array_keys( get_object_vars( $this ) );

        $config_vars = array( 'title' , 'description' , 'capability' );

        foreach ( $keys as $key ) {
            if ( in_array( $key , $config_vars ) && isset( $config[ $key ] ) ) {
                $this->$key = $config[ $key ];
            }
        }

    }

    function add_toolbar_elements(){
        global $site_editor_app;

        $site_editor_app->toolbar->add_element(
            "layout" ,
            "settings" ,
            "page-options" ,
            __("Page Settings","site-editor") ,
            "page_options_element" ,     //$func_action
            "" ,                //icon
            "" ,  //$capability=
            array(  ),  //"class"  => "btn_default3"
            array( "row" => 1 ,"rowspan" => 2 ),
            array('module' => 'options-engine' , 'file' => 'page_options.php'),
            //array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
            'all' ,
            array() ,
            array()
        );

    }

    /**
     * Registered all general and private page options
     *
     * @since 1.0.0
     * @access public
     */
    public function register_private_settings( $settings ){

        foreach( $this->fields AS $id => $args ){

            if( !isset( $args['setting_id'] ) )
                continue;

            $setting_id = $args['setting_id'];

            unset( $args['setting_id'] );

            if( isset( $args['id'] ) )
                unset( $args['id'] );

            if( isset( $args['type'] ) )
                unset( $args['type'] );

            $this->settings[$setting_id] = $args;

            $settings[ $setting_id ] = $args;
        }

        return $settings;
    }

    /**
     * Registered page options group
     *
     * @since 1.0.0
     * @access public
     */
    public function register_page_options_group(){

        SED()->editor->manager->add_group( $this->option_group , array(
            'capability'        => 'edit_theme_options',
            'theme_supports'    => '',
            'title'             => $this->title ,
            'description'       => $this->description ,
            'type'              => 'default',
            'pages_dependency'  => true
        ));

        SED()->editor->manager->add_group( $this->option_group . "_design_group" , array(
            'capability'        => $this->capability,
            'theme_supports'    => '',
            'title'             => $this->title ,
            'description'       => $this->description ,
            'type'              => 'default'
        ));

    }

    public function register_pages_options(){

        $options = $this->get_page_options( $_POST['page_id'] , $_POST['page_type'] , $_POST['post_type'] );

        $panels = $options['panels']; //var_dump( $panels );

        sed_options()->add_panels( $panels );

        $fields = $options['fields']; //var_dump( $fields );

        sed_options()->add_fields( $fields );

    }

    /**
     * Create private , layout , public fields & panels
     *
     * @param $page_id
     * @param $page_type
     * @param string $post_type
     * @return array
     */
    private function get_page_options( $page_id , $page_type , $post_type = '' ){

        $fields = $this->fields;
        $panels = $this->panels;

        $page_option_name = ( $page_type != "post" ) ? "sed_{$page_id}_settings" : "postmeta[{$post_type}][{$page_id}]";

        $page_control_prefix = $this->control_prefix . "_" . $page_id;

        $page_fields = array();
        $page_panels = array();

        foreach( $panels AS $key => $args ){

            $args['option_group'] = $this->option_group;

            if( ! isset( $args['capability'] ) || empty( $args['capability'] ) )
                $args['capability'] = $this->capability;

            $page_panels[ $key ] = $args ;
        }

        foreach( $fields AS $id => $args ){

            $args['category']  = isset( $args['category'] ) ? $args['category'] : 'page-settings';

            $args['option_group'] = $this->option_group;

            if( isset( $args['setting_id'] ) ) {

                $setting_id = $args['setting_id'];

                $args['setting_id'] = $page_option_name . "[" . $setting_id . "]";

            }

            if( ! isset( $args['capability'] ) || empty( $args['capability'] ) )
                $args['capability'] = $this->capability;

            if( $args['category'] == "style-editor" ){
                $args['css_setting_type'] = $this->css_setting_type;
            }

            $args['option_type'] = ( $page_type != "post" ) ? "option" : "postmeta";

            if( $page_type == "post" ){
                $args['setting_class'] = 'SiteEditorPostmetaSetting';
            }

            $page_fields[$id] = $args ;

        }

        $page_options = sed_options()->fix_controls_panels_ids( $page_fields , $page_panels , $page_control_prefix );

        $new_fields = $page_options['fields'] ;

        $new_panels = $page_options['panels'] ;

        return array(
            "fields"    => $new_fields ,
            "panels"    => $new_panels
        );

    }

    public function add_design_field( $fields ){

        if( ! isset( $_POST['setting_id'] ) || ! isset( $_POST['options_group'] ) || $_POST['options_group'] != $this->option_group ){
            return $fields;
        }

        $this->register_style_options();

        /**
         * please not change "design_panel" field id , it is using in js
         */
        if( $this->has_styles_settings === true ){
            $page_control_prefix = $this->control_prefix . "_" . $_POST['page_id'];
            $fields[ 'design_panel' ] = SED()->editor->design->get_design_options_field( $_POST['setting_id'] , $this->css_setting_type , $page_control_prefix );
        }

        return $fields;

    }

    public function register_style_options(){

        $options = apply_filters( "{$this->option_group}_design_options" , array() );

        if( !empty( $options ) ){

            $this->has_styles_settings = true;

            $option_group = $this->option_group . "_design_group";

            $control_prefix = $option_group;

            $page_control_prefix = $this->control_prefix . "_" . $_POST['page_id'];

            SED()->editor->design->add_style_options( $options , $option_group , $control_prefix , $page_control_prefix );// $this->option_group );

        }

    }


    public function register_options(){

        $panels = array();

        $fields = array();

        $this->fields = apply_filters( "{$this->option_group}_fields_filter" , $fields );

        $this->panels = apply_filters( "{$this->option_group}_panels_filter" , $panels );

    }

    /**
     * sub_category === settings id === $this->option_group . "_" . $page_id
     *
     * @param $sub_category
     * @param $control
     * @return string
     */
    public function set_sub_category( $sub_category , $control ){

        if( $control->option_group == $this->option_group && isset( $_POST['setting_id'] ) ){
            $sub_category = $_POST['setting_id'];
        }

        return $sub_category;

    }


    public function sed_app_preview_init(){

        add_action( 'wp_footer'           , array( $this, 'preview_enqueue_scripts' ), 10 );

    }

    public function preview_enqueue_scripts(){
        wp_enqueue_script( 'sed-pages-options-preview' );

        $page_id = SED()->framework->sed_page_id;

        if( SED()->framework->sed_page_type != "post" ) {
            $page_option_name = "sed_{$page_id}_settings";
        }else{
            $post = get_post( SED()->framework->sed_page_id );
            $page_option_name = "postmeta[{$post->post_type}][{$page_id}]";
        }

        $settings = array();

        foreach ( $this->settings as $id => $args ) {

            $setting = SED()->editor->manager->get_setting( "{$page_option_name}[{$id}]" ); 

            if( ! is_object( $setting ) || ! method_exists( $setting , 'check_capabilities' ) || ! $setting->check_capabilities() ){
                continue;
            }

            $settings[$id] = array(
                'transport' => isset( $args['transport'] ) ? $args['transport'] : 'refresh',
                'value'     => isset( $args['default'] ) ? $args['default'] : ''
            );
        }

        $exports = array(
            'settings'              => $settings ,
            'privateOption'         => $page_option_name . '[##id##]' ,
        );

        wp_scripts()->add_data( 'sed-pages-options-preview' , 'data', sprintf( 'var _sedAppPreviewPageOptionsData = %s;', wp_json_encode( $exports ) ) );
    }


    public function register_scripts( WP_Scripts $wp_scripts ){

        $suffix = ( SCRIPT_DEBUG ? '' : '.min' ) . '.js';

        $handle = 'sed-pages-options-preview';
        $src = SED_EXT_URL . 'options-engine/assets/js/pages-options-preview' . $suffix ;
        $deps = array( 'sed-frontend-editor' );

        $in_footer = 1;
        $wp_scripts->add( $handle, $src, $deps, SED_VERSION, $in_footer );

    }

}



