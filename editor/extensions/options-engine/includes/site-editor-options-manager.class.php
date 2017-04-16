<?php
/**
 * SiteEditor Options Manager classes
 *
 * @package SiteEditor
 * @subpackage Options
 * @since 1.0.0
 */

/**
 * SiteEditor Options Manager class.
 *
 * Manage all SiteEditor Application Options
 *
 * Serves as a factory for Fields and Settings and Controls, and
 * instantiates default Fields and Settings and Controls.
 *
 * @since 1.0.0
 */

final class SiteEditorOptionsManager{

    /**
     * An array of our panel types.
     * Or panel types that may be rendered from JS templates.
     *
     * @access private
     * @var array
     */
    private $panel_types = array(
        'default'   => 'SiteEditorOptionsPanel'
    );

    /**
     * An array of our control types.
     * Or panel types that may be rendered from JS templates.
     *
     * @access private
     * @var array
     */
    private $control_types = array();

    /**
     * An array of our field types.
     * Or panel types that may be rendered from JS templates.
     *
     * @access private
     * @var array
     */
    private $field_types = array();

    /**
     * Instance of SiteEditorOptionsDependencyManager
     *
     * @access private
     * @var array
     */
    private $dependency_manager;

    /**
     * Registered instances of SiteEditorField.
     *
     * @since 1.0.0
     * @access protected
     * @var array
     */
    protected $fields = array();

    /**
     * Registered Preview Params
     *
     * @since 1.0.0
     * @access protected
     * @var array
     */
    protected $preview_params = array();

    /**
     * Registered instances of SiteEditorPostOptions.
     *
     * @since 1.0.0
     * @access protected
     * @var array
     */
    public $post = array();

    /**
     * instances of SiteEditorColorScheme.
     *
     * @since 1.0.0
     * @access public
     * @var object
     */
    public $color_scheme;

    /**
     * SiteEditorOptionsManager constructor.
     * @param $editor object instance of SiteEditorApp
     */
    function __construct( $editor ) {

        require_once dirname( __FILE__ ) . DS . 'site-editor-options-template.class.php';

        require_once dirname( __FILE__ ) . DS . 'site-editor-field.class.php';

        require_once dirname( __FILE__ ) . DS . 'site-editor-dependency-manager.class.php';

        $this->dependency_manager = new SiteEditorOptionsDependencyManager();

        require_once dirname( __FILE__ ) . DS . 'site-editor-page-options.class.php';

        new SiteEditorPageOptions();

        require_once dirname( __FILE__ ) . DS . 'site-editor-post-options.class.php';

        $this->post = new SiteEditorPostOptions();

        $developer_options = sed_get_plugin_options("developer-sample-options");

        if( $developer_options == "on" ) {
            //include sample options
            require_once dirname(__FILE__) . DS . 'demo' . DS . 'site-editor-sample-options.php';
        }

        require_once dirname( __FILE__ ) . DS . 'site-editor-site-options.class.php';

        new SiteEditorSiteOptions();

        require_once dirname( __FILE__ ) . DS . 'site-editor-theme-options.class.php';

        new SiteEditorThemeOptions();

        require_once dirname( __FILE__ ) . DS . 'site-editor-custom-code.class.php';

        new SiteEditorCustomCodeOptions();

        require_once dirname( __FILE__ ) . DS . 'site-editor-site-custom-css.class.php';

        new SiteEditorSiteCustomCss();

        require_once dirname( __FILE__ ) . DS . 'site-editor-color-scheme.class.php';

        $this->color_scheme = new SiteEditorColorScheme();

        require_once dirname( __FILE__ ) . DS . 'site-editor-color-options.class.php';

        new SiteEditorColorOptions( $this->color_scheme );

        require_once dirname( __FILE__ ) . DS . 'site-editor-font-options.class.php';

        new SiteEditorFontOptions();

        require_once dirname( __FILE__ ) . DS . 'site-editor-sanitize-settings.class.php';
        
        add_action( 'sed_after_init_manager', array( $this, 'register_components' ) , 10 , 1 );

        add_action( 'sed_save_after_init_manager', array( $this, 'register_components' ) , 10 , 1 );

        add_action( 'sed_preview_after_init_manager', array( $this, 'register_components' ) , 10 , 1 );

        add_action( 'sed_app_register', array( $this, 'register_fields' ) , 1000 );

        add_action( 'wp_ajax_sed_load_options', array($this,'sed_ajax_load_options' ) );//wp_ajax_sed_load_options

        add_action( 'sed_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        add_action( 'sed_print_footer_scripts', array( $this, 'print_templates' ) );

        //add_action( 'sed_print_footer_scripts', 'customize_themes_print_templates' );

        add_action( 'sed_app_controls_init', array( $this, 'enqueue_wp_editor' ) );

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_preview_scripts' ) );

        add_action( 'wp_enqueue_media' , array( $this , 'print_media_templates' ) );

        add_action( 'sed_print_styles', 'sed_site_icon', 99 );

        add_filter( "sed_app_refresh_nonces" , array( $this, 'set_nonces' ) , 10 , 2 );

    }

    public function set_nonces( $nonces , $manager ){

        $nonces['options'] = array(
            'load'                  =>  wp_create_nonce( 'sed_app_options_load_' . $manager->get_stylesheet() )
        );

        return $nonces;
    }

    public function sed_ajax_load_options(){ 

        SED()->editor->manager->check_ajax_handler( 'sed_options_loader' , 'sed_app_options_load' , 'sed_manage_settings' );

        if( !isset( $_POST['options_group'] ) || empty( $_POST['options_group'] ) || !isset( $_POST['setting_id'] ) || empty( $_POST['setting_id'] ) ){

            $data = array(
                'settingId'   => isset( $_POST['setting_id'] ) ? $_POST['setting_id'] : '',
                'message'     => __( 'Data is not valid.' , 'site-editor' ),
            );

            wp_send_json_error( $data );

        }

        do_action( "sed_register_{$_POST['options_group']}_options" );

        $this->register_fields();

        $data = $this->get_settings_data( $_POST['options_group'] , $_POST['setting_type'] );

        if( is_wp_error( $data ) ){

            $data = array(
                'settingId'   => $_POST['setting_id'],
                'message'     => $data->get_error_message(),
            );

            wp_send_json_error( $data );

        }

        wp_send_json_success( $data );

    }

    public function get_settings_data( $group_id , $group_type ){

        $data = array(
            "settings"          =>  array() ,
            "controls"          =>  array() ,
            "panels"            =>  array() ,
            "relations"         =>  array() ,
            "output"            =>  "" ,
            "settingId"         =>  $_POST['setting_id'] ,
            "settingType"       =>  $group_type ,
            "groups"            =>  array() ,
            "designTemplate"    =>  "" ,
            "partials"          =>  array() ,
            "previewParams"     =>  array()
        );

        $groups = SED()->editor->manager->groups();

        /**
         * Design Group Not Support Js Controls , JS Panels
         * Dependency And Settings
         * This Group only output support
         */
        $design_group_id = $group_id . "_design_group";

        foreach ( $groups AS $id => $group ){

            if( $id == $group_id ){
                $current_group = $group;
            }else if( $id == $design_group_id ){
                $design_group = $group;
            }

        }

        if( ! isset( $current_group ) ){
            return new WP_Error( 'options_group_invalid', __( "This group not registered already", "site-editor" ) );
        }

        if( ! $current_group->check_capabilities() ){
            return new WP_Error( 'options_not_access', __( "You can not access to this group options", "site-editor" ) );
        }

        $allow_design = isset( $design_group ) &&  $design_group->check_capabilities(); 

        $data['groups'][$current_group->id] = $current_group->json();

        $panels = SED()->editor->manager->panels();

        $group_panels = array();

        $design_group_panels = array();

        foreach ( $panels AS $panel_id => $panel ){

            if ( $panel->check_capabilities() ) {

                if ( $panel->option_group == $group_id ) {

                    $group_panels[$panel_id] = $panel;

                    $data['panels'][$panel_id] = $panel->json();

                }else if( $panel->option_group == $design_group_id ){

                    $design_group_panels[$panel_id] = $panel;

                }

            }

        }

        $current_group->panels = $group_panels;

        if( $allow_design )
            $design_group->panels = $design_group_panels;

        $controls = SED()->editor->manager->controls(); //var_dump( $group_panels );

        $group_controls = array();

        $design_group_controls = array();

        foreach ( $controls AS $control_id => $control ){

            if ( $control->check_capabilities() ) {

                if ( $control->option_group == $group_id ) {

                    $group_controls[$control_id] = $control;

                    $data['controls'][$control_id] = $control->json();

                }else if( $control->option_group == $design_group_id ){

                    $design_group_controls[$control_id] = $control;

                }

            }

        }

        //var_dump( $allow_design );

        $current_group->controls = $group_controls; //var_dump( $design_group_controls );

        if( $allow_design )
            $design_group->controls = $design_group_controls;

        $settings = SED()->editor->manager->settings();

        $group_settings = array();

        foreach ( $settings AS $setting_id => $setting ){

            if ( $setting->check_capabilities() ) {

                if ( $setting->option_group == $group_id ) {

                    $group_settings[$setting_id] = $setting;

                    $data['settings'][$setting_id] = array(
                        'value'     	=> $setting->js_value(),
                        'transport' 	=> $setting->transport,
                        'dirty'     	=> $setting->dirty,
                        'type'          => $setting->type ,
                        'option_type'   => $setting->option_type ,
                    );

                }

            }

        }

        $current_group->settings = $group_settings;

        if( $allow_design )
            $design_group->settings = array();

        $dependencies = $this->dependency_manager->dependencies( true );

        $data['relations'] = isset( $dependencies[ $group_id ] ) ? $dependencies[ $group_id ] : array();

        $data['output'] = $current_group->get_content();

        $data['designTemplate'] = ( $allow_design ) ? $design_group->get_content() : "";

        $partials = SED()->editor->manager->selective_refresh->partials();

        $group_partials = array();

        foreach ( $partials as $partial ) {

            if ( $partial->check_capabilities() && $partial->option_group == $group_id ) {

                $group_partials[ $partial->id ] = $partial->json();

            }

        }

        $data['partials'] = $group_partials;

        $group_preview_params = array(); 

        foreach ( $this->preview_params as $preview_param_id => $preview_param_args  ) {

            if ( isset( $preview_param_args['option_group'] ) &&  $preview_param_args['option_group'] == $group_id ) {

                $group_preview_params[ $preview_param_id ] = $preview_param_args;

            }

        }

        $data['previewParams'] = $group_preview_params;
        
        return $data;
    }

    public function register_components(  ){

        $this->register_panels_components(  );

        $this->register_controls_components(  );

        $this->register_fields_components(  );

    }

    private function register_panels_components(  ){

        $panels_path = dirname( __FILE__ ) . DS . "panels" . DS . "*panel.class.php" ;

        foreach ( glob( $panels_path ) as $php_file ) {
            require_once $php_file;
        }

    }

    /**
     * Full support nesting level by all panels
     * Load all panel file
     * Current register types : 1. default(fieldset) 2.expanded( accordion-item ) 3. inner_box
     * @todo add 3 panels new type : 1.group-expanded( accordion )  2.tab-items   3.dialog   4.tab-tour
     */
    private function register_fields_components(  ){

        $panels_path = dirname( __FILE__ ) . DS . "fields" . DS . "*field.class.php" ;

        foreach ( glob( $panels_path ) as $php_file ) {
            require_once $php_file;
        }

    }

    private function register_controls_components(  ){

        $panels_path = dirname( __FILE__ ) . DS . "controls" . DS . "*control.class.php" ;

        foreach ( glob( $panels_path ) as $php_file ) {
            require_once $php_file;
        }

    }

    final protected function add_setting( $id , $args = array() ) {

        if ( $id instanceof SedAppSettings ) {
            $setting = $id;
        } else {

            $class_name = 'SedAppSettings';

            if ( isset( $args['setting_class'] ) && class_exists( $args['setting_class'] ) ) {
                $class_name = $args['setting_class'] ;
            }

            $setting = new $class_name( SED()->editor->manager , $id, $args );

        }

        SED()->editor->manager->add_setting( $setting );

        return $setting;

    }

    /**
     * Register new control type
     *
     * @since 1.0.0
     * @access public
     *
     * @param $type
     * @param $php_class --- panel class
     */
    public function register_control_type( $type , $php_class ){

        $this->control_types[ $type ] = $php_class;

        SED()->editor->manager->register_control_type( $php_class );

    }

    /**
     * Adds the control.
     *
     * @access protected
     * @param array $args The field definition as sanitized in SiteEditorField
     */
    final protected function add_control( $id, $args = array() ) {

        if ( $id instanceof SiteEditorOptionsControl ) {
            $control = $id;
        } else {

            $class_name = 'SiteEditorOptionsControl';

            if ( array_key_exists( $args['type'], $this->control_types ) ) {
                $class_name = $this->control_types[ $args['type'] ];
            }

            $control = new $class_name( SED()->editor->manager , sanitize_key( $id ), $args );

        }

        SED()->editor->manager->add_control( $control );

    }

    /**
     * Register multi controls
     *
     * @param array $panels
     * @access public
     */
    public static function add_controls( $panels = array() ){

        if( !empty( $panels ) && is_array( $panels ) ) {

            foreach ( $panels AS $panel_id => $args ) {

                if ( $args instanceof SiteEditorOptionsControl ) {
                    self::add_control( $args );
                }else {
                    self::add_control( $panel_id , $args );
                }

            }

        }

    }

    /**
     * Register new panel type
     *
     * @since 1.0.0
     * @access public
     *
     * @param $type
     * @param $php_class --- panel class
     */
    public function register_panel_type( $type , $php_class ){

        $this->panel_types[ $type ] = $php_class;

        SED()->editor->manager->register_panel_type( $php_class );

    }

    /**
     * Add a customize panel.
     *
     * @since 4.0.0
     * @since 4.5.0 Return added WP_Customize_Panel instance.
     * @access public
     *
     * @param WP_Customize_Panel|string $id   Customize Panel object, or Panel ID.
     * @param array                     $args Optional. Panel arguments. Default empty array.
     *
     * @return WP_Customize_Panel             The instance of the panel that was added.
     */
    public function add_panel( $id, $args = array() ) {

        if ( $id instanceof SiteEditorOptionsPanel ) {
            $panel = $id;
        } else {

            if ( ! isset( $args['type'] ) || ! array_key_exists( $args['type'], $this->panel_types ) ) {
                $args['type'] = 'default';
            }

            $panel_class_name = $this->panel_types[ $args['type'] ];

            $panel = new $panel_class_name( SED()->editor->manager, sanitize_key( $id ), $args );

        }

        /**
         * @important todo For Fix Bug
         * @todo add panel dependencies to site editor manager class
         * in this version not support call directly SED()->editor->manager->add_panel()
         * if panel dependency not empty instead using sed_options()->add_panel()
         */

        if( !empty( $panel->dependency ) && is_array( $panel->dependency ) ){
            $this->dependency_manager->add( $panel->option_group , $panel->id , $panel->dependency );
        }

        SED()->editor->manager->add_panel( $panel );
    }

    /**
     * Register multi panels
     *
     * @param array $panels
     * @access public
     */
    public function add_panels( $panels = array() ){

        if( !empty( $panels ) && is_array( $panels ) ) {

            foreach ( $panels AS $panel_id => $args ) {

                if ( $args instanceof SiteEditorOptionsPanel ) {
                    $this->add_panel($args);
                }else {
                    $this->add_panel($panel_id, $args);
                }

            }

        }

    }

    /**
     * Fix all controls ids for a special group options
     * Add prefix for Controls ids for prevent conflict controls in php & js
     * Fix Dependency Controls Ids
     *
     * @param array $fields
     * @param array $panels
     * @param string $prefix
     * @return array
     * @access public
     */
    public function fix_controls_panels_ids( $fields , $panels , $prefix ){

        $new_panels_ids = array();

        $new_panels = array();

        if( !empty( $panels ) ){

            foreach ( $panels AS $panel_id => $args ){

                if( isset( $args['parent_id'] ) && $args['parent_id'] != "root" ) {
                    $args['parent_id'] = "{$prefix}_{$args['parent_id']}";
                }

                if( isset( $args['dependency'] ) && !empty( $args['dependency'] ) ) {
                    $args['dependency'] = $this->dependency_manager->fix_dependency_controls_ids( $args['dependency'] , $prefix );
                }

                $new_panels["{$prefix}_{$panel_id }"] = $args;

                $new_panels_ids[$panel_id] = "{$prefix}_{$panel_id}";

            }

        }

        $new_fields = array();

        if( !empty( $fields ) ){

            foreach ( $fields AS $field_id => $args ){

                if( isset( $args['panel'] ) && in_array( $args['panel'] , array_keys( $new_panels_ids ) ) ) {
                    $args['panel'] = $new_panels_ids[$args['panel']];
                }

                if( isset( $args['dependency'] ) && !empty( $args['dependency'] ) ) {
                    $args['dependency'] = $this->dependency_manager->fix_dependency_controls_ids( $args['dependency'] , $prefix );
                }

                if( isset( $args['lock_id'] ) && !empty( $args['lock_id'] ) ){
                    $args['lock_id'] = "{$prefix}_{$args['lock_id']}";
                }
                
                $new_fields["{$prefix}_{$field_id }"] = $args;

            }

        }

        return array(
            'fields'    => $new_fields ,
            'panels'    => $new_panels
        );

    }

    /**
     * Register Controls And Settings By fields
     */
    public function register_fields(){

        foreach ( $this->fields as $id => $field ) {

            if ( ! $field instanceof SiteEditorField ) {
                continue;
            }

            if( ! $field->check_capabilities() ){
                continue;
            }

            $args = get_object_vars( $field );

            $primary_args = $args['primary_args'];

            unset( $args['primary_args'] );

            $setting_id = $args['setting_id'];

            unset( $args['setting_id'] );

            if( isset( $primary_args['setting_id'] ) ){
                unset( $primary_args['setting_id'] );
            }

            unset( $args['id'] );

            if( isset( $primary_args['id'] ) ){
                unset( $primary_args['id'] );
            }

            $setting = SED()->editor->manager->get_setting( $setting_id );

            if( ! isset( $setting ) ) {

                $setting_args = array_merge($primary_args, $args);

                unset($setting_args['type']);

                if( isset( $setting_args['category'] ) && $setting_args['category'] == "style-editor" ){
                    $setting_args['type'] = "style-editor";
                    $setting_args['option_type'] = "base";
                }

                /*$setting_args = array(
                    'option_type'           =>  $field->option_type ,
                    'capability'            =>  $field->capability ,
                    'theme_supports'        =>  $field->theme_supports ,
                    'default'               =>  $field->default ,
                    'transport'             =>  $field->transport ,
                    'sanitize_callback'     =>  $field->sanitize_callback ,
                    'sanitize_js_callback'  =>  $field->sanitize_js_callback
                );*/

                // Create the settings.
                $this->add_setting($setting_id, $setting_args);
            }

            $control_args = array_merge( $primary_args , $args );

            $control_args['settings'] = $setting_id;

            /*$control_args = array(
                'capability'        =>  $field->capability ,
                'priority'          =>  $field->priority ,
                'panel'             =>  $field->panel ,
                'label'             =>  $field->label ,
                'description'       =>  $field->description ,
                'choices'           =>  $field->choices ,
                'atts'              =>  $field->atts ,
                'type'              =>  $field->type ,
                'option_group'      =>  $field->option_group ,
                'active_callback'   =>  $field->active_callback
            );*/

            if( isset( $control_args['category'] ) && $control_args['category'] == "style-editor" && isset( $control_args['default'] ) ){
                $control_args['default_value'] = $control_args['default'];
            }

            if( isset( $control_args["dependency"] ) && ! empty( $control_args["dependency"] ) ){

                $this->dependency_manager->add( $control_args['option_group'] , $id , $control_args["dependency"] );

                unset( $control_args["dependency"] );

            }

            if( isset( $args['partial_refresh'] ) ) {

                $partial_args = $args['partial_refresh'];

                $this->register_field_partial( $partial_args , $setting_id , $args );
            }


            if( isset( $args['preview_params'] ) && isset( $args['preview_params']['type'] ) ) {

                $preview_args = $args['preview_params'];
                $preview_args['settingId'] = $setting_id;

                if ( isset($args['option_group']) ) {
                    $preview_args['option_group'] = $args['option_group'];
                }

                $this->preview_params[$id] = $preview_args;


            }

            // Create the control.
            $this->add_control( $id , $control_args );

        }

    }

    private function register_field_partial( $partial_args , $setting_id , $field_args = array() , $partial_id = '' ){


        if ( $partial_args instanceof SiteEditorPartial ) {

            $this->add_partial( $partial_args );

        }else if ( is_array( $partial_args ) && isset( $partial_args['render_callback'] ) && isset( $partial_args['selector'] ) ) {

            $partial_args = $this->get_partial_args( $partial_args , $setting_id , $field_args );

            if( empty( $partial_id ) || ! is_string( $partial_id ) ) {
                $partial_id = $setting_id;
            }

            $partial_id = ! isset( $partial_args['partial_id'] ) ? $partial_id : $partial_args['partial_id'];

            if( isset( $partial_args['partial_id'] ) ){
                unset( $partial_args['partial_id'] );
            }

            $this->add_partial($partial_id, $partial_args);

        }elseif( is_array( $partial_args ) ) {

            $partials = $partial_args;

            foreach ( $partials AS $curr_partial_id => $curr_partial_args ){

                $this->register_field_partial( $curr_partial_args , $setting_id , $field_args , $curr_partial_id  );

            }

        }

    }

    public function get_partial_args( $partial_args , $setting_id , $field_args ){

        if (isset($field_args['capability'])) {
            $partial_args['capability'] = $field_args['capability'];
        }

        if (isset($field_args['option_group'])) {
            $partial_args['option_group'] = $field_args['option_group'];
        }

        if (!isset($partial_args['settings'])) {
            $partial_args['settings'] = array( $setting_id );
        }

        return $partial_args;

    }

    /**
     * @param $id
     * @param array $args
     * @return SiteEditorField
     */
    public function add_field(  $id , $args = array() ){

        if ( $id instanceof SiteEditorField ) {
            $field = $id;
        } else {

            $class_name = 'SiteEditorField';

            if ( array_key_exists( $args['type'], $this->field_types ) ) {
                $class_name = $this->field_types[ $args['type'] ];
            }

            $field = new $class_name( sanitize_key( $id ), $args );

        }

        $this->fields[ $field->id ] = $field;

        return $field;

    }

    /**
     * @param array $fields
     */
    public function add_fields( $fields = array() ){

        if( !empty( $fields ) && is_array( $fields ) ) {

            foreach ( $fields AS $field_id => $args ) {

                if ( $args instanceof SiteEditorField ) {
                    $this->add_field( $args );
                }else {
                    $this->add_field( $field_id , $args );
                }

            }

        }

    }

    /**
     * Retrieve a customize panel.
     *
     * @since 4.0.0
     * @access public
     *
     * @param string $id Panel ID to get.
     * @return WP_Customize_Panel|void Requested panel instance, if set.
     */
    public function get_field( $id ) {
        if ( isset( $this->fields[ $id ] ) ) {
            return $this->fields[ $id ];
        }
    }

    /**
     * Remove a customize panel.
     *
     * @since 4.0.0
     * @access public
     *
     * @param string $id Panel ID to remove.
     */
    public function remove_field( $id ) {

        if( isset( $this->fields[ $id ] ) ) {
            unset( $this->fields[$id] );
        }

    }

    /**
     * Register a customize field type.
     *
     * Registered types are eligible to be rendered via JS and created dynamically.
     *
     * @since 1.0.0
     * @access public
     *
     * @see WP_Customize_Panel
     *
     * @param string $php_class Name of a custom field which is a subclass of SiteEditorField.
     */
    public function register_field_type( $type , $php_class ) {

        $this->field_types[$type] = $php_class;

    }

    /**
     * Adds a partial.
     *
     * @since 1.0.0
     * @access public
     *
     * @param SiteEditorPartial|string $id   Customize Partial object, or Panel ID.
     * @param array                       $args Optional. Partial arguments. Default empty array.
     * @return SiteEditorPartial             The instance of the panel that was added.
     */
    public function add_partial( $id, $args = array() ) {

        if ( $id instanceof SiteEditorPartial ) {

            SED()->editor->manager->selective_refresh->add_partial( $id );

        }else if ( isset( $args['render_callback'] ) && isset( $args['selector'] ) ) {

            SED()->editor->manager->selective_refresh->add_partial( $id, $args );

        }

    }


    public static function add_group(  $id , $args = array() ){

    }


    public static function add_groups( $groups = array() ){

    }

    public function enqueue_scripts(){

        //wp_enqueue_script('editor-expand');

        //add_thickbox();
        wp_enqueue_media();

        //wp_enqueue_script('image-edit');

        //wp_enqueue_style('imgareaselect');

        wp_enqueue_script( 'jquery-ui-slider' );
        wp_enqueue_script( 'jquery-ui-datepicker' );

        wp_enqueue_script( 'sed-options-controls' );
        wp_enqueue_style( 'sed-options-controls' );

        wp_enqueue_script( 'sed-app-settings-manager' );

        //select 2
        wp_enqueue_script( 'select2' );
        wp_enqueue_style( 'select2' );

        //code mirror
        wp_enqueue_script( 'codemirror' );
        wp_enqueue_style( 'codemirror' );

        /**codemirror
        // Add theme styles.
        wp_enqueue_style( 'codemirror-theme-' . $this->choices['theme'], SED_EDITOR_ASSETS_URL . '/libs/codemirror/theme/' . $this->choices['theme'] . '.css' );
        *********************/
        // If we're using html mode, we'll also need to include the multiplex addon
        // as well as dependencies for XML, JS, CSS languages.
        //if ( in_array( $this->choices['language'], array( 'html', 'htmlmixed' ) ) ) {
            wp_enqueue_script( 'codemirror-selection', SED_EDITOR_ASSETS_URL . '/libs/codemirror/addon/selection/selection-pointer.js', array( 'jquery', 'codemirror' ) );
            wp_enqueue_script( 'codemirror-multiplex', SED_EDITOR_ASSETS_URL . '/libs/codemirror/addon/mode/multiplex.js', array( 'jquery', 'codemirror' ) );
            wp_enqueue_script( 'codemirror-language-xml', SED_EDITOR_ASSETS_URL . '/libs/codemirror/mode/xml/xml.js', array( 'jquery', 'codemirror' ) );
            wp_enqueue_script( 'codemirror-language-javascript', SED_EDITOR_ASSETS_URL . '/libs/codemirror/mode/javascript/javascript.js', array( 'jquery', 'codemirror' ) );
            wp_enqueue_script( 'codemirror-language-css', SED_EDITOR_ASSETS_URL . '/libs/codemirror/mode/css/css.js', array( 'jquery', 'codemirror' ) );
            wp_enqueue_script( 'codemirror-language-htmlmixed', SED_EDITOR_ASSETS_URL . '/libs/codemirror/mode/htmlmixed/htmlmixed.js', array( 'jquery', 'codemirror', 'codemirror-multiplex', 'codemirror-language-xml', 'codemirror-language-javascript', 'codemirror-language-css' ) );
        //} elseif ( 'php' === $this->choices['language'] ) {
            wp_enqueue_script( 'codemirror-language-xml', SED_EDITOR_ASSETS_URL . '/libs/codemirror/mode/xml/xml.js', array( 'jquery', 'codemirror' ) );
            wp_enqueue_script( 'codemirror-language-php', SED_EDITOR_ASSETS_URL . '/libs/codemirror/mode/php/php.js', array( 'jquery', 'codemirror' ) );
        //} else {
            // Add language script.
            //wp_enqueue_script( 'codemirror-language-' . $this->choices['language'], SED_EDITOR_ASSETS_URL . '/libs/codemirror/mode/' . $this->choices['language'] . '/' . $this->choices['language'] . '.js', array( 'jquery', 'codemirror' ) );
        //}

        //wp_enqueue_style( 'codemirror-theme-default' , SED_EDITOR_ASSETS_URL . '/libs/codemirror/theme/default.css' );

        //wp_enqueue_style( 'codemirror-theme-abcdef' , SED_EDITOR_ASSETS_URL . '/libs/codemirror/theme/abcdef.css' );

    }

    public function print_templates(){
        ?>
        <script type="text/html" id="tmpl-sed-load-options-errors" >
            <div id="sed-load-options-errors-box" data-title="<?php echo __("Error" , "site-editor");?>" class="dialog-level-box-settings-container " >

                <div  class="error-box">

                    <div class="load-options-error error">
                        <span class="error-message">{{data.message}}</span>
                    </div>

                </div>

            </div>
        </script>
        <?php
    }

    public function enqueue_preview_scripts(){

        wp_enqueue_script( 'sed-options-controls-preview' );

    }

    public function print_media_templates(){
        add_action( 'sed_print_footer_scripts', 'wp_print_media_templates' );
    }

    /**
     * Enqueue a WP Editor instance we can use for rich text editing.
     */
    public function enqueue_wp_editor() {

        add_action( 'sed_print_footer_scripts', array( $this, 'render_wp_editor' ), 0 );

        // Note that WP_Customize_Widgets::print_footer_scripts() happens at priority 10.
        add_action( 'sed_print_footer_scripts', array( $this, 'maybe_do_admin_print_footer_scripts' ), 20 );

        // @todo These should be included in _WP_Editors::editor_settings()
        if ( false === has_action( 'sed_print_footer_scripts', array( '_WP_Editors', 'enqueue_scripts' ) ) ) {
            add_action( 'sed_print_footer_scripts', array( '_WP_Editors', 'enqueue_scripts' ) );
        }
    }

    /**
     * Render rich text editor.
     */
    public function render_wp_editor() {
        ?>
        <div id="sed-wp-text-editor-pane">

            <div id="sed-wp-editor-dragbar">
                <span class="ui-dialog-title"><?php echo __("Wp Text Editor" , "site-editor");?></span>
                <button class="sed-close-wp-editor">
                    <span class="icon-delete"></span>
                    <span class="ui-button-text"><?php echo __("Close" , "site-editor");?></span>
                </button>
            </div>

            <?php

            /**
             * Remove 3rd party editor buttons.
             *

            remove_all_actions('media_buttons', 999999);
            remove_all_actions('media_buttons_context', 999999);
            remove_all_filters('mce_external_plugins', 999999);*/

            // The settings passed in here are derived from those used in edit-form-advanced.php.
            wp_editor( '', 'sed-wp-tinymce-text-editor', array(
                '_content_editor_dfw' => false,
                'drag_drop_upload' => true,
                'tabfocus_elements' => 'content-html,save-post',
                'editor_height' => 390,
                'default_editor' => 'tinymce',
                'tinymce' => array(
                    'resize' => false,
                    'wp_autoresize_on' => false,
                    'add_unload_trigger' => false,
                ),
            ) );
            ?>

        </div>
        <?php
    }

    /**
     * Do the admin_print_footer_scripts actions if not done already.
     *
     * Another possibility here is to opt-in selectively to the desired widgets
     * via:
     * Shortcode_UI::get_instance()->action_admin_enqueue_scripts();
     * Shortcake_Bakery::get_instance()->action_admin_enqueue_scripts();
     *
     * Note that this action is also done in WP_Customize_Widgets::print_footer_scripts()
     * at priority 10, so this method runs at a later priority to ensure the action is
     * not done twice.
     *
     * @codeCoverageIgnore
     */
    public function maybe_do_admin_print_footer_scripts() {
        if ( ! did_action( 'admin_print_footer_scripts' ) ) {
            /** This action is documented in wp-admin/admin-footer.php */
            do_action( 'admin_print_footer_scripts' );
        }

        if ( ! did_action( 'admin_footer-post.php' ) ) {
            /** This action is documented in wp-admin/admin-footer.php */
            do_action( 'admin_footer-post.php' );
        }
    }

}


