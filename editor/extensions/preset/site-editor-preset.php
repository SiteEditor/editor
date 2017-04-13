<?php
/*
Module Name: Preset
Module URI: http://www.siteeditor.org/modules/preset
Description: Preset Module For SiteEditor Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/


/**
* @SiteEditorPreset
*/
class SiteEditorPreset{

    public function __construct(){

        //add_filter('sed_custom_js_plugins' , array( $this, 'add_js_plugin' ) );
        add_action('sed_enqueue_scripts' , array( $this, 'add_js_plugin' ) );

        add_action( 'wp_enqueue_scripts' , array( $this, 'render_scripts' ) );

        //add_filter( "sed_js_I18n" , array( $this ,'js_I18n' ) );

        add_action( "sed_footer" , array( $this , "print_templates" ) );

        add_action( "site_editor_ajax_sed_create_preset" , array( $this, "create_ajax_preset" ) );

        add_action( "site_editor_ajax_sed_save_preset" , array( $this, "save_preset" ) );

        add_action( "site_editor_ajax_sed_save_presets" , array( $this, "save_presets" ) );

        add_action( "site_editor_ajax_sed_get_preset" , array( $this, "get_preset" ) );

        add_action( "site_editor_ajax_sed_delete_preset" , array( $this, "delete_preset" ) );

        add_filter( "sed_shortcode_settings" , array( $this, "add_preset_settings" ) , 10 ,2 );

        add_action( "site_editor_ajax_sed_module_presets" , array( __CLASS__ , "get_module_presets" ) );

        //add_filter( "sed_addon_settings", array($this,'preset_settings'));

        add_filter( "sed_app_refresh_nonces", array($this,'preset_nonces') , 10 , 2);

        //add_filter( "sed_default_shortcode_pattern" , array( $this , 'set_as_default_pattern' ) , 10 , 2 );

        add_action( "sed_print_footer_scripts" , array( $this , 'print_default_presets' ) , 10  );
    }

    public function preset_nonces( $nonces , $manager ){

        $nonces['preset'] = array(
            'create'            =>  wp_create_nonce( 'sed-create-preset' ) ,
            'get'               =>  wp_create_nonce( 'sed-get-preset' ) ,
            'collection'        =>  wp_create_nonce( 'sed-get-collection-presets' ) ,
            'saveCollection'    =>  wp_create_nonce( 'sed-save-collection-presets' )
        );

        return $nonces;
    }

    public function add_js_plugin() {
        wp_register_script("sed-app-preset", SED_EXT_URL . 'preset/assets/js/app-preset-plugin.min.js' , array( 'siteeditor' ) , "1.0.0",1 );
        wp_enqueue_script( 'sed-app-preset' );
    }

    public function render_scripts(){
        wp_enqueue_script( 'app-preset-module', SED_EXT_URL . 'preset/assets/js/app-preset-module.min.js', array( 'sed-frontend-editor' ) ,"1.0.0" , 1);
    }

    /*public function js_I18n( $I18n ){
        $I18n['ok_confirm']         =  __("Ok" , "site-editor");
        $I18n['cancel_confirm']     =  __("Cancel" , "site-editor");

        return $I18n;
    }*/

    public static function create_preset_content( $content_shortcodes ){

        $content_shortcodes = json_decode( wp_unslash( $content_shortcodes ), true );

        global $sed_apps;
        $tree_shortcodes = $sed_apps->editor->save->build_tree_shortcode( $content_shortcodes , $content_shortcodes[0]['parent_id'] );
        $content = $sed_apps->editor->save->create_shortcode_content( $tree_shortcodes , array() );

        return $content;
    }

    /**
     * Ajax handler for Creating Preset
     *
     * @since 1.0.0
     */
    function create_ajax_preset() {
        check_ajax_referer( 'sed-create-preset' , 'nonce' );

        if ( ! current_user_can( 'manage_site_editor_preset' ) ) {

            $data = array(
                'message'  => __( "You don't have permission to manage presets." , "site-editor" ),
            );

            wp_send_json_error( $data );

        }

        $shortcode              = isset( $_REQUEST['shortcode'] ) ? $_REQUEST['shortcode'] : '';
        $title                  = isset( $_REQUEST['title'] ) ? $_REQUEST['title'] : '';
        $content_shortcodes     = isset( $_REQUEST['content'] ) ? $_REQUEST['content'] : '';
        $menu_order             = isset( $_REQUEST['menu_order'] ) ? $_REQUEST['menu_order'] : 0;
        $attachment_ids         = isset( $_REQUEST['attachment_ids'] ) ? $_REQUEST['attachment_ids'] : array();

        if( empty( $shortcode ) || empty( $title ) || empty( $content_shortcodes ) ){

            $data = array(
                'message'  => __("invalid preset. shortcode or title or content is incorrect.","site-editor"),
            );
            wp_send_json_error( $data );

        }

        $content = self::create_preset_content( $content_shortcodes );

        $post_id = self::create_preset( $shortcode , $title , $content , false , $menu_order );

        if ( !$post_id || is_wp_error( $post_id ) ) {

            if( ! $message = $post_id->get_error_message() ){
                $message =  __("occurrence a problem in creating preset.","site-editor");
            }


            $data = array(
                'message'  => $message,
            );

            wp_send_json_error( $data );

        }

        self::update_preset_attachment_ids( $post_id , $attachment_ids );

        if ( ! $preset = self::prepare_preset_for_js( $post_id ) ) {

            $message = __("occurrence a problem in prepare preset data for js.","site-editor");

            $data = array(
                'message'  => $message,
            );

            wp_send_json_error( $data );

        }

        wp_send_json_success( $preset );

    }

    /**
     * Add OR Update Attachment Ids For a preset
     * using "api.sedShortcode.getPatternAttachmentIds" find all Attachment Ids for a preset shortcodes pattern( content )
     *
     * @param $post_id
     * @param $new_value
     */
    public static function update_preset_attachment_ids( $post_id , $new_value ){

        $option_name = 'sed_preset_attachment_ids';

        if( $post_id && $post_id > 0 && !is_wp_error( $post_id ) ){

            if( !update_post_meta( $post_id , $option_name , $new_value ) )
                add_post_meta( $post_id , $option_name , $new_value, true );

        }

    }

    /**
     * Create shortcode preset
     *
     * @since 4.7
     *
     * @param string $shortcode_name
     * @param string $title
     * @param string $content
     * @param boolean $is_default
     *
     * @return mixed int|false Post ID
     */
    public static function create_preset( $shortcode_name, $title, $content, $is_default = false , $menu_order = 0 ) {

        $args = array(
            'post_title'        => $title,
            'post_content'      => $content,
            'post_status'       => 'publish',
            'post_type'         => 'sed_preset_settings',
            'post_mime_type'    => self::get_shortcode_mime_type( $shortcode_name ),
        );

        if( $menu_order > 0 ){
            $args['menu_order'] = $menu_order;
        }

        $post_id = wp_insert_post( $args , false );

        if ( $post_id && $is_default ) {
            self::set_as_default_preset( $post_id, $shortcode_name );
        }

        return $post_id;
    }

    /**
     * Get mime type for specific shortcode
     *
     * @since 1.0
     *
     * @param $shortcode_name
     *
     * @return string
     */
    public static function get_shortcode_mime_type( $shortcode_name ) {
        return 'sed-preset-settings/' . str_replace( '_', '-', $shortcode_name );
    }

    /**
     * Get shortcode name from post's mime type
     *
     * @since 1.0
     *
     * @param string $post_mime_type
     *
     * @return string
     */
    public static function extract_shortcode_mime_type( $post_mime_type ) {
        $chunks = explode( '/', $post_mime_type );

        if ( 2 !== count( $chunks ) ) {
            return '';
        }

        return str_replace( '-', '_', $chunks[1] );
    }

    function print_default_presets(){

        $args = array(
            'post_type'         => 'sed_preset_settings',
            'posts_per_page'    => -1,
            'meta_key'          => '_sed_default',
            'meta_value'        => true,
        );

        $posts = get_posts( $args );

        $posts = array_map( array( __CLASS__ , 'prepare_preset_for_js' ) , $posts );

        $posts = array_filter( $posts );

        $default_presets = array();

        if( !empty( $posts ) ) {
            foreach ($posts AS $post) {
                $default_presets[$post['shortcode']] = $post['content'];
            }
        }

        ?>
        <script>
            var _sedAppDefaultPresetsChanges = <?php echo wp_json_encode( $default_presets );?>;
        </script>
        <?php
    }

    function set_as_default_pattern( $pattern , $shortcode ){

        $args = array(
            'post_type'         => 'sed_preset_settings',
            'post_mime_type'    => self::get_shortcode_mime_type( $shortcode['name'] ),
            'posts_per_page'    => -1,
            'meta_key'          => '_sed_default',
            'meta_value'        => true,
        );

        $posts = get_posts( $args );

        if ( $posts ) {
            $preset_content = $posts[0]->post_content;
            return $preset_content;
        }

        return $pattern;

    }

    /**
     * Set existing preset as default
     *
     * If this is vendor preset, clone it and set new one as default
     *
     * @param int $id If falsy, no default will be set
     * @param string $shortcode_name
     *
     * @return boolean
     *
     * @since 1.0
     */
    public static function set_as_default_preset( $id, $shortcode_name ) {
        $post_id = self::get_default_preset_id( $shortcode_name );
        if ( $post_id ) {
            delete_post_meta( $post_id, '_sed_default' );
        }

        if ( $id ) {
            if ( is_numeric( $id ) ) {
                // user preset
                update_post_meta( $id, '_sed_default', true );
            }
        }

        return true;
    }

    public static function remove_default_preset( $preset_id, $shortcode_name ) {
        $post_id = self::get_default_preset_id( $shortcode_name );
        if ( $post_id == $preset_id ) {
            delete_post_meta( $post_id, '_sed_default' );
        }
    }

    public static function is_default_preset( $preset_id , $shortcode_name ) {
        $default_id = self::get_default_preset_id( $shortcode_name );
        if( $default_id == $preset_id )
            return true;
        else
            return false;
    }

    /**
     * Get default preset id for specific shortcode
     *
     * @since 1.0
     *
     * @param string $shortcode_name
     *
     * @return mixed int|null
     */
    public static function get_default_preset_id( $shortcode_name = null ) {
        if ( ! $shortcode_name ) {
            return null;
        }

        $args = array(
            'post_type'         => 'sed_preset_settings',
            'post_mime_type'    => self::get_shortcode_mime_type( $shortcode_name ),
            'posts_per_page'    => -1,
            'meta_key'          => '_sed_default',
            'meta_value'        => true,
        );

        $posts = get_posts( $args );

        if ( $posts ) {
            $default_id = $posts[0]->ID;
            return $default_id;
        }

        return false;
    }

    function save_presets(){

        check_ajax_referer( 'sed-save-collection-presets' , 'nonce' );

        if ( ! current_user_can( 'manage_site_editor_preset' ) ) {

            $data = array(
                'message'  => __( "You don't have permission to manage presets." , "site-editor" ),
            );

            wp_send_json_error( $data );

        }

        $changes  = isset( $_REQUEST['changes'] ) ? $_REQUEST['changes'] : '';

        if( empty( $changes ) ){

            $data = array(
                'message'  => __("invalid data : not changed any data.","site-editor"),
            );

            wp_send_json_error( $data );

        }

        foreach ( $changes AS $id => $model_changes ){

            if ( ! $id = absint( $id ) )
                wp_send_json_error();

            $post = get_post( $id, ARRAY_A );

            if ( 'sed_preset_settings' != $post['post_type'] )
                wp_send_json_error();

            if ( isset( $model_changes['title'] ) )
                $post['post_title'] = $model_changes['title'];

            if ( isset( $model_changes['menuOrder'] ) )
                $post['menu_order'] = $model_changes['menuOrder'];

            if ( isset( $model_changes['content'] ) )
                $post['post_content'] = self::create_preset_content( $model_changes['content'] );

            if ( isset( $changes['attachment_ids'] ) )
                self::update_preset_attachment_ids( $id , $changes['attachment_ids'] );

            wp_update_post( $post );

        }

        wp_send_json_success();

    }

    function save_preset(){

        if ( ! current_user_can( 'manage_site_editor_preset' ) ) {

            $data = array(
                'message'  => __( "You don't have permission to manage presets." , "site-editor" ),
            );

            wp_send_json_error( $data );

        }

        if ( ! isset( $_REQUEST['id'] ) || ! isset( $_REQUEST['changes'] ) )
            wp_send_json_error();

        if ( ! $id = absint( $_REQUEST['id'] ) )
            wp_send_json_error();

        $shortcode_name  = isset( $_REQUEST['shortcode'] ) ? $_REQUEST['shortcode'] : '';

        if( empty( $shortcode_name ) ){

            $data = array(
                'message'  => __("invalid data : shortcode name is incorrect.","site-editor"),
            );

            wp_send_json_error( $data );

        }

        check_ajax_referer( 'update_preset_' . $id, 'nonce' );

        /*if ( ! current_user_can( 'edit_post', $id ) )
            wp_send_json_error();*/

        $changes = $_REQUEST['changes'];
        $post    = get_post( $id, ARRAY_A );

        if ( 'sed_preset_settings' != $post['post_type'] )
            wp_send_json_error();

        if ( isset( $changes['isDefault'] ) ) {
            $is_default = $changes['isDefault'];
            if( $is_default && ( $is_default === 'true' || $is_default === true ) )
                self::set_as_default_preset( $id, $shortcode_name );
            else
                self::remove_default_preset( $id, $shortcode_name );

            wp_send_json_success();
        }

        if ( isset( $changes['title'] ) )
            $post['post_title'] = $changes['title'];

        if ( isset( $changes['menuOrder'] ) )
            $post['menu_order'] = $changes['menuOrder'];

        if ( isset( $changes['content'] ) )
            $post['post_content'] = self::create_preset_content( $changes['content'] );

        if ( isset( $changes['attachment_ids'] ) )
            self::update_preset_attachment_ids( $id , $changes['attachment_ids'] );

        wp_update_post( $post );

        wp_send_json_success();
    }

    function get_preset(){
        check_ajax_referer( 'sed-get-preset' , 'nonce' );

        if ( ! current_user_can( 'manage_site_editor_preset' ) ) {

            $data = array(
                'message'  => __( "You don't have permission to manage presets." , "site-editor" ),
            );

            wp_send_json_error( $data );

        }

        if ( ! isset( $_REQUEST['id'] ) )
            wp_send_json_error();

        if ( ! $id = absint( $_REQUEST['id'] ) )
            wp_send_json_error();

        $post    = get_post( $id );

        if ( ! $preset = self::prepare_preset_for_js( $post ) ) {

            $message = __("occurrence a problem in prepare preset data for js.","site-editor");

            $data = array(
                'message'  => $message,
            );

            wp_send_json_error( $data );

        }

        wp_send_json_success( $preset );

    }

    function delete_preset(){

        if ( ! current_user_can( 'manage_site_editor_preset' ) ) {

            $data = array(
                'message'  => __( "You don't have permission to manage presets." , "site-editor" ),
            );

            wp_send_json_error( $data );

        }

        if ( ! isset( $_REQUEST['id'] ) )
            wp_send_json_error();

        if ( ! $id = absint( $_REQUEST['id'] ) )
            wp_send_json_error();

        check_ajax_referer( 'delete_preset_' . $id , 'nonce' );

        wp_delete_post( $id, true );

        wp_send_json_success();
    }

    public function add_preset_settings( $settings , $shortcode_obj ){

        if ( ! current_user_can( 'manage_site_editor_preset' ) || $shortcode_obj->shortcode->name == "sed_row" )
            return $settings;

        ob_start();

        include dirname( __FILE__ ) . "/view/preset-settings.php";

        $html = ob_get_clean();

        $settings[ 'preset_settings' ] = array(
            'type'              => 'panel-button',
            'label'             => __('Preset Settings', 'site-editor'),
            //'description'       => __('Preset settings for save module as custom layouts', 'site-editor') ,
            'button_style'      => 'menu' ,
            'has_border_box'    => false ,
            'icon'              => 'sedico-preset' ,
            'field_spacing'     => 'sm',
            'panel_title'       => __('Preset Settings', 'site-editor') ,
            'panel_content'     => $html ,
            'atts'              => array(
                'data-shortcode-name'       =>  $shortcode_obj->shortcode->name ,
                'class'                     =>  'sed_preset_settings_button'
            ),
            'priority'          => 1000
        );

        return $settings;
    }

    public function print_templates(){
        include dirname( __FILE__ ) . "/view/preset-tpl.php";
    }

    public static function prepare_preset_for_js( $preset ){

        if ( ! $preset = get_post( $preset ) )
            return;

        if ( 'sed_preset_settings' != $preset->post_type )
            return;

        $shortcode_name = self::extract_shortcode_mime_type( $preset->post_mime_type );

        if( !empty( $preset->post_content ) ) {

            if( ! did_action( 'sed_shortcode_register' ) ) { 
                do_action('sed_shortcode_register');
            }

            $shortcodes_models = PageBuilderApplication::get_pattern_shortcodes( $preset->post_content );

            $content = $shortcodes_models['shortcodes'];
        }else{
            $content = array();
        }

        $attachment_ids = ! is_array( get_post_meta( $preset->ID , 'sed_preset_attachment_ids' , true ) ) ? array() : get_post_meta( $preset->ID , 'sed_preset_attachment_ids' , true );

        $response = array(
            'id'                => $preset->ID,
            'title'             => $preset->post_title,
            'author'            => $preset->post_author,
            'content'           => $content ,
            'name'              => $preset->post_name,
            'status'            => $preset->post_status,
            'date'              => strtotime( $preset->post_date_gmt ) * 1000,
            'modified'          => strtotime( $preset->post_modified_gmt ) * 1000,
            'menuOrder'         => $preset->menu_order,
            'isDefault'         => self::is_default_preset( $preset->ID , $shortcode_name ),
            'shortcode'         => $shortcode_name ,
            'attachment_ids'    => $attachment_ids ,
            'nonces'            => array(
                'update'            => false,
                'delete'            => false,
                //'edit'          => false
            ),
        );

        $author = new WP_User( $preset->post_author );
        $response['authorName'] = $author->display_name;

        if ( current_user_can( 'edit_post', $preset->ID ) ) {
            $response['nonces']['update']   = wp_create_nonce( 'update_preset_' . $preset->ID );
            //$response['nonces']['edit']     = wp_create_nonce( 'edit_preset_' . $preset->ID );
        }

        if ( current_user_can( 'delete_post', $preset->ID ) )
            $response['nonces']['delete']   = wp_create_nonce( 'delete_preset_' . $preset->ID );

        return apply_filters( 'sed_prepare_preset_for_js', $response, $preset );

    }

    public static function get_module_presets(){

        check_ajax_referer( 'sed-get-collection-presets' , 'nonce' );

        if ( ! current_user_can( 'manage_site_editor_preset' ) ) {

            $data = array(
                'message'  => __( "You don't have permission to manage presets." , "site-editor" ),
            );

            wp_send_json_error( $data );

        }

        $args = ( isset( $_REQUEST['query'] ) ) ? $_REQUEST['query'] : array();

        $defaults = array(
            'post_type'        => 'sed_preset_settings',
            'posts_per_page'   => -1,
            'offset'           => 0,
            'post_status'      => 'publish' ,
            'orderby'          => 'date',
            'order'            => 'DESC',
        );

        $args = wp_parse_args( $args, $defaults );

        $shortcode_name = ( isset( $_REQUEST['shortcode'] ) ) ? $_REQUEST['shortcode'] : '';

        if( !empty( $shortcode_name ) )
            $args['post_mime_type'] = self::get_shortcode_mime_type( $shortcode_name );

        $args = apply_filters( 'ajax_query_presets_args', $args );
        $query = new WP_Query( $args );

        $posts = array_map( array( __CLASS__ , 'prepare_preset_for_js' ) , $query->posts );
        $posts = array_filter( $posts );

        wp_send_json_success( $posts );
    }

}

new SiteEditorPreset;