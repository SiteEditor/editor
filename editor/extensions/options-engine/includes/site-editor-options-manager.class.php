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
     * An array of our fields dependencies.
     *
     * @access private
     * @var array
     */
    private $settings_dependencies = array();

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

        require_once dirname( __FILE__ ) . DS . 'site-editor-page-options.class.php';

        new SiteEditorPageOptions();

        //include sample options
        require_once dirname( __FILE__ ) . DS . 'demo' . DS . 'site-editor-sample-options.php';

        require_once dirname( __FILE__ ) . DS . 'site-editor-site-options.class.php';

        new SiteEditorSiteOptions();

        require_once dirname( __FILE__ ) . DS . 'site-editor-theme-options.class.php';

        new SiteEditorThemeOptions();

        require_once dirname( __FILE__ ) . DS . 'site-editor-custom-code.class.php';

        new SiteEditorCustomCodeOptions();

        add_action( 'sed_after_init_manager', array( $this, 'register_components' ) , 10 , 1 );

        add_action( 'sed_app_register', array( $this, 'register_fields' ) , 1000 );

        add_action( 'site_editor_ajax_sed_load_options', array($this,'sed_ajax_load_options' ) );//wp_ajax_sed_load_options

        add_action( 'sed_app_register', array( $this, 'register_partials' ), 99 );

        add_action( 'sed_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        add_action( 'sed_print_footer_scripts', array( $this, 'print_templates' ) );

        //add_action( 'sed_print_footer_scripts', 'customize_themes_print_templates' );

        add_action( 'sed_app_controls_init', array( $this, 'enqueue_wp_editor' ) );

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_preview_scripts' ) );

        add_action( 'sed_print_footer_scripts' , array($this, 'print_settings_dependencies') , 10000 );

        add_action( 'wp_enqueue_media' , array( $this , 'print_media_templates' ) );

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
            "settings"      =>  array() ,
            "controls"      =>  array() ,
            "panels"        =>  array() ,
            "relations"     =>  array() ,
            "output"        =>  "" ,
            "settingId"     =>  $_POST['setting_id'] ,
            "settingType"   =>  $group_type ,
            "groups"        =>  array()
        );

        $groups = SED()->editor->manager->groups();

        foreach ( $groups AS $id => $group ){

            if( $id == $group_id ){
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

        $data['groups'][$current_group->id] = $current_group->json();

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

        $data['relations'] = isset( $this->settings_dependencies[ $group_id ] ) ? $this->settings_dependencies[ $group_id ] : array();

        $data['output'] = $current_group->get_content();

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

        require_once dirname( __FILE__ ) . DS . "controls" . DS . "site-editor-media-controls.class .php" ;
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

        /**
         * @important todo For Fix Bug
         * @todo add panel dependencies to site editor manager class
         * in this version not support call directly SED()->editor->manager->add_panel()
         * if panel dependency not empty instead using sed_options()->add_panel()
         */

        if( !empty( $panel->dependency ) && is_array( $panel->dependency ) ){
            $this->set_group_dependencies( $panel->option_group , $panel->dependency , $panel->id );
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
                unset( $args['setting_id'] );
            }

            unset( $args['id'] );

            if( isset( $primary_args['id'] ) ){
                unset( $args['id'] );
            }

            $setting = SED()->editor->manager->get_setting( $setting_id );

            if( ! isset( $setting ) ) {

                $setting_args = array_merge($primary_args, $args);

                unset($setting_args['type']);

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

            if( isset( $control_args["dependency"] ) && ! empty( $control_args["dependency"] ) ){
                $this->set_group_dependencies( $control_args['option_group'] , $control_args["dependency"] , $id );
                unset( $control_args["dependency"] );
            }

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


    /**
     * set field dependencies for each group
     *
     * @param $group
     * @param $dependency
     * @param $id
     */
    public function set_group_dependencies( $group , $dependency , $id ){
        if( !isset( $this->settings_dependencies[$group] ) ){
            $this->settings_dependencies[$group] = array();
        }

        /*if( isset( $dependency['controls'] ) ){
            if( isset( $dependency['controls']['control'] ) ){
                $dependency['controls']['control'] = $group."_".$dependency['controls']['control'];
            }else{
                foreach( $dependency['controls'] AS $index => $control ){
                    if( isset( $control['control'] ) )
                        $dependency['controls'][$index]['control'] = $group."_".$control['control'];
                }
            }
        }*/
        $this->settings_dependencies[$group][$id] = $dependency;
    }

    public function print_settings_dependencies(){
        ?>
        <script type="text/javascript">
            var _sedAppModulesSettingsRelations = <?php echo wp_json_encode( $this->settings_dependencies ); ?>;

        </script>
        <?php
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

        wp_enqueue_script( 'codemirror' );
        wp_enqueue_style( 'codemirror' );

        /**codemirror
        // Add theme styles.
        wp_enqueue_style( 'codemirror-theme-' . $this->choices['theme'], SED_EDITOR_ASSETS_URL . '/libs/codemirror/theme/' . $this->choices['theme'] . '.css' );
        *********************/
        // If we're using html mode, we'll also need to include the multiplex addon
        // as well as dependencies for XML, JS, CSS languages.
        //if ( in_array( $this->choices['language'], array( 'html', 'htmlmixed' ) ) ) {
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

        wp_enqueue_style( 'codemirror-theme-default' , SED_EDITOR_ASSETS_URL . '/libs/codemirror/theme/default.css' );

        wp_enqueue_style( 'codemirror-theme-abcdef' , SED_EDITOR_ASSETS_URL . '/libs/codemirror/theme/abcdef.css' );

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

            /** Remove 3rd party editor buttons.
            remove_all_actions('media_buttons', 999999);
            remove_all_actions('media_buttons_context', 999999);
            remove_all_filters('mce_external_plugins', 999999);**/

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


