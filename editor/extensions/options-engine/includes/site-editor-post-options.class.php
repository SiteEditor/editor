<?php

/**
 * Manage Post And Post Meta Options Class
 *
 * Implements Post And Post Meta Options management in the SiteEditor Application.
 *
 * @package SiteEditor
 * @subpackage Options
 */

/**
 * Add Custom Post Meta Options
 *
 * @Class SiteEditorPostOptions
 */
class SiteEditorPostOptions {

    /**
     * pattern for post options partials
     */
    const PARTIAL_ID_PATTERN = '/^sed_post_partials\[(?P<post_type>[^\]]+)\]\[(?P<post_id>-?\d+)\]\[(?P<partial_key>.+)\]$/';

    /**
     * pattern for post options groups
     */
    const OPTION_GROUP_PATTERN = '/^sed_post_options_(?P<post_id>.+)$/';

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
    private $option_group = '';

    /**
     * default option type
     *
     * @access public
     * @var array
     */
    public $option_type  = "postmeta";

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
     * All partials
     *
     * @access protected
     * @var string
     */
    protected $partials = array();

    /**
     * prefix for controls ids for prevent conflict
     *
     * @var string
     * @access public
     */
    public $control_prefix = '';


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
     * If current group is a post options group
     *
     * @var string
     * @access public
     */
    public $is_post_options_group = false;

    /**
     * SiteEditorPostOptions constructor.
     */
    public function __construct(){

        add_action( "sed_editor_init"               , array( $this, "add_toolbar_elements" ) );

        add_action( "init"                          , array( $this , 'register_options' ) , 80  );

        add_action( "init"                          , array( $this , 'set_settings' ) , 85  );

        add_action( "init"                          , array( $this , 'create_post_meta' ) , 90  );

        $this->is_post_options_group = isset( $_POST['action'] ) && $_POST['action'] == "sed_load_options" && isset( $_POST['options_group'] ) && preg_match( self::OPTION_GROUP_PATTERN , $_POST['options_group'], $matches );

        if( $this->is_post_options_group ) {

            $this->option_group = $_POST['options_group'];

            $this->control_prefix = $this->option_group;

            add_action( "init", array( $this, 'set_post_settings_info' ) );

            add_action("sed_register_{$this->option_group}_options", array($this, 'register_post_options'));

            add_action("sed_register_{$this->option_group}_options", array($this, 'register_post_options_group'), -9999);

            add_action( "sed_register_{$this->option_group}_options"    , array( $this , 'add_design_field' ) , 1 );

        }

        add_action( 'sed_app_register'                              , array( $this, 'setup_selective_refresh' ) );

    }

    public function set_post_settings_info(){

        $post_type = get_post_type_object( $_POST['post_type'] );

        $this->title = sprintf(__("Single %s Options" , "site-editor") , $post_type->labels->name );

        $this->description = $post_type->description;
    }

    public function add_toolbar_elements(){
        global $site_editor_app;

        $site_editor_app->toolbar->add_element(
            "layout" ,
            "settings" ,
            "post-options" ,
            __("Current Post Customize","site-editor") ,
            "post_options_element" ,     //$func_action
            "" ,                //icon
            "" ,  //$capability=
            array(  ),  //"class"  => "btn_default3"
            array( "row" => 1 ,"rowspan" => 2 ),
            array('module' => 'options-engine' , 'file' => 'post_options.php'),
            //array( "pages" , "blog" , "woocammece" , "search" , "single_post" , "archive" )
            'all' ,
            array() ,
            array()
        );

    }

    /**
     * Registered page options group
     *
     * @since 1.0.0
     * @access public
     */
    public function register_post_options_group(){

        SED()->editor->manager->add_group( $this->option_group , array(
            'capability'        => 'edit_theme_options',
            'theme_supports'    => '',
            'title'             => $this->title ,
            'description'       => $this->description ,
            'type'              => 'default' ,
            //'pages_dependency'  => true
        ));

        SED()->editor->manager->add_group( $this->option_group . "_design_group" , array(
            'capability'        => $this->capability,
            'theme_supports'    => '',
            'title'             => $this->title ,
            'description'       => $this->description ,
            'type'              => 'default'
        ));

    }

    public function register_post_options(){

        $options = $this->get_post_options( $_POST['page_id'] , $_POST['page_type'] , $_POST['post_type'] );

        $panels = $options['panels']; //var_dump( $panels );

        sed_options()->add_panels( $panels );

        $fields = $options['fields']; //var_dump( $fields );

        sed_options()->add_fields( $fields );

    }

    /**
     * Create private , layout , public fields & panels
     * in this version only support postmeta ( not support post settings )
     *
     * @param $page_id
     * @param $page_type
     * @param string $post_type
     * @return array
     */
    private function get_post_options( $page_id , $page_type , $post_type = '' ){

        if( ! isset( $this->fields[$post_type] ) ){

            return array(
                "fields"    => array() ,
                "panels"    => array()
            );

        }

        $fields = $this->fields[$post_type];

        $panels =  ( isset( $this->panels[$post_type] ) ) ?  $this->panels[$post_type] : array();

        $post_option_name = "postmeta[{$post_type}][{$page_id}]";

        $post_fields = array();
        $post_panels = array();

        foreach( $panels AS $key => $args ){

            $args['option_group'] = $this->option_group;

            if( ! isset( $args['capability'] ) || empty( $args['capability'] ) )
                $args['capability'] = $this->capability;

            $post_panels[ $key ] = $args ;
        }

        foreach( $fields AS $id => $args ){

            $args['category']  = isset( $args['category'] ) ? $args['category'] : 'post-settings';

            $args['option_group'] = $this->option_group;

            if( $args['category'] != "style-editor" && isset( $args['setting_id'] ) ) {

                //$primary_setting_id === meta_key
                $primary_setting_id = $args['setting_id'];

                $args['setting_id'] = $post_option_name . "[" . $primary_setting_id . "]";

            }

            if( ! isset( $args['option_type'] ) || empty( $args['option_type'] ) )
                $args['option_type'] = $this->option_type;

            if( ! isset( $args['capability'] ) || empty( $args['capability'] ) )
                $args['capability'] = $this->capability;

            if( $args['category'] == "style-editor" && ( !isset( $args['css_setting_type'] ) || empty( $args['css_setting_type'] ) )){
                $args['css_setting_type'] = $this->css_setting_type;
            }

            if( isset( $args['partial_refresh'] ) && $args['category'] != "style-editor" && isset( $args['setting_id'] ) ){

                $partial_args = $args['partial_refresh'];

                $partial_id_base = "sed_post_partials[{$post_type}][{$page_id}][##id##]";

                if( is_array( $partial_args ) && isset( $partial_args['render_callback'] ) && isset( $partial_args['selector'] ) ) {

                    $args['partial_refresh']['partial_id'] = str_replace( "##id##" , $primary_setting_id , $partial_id_base );

                }elseif( is_array( $partial_args ) ) {

                    foreach ( $partial_args AS $curr_partial_id => $curr_partial_args ) {

                        if( is_array( $curr_partial_args ) && isset( $curr_partial_args['render_callback'] ) && isset( $curr_partial_args['selector'] ) ) {

                            $args['partial_refresh'][$curr_partial_id]['partial_id'] = str_replace( "##id##" , $curr_partial_id , $partial_id_base );

                        }

                    }

                }

            }

            $post_fields[ $id ] = $args ;

        }

        $page_options = sed_options()->fix_controls_panels_ids( $post_fields , $post_panels , $this->control_prefix );

        $new_fields = $page_options['fields'] ;

        $new_panels = $page_options['panels'] ;

        return array(
            "fields"    => $new_fields ,
            "panels"    => $new_panels
        );

    }

    public function add_design_field( ){

        $this->register_style_options();

        $fields = array();

        /**
         * please not change "design_panel" field id , it is using in js
         */
        if( $this->has_styles_settings === true ){
            $fields[ 'design_panel' ] = SED()->editor->design->get_design_options_field( $this->option_group , $this->css_setting_type );

            $this->add_fields( $fields , $_POST['post_type'] );
        }

    }

    public function register_style_options(){

        $options = apply_filters( "sed_post_design_options" , array() , $_POST['page_id'] , $_POST['post_type'] );

        if( !empty( $options ) ){

            $this->has_styles_settings = true;

            $option_group = $this->option_group . "_design_group";

            $control_prefix = $option_group;

            SED()->editor->design->add_style_options( $options , $option_group , $control_prefix , $this->option_group );

        }

    }


    public function register_options(){

        do_action( 'sed_add_meta_panels' );

    }

    public function add_fields( $fields , $post_types ){

        if( is_string( $post_types ) ){
            $post_types = ( $post_types == "all" ) ? array_keys( $this->get_post_types() ) : array( $post_types );
        }

        if ( is_array( $post_types ) && !empty( $post_types ) && is_array($fields) && !empty($fields) ) {

            foreach ( $post_types AS $post_type ) {

                if ( ! isset( $this->fields[$post_type] ) ) {

                    $this->fields[$post_type] = array();

                }

                $this->fields[$post_type] = array_merge( $this->fields[$post_type], $fields );

            }

            foreach( $fields AS $field_id => $field_args ){
                $field_args['post_types'] = $post_types;
                $this->settings[$field_id] = $field_args;
            }

        }

    }

    public function set_settings( ){

        //$this->register_options();

        foreach( $this->settings AS $id => $args ){

            if( !isset( $args['setting_id'] ) && !isset( $args['postmeta_class'] ) )
                continue;

            if( isset( $args['setting_id'] ) ) {

                $setting_id = $args['setting_id'];

            }else{

                $setting_id = $id;

            }

            if( isset( $args['id'] ) )
                unset( $args['id'] );

            if( isset( $args['type'] ) )
                unset( $args['type'] );

            $this->settings[$id] = $args;

            if( isset( $args['partial_refresh'] ) && site_editor_app_on() ){

                $partial_args = $args['partial_refresh'];

                $args['option_group'] = $this->option_group;

                if( is_array( $partial_args ) && isset( $partial_args['render_callback'] ) && isset( $partial_args['selector'] ) ) {
                    $partial_args['setting_id'] = $setting_id;
                    $this->partials[$setting_id] = sed_options()->get_partial_args( $partial_args , $setting_id , $args );
                }elseif( is_array( $partial_args ) ) {

                    foreach ( $partial_args AS $curr_partial_id => $curr_partial_args ) {

                        if( is_array( $curr_partial_args ) && isset( $curr_partial_args['render_callback'] ) && isset( $curr_partial_args['selector'] ) ) {

                            $curr_partial_args['setting_id'] = $setting_id;

                            $this->partials[$curr_partial_id] = sed_options()->get_partial_args( $curr_partial_args , $setting_id , $args );

                        }

                    }

                }

            }
        }

    }

    public function add_panels( $panels , $post_types ){

        if( is_string( $post_types ) ){
            $post_types = ( $post_types == "all" ) ? array_keys( $this->get_post_types() ) : array( $post_types );
        }

        if ( is_array( $post_types ) && !empty( $post_types ) && is_array($panels) && !empty($panels) ) {

            foreach ( $post_types AS $post_type ) {

                if ( ! isset( $this->panels[$post_type] ) ) {

                    $this->panels[$post_type] = array();

                }

                $this->panels[$post_type] = array_merge( $this->panels[$post_type], $panels );

            }

        }

    }

    public function get_post_types( ){

        $post_type_objects = get_post_types( array( 'public' => true , 'show_ui' => true ), 'objects' );

        unset( $post_type_objects['attachment'] );

        $post_types = array_keys( $post_type_objects );

        return $post_types;
    }

    public function create_post_meta(){

        if( !empty( $this->settings ) && is_array( $this->settings ) ) {

            foreach ( $this->settings AS $id => $args ) {

                if( ! isset( $args['postmeta_class'] ) ) {

                    if(  ! isset( $args['setting_id'] ) || ( isset( $args['category'] ) && $args['category'] == "style-editor" ) ){
                        continue;
                    }

                    if( ( ! isset( $args['post_types'] ) && ! isset( $args['post_type_supports'] ) ) || ( empty( $args['post_types'] ) && empty( $args['post_type_supports'] ) ) ){
                        continue;
                    }

                    $args = array(
                        'meta_key'              => $args['setting_id'],
                        'post_types'            => isset( $args['post_types'] ) ? $args['post_types'] : '' ,
                        'default'               => isset($args['default']) ? $args['default'] : '',
                        'setting_transport'     => isset($args['transport']) ? $args['transport'] : 'refresh',
                        'unique'                => true ,
                        //'theme_supports'        => isset( $args['theme_supports'] ) ? $args['theme_supports'] : '' ,
                        //'post_type_supports'    => isset( $args['post_type_supports'] ) ? $args['theme_supports'] : '',
                        //'capability'            => isset( $args['capability'] ) ? $args['capability'] : '' ,
                        //'sanitize_callback'     => isset( $args['sanitize_callback'] ) ? $args['sanitize_callback'] : '' ,
                        //'sanitize_js_callback'  => isset( $args['sanitize_js_callback'] ) ? $args['sanitize_js_callback'] : '' ,
                        //'validate_callback'     => isset( $args['validate_callback'] ) ? $args['validate_callback'] : ''
                    );

                    new SiteEditorPostmetaOption($args);

                }else{

                    $class_name = $args['postmeta_class'];

                    new $class_name();

                }

            }

        }

    }

    public function setup_selective_refresh(){

        if( site_editor_app_on() ) {
            add_filter('sed_app_dynamic_partial_args', array($this, 'filter_dynamic_partial_args'), 10, 2);

            add_filter('sed_app_dynamic_partial_class', array($this, 'filter_dynamic_partial_class'), 5, 3);
        }
    }

    public function filter_dynamic_partial_args( $args, $id ){

        if ( preg_match( self::PARTIAL_ID_PATTERN, $id, $matches ) ) {

            if ( ! isset( $this->partials[ $matches['partial_key'] ] ) ) {
                return $args;
            }

            $registered = $this->partials[ $matches['partial_key'] ];

            if ( false === $args ) {
                $args = array();
            }

            $args = array_merge(
                $args,
                $registered
            );

            if( isset( $args['setting_id'] ) ){
                $args['settings'] = array( "postmeta[{$matches['post_type']}][{$matches['post_id']}][{$args['setting_id']}]" );
                unset( $args['setting_id'] );
            }

        }

        return $args;
    }

    public function filter_dynamic_partial_class( $class, $id, $args ){

        unset( $id );

        if ( isset( $args['partial_class'] ) ) {
            $class = $args['partial_class'];
        }

        return $class;
    }

}

/**
 * add meta panel to customize post options
 *
 * @param $id
 * @param $args
 */
function sed_add_meta_panel( $id , $args ){

    $defaults = array(
        'title'             => '' ,
        'description'       => '' ,
        'post_types'        => array() ,
        'fields'            => array() ,
        'panels'            => array() ,
        'priority'          => 100 ,
        'capability'        => '' ,
        'theme_supports'    => '' ,
        'type'              => 'default'
    );

    $args = wp_parse_args(  $args , $defaults );

    $panels = array(
        $id     =>   array(
            'title'             => $args['title'],
            'description'       => $args['description'],
            'priority'          => $args['priority'],
            'capability'        => $args['capability'],
            'theme_supports'    => $args['theme_supports'],
            'type'              => $args['type']
        )
    );

    foreach( $args['panels'] AS $panel_id => $panel_args ){

        if( isset( $panel_args['parent_id'] ) && $panel_args['parent_id'] == "root" ){
            $panel_args['parent_id'] = $id;
        }

        $panels[$panel_id] = $panel_args;
    }

    sed_options()->post->add_panels( $panels , $args['post_types'] );

    $fields = $args['fields'];

    foreach( $fields AS $field_id => $field_args ){

        if( !isset( $field_args['panel'] ) || ! array_key_exists( $field_args['panel'] , $panels  ) ){
            $fields[$field_id]['panel'] = $id;
        }

    }

    sed_options()->post->add_fields( $fields , $args['post_types'] );

}

/*function add_custom_meta_panels(){

    $fields = array(

        'page_length' => array(
            'setting_id'            => "new_page_length" , // setting_id === meta_key
            "type"                  => "radio-buttonset" ,
            "label"                 => __("Page Length", "site-editor"),
            "description"           => __("This option allows you to set a title for your image.", "site-editor"),
            'default'               => 'default',
            "choices"               =>  array(
                "default"               =>    __( "Default" , "site-editor" ) ,
                "wide"                  =>    __( "Wide" , "site-editor" ) ,
                "boxed"                 =>    __( "Boxed" , "site-editor" )
            ),
            'transport'             => 'postMessage' ,
            'priority'              => 5 ,
        ),

        'radio_section' => array(
            'setting_id'        => 'sed_radio_setting',
            'label'             => __('My custom control', 'translation_domain'),
            'type'              => 'radio',
            'priority'          => 10,
            'default'           => 'options3_key',
            'transport'         => 'refresh' ,
            'choices'           => array(
                "options1_key"      =>    "options1_value" ,
                "options2_key"      =>    "options2_value" ,
                "options3_key"      =>    "options3_value" ,
                "options4_key"      =>    "options4_value" ,
            ) ,
            //'input_attrs'
            "atts"              => array(
                "class"         =>    "custom-textarea-class1 custom-textarea-class2" ,
                "data-custom"   =>    "test" ,
            )
        )

    );
    
    sed_add_meta_panel( 'sed_post_custom_options' , array(
        'title'             => __("Test Post Meta Panel", "site-editor") ,
        'description'       => __("Test Post Meta Panel", "site-editor") ,
        'post_types'        => array( 'post' , 'page' ) ,
        'fields'            => $fields ,
        'panels'            => array() ,
        'priority'          => 100 ,
        'type'              => 'default'
    ) );

}

add_action( 'sed_add_meta_panels' , 'add_custom_meta_panels' );*/

 /*$fields = array(

    'page_length' => array(
        'setting_id'            => "page_length" , // setting_id === meta_key
        "type"                  => "radio-buttonset" ,
        "label"                 => __("Page Length", "site-editor"),
        "description"           => __("This option allows you to set a title for your image.", "site-editor"),
        'default'               => 'default',
        'theme_supports'        => 'site_layout_feature' ,
        "choices"               =>  array(
            "default"               =>    __( "Default" , "site-editor" ) ,
            "wide"                  =>    __( "Wide" , "site-editor" ) ,
            "boxed"                 =>    __( "Boxed" , "site-editor" )
        ),
        'transport'             => 'postMessage' ,
        'priority'              => 5 ,
        'option_type'           => 'postmeta' ,
        'capability'            => 'edit_posts' ,
        'sanitize_callback'     => '' ,
        'sanitize_js_callback'  => '' ,
        'validate_callback'     => '' ,
        'post_type_supports'    => '' ,
        //'postmeta_class'        => 'SiteEditorPageTemplateController'
    ),

    'page_template' => array(
        "type"                  => "select" ,
        "label"                 => __("Page Template", "site-editor"),
        "description"           => __("This option allows you to set a title for your image.", "site-editor"),
        "choices"               =>  array(
            "default"               =>    __( "Default" , "site-editor" ) ,
            "wide"                  =>    __( "Wide" , "site-editor" ) ,
            "boxed"                 =>    __( "Boxed" , "site-editor" )
        ),
        'priority'              => 5 ,
        'postmeta_class'        => 'SiteEditorPageTemplateController'
    )

);*/