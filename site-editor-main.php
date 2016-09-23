<?php
$sed_inline_css = "";
$GLOBALS['sed_inline_css'] = $sed_inline_css;

if(isset( $_POST['sed_page_ajax'] ) ){
    require_once SED_PLUGIN_DIR . DS . 'wp-inc' . DS . 'sed_ajax.php';
}


Class SiteEditorApp {
    private $_pagehooks = array();
    var $app_db_version = '1.0';
    var $app_name;
    var $option_settings;
    var $domain;
    var $lang_rel_path;
    var $template_apps;
    var $site_editor_page_processed = false;
    var $less_framework_files = array();
    var $app_save;
    var $editor_manager;
    var $typography;
    public $post_value;
    public $page_settings_loaded = false;
    public $siteeditor_loaded = false;
    public $sed_page_id;
    public $sed_page_type;
    public $dynamic_css_data = array();
    /*
    // is one array include all attachments loaded in modules and ...
    info attachments are using in site editor app and not loaded in site
    using in pb-shortcodes.class.php for set attachments in modules like image
    , video , audio , playlist video and audio , slideshows , gallery and ...
    */
    public $attachments_loaded = array();


    function __construct($app_name,$option_settings,$domain,$lang_rel_path) {

        $this->app_name = $app_name;
        $this->option_settings = $option_settings;
        $this->domain = $domain;
        $this->lang_rel_path = $lang_rel_path;

        $this->module_info = sed_get_setting("module_info");

        if( site_editor_app_on() ){
            require_once SED_BASE_DIR . DS . 'libraries' . DS . 'siteeditor' . DS . 'site-iframe' . DS . 'tmpl.php';
        }

        if( !site_editor_app_on() ){
            //site-editor template
            add_filter( 'template_include', array(&$this,'template_chooser') , 1 );
        }

        // Add specific CSS class by filter
        add_filter( 'body_class', array($this, 'add_rtl_body_class' ) );

        add_action( "wp_footer" , array( $this , "sed_add_dynamic_css_file" ) , 10000 );
        add_action( "wp_footer" , array( $this , "print_parallax_data" ) );



        require_once SED_PLUGIN_DIR . DS . 'site-editor-base.php';
        $this->editor_manager = new SiteEditorManager();

        require_once SED_PLUGIN_DIR . DS . 'site-editor-save.php';
        $this->app_save = new SEDAppSave();

        if(!is_admin()){
            add_action( 'init', array(&$this,'register_scripts') );

            add_action( 'init', array(&$this, 'register_styles') );

            add_action( 'init', array(&$this, 'register_theme_framework_scripts') );

            add_action( 'init', array(&$this, 'register_theme_framework_styles') );

            add_action( 'wp_enqueue_scripts', array( $this,'render_base_scripts') );

            add_action( 'wp_enqueue_scripts', array( $this, 'render_base_styles') );
        }

        //run page builder in siteeditor and site
        add_action( 'init', 'load_page_builder_app' , 0 );

        if( is_site_editor() || site_editor_app_on() || is_sed_save() || isset( $_POST['sed_page_ajax']) ){
            add_action( 'init', array(&$this, 'load_site_editor_app') , 1 );
        }

        add_filter( "sed_posts_content_filter" , array( $this, "content_filter_for_ajax_refresh" ) , 10 , 1 );
        add_filter( "sed_current_page_options" , array( $this, "options_filter_for_ajax_refresh" ) , 9 , 1 );
        //add_filter( "sed_current_page_options" , array( $this, "filter_by_theme_options" ) , 10 , 1 );
        //add_filter( "sed_current_page_options" , array( $this, "filter_by_general_options" ) , 20 , 1 );

        if( !is_admin() && !is_site_editor() )
            add_action( 'wp',  array( $this  , 'set_page_info') , -10000  );

        //add_action('wp_print_styles',         array($this, 'wp_print_styles_action'), -10000);

        $this->load_framework();
    }

    function load_framework(){

        require_once SED_FRAMEWORK_DIR . DS . 'typography.class.php';
        $this->typography = new SiteeditorTypography();

        require_once SED_FRAMEWORK_DIR . DS . 'theme-integration.class.php';

        require_once SED_FRAMEWORK_DIR . DS . 'theme-framework.class.php';

        require_once SED_FRAMEWORK_DIR . DS . 'class_site_editor_css.php';
    }

    function add_rtl_body_class( $classes ) {
        if( is_rtl() ){
            // add 'class-name' to the $classes array
            $classes[] = 'rtl-body';
        }

        return $classes;
    }


    function wp_print_styles_action(){
      global $wp_scripts;
        $queue = $wp_scripts->queue;   //var_dump( $wp_scripts->to_do );
        $wp_scripts->all_deps($queue);
        foreach( $wp_scripts->to_do as $key => $handle ) {

                    ob_start();
                    if ( $wp_scripts->do_item( $handle, $group ) ) {
                        $wp_scripts->done[] = $handle;
                    }
                    ob_end_clean();
       }
    }

    //an easy way to get to our settings array without undefined indexes
    function get_setting($key, $default = null) {
        return sed_get_setting($key, $default);
    }

    function update_setting($key, $value) {
        $settings = get_option( 'site-editor-settings' );
        $settings[$key] = $value;
        return update_option('site-editor-settings', $settings);
    }

    function delete_setting( $key ) {
        $settings = get_option( 'site-editor-settings' );
        if( isset( $settings[$key] ) )
            unset( $settings[$key] );
        return update_option('site-editor-settings', $settings);
    }



    function add_image_sizes() {
          add_image_size( 'seduploaded', 59, 51, true );
          add_image_size( 'site-editor-lib', 48, 38, true );
          //add_image_size( 'site-editor-icon-gallery', 20, 20, true );
    }

/*********************************** START SCRIPT & STYLE  ***************************/

    function register_scripts(){
        wp_register_script( 'modernizr',SED_BASE_URL.'libraries/modernizr/modernizr.custom.min.js', array( ) );

        wp_register_script( 'handlebars',SED_BASE_URL.'libraries/handlebars/handlebars.min.js', array( ) );

        wp_register_script( 'sed-handlebars',SED_BASE_URL.'libraries/handlebars/sed.handlebars.min.js', array('handlebars' , 'jquery' , 'underscore' ) );

        wp_register_script( 'jquery-ui-full',SED_BASE_URL.'libraries/jquery/ui/full-js/jquery.ui.min.js', array( 'jquery' ) );

        //draggable overlap
        wp_register_script( 'sed-overlap',SED_BASE_URL.'libraries/siteeditor/site-iframe/drag-drop/overlap.min.js', array( 'jquery-ui-full' ),"",1 );

        //draggable guidelines
        wp_register_script( 'sed-guidelines',SED_BASE_URL.'libraries/siteeditor/site-iframe/drag-drop/guidelines.min.js', array( 'jquery-ui-full' ),"",1 );

        wp_register_script( 'jquery-contextmenu',SED_BASE_URL.'libraries/contextmenu/js/jquery.contextmenu.min.js', array( 'jquery','jquery-ui-full' , 'siteeditor-base' ) );

        wp_register_script( 'jquery-contenteditable', SED_BASE_URL.'libraries/jquery/jquery.contenteditable.min.js', array( 'jquery' ) , '1.0.0' );

        wp_register_script( 'jquery-livequery',SED_BASE_URL.'libraries/livequery/jquery.livequery.min.js', array( 'jquery','jquery-ui-full' ) );

        wp_register_script( 'sed-livequery',SED_BASE_URL.'libraries/livequery/sed.livequery.min.js', array( 'jquery-livequery' ) );

        wp_register_script( 'column-resize',SED_BASE_URL.'libraries/siteeditor/site-iframe/column-resize.min.js', array( 'jquery','jquery-ui-full' ) );

        wp_register_script( 'siteeditor-base',SED_BASE_URL.'libraries/siteeditor/siteeditor-base.min.js', array( 'jquery' ),"",1 );

        //plugins
        wp_register_script( 'delete-plugin',SED_BASE_URL.'libraries/siteeditor/site-iframe/plugins/delete.min.js', array( 'siteeditor-base' , 'sed-app-preview' , 'sed-pagebuilder' ),"",1 );
        wp_register_script( 'select-plugin',SED_BASE_URL.'libraries/siteeditor/site-iframe/plugins/select.min.js', array( 'siteeditor-base' ),"",1 );
        wp_register_script( 'media-plugin',SED_BASE_URL.'libraries/siteeditor/site-iframe/plugins/media.min.js', array( 'siteeditor-base' , 'sed-app-preview' , 'sed-pagebuilder' ),"",1 );
        wp_register_script( 'preview-plugin',SED_BASE_URL.'libraries/siteeditor/site-iframe/plugins/app-preview.min.js', array( 'siteeditor-base' , 'sed-app-preview' , 'site-iframe' ),"",1 );
        wp_register_script( 'duplicate-plugin',SED_BASE_URL.'libraries/siteeditor/site-iframe/plugins/duplicate.min.js', array( 'siteeditor-base' , 'sed-app-preview' , 'sed-pagebuilder' ),"",1 );

        wp_register_script( 'siteeditor-modules-scripts',SED_BASE_URL.'libraries/siteeditor/siteeditor-modules-scripts.min.js', array( 'siteeditor-base' ),"",1 );

        wp_register_script( 'siteeditor-ajax',SED_BASE_URL.'libraries/siteeditor/siteeditor-ajax.min.js', array( 'siteeditor-base' ),"",1 );

        wp_register_script( 'tinycolor',SED_BASE_URL.'libraries/colorpicker/js/tinycolor.min.js', array( ),"",1 );

        wp_register_script( 'siteeditor-css',SED_BASE_URL.'libraries/siteeditor/siteEditorCss.min.js', array( 'jquery', 'modernizr' , 'tinycolor' ),"",1 );

        wp_register_script( 'sed-app-preview',SED_BASE_URL.'libraries/siteeditor/site-iframe/siteeditor-preview.min.js', array( 'jquery','siteeditor-base' , 'jquery-livequery' , 'underscore' ),"",1 );

        wp_register_script( 'sed-app-preview-render',SED_BASE_URL.'libraries/siteeditor/site-iframe/siteeditor-preview-render.min.js', array( 'sed-app-preview' , 'siteeditor-css' ),"",1 );

        //wp_register_script( 'sed-style-editor', SED_BASE_URL.'libraries/siteeditor/site-iframe/style-editor.min.js', array( 'sed-app-preview-render' ) ,"",1 );

        //wp_register_script( 'sed-app-synchronization',SED_BASE_URL.'libraries/siteeditor/site-iframe/siteeditor-synchronization.min.js', array( 'sed-app-preview-render' ),"",1 );

        wp_register_script( 'sed-app-shortcode-builder',SED_BASE_URL.'libraries/siteeditor/site-iframe/shortcode-content-builder.min.js', array( 'sed-app-preview-render' ),"",1 );

        wp_register_script( 'site-iframe',SED_BASE_URL.'libraries/siteeditor/site-iframe/site-iframe.min.js', array( 'sed-app-preview-render' , 'column-resize' , 'jquery-ui-full' , 'sed-app-shortcode-builder' ),"1.0.0" , 1 );

        wp_register_script( 'sed-pagebuilder',SED_BASE_URL.'libraries/siteeditor/site-iframe/pagebuilder.min.js', array( 'site-iframe' , 'siteeditor-modules-scripts' , 'preview-plugin' ),"1.0.0" , 1 );

        wp_register_script( 'sed-module-free-draggable',SED_BASE_URL.'libraries/siteeditor/site-iframe/drag-drop/sed-module-free-draggable.min.js', array( 'sed-pagebuilder' ),"1.0.0" , 1 );

        wp_register_script( 'sed-app-contextmenu-render',SED_BASE_URL.'libraries/siteeditor/site-iframe/siteeditor-contextmenu.min.js', array( 'sed-app-preview-render' , 'jquery-contextmenu' ),"",1 );

        wp_register_script( 'sed-app-widgets',SED_BASE_URL.'libraries/siteeditor/site-iframe/siteeditor-widgets.min.js', array( 'sed-pagebuilder' ),"",1 );

        $this->register_bootstrap_script();

    }

    function register_bootstrap_script(){
        wp_register_script( 'bootstrap-tooltip',SED_BASE_URL.'libraries/bootstrap/js/tooltip/tooltip.min.js', array( 'jquery'  ),"" );

        wp_register_script( 'bootstrap-popover',SED_BASE_URL.'libraries/bootstrap/js/popover/popover.min.js', array( 'bootstrap-tooltip' ),"" );
    }

    function register_theme_framework_scripts(){

        //images loaded
        wp_register_script( 'images-loaded',SED_PLUGIN_URL.'/wp-inc/framework/js/imagesloaded/imagesloaded.pkgd.min.js', array() ,"1.2.4");

        wp_register_script('sed-app-site',SED_PLUGIN_URL.'/wp-inc/framework/js/sed_app_site.min.js', array( 'jquery' ) , "1.0.0" );
        wp_register_script( 'wow-animate',SED_BASE_URL.'libraries/animate/js/wow.min.js', array( ) , "1.0.2" , 1 );
        wp_register_script('lightbox',SED_PLUGIN_URL.'/wp-inc/framework/js/lightbox/lightbox.min.js', array( 'jquery' ) );

        wp_register_script( 'carousel',SED_PLUGIN_URL.'/wp-inc/framework/js/slick.carousel/slick.min.js', array( ) ,"1.3.7");
        wp_register_script( 'easing',SED_PLUGIN_URL.'/wp-inc/framework/js/easing/jquery.easing.1.3.js', array('jquery') ,"1.3");
        wp_register_script( 'packery',SED_PLUGIN_URL.'/wp-inc/framework/js/metafizzy/packery.pkgd.min.js', array() ,"1.2.4");

        //deregister wordpress masonry
        wp_deregister_script( 'masonry' );
        wp_register_script( 'masonry',SED_PLUGIN_URL.'/wp-inc/framework/js/masonry/masonry.pkgd.min.js', array() ,"1.2.4");
        wp_register_script( 'sed-masonry',SED_PLUGIN_URL.'/wp-inc/framework/js/masonry/sed-masonry.min.js', array('masonry','sed-livequery' , 'images-loaded') ,"1.2.4");

        wp_register_script( 'isotope',SED_PLUGIN_URL.'/wp-inc/framework/js/isotope/isotope.pkgd.min.js', array() ,"2.2.0");

        wp_register_script( 'waypoints',SED_PLUGIN_URL.'/wp-inc/framework/js/waypoints/waypoints.js', array('jquery') ,"2.0.5");
        wp_register_script( 'jquery-parallax',SED_BASE_URL.'libraries/parallax/jquery.parallax.min.js', array( 'jquery' ) , "1.1.3" , 1 );

        wp_register_script( 'sed-ajax-load-posts',SED_PLUGIN_URL.'/wp-inc/framework/js/post.ajax/sed-ajax-load-posts.min.js', array( ) , "1.0.0" , 1 );

        wp_register_script( 'sed-tinymce',SED_BASE_URL.'libraries/tinymce/tinymce.min.js', array() ,"4.0.5");
        /* REGISTER SCRIPT BOOTSTRAP
        ============================*/
        wp_register_script( 'bootstrap-tab',SED_PLUGIN_URL.'/wp-inc/framework/js/bootstrap/bootstrap-tab.min.js', array('jquery') ,"3.3.2");
        wp_register_script( 'bootstrap-dropdown',SED_PLUGIN_URL.'/wp-inc/framework/js/bootstrap/bootstrap-dropdown.js', array('jquery') ,"3.3.2");

        wp_register_script('render-scripts',SED_PLUGIN_URL.'/wp-inc/framework/js/render.min.js', array( 'jquery' , 'wow-animate' , 'jquery-parallax' ) , "1.0.0" , 1 );
        /* REGISTER SCRIPT jplayer
        ========================*/
        wp_register_script( 'jplayer-plugin',SED_PLUGIN_URL.'/wp-inc/framework/js/jplayer/jquery.jplayer.js', array('jquery') ,"2.7.0");
        wp_register_script( 'jplayer-playlist',SED_PLUGIN_URL.'/wp-inc/framework/js/jplayer/jplayer.playlist.js', array('jquery','jplayer-plugin') ,"2.4.0");

        //scrollbar
        wp_register_script( 'custom-scrollbar', SED_BASE_URL.'libraries/scrollbar/js/jquery.mCustomScrollbar.concat.min.js', array('jquery'), '2.3' );

        /* REGISTER SCRIPT JQUERY UI
        ==========================*/
        /*wp_register_script( 'jquery-ui-core',SED_PLUGIN_URL.'/wp-inc/framework/js/jquery/ui/jquery.ui.core.min.js', array('jquery') ,"1.10.4");
        wp_register_script( 'jquery-ui-widget',SED_PLUGIN_URL.'/wp-inc/framework/js/jquery/ui/jquery.ui.widget.min.js', array('jquery','ui-core') ,"1.10.4");
        wp_register_script( 'jquery-effects-core',SED_PLUGIN_URL.'/wp-inc/framework/js/jquery/ui/jquery.ui.effect.min.js', array('jquery','jquery-ui-core') ,"1.10.4");

        wp_register_script( 'jquery-ui-tabs',SED_PLUGIN_URL.'/wp-inc/framework/js/jquery/ui/jquery.ui.tabs.min.js', array('jquery','ui-core','ui-widget') ,"1.10.4");
        wp_register_script( 'jquery-ui-accordion',SED_PLUGIN_URL.'/wp-inc/framework/js/jquery/ui/jquery.ui.accordion.min.js', array('jquery','jquery-ui-core','jquery-ui-widget','jquery-effects-core') ,"1.10.4");
        */
    }

    function register_theme_framework_styles(){
        wp_register_style( 'css3-animate',SED_BASE_URL.'libraries/animate/css/animate.min.css' );
        wp_register_style( 'lightbox',SED_PLUGIN_URL.'/wp-inc/framework/css/lightbox/lightbox.min.css' );
        wp_register_style( 'carousel',SED_PLUGIN_URL.'/wp-inc/framework/css/slick.carousel/slick.css' ,"1.3.7");

        wp_register_style( 'general', SED_PLUGIN_URL.'/wp-inc/framework/css/general.css' );
        wp_register_style( 'bootstrap-popover',SED_BASE_URL.'libraries/bootstrap/css/popover/popover.css');

        //scrollbar
        wp_register_style( 'custom-scrollbar', SED_BASE_URL.'libraries/scrollbar/css/jquery.mCustomScrollbar.css', array(), '2.3' );

        /* REGISTER STYLE FOR jplayer
        ============================*/
        //wp_register_style( 'jplayer-audio',SED_PLUGIN_URL.'/wp-inc/framework/css/fonts-icon/font-awesome-4.2.0/css/font-awesome.min.css' ,"4.2.0");
    }

    function render_base_scripts(){

        if( !site_editor_app_on() ){
            wp_enqueue_script('sed-app-site');
        }

        wp_enqueue_script('wow-animate');
        wp_enqueue_script('jquery-parallax');
        wp_enqueue_script('render-scripts');
        wp_enqueue_script('jquery-livequery');
        wp_enqueue_script('sed-livequery');

    }

    function render_base_styles(){

        //call base styles( base less framework )
        $main_style = array(
            "handle"    => 'main-style' ,
            'src'       => SED_UPLOAD_URL . '/style/siteeditor.css',
            'deps'      => array(),
            'ver'       => SED_APP_VERSION,
            'media'     => 'all',
        );

        extract( $main_style );

        wp_register_style( $handle , $src , $deps , $ver , $media ) ;
        wp_enqueue_style( $handle ) ;

        wp_enqueue_style( 'css3-animate');
        wp_enqueue_style( 'general' );

    }

    function register_styles(){
       // wp_register_style( 'jquery-ui-full',SED_BASE_URL.'libraries/jquery/ui/css/jquery-ui.min.css' );

        wp_register_style( 'site-iframe',SED_BASE_URL.'libraries/siteeditor/site-iframe/site-iframe.min.css' );

        wp_register_style( 'contextmenu',SED_BASE_URL.'libraries/contextmenu/css/style.min.css' );

        //wp_register_style( 'font-line-icon',SED_BASE_URL.'libraries/siteeditor/site-iframe/simple-line-icons.css' );
        wp_register_style( 'fonts-sed-iframe',SED_BASE_URL.'templates/default/css/fonts-sed-iframe.css' );
    }

/*********************************** END SCRIPT & STYLE  ***************************/


/*********************************** Start media Library settings  ***************************/
    function sed_max_upload_size(){
        $upload_size_unit = $max_upload_size = wp_max_upload_size();
        $sizes = array( 'KB', 'MB', 'GB' );

        for ( $u = -1; $upload_size_unit > 1024 && $u < count( $sizes ) - 1; $u++ ) {
            $upload_size_unit /= 1024;
        }

        if ( $u < 0 ) {
            $upload_size_unit = 0;
            $u = 0;
        } else {
            $upload_size_unit = (int) $upload_size_unit;
        }
        return $upload_size_unit;
    }

    function media_types(){
        $sedmediatypes = apply_filters( 'sedext2type', array(
            'image'       => array(
                'caption'  =>  __('Image' , 'site-editor'),
                'ext'      =>  array( 'jpg', 'jpeg', 'jpe',  'gif',  'png',  'bmp',   'tif',  'tiff', 'ico' )
            ),
            'audio'       => array(
                'caption'  =>  __('Audio' , 'site-editor'),
                'ext'      =>  array( 'aac', 'ac3',  'aif',  'aiff', 'm3a',  'm4a',   'm4b',  'mka',  'mp1',  'mp2',  'mp3', 'ogg', 'oga', 'ram', 'wav', 'wma' , "webma" , "webm" )
            ),
            'video'       => array(
                'caption'  =>  __('Video' , 'site-editor'),
                'ext'      =>  array( '3g2',  '3gp', '3gpp', 'asf', 'avi',  'divx', 'dv',   'flv',  'm4v',   'mkv',  'mov',  'mp4',  'mpeg', 'mpg', 'mpv', 'ogm', 'ogv', 'qt',  'rm', 'vob', 'wmv' , "webmv" , "webm" , "ogg"  )
            ),
            'document'    => array(
                'caption'  =>  __('Document' , 'site-editor'),
                'ext'      => array( 'doc', 'docx', 'docm', 'dotm', 'odt',  'pages', 'pdf',  'xps',  'oxps', 'rtf',  'wp',   'wpd' )
            ),
            'spreadsheet' => array(
                'caption'  =>  __('Spreadsheet' , 'site-editor'),
                'ext'      =>  array( 'numbers',     'ods',  'xls',  'xlsx', 'xlsm',  'xlsb' )
            ),
            'interactive' => array(
                'caption'  =>  __('Interactive' , 'site-editor'),
                'ext'      =>  array( 'swf', 'key',  'ppt',  'pptx', 'pptm', 'pps',   'ppsx', 'ppsm', 'sldx', 'sldm', 'odp' )
            ),
            'text'        => array(
                'caption'  =>  __('Text' , 'site-editor'),
                'ext'      =>  array( 'asc', 'csv',  'tsv',  'txt' , 'c' , 'cc' , 'h' , 'htm' , 'html' , 'css' , 'rtx' , 'ics' )
            ),
            'archive'     => array(
                'caption'  =>  __('Archive' , 'site-editor'),
                'ext'      => array( 'bz2', 'cab',  'dmg',  'gz',   'rar',  'sea',   'sit',  'sqx',  'tar',  'tgz',  'zip', '7z' )
            )
            /*'code'        => array(
                'caption'  =>  __('Code' , 'site-editor'),
                'ext'      =>  array( 'css', 'htm',  'html', 'php',  'js' )
            ) */
        ) );

        return $sedmediatypes;
    }
/*********************************** End media Library settings  ***************************/

/*********************************** Start Helper Function  ***************************/

    /**
     * Return true if it's an AJAX request.
     *
     * @since 3.4.0
     *
     * @return bool
     */
    public function doing_ajax() {
        return isset( $_POST['sed_page_customized'] ) || ( defined( 'DOING_SITE_EDITOR_AJAX' ) && DOING_SITE_EDITOR_AJAX );
    }

    function check_ajax_handler($ajax , $nonce){
        if ( is_admin() && ! $this->doing_ajax() )
            auth_redirect();
        elseif ( $this->doing_ajax() && ! is_user_logged_in() ){
            $this->sed_die( 0 );
        }

        if ( ! current_user_can( 'edit_theme_options' ) )
            $this->sed_die( -1 );

       if( !check_ajax_referer( $nonce . '_' . $this->editor_manager->get_stylesheet(), 'nonce' , false ) ){
            $this->sed_die( -2 );
       }
       if( !isset($_POST['sed_page_ajax']) || $_POST['sed_page_ajax'] !=  $ajax){
            $this->sed_die( -2 );
       }
    }

    /**
     * Custom wp_die wrapper. Returns either the standard message for UI
     * or the AJAX message.
     *
     * @since 3.4.0
     *
     * @param mixed $ajax_message AJAX return
     * @param mixed $message UI message
     */
    public function sed_die( $message = null ) {
        if ( is_scalar( $message ) )
            die( (string) $message );
        die( '0' );
    }

    function sed_custom_js_plugins(){
        $plugin_array = array();
        return apply_filters('sed_custom_js_plugins' , $plugin_array);
    }
/*********************************** End Helper Function  ***************************/

    function load_site_editor_app(){

        //include_once( SED_BASE_DIR . DS . 'application' . DS . "modules.class.php"  );
        include_once( SED_BASE_DIR . DS . 'application' . DS . "application.class.php"  );
        include_once( SED_BASE_DIR . DS . 'applications' . DS . 'siteeditor' . DS . "siteeditor.class.php"  );
        require_once SED_BASE_DIR . DS . 'applications' . DS . 'siteeditor' . DS . 'index.php';

        do_action( 'sed_app_register', $this->editor_manager );

        $this->siteeditor_loaded = true;

        /*if( !is_site_editor() ){

            $settings = array();

            $_post_values = json_decode( wp_unslash( $_POST['sed_page_customized'] ), true );
            $sed_app_settings = $this->editor_manager->settings();
            foreach ( $_post_values as $id => $value ) {
               $setting = $sed_app_settings[$id];
               if($setting->option_type == "base" || empty( $setting->option_type ) ){
                   $settings[$id] = $value;  //$setting->id
               }
            }

            var_export( $settings );



            $settings = $this->get_page_settings();

            $GLOBALS['sed_data'] = $settings;
        }*/

    }


/*********************************** Start front end load in SiteEditor  ***************************/
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

    function content_filter_for_ajax_refresh( $posts_content ){

        if( isset( $_POST['sed_ajax_refresh'] ) && isset( $_POST['sed_posts_content'] ) ){
            $sed_posts_content = json_decode( wp_unslash( $_POST['sed_posts_content'] ), true );
            return $sed_posts_content;
        }

        return $posts_content;

    }

    function options_filter_for_ajax_refresh( $page_options ) {
        if( isset( $_POST['sed_ajax_refresh'] ) && isset( $_POST['page_options'] ) ){
            $options = json_decode( wp_unslash( $_POST['page_options'] ), true );
            $page_options['theme_content'] = $options['theme_content'];
        }

        return $page_options;

        /*
        global $sed_apps ;  //@args ::: sed_page_ajax , nonce
        $sed_apps->check_ajax_handler('sed_load_modules' , 'sed_app_modules_load');

        $shortcodes = json_decode( wp_unslash( $_REQUEST['pattern'] ), true );
        $parent_id = $_REQUEST['parent_id'];
        $tree_shortcodes = $sed_apps->app_save->build_tree_shortcode( $shortcodes , $parent_id );
        //convert to normal content with sed_do_shortcode
        $content = $this->sed_do_shortcode( $tree_shortcodes );

        $output = do_shortcode( $content );

        wp_send_json_success( $output );
        */
    }

    /*function filter_by_theme_options( $page_options ) {

        if ( isset( $_POST['sed_page_customized'] ) ){

            $_post_values = json_decode( wp_unslash( $_POST['sed_page_customized'] ), true );

            if( isset( $_post_values['sed_layouts_models'] ) )
                $sub_themes_models = $_post_values['sed_layouts_models'];
            else
                $sub_themes_models = get_option("sed_layouts_models");


            if( isset( $_post_values['sed_theme_options'] ) )
                $sed_theme_options = $_post_values['sed_theme_options'];
            else
                $sed_theme_options = get_option("sed_theme_options");

        }else{
            $sub_themes_models = get_option("sed_layouts_models");

            $sed_theme_options = get_option("sed_theme_options");
        }


        if( !is_array( $sed_theme_options ) || $sed_theme_options === false )
            return $page_options;

        if( !is_array($page_options) )
            $page_options = get_pages_default_options();

        $sub_theme = ( isset( $page_options['page_layout'] ) && !empty( $page_options['page_layout'] ) ) ? $page_options['page_layout'] : $this->default_page_layout;

        if( !isset( $sub_themes_models[ $sub_theme ] ) || !is_array( $sub_themes_models[ $sub_theme ] ) || empty( $sub_themes_models[ $sub_theme ] ) )
            return $page_options;

        $curr_sub_themes_models = $sub_themes_models[ $sub_theme ];

        $new_page_options = $page_options;

        foreach( $curr_sub_themes_models AS $key => $model ){
            if( !in_array( $this->sed_page_id , $model['exclude'] ) && is_array( $sed_theme_options[$model["theme_id"]] ) ){ //var_dump( $model["theme_id"] ); // var_dump($sed_theme_options[$model["theme_id"]]);
                $new_page_options =  array_replace_recursive( $new_page_options , $sed_theme_options[$model["theme_id"]] );
            }

        }

        return $new_page_options;

    }

    function filter_by_general_options( $page_options ) {

        if ( isset( $_POST['sed_page_customized'] ) ){

            $_post_values = json_decode( wp_unslash( $_POST['sed_page_customized'] ), true );

            if( isset( $_post_values['sed_theme_options'] ) )
                $sed_general_options = $_post_values['sed_general_theme_options'];
            else
                $sed_general_options = get_option("sed_general_theme_options");

        }else{
            $sed_general_options = get_option("sed_general_theme_options");
        }


        if( !is_array( $sed_general_options ) || $sed_general_options === false )
            return $page_options;

        if( !is_array($page_options) )
            $page_options = get_pages_default_options();

        $sub_theme = ( isset( $page_options['page_layout'] ) && !empty( $page_options['page_layout'] ) ) ? $page_options['page_layout'] : $this->default_page_layout;

        foreach( $sed_general_options As $setting_id => $data ){
            if( in_array( $sub_theme , $data['scope']['sub_themes'] ) && !in_array( $this->sed_page_id  , $data['scope']['exclude'] ) ){
                if( $data['type'] == "style-editor" ){
                    $page_options[$setting_id]["#page"] = $data['value'];
                }else{
                    $page_options[$setting_id] = $data['value'];
                }
            }
        }

        return $page_options;

    }*/


    function get_page_settings(){

        if($this->siteeditor_loaded === true){
            if( $this->page_settings_loaded === true  ){
                return $this->sed_page_settings;
            }else{

                $this->page_settings_loaded = true;
                $this->sed_page_settings = sed_get_page_options( $this->sed_page_id  , $this->sed_page_type );

                return $this->sed_page_settings;
            }
        }else
            return new WP_Error( 'sed-app-loaded', __( "siteeditor app not loaded yet", "site-editor" ) );

    }


    function set_page_info(  ) {
        $info_u = $this->get_sed_page_info_uniqe();

        $this->sed_page_id = $info_u['id'];
        $this->sed_page_type = $info_u['type'];
        //for sub_theme module
        global $site_editor_app; 

        if( !site_editor_app_on() ){
            $this->load_site_editor_app();
        }

            /*if( !$settings = sed_get_page_options($this->sed_page_id , $this->sed_page_type) )
                $settings = get_pages_default_options();

            $sed_data = $settings;
            $GLOBALS['sed_data'] = $settings;*/

        $settings = sed_get_page_options( $this->sed_page_id  , $this->sed_page_type );

        $GLOBALS['sed_data'] = $settings;

        //var_dump( $settings );

        global $sed_data;

        $sed_data['page_id']  = $this->sed_page_id;
        $sed_data['page_type'] = $this->sed_page_type;

    }

    function template_chooser( $template ) {

        // For all other CPT
        if ( !is_site_editor() ) {

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

        }else{
          if (!is_user_logged_in()) {
              auth_redirect();
          }else{

              return $this->get_site_editor();
          }
       }
    }

    //this function call front end site-editor
    function get_site_editor( ) {
        require_once SED_BASE_DIR . DS . 'index.php';
    }                                                                                             //, $lang = ''

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

        //$sed_data = $this->editor_manager->post_value;

        ob_start();
        include SED_PLUGIN_DIR . DS . 'wp-inc' . DS . 'framework' . DS . 'dynamic_css.php';
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

    function print_parallax_data(){
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

/*********************************** End front end load in SiteEditor  ***************************/

}


//global $sed_apps;
$GLOBALS['sed_apps'] = new SiteEditorApp("site_editor","site_editor_settings","site-editor","/languages/");


function sed_get_page_options($sed_page_id = "general_home" , $sed_page_type = "general"){
    global $sed_apps;
    if($sed_page_type == "post")
        $option_name = 'sed_post_settings' ;
    else
        $option_name = 'sed_'. $sed_page_id .'_settings' ;

    return apply_filters( "sed_current_page_options" , $sed_apps->app_save->sed_get_page_options( $option_name , $sed_page_id  , $sed_page_type ) );

}



function sed_update_page_options($settings , $sed_page_id = "general_home" , $sed_page_type = "general"){
    global $sed_apps;
    if($sed_page_type == "post")
        $option_name = 'sed_post_settings' ;
    else
        $option_name = 'sed_'. $sed_page_id .'_settings' ;

    $sed_apps->app_save->sed_update_page_options($settings , $option_name , $sed_page_id  , $sed_page_type );
}


