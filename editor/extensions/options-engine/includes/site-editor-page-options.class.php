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
    public $fields = array();

    /**
     * All page options panels.
     *
     * @var string
     */
    public $panels = array();

    /**
     * All Public options
     *
     * @var string
     */
    public $public_settings = array();

    /**
     * All page options
     *
     * @var string
     */
    public $settings = array();

    /**
     * Public base setting id for save in db
     *
     * @var string
     */
    public $public_option_name = 'sed_page_options'; //sed_theme_options

    /**
     * layout base setting id for save in db
     *
     * @var string
     */
    public $layout_option_name = 'sed_layout_options';

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
     * SiteEditorPageOptions constructor.
     */
    public function __construct(){

        $this->title = __("Page Settings" , "site-editor");

        $this->description = __("Page general settings" , "site-editor");

        add_action( "sed_editor_init"               , array( $this, "add_toolbar_elements" ) );

        add_action( "plugins_loaded"                , array( $this , 'register_options' ) , 9998  );

        add_filter( 'sed_app_dynamic_setting_args'  , array( $this , 'filter_dynamic_setting_args' ), 10, 2 );

        add_filter( 'sed_app_dynamic_setting_class' , array( $this , 'filter_dynamic_setting_class' ), 5, 3 );

        add_action( 'sed_app_register_general_options' , array( $this, 'register_private_settings' ) );

        add_action( "sed_register_{$this->option_group}_options" , array( $this , 'register_pages_options' ) );

        add_action( "sed_register_{$this->option_group}_options" , array( $this, 'register_page_options_group' ) , -9999 );

        add_action( 'sed_app_preview_init'          , array( $this, 'sed_app_preview_init' ) );

        add_action( 'wp_default_scripts'			, array( $this, 'register_scripts' ), 11 );

        add_filter( 'sed_control_sub_category'      , array( $this, 'set_sub_category' ) , 10 , 2 );

        //add_action( 'sed_enqueue_scripts'           , array( $this, 'enqueue_scripts' ), 10 );

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

            $this->public_settings[ $this->layout_option_name . "[" . $setting_id . "]" ] = $args;

            $this->public_settings[ $this->public_option_name . "[" . $setting_id . "]" ] = $args;

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

    }

    public function register_pages_options(){

        $options = $this->get_page_options( $_POST['page_id'] , $_POST['page_type'] , $_POST['post_type'] );

        $panels = $options['panels']; //var_dump( $panels );

        sed_options()->add_panels( $panels );

        $fields = $options['fields']; //var_dump( $fields );

        sed_options()->add_fields( $fields );

    }

    private function get_panel( $id, $args = array() ){
        /**
         * Define the array of defaults
         */
        $defaults = array(
            'id'            => $id  ,
            'title'         => ''  ,
            'capability'    => 'edit_theme_options' ,
            'type'          => 'fieldset' ,
            'description'   => '' ,
            'priority'      => 10
        );

        /**
         * Parse incoming $args into an array and merge it with $defaults
         */
        $args = wp_parse_args( $args, $defaults );

        return $args;
    }

    private function view_tab_scope( $layout = true ){
        ob_start();

        ?>
        <div class="sed-tab-scope-options" sed-role="tab-scope">
            <ul>
                <li data-type="public-scope" class="tab-scope-item active"><a href="#"><span><?php echo __( "Public" , "site-editor");?></span></a></li>
                <?php if( $layout === true ){ ?>
                    <li data-type="layout-scope" class="tab-scope-item"><a href="#"><span><?php echo __( "Current Layout" , "site-editor");?></span></a></li>
                <?php } ?>
                <li data-type="page-customize-scope" class="tab-scope-item"><a href="#"><span><?php echo __( "Current Page" , "site-editor");?></span></a></li>
            </ul>
        </div>
        <?php

        return ob_get_clean();
    }

    private function get_page_options( $page_id , $page_type , $post_type = '' ){

        $new_fields = array();
        $fields = $this->fields;

        $new_panels = array();
        $panels = $this->panels;

        /*$new_fields['sed_tab_scope_options'] = array(
            'type'              =>  'custom',
            'custom_template'   =>  $this->view_tab_scope() ,
            'priority'          => 0 ,
            'has_border_box'    => false ,
            'js_type'           => '' ,
            'category'          => 'page-settings' ,
            'option_group'      => $this->option_group,
        );*/

        $page_option_name = ( $page_type != "post" ) ? "sed_{$page_id}_settings" : "postmeta[{$post_type}][{$page_id}]";

        $new_fields['sed_page_options_scope'] = array(
            'type'              => 'radio-buttonset',
            'setting_id'        => "page_options_scope",
            'label'             => __("Select Scope", "site-editor"),
            'default'           => 'public-scope',
            'priority'          => 0 ,
            'description'       => __("This option allows you to set a title for your image.", "site-editor"),
            'choices'       =>  array(
                "public-scope"              =>    __( "Public" , "site-editor") ,
                "layout-scope"              =>    __( "Layout" , "site-editor") ,
                "page-customize-scope"      =>    __( "Current Page" , "site-editor") ,
            ),
            'transport'         => 'postMessage' ,
            'category'          => 'page-settings' ,
            'option_group'      => $this->option_group
        );

        foreach( $panels AS $key => $args ){

            if( !isset( $args['atts'] ) ){
                $args['atts'] = array();
            }

            if( isset( $args['atts']['class'] ) ){
                $org_class = $args['atts']['class'] . " ";
            }else{
                $org_class = "";
            }

            $private_id = $page_id . "_" . $key;
            $public_id  = "sed_public_" . $key;
            $layout_id  = "sed_layout_" . $key;

            $args['option_group'] = $this->option_group;

            $args['atts']['class'] = $org_class . "page-customize-scope sed-option-scope";
            $new_panels[ $private_id ] = $this->get_panel( $private_id , $args );

            $args['atts']['class'] = $org_class . "layout-scope sed-option-scope";
            $new_panels[ $layout_id ] = $this->get_panel( $layout_id , $args );

            $args['atts']['class'] = $org_class . "public-scope sed-option-scope";
            $new_panels[ $public_id ] = $this->get_panel( $public_id , $args );

            if( isset( $args['dependency'] ) && !empty( $args['dependency'] ) && is_array( $args['dependency'] ) ){
                $dependency_original = $args['dependency'];
                $new_panels[$private_id]['dependency'] = $this->fix_dependency_controls_ids( $dependency_original , $page_id );
                $new_panels[$layout_id]['dependency']  = $this->fix_dependency_controls_ids( $dependency_original , "sed_layout" );
                $new_panels[$public_id]['dependency']  = $this->fix_dependency_controls_ids( $dependency_original , "sed_public" );
            }

        }

        foreach( $fields AS $id => $args ){

            $args['category']  = 'page-settings';

            $args['option_group'] = $this->option_group;

            if( !isset( $args['panel'] ) ) {
                if (!isset($args['atts'])) {
                    $args['atts'] = array();
                }

                if (isset($args['atts']['class'])) {
                    $org_class = $args['atts']['class'] . " ";
                } else {
                    $org_class = "";
                }
            }

            $private_id = $page_id . "_" . $id;
            $public_id  = "sed_public_" . $id;
            $layout_id  = "sed_layout_" . $id;

            if( !isset( $args['panel'] ) )
                $args['atts']['class'] = $org_class . "page-customize-scope sed-option-scope";
            else {
                $org_panel = $args['panel'];
                $args['panel'] = $page_id . "_" . $org_panel;
            }

            $setting_id = $args['setting_id'];

            $args['setting_id'] = $page_option_name . "[" . $setting_id . "]";

            $new_fields[$private_id] = $args;

            if( !isset( $args['panel'] ) )
                $args['atts']['class'] = $org_class . "layout-scope sed-option-scope";
            else
                $args['panel'] = "sed_layout_" . $org_panel;

            $args['setting_id'] = $this->layout_option_name . "[" . $setting_id . "]";
            $new_fields[$layout_id] = $args;

            if( !isset( $args['panel'] ) )
                $args['atts']['class'] = $org_class . "public-scope sed-option-scope";
            else
                $args['panel'] = "sed_public_" . $org_panel;

            $args['setting_id'] = $this->public_option_name . "[" . $setting_id . "]";
            $new_fields[$public_id] = $args;

            if( isset( $args['dependency'] ) && !empty( $args['dependency'] ) && is_array( $args['dependency'] ) ){
                $dependency_original = $args['dependency'];
                $new_fields[$private_id]['dependency'] = $this->fix_dependency_controls_ids( $dependency_original , $page_id );
                $new_fields[$layout_id]['dependency']  = $this->fix_dependency_controls_ids( $dependency_original , "sed_layout" );
                $new_fields[$public_id]['dependency']  = $this->fix_dependency_controls_ids( $dependency_original , "sed_public" );
            }

        }

        return array(
            "fields"    => $new_fields ,
            "panels"    => $new_panels
        );

    }

    public function fix_dependency_controls_ids( $dependency , $prefix ){

        if( is_array( $dependency ) && isset( $dependency['controls'] ) ){
            if( isset( $dependency['controls']['control'] ) ){
                $dependency['controls']['control'] = $prefix."_".$dependency['controls']['control'];
            }else{
                foreach( $dependency['controls'] AS $index => $control ){
                    if( isset( $control['control'] ) )
                        $dependency['controls'][$index]['control'] = $prefix."_".$control['control'];
                }
            }
        }

        return $dependency;
    }


    public function register_options(){

        $panels = array();

        $fields = array();

        $this->fields = apply_filters( 'sed_page_options_fields_filter' , $fields );

        $this->panels = apply_filters( 'sed_page_options_panels_filter' , $panels );

    }

    public function filter_dynamic_setting_args( $args, $setting_id ) {

        $settings = $this->public_settings;

        if (  array_key_exists( $setting_id ,  $settings )) {

            $registered = $settings[ $setting_id ];

            if ( isset( $registered['theme_supports'] ) && ! current_theme_supports( $registered['theme_supports'] ) ) {
                // We don't really need this because theme_supports will already filter it out of being exported.
                return $args;
            }

            if ( false === $args ) {
                $args = array();
            }

            $args = array_merge(
                $args,
                $registered
            );

            $args['option_type'] = 'option';

        }

        return $args;
    }

    public function filter_dynamic_setting_class( $class, $setting_id, $args ){
        unset( $setting_id );
        if ( isset( $args['option_type'] ) ) {

            if ( isset( $args['setting_class'] ) ) {
                $class = $args['setting_class'];
            } else {
                $class = 'SedAppSettings';
            }

        }
        return $class;
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

        add_action( 'wp_enqueue_scripts'           , array( $this, 'preview_enqueue_scripts' ), 10 );

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
            $settings[$id] = array(
                'transport' => isset( $args['transport'] ) ? $args['transport'] : 'refresh',
                'value'     => isset( $args['default'] ) ? $args['default'] : ''
            );
        }

        $exports = array(
            'settings'              => $settings ,
            'publicOption'          => $this->public_option_name . '[##id##]' ,
            'layoutOption'          => $this->layout_option_name . '[##id##]' ,
            'privateOption'         => $page_option_name . '[##id##]' ,
        );

        wp_scripts()->add_data( 'sed-pages-options-preview' , 'data', sprintf( 'var _sedAppPreviewPageOptionsData = %s;', wp_json_encode( $exports ) ) );
    }


    public function register_scripts( WP_Scripts $wp_scripts ){

        $suffix = ( SCRIPT_DEBUG ? '' : '.min' ) . '.js';

        /*$handle = 'sed-pages-general-options';
        $src = SED_EXT_URL . 'options-engine/assets/js/pages-general-options' . $suffix ;
        $deps = array( 'siteeditor' );

        $in_footer = 1;
        $wp_scripts->add( $handle, $src, $deps, SED_VERSION, $in_footer );*/

        $handle = 'sed-pages-options-preview';
        $src = SED_EXT_URL . 'options-engine/assets/js/pages-options-preview' . $suffix ;
        $deps = array( 'sed-frontend-editor' );

        $in_footer = 1;
        $wp_scripts->add( $handle, $src, $deps, SED_VERSION, $in_footer );

    }

    /*public function enqueue_scripts(){

        wp_enqueue_script( 'sed-pages-general-options' );

        /*$exports = array(
            'settings'      => $general_settings ,
            'l10n'          => array(
                'fieldTitleLabel' => __( 'Title', 'site-editor' ),

            ),
        );

        wp_scripts()->add_data( 'sed-pages-general-options' , 'data', sprintf( 'var _sedAppPagesGeneralSettings = %s;', wp_json_encode( $exports ) ) );

    }*/

}



