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
     * Registered instances of SiteEditorField.
     *
     * @since 1.0.0
     * @access protected
     * @var array
     */
    protected $fields = array();

    /**
     * SiteEditorOptionsManager constructor.
     */
    function __construct(  ) {
        
        require_once dirname( __FILE__ ) . DS . 'site-editor-options-template.class.php';

        require_once dirname( __FILE__ ) . DS . 'site-editor-field.class.php';

        add_action( 'sed_after_init_manager', array( $this, 'register_components' ) , 10 , 1 );

        add_action( 'sed_after_init_manager', array( $this, 'register_default_groups' ) , 10 , 1 );

        add_action( 'sed_app_register', array( $this, 'register_fields' ) , 1000 );

        add_action( 'site_editor_ajax_sed_load_options', array($this,'sed_ajax_load_options' ) );//wp_ajax_sed_load_options

        add_action( 'sed_app_register', array( $this, 'register_partials' ), 99 );

        add_action( 'sed_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_preview_scripts' ) );

    }

    public function sed_ajax_load_options(){

        do_action( "sed_register_{$_POST['setting_id']}_options" );

        $this->register_fields();

        $data = self::get_settings_data( $_POST['setting_id'] );

        if( is_wp_error( $data ) ){

            die( wp_json_encode( array(
                'success'   => false,
                'message'   => $data->get_error_message(),
            ) ) );

        }

        die( wp_json_encode( array(
            'success' => true,
            'data'    => $data,
        ) ) );

    }

    public function get_settings_data( $group_id ){

        $data = array(
            "settings"      =>  array() ,
            "controls"      =>  array() ,
            "panels"        =>  array() ,
            "relations"     =>  array() ,
            "output"        =>  ""
        );

        $groups = SED()->editor->manager->groups();

        foreach ( $groups AS $group_id => $group ){

            if( $group->id == $group_id ){
                $current_group = $group;
                break;
            }

        }

        if( ! isset( $current_group ) ){
            return new WP_Error( 'options_group_invalid', __( "This group not registered already", "site-editor" ) );
        }

        if( ! $current_group->check_capabilities() ){
            return new WP_Error( 'options_not_access', __( "You can not access to this group options", "site-editor" ) );
        }

        $panels = SED()->editor->manager->panels();

        $group_panels = array();

        foreach ( $panels AS $panel_id => $panel ){

            if ( $panel->check_capabilities() ) {

                if ( $panel->option_group == $group_id ) {

                    $group_panels[$panel_id] = $panel;

                    $data['panels'][$panel_id] = $panel->json();

                }

            }

        }

        $current_group->panels = $group_panels;

        $controls = SED()->editor->manager->controls(); //var_dump( $group_panels );

        $group_controls = array();

        foreach ( $controls AS $control_id => $control ){

            if ( $control->check_capabilities() ) {

                if ( $control->option_group == $group_id ) {

                    $group_controls[$control_id] = $control;

                    $data['controls'][$control_id] = $control->json();

                }

            }

        }

        $current_group->controls = $group_controls;

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


        $data['output'] = $current_group->get_content();

        return $data;
    }

    public function register_components(  ){

        $this->register_panels_components(  );

        $this->register_controls_components(  );

        $this->register_fields_components(  );

    }


    public function register_default_groups(){

        SED()->editor->manager->add_group('sed_add_layout', array(
            'capability'        => 'edit_theme_options',
            'theme_supports'    => '',
            'title'             => __("Add New Layout", "site-editor"),
            'description'       => __("Add Layout Options", "site-editor"),
            'type'              => 'default',
        ));

    }

    private function register_panels_components(  ){

        $panels_path = dirname( __FILE__ ) . DS . "panels" . DS . "*panel.class.php" ;

        foreach ( glob( $panels_path ) as $php_file ) {
            require_once $php_file;
        }

    }

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

        SED()->editor->manager->add_setting( $id , $args );

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
     * @param array $args The field definition as sanitized in Kirki_Field.
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

        SED()->editor->manager->add_panel( $panel );
    }
    
    /**
     * Register multi panels
     *
     * @param array $panels
     * @access public
     */
    public static function add_panels( $panels = array() ){

        if( !empty( $panels ) && is_array( $panels ) ) {

            foreach ( $panels AS $panel_id => $args ) {

                if ( $args instanceof SiteEditorOptionsPanel ) {
                    self::add_panel($args);
                }else {
                    self::add_panel($panel_id, $args);
                }

            }

        }

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

            unset( $args['id'] );

            $setting_args = array_merge( $primary_args , $args );

            unset( $setting_args['type'] );

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
            $this->add_setting( $setting_id , $setting_args );

            $control_args = array_merge( $primary_args , $args );

            $control_args['settings'] = $setting_id;

            /*$control_args = array(
                'capability'        =>  $field->capability ,
                'priority'          =>  $field->priority ,
                'panel'             =>  $field->panel ,
                'label'             =>  $field->label ,
                'description'       =>  $field->description ,
                'choices'           =>  $field->choices ,
                'input_attrs'       =>  $field->input_attrs ,
                'type'              =>  $field->type ,
                'option_group'      =>  $field->option_group ,
                'active_callback'   =>  $field->active_callback
            );*/

            // Create the control.
            $this->add_control( $id , $control_args );

        }

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
     * Parses all fields and searches for the "partial_refresh" argument inside them.
     * If that argument is found, then it starts parsing the array of arguments.
     * Registers a selective_refresh in the customizer for each one of them.
     *
     * @param object $wp_customize WP_Customize_Manager.
     */
    public function register_partials( $manager ) {

        // Get an array of all fields.
        $fields = $this->fields;

        // Start parsing the fields.
        foreach ( $fields as $field_id => $field ) {
            if ( isset( $field->partial_refresh ) && ! empty( $field->partial_refresh ) ) {
                // Start going through each item in the array of partial refreshes.
                foreach ( $field->partial_refresh as $partial_refresh => $partial_refresh_args ) {
                    // If we have all we need, create the selective refresh call.
                    if ( isset( $partial_refresh_args['render_callback'] ) && isset( $partial_refresh_args['selector'] ) ) {
                        $manager->selective_refresh->add_partial( $partial_refresh, array(
                            'selector'        => $partial_refresh_args['selector'],
                            'settings'        => array( $field->setting_id ),
                            'render_callback' => $partial_refresh_args['render_callback'],
                        ) );
                    }
                }
            }
        }
    }

    public static function add_group(  $id , $args = array() ){

    }

    public static function add_groups( $groups = array() ){

    }

    public function enqueue_scripts(){

        wp_enqueue_script( 'sed-options-controls' );
        wp_enqueue_style( 'sed-options-controls' );

    }

    public function enqueue_preview_scripts(){

        wp_enqueue_script( 'sed-options-controls-preview' );

    }



}

/*function register_default_groups(){

    sed_options()->add_group('site', array(
        'capability'        => 'edit_theme_options',
        'theme_supports'    => '',
        'title'             => __("Site Options", "site-editor"),
        'description'       => __("General Site Options", "site-editor"),
        'type'              => 'default',
    ));

    sed_options()->add_group('theme', array(
        'capability'        => 'edit_theme_options',
        'theme_supports'    => '',
        'title'             => __("Theme Options", "site-editor"),
        'description'       => __("Theme Options for any theme", "site-editor"),
        'type'              => 'default',
    ));

    sed_options()->add_group('add_layout', array(
        'capability'        => 'edit_theme_options',
        'theme_supports'    => '',
        'title'             => __("Add Layout", "site-editor"),
        'description'       => __("Add Custom Layout", "site-editor"),
        'type'              => 'default',
    ));

    sed_options()->add_group('pages_layouts', array(
        'capability'        => 'edit_theme_options',
        'theme_supports'    => '',
        'title'             => __("Layout settings", "site-editor"),
        'description'       => __("Page layout settings", "site-editor"),
        'type'              => 'default',
    ));

    sed_options()->add_group('page_general', array(
        'capability'        => 'edit_theme_options',
        'theme_supports'    => '',
        'title'             => __("Page options", "site-editor"),
        'description'       => __("Page general settings", "site-editor"),
        'type'              => 'default',
    ));

    sed_options()->add_group('single_posts', array(
        'capability'        => 'edit_theme_options',
        'theme_supports'    => '',
        'title'             => __("Single posts options", "site-editor"),
        'description'       => __("Single posts settings", "site-editor"),
        'type'              => 'default',
    ));

    sed_options()->add_group('posts_archive', array(
        'capability'        => 'edit_theme_options',
        'theme_supports'    => '',
        'title'             => __("Posts archive options", "site-editor"),
        'description'       => __("Archive settings", "site-editor"),
        'type'              => 'default',
    ));

    sed_options()->add_group('sed_image', array(
        'capability'        => 'edit_theme_options',
        'theme_supports'    => '',
        'title'             => __("Image settings", "site-editor"),
        'description'       => __("Image module settings", "site-editor"),
        'type'              => 'default',
    ));

}

add_action( 'sed_app_register_components', 'register_default_groups' );


function register_site_options(){

    sed_options()->add_panel('panel_id', array(
        'priority'          => 10,
        'type'              => 'inner_panel',
        'title'             => __('My Title', 'textdomain'),
        'description'       => __('My Description', 'textdomain'),
        'option_group'      => 'site_options',
        'capability'        => '',
        'theme_supports'    => '',
        'parent_id '        => "root",
        'atts'              => array(),
        'active_callback'   => ''
    ));

    sed_options()->add_field('field_id' , array(
        'settings'          => 'my_setting',
        'label'             => __('My custom control', 'translation_domain'),
        'type'              => 'text',
        'priority'          => 10,
        'default'           => 'some-default-value',
        //panel or group
        'panel'             => 'panel_id',
        'option_group'      => 'site_options',
    ));

}

add_action( 'sed_register_site_options' , 'register_site_options' );


function register_options_groups()
{

    sed_options()->add_group($group_id, array(

        'title' => __('My Title', 'textdomain'),
        'description' => __('My Description', 'textdomain'),

    ));

    $groups = array(

        $group_id1 => array(

            'title' => __('My Title', 'textdomain'),
            'description' => __('My Description', 'textdomain'),
        ),

        $group_id2 => array(

            'title' => __('My Title', 'textdomain'),
            'description' => __('My Description', 'textdomain'),
        ),

    );

    sed_options()->add_groups( $groups );
}

add_action("sed_app_register", "register_options_groups");


function register_params( )
{
    $panels = array(

        'panel_id1' =>  array(
            'priority'          => 10,
            'type'              => 'inner_panel',
            'title'             => __('My Title', 'textdomain'),
            'description'       => __('My Description', 'textdomain'),
            'option_group'      => 'group_id' ,
            'capability'        => '' ,
            'theme_supports'    => '' ,
            'parent_id '        => "root",
            'atts'              => array() ,
            'active_callback'   => ''
        ) ,

        new myCustomOptionsPanel( 'panel_id2' , array(
            'priority'          => 10,
            'type'              => 'inner_panel',
            'title'             => __('My Title', 'textdomain'),
            'description'       => __('My Description', 'textdomain'),
            'option_group'      => 'group_id' ,
            'capability'        => '' ,
            'theme_supports'    => '' ,
            'parent_id '        => "root",
            'atts'              => array() ,
            'active_callback'   => ''
        )) ,

        'panel_id3' =>  array(
            'priority'          => 10,
            'type'              => 'inner_panel',
            'title'             => __('My Title', 'textdomain'),
            'description'       => __('My Description', 'textdomain'),
            'option_group'      => 'group_id' ,
            'capability'        => '' ,
            'theme_supports'    => '' ,
            'parent_id '        => "root",
            'atts'              => array() ,
            'active_callback'   => ''
        ) ,

    );

    sed_options()->add_panels( $panels );


    $fields = array(

        'field_id1'=> array(
            'settings' => 'my_setting',
            'label' => __('My custom control', 'translation_domain'),
            'section' => 'my_section',
            'type' => 'text',
            'priority' => 10,
            'default' => 'some-default-value',
            //panel or group
            'panel' => 'panel_id'
        ) ,

        new myCustomField( 'field_id2' , array(
            'settings' => 'my_setting',
            'label' => __('My custom control', 'translation_domain'),
            'section' => 'my_section',
            'type' => 'text',
            'priority' => 10,
            'default' => 'some-default-value',
            //panel or group
            'panel' => 'panel_id'
        )) ,

        'field_id3'=> array(
            'settings' => 'my_setting',
            'label' => __('My custom control', 'translation_domain'),
            'section' => 'my_section',
            'type' => 'text',
            'priority' => 10,
            'default' => 'some-default-value',
            //panel or group
            'panel' => 'panel_id'
        ) ,

    );

    sed_options()->add_fields( $fields );
}

add_action( "sed_load_options_group_id1" , "register_params" );
*/ 

function sed_add_layout_options(){

    sed_options()->add_field( 'radio_field_id' , array(
        'setting_id'        => 'my_setting',
        'label'             => __('My custom control', 'translation_domain'),
        'type'              => 'radio',
        'priority'          => 10,
        'default'           => 'options3_key',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_add_layout',
        'transport'         => 'postMessage' ,
        'choices'           => array(
            "options1_key"      =>    "options1_value" ,
            "options2_key"      =>    "options2_value" ,
            "options3_key"      =>    "options3_value" ,
            "options4_key"      =>    "options4_value" ,
        ) ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'change_media_field_id' , array(
        'setting_id'        => 'my_setting2',
        'label'             => __('Change Media', 'translation_domain'),  
        'type'              => 'change_media',
        'priority'          => 11,
        'default'           => '',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_add_layout',
        'transport'         => 'postMessage' ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'checkbox_field_id' , array(
        'setting_id'        => 'my_setting3',
        'label'             => __('Checkbox', 'translation_domain'),
        'type'              => 'checkbox',
        'priority'          => 12,
        'default'           => true,
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_add_layout',
        'transport'         => 'postMessage' ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'color_field_id' , array(
        'setting_id'        => 'my_setting4',
        'label'             => __('Color control', 'translation_domain'),
        'type'              => 'color',
        'priority'          => 13,
        'default'           => '',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_add_layout',
        'transport'         => 'postMessage' ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'icon_field_id' , array(
        'setting_id'        => 'my_setting5',
        'label'             => __('Icon control', 'translation_domain'),
        'type'              => 'icon',
        'priority'          => 14,
        'default'           => '',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_add_layout',
        'transport'         => 'postMessage' ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'multi_icons_field_id' , array(
        'setting_id'        => 'my_setting6',
        'label'             => __('Multi Icons control', 'translation_domain'),
        'type'              => 'multi_icons',
        'priority'          => 15,
        'default'           => '',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_add_layout',
        'transport'         => 'postMessage' ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'image_field_id' , array(
        'setting_id'        => 'my_setting7',
        'label'             => __('Image control', 'translation_domain'),
        'type'              => 'image',
        'priority'          => 16,
        'default'           => '',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_add_layout',
        'transport'         => 'postMessage' ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'multi_images_field_id' , array(
        'setting_id'        => 'my_setting8',
        'label'             => __('Multi Images control', 'translation_domain'),
        'type'              => 'multi_images', 
        'priority'          => 17,
        'default'           => '',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_add_layout',
        'transport'         => 'postMessage' ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'multicheck_field_id' , array(
        'setting_id'        => 'my_setting9',
        'label'             => __('Multi Checkbox', 'translation_domain'),
        'type'              => 'multicheck',
        'priority'          => 18,
        'default'           => 'options3_key',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_add_layout',
        'transport'         => 'postMessage' ,
        'choices'           => array(
            "options1_key"      =>    "options1_value" ,
            "options2_key"      =>    "options2_value" ,
            "options3_key"      =>    "options3_value" ,
            "options4_key"      =>    "options4_value" ,
        ) ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'select_field_id' , array(
        'setting_id'        => 'my_setting10',
        'label'             => __('Select', 'translation_domain'),
        'type'              => 'select',
        'priority'          => 19,
        'default'           => 'options3_key',
        'subtype'           => 'single',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_add_layout',
        'transport'         => 'postMessage' ,
        'choices'           => array(
            "options1_key"      =>    "options1_value" ,
            "options2_key"      =>    "options2_value" ,
            "options3_key"      =>    "options3_value" ,
            "options4_key"      =>    "options4_value" ,
        ) ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'multiselect_field_id' , array(
        'setting_id'        => 'my_setting11',
        'label'             => __('Multi Select', 'translation_domain'),
        'type'              => 'select',
        'priority'          => 20,
        'default'           => 'options3_key',
        'subtype'           => 'multi',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_add_layout',
        'transport'         => 'postMessage' ,
        'choices'           => array(
            "options1_key"      =>    "options1_value" ,
            "options2_key"      =>    "options2_value" ,
            "options3_key"      =>    "options3_value" ,
            "options4_key"      =>    "options4_value" ,
        ) ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'spinner_field_id' , array(
        'setting_id'        => 'my_setting12',
        'label'             => __('Spinner control', 'translation_domain'),
        'type'              => 'spinner', 
        'priority'          => 21,
        'default'           => '',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_add_layout',
        'transport'         => 'postMessage' ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'text_field_id' , array(
        'setting_id'        => 'my_setting13',
        'label'             => __('Text control', 'translation_domain'),
        'type'              => 'text', 
        'priority'          => 22,
        'default'           => '',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_add_layout',
        'transport'         => 'postMessage' ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'text_field_id_2' , array(
        'setting_id'        => 'my_setting14',
        'label'             => __('Text control', 'translation_domain'),
        'type'              => 'text', 
        'subtype'           => 'email', 
        'priority'          => 23,
        'default'           => '',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_add_layout',
        'transport'         => 'postMessage' ,
        //'input_attrs'
    ));

    sed_options()->add_field( 'textarea_field_id' , array(
        'setting_id'        => 'my_setting15',
        'label'             => __('Textarea control', 'translation_domain'),
        'type'              => 'textarea', 
        'priority'          => 24,
        'default'           => '',
        //panel or group
        //'panel'             => 'panel_id',
        'option_group'      => 'sed_add_layout',
        'transport'         => 'postMessage' ,
        //'input_attrs'
    ));

}

add_action( "sed_register_sed_add_layout_options" , "sed_add_layout_options" );