<?php
/**
 * Class SiteEditorAdminRender
 */
class SiteEditorAdminRender{

    private $app_name = 'site_editor';

    var $_pagehooks = array();

    function __construct(){

        if( !defined( 'SED_ADMIN_INC_PATH' ) )
            define('SED_ADMIN_INC_PATH', SED_ADMIN_DIR . DS . 'includes');

        /*if( !class_exists( 'SiteEditorSetup' ) )
            require_once( SED_ADMIN_INC_PATH . DS . 'sed-setup.class.php' );*/

        require_once( SED_ADMIN_INC_PATH . DS . 'class_sed_error.php' );
        $GLOBALS['sed_error'] = new SED_Error;

        
        add_action("init" ,  array( "SiteEditorAdminRender" ,"load_page_builder_app") );

        //add admin menu
        add_action( 'admin_menu', array( $this ,'app_create_menu') );
        
        //add style & javascript to admin
        add_action( 'admin_enqueue_scripts', array( $this , 'render_scripts' ) );

        add_action('admin_init', array( $this, 'options_admin_init' ) );

        //if( SiteEditorSetup::is_installed() ){

            // add button Edit With SiteEditor in default editor wordpress
            add_action('media_buttons',  array( $this , 'add_button_to_editor' ) );

            add_filter('tag_row_actions', array( $this , 'sed_tag_row_actions' ) , 10, 2);
            // add row action edit for posts list and pages list
            add_filter('post_row_actions', array( $this , 'add_actions_to_list' ) , 10, 2);
            add_filter('page_row_actions', array( $this , 'add_actions_to_list' ) , 10, 2);

            add_action( 'admin_enqueue_scripts', array( $this, 'options_media_scripts' ));

            add_filter('upload_mimes', array( $this , 'filter_mime_types') );

            add_action( 'admin_init', array( $this , 'admin_init')  );

        /*}else{

            add_action( 'admin_notices', array( $this , 'admin_notices'));

        }*/

        add_filter('set-screen-option', array( $this , 'module_set_screen_option' ), 10, 3);

    }

    public static function load_page_builder_app(){


        //ini_set('xdebug.var_display_max_children',1000 );
        //ini_set('xdebug.var_display_max_depth',20 );
        //ini_set('xdebug.var_display_max_data' , 100000 );

        //var_dump( get_option( 'site-editor-settings' ) );

        global $sed_pb_modules;

        if ( !$sed_pb_modules instanceof SEDPageBuilderModules ) {

            include_once(SED_INC_DIR . DS . "modules.class.php");
            include_once(SED_INC_DIR . DS . "app_pb_modules.class.php");

            $pb_modules = new SEDPageBuilderModules();
            $pb_modules->app_modules_dir = SED_PB_MODULES_PATH;
            $sed_pb_modules = $pb_modules;

        }

    }

    /**
     * TODO : Add to site editor install class
     */
    function options_admin_init(){
        
        global $sed_general_data , $options_machine;

        include SED_ADMIN_INC_PATH . DS . 'options.php';

        require_once SED_ADMIN_INC_PATH . DS . 'sed-admin-options.class.php';

        $options_machine = new SED_Admin_Options( $tabs , $items );

        $sed_general_data = sed_get_plugin_options();
        $data = $sed_general_data;

        do_action('sed_plugin_options_admin_init_before', array(
            'options'		    => $items,
            'options_machine'	=> $options_machine,
            'sed_general_data'	=> $sed_general_data
        ));

        if (empty($sed_general_data['sed_init'])) { // Let's set the values if the theme's already been active

            $defaults = $options_machine->get_default_values();

            sed_save_plugin_options( $defaults );
            sed_save_plugin_options(date('r'), 'sed_init');
            $sed_general_data = sed_get_plugin_options();

        }

        do_action('sed_plugin_options_admin_init_after', array(
            'of_options'		=> $items,
            'options_machine'	=> $options_machine,
            'sed_general_data'	=> $sed_general_data
        ));

    }

    function admin_init() {
        if ( current_user_can( 'delete_posts' ) )
            add_action( 'delete_post', array( $this , 'post_remove_from_exclude') , 10 );
    }

    function post_remove_from_exclude( $pid ) {

        /*$model_changed = false;

        $sed_general_theme_options = get_option( 'sed_general_theme_options' );

        foreach( $sed_general_theme_options AS  $settings_id => $options ){
            if( isset( $options['scope'] ) && isset( $options['scope']['exclude'] ) && !empty( $options['scope']['exclude'] ) ){
                foreach( $options['scope']['exclude'] AS  $index => $post_id ){
                    if( $post_id == $pid ){
                        $model_changed = true;
                        unset( $sed_general_theme_options[$settings_id]['scope']['exclude'][$index] );
                    }
                }
            }
        }

        if( $model_changed === true ){
            update_option('sed_general_theme_options', $sed_general_theme_options );
        }*/

        $sed_layouts_models = get_option( 'sed_layouts_models' , array() );
        $model_changed = false;

        if( is_array( $sed_layouts_models ) && !empty( $sed_layouts_models ) ) {

            foreach ($sed_layouts_models AS $sub_theme => $rows) {
                foreach ($rows AS $row_idx => $row) {
                    if (!empty($row['exclude'])) {
                        foreach ($row['exclude'] AS $index => $post_id) {
                            if ($post_id == $pid) {
                                $model_changed = true;
                                unset($sed_layouts_models[$sub_theme][$row_idx]['exclude'][$index]);
                            }
                        }
                    }
                }
            }

            if ($model_changed === true) {
                update_option('sed_layouts_models', $sed_layouts_models);
            }

        }

    }

    function filter_mime_types($mimes){

        $mimes['ttf'] = 'font/ttf';
        $mimes['woff'] = 'font/woff';
        $mimes['svg'] = 'font/svg';
        $mimes['eot'] = 'font/eot';

        return $mimes;
    }

    function render_scripts( $hook ){

        if( in_array( $hook , $this->_pagehooks ) ){

            wp_enqueue_script( "sed-admin-scripts" , plugins_url('templates/default/js/scripts.js', __FILE__ ) , array('jquery' , 'wp-color-picker') , '1.0.0' , false );
            wp_enqueue_style( "sed-admin-style" , plugins_url('templates/default/css/style.css', __FILE__ ) , array() , '1.0.0' , 'all');
        }

        if( !isset( $this->_pagehooks['site-editor-settings'] ) || !isset( $this->_pagehooks['site-editor'] ) )
            return false;

        if( in_array( $hook , array( $this->_pagehooks['site-editor-settings'] , $this->_pagehooks['site-editor'] ) ) )
            wp_enqueue_style( 'wp-color-picker' );
    }


    /**
     * Enqueue scripts for file uploader
     */
    function options_media_scripts( $hook ) {


        if( !in_array( $hook , array( $this->_pagehooks['site-editor-settings'] , $this->_pagehooks['site-editor'] ) ) )
            return ;

        if ( function_exists( 'wp_enqueue_media' ) )
            wp_enqueue_media();

        wp_register_script( 'of-media-uploader', SED_ADMIN_URL .'templates/default/js/media-uploader.js', array( 'jquery' ) );
        wp_enqueue_script( 'of-media-uploader' );
        wp_localize_script( 'of-media-uploader', 'optionsframework_l10n', array(
            'upload' => __( 'Upload', 'site-editor' ),
            'remove' => __( 'Remove', 'site-editor' )
        ) );
    }


    public function admin_notices( $msg = ''){

        if( isset( $_GET['page'] ) && $_GET['page'] == "site_editor_index" )
            return '';

        $msg = empty( $msg ) ? sprintf( __( '<a href="%1$s">complete install Site Editor plugin</a>', 'site-editor' ), admin_url('admin.php?page=site_editor_index') ) : $msg ;

        echo "<div class='error fade'><p>$msg</p></div>";

    }


    function app_create_menu(){
        //create new top-level menu
        $menu           = new stdClass;
        $modules_menu   = new stdClass;
        $settings_menu  = new stdClass;
        $edit_module    = new stdClass;
        $skins_menu     = new stdClass;
        $extend_menu    = new stdClass;

        // DEFINE MAIN MENU FOR SITE EDITOR
        //=================================
        $menu->page_title = __("SiteEditor",'site-editor');
        $menu->menu_title = __("SiteEditor",'site-editor');
        $menu->capability = 'site_editor_manage';
        $menu->menu_slug  = $this->app_name."_index";
        $menu->func       = array( $this, 'get_site_editor_admin' );
        $menu->icon_url   = '';
        $menu->position   = 300;


        // DEFINE SETTINGS MENU
        //====================
        $settings_menu->parent            = $menu->menu_slug;
        $settings_menu->page_title      = __("SiteEditor Settings", 'site-editor');
        $settings_menu->menu_title      = __("Settings",'site-editor') ;
        $settings_menu->capability      = 'sed_manage_settings';
        $settings_menu->menu_slug       = $this->app_name."_index";
        $settings_menu->func            = array( $this, 'get_site_editor_admin' );

        // DEFINE MODULES MENU
        //====================
        $modules_menu->parent            = $menu->menu_slug;
        $modules_menu->page_title      = __("Installed Modules", 'site-editor');
        $modules_menu->menu_title      = __("Modules",'site-editor') ;
        $modules_menu->capability      = 'manage_modules';
        $modules_menu->menu_slug       = $this->app_name."_module";
        $modules_menu->func            = array( $this, 'get_site_editor_admin' );

        // DEFINE SKINS MENU
        //===================
        $skins_menu->parent            = $menu->menu_slug;
        $skins_menu->page_title        = __("Skins",'site-editor') ;
        $skins_menu->menu_title        = __("Skins", 'site-editor');
        $skins_menu->capability        = 'manage_module_skins';
        $skins_menu->menu_slug         = $this->app_name."_skin";
        $skins_menu->func              = array( $this, 'get_site_editor_admin' );

        // DEFINE EXTEND MENU
        //===================
        $extend_menu->parent            = $menu->menu_slug;
        $extend_menu->page_title        = __("Extensions",'site-editor') ;
        $extend_menu->menu_title        = __("Extensions", 'site-editor');
        $extend_menu->capability        = 'site_editor_manage';
        $extend_menu->menu_slug         = $this->app_name."_extend";
        $extend_menu->func              = array( $this, 'get_site_editor_admin' );

        // DEFINE EDIT MODULE MENU
        //========================
        $edit_module->parent           = $menu->menu_slug;
        $edit_module->page_title       = __("Less Editor",'site-editor') ;
        $edit_module->menu_title       = __("Less Editor", 'site-editor');
        $edit_module->capability       = 'sed_edit_less';
        $edit_module->menu_slug        = $this->app_name."_edit_module";
        $edit_module->func             = array( $this, 'get_site_editor_admin' );


        // CREATE MENU WITH METHODE->modules_menu
        //=======================================
        $this->_pagehooks['site-editor']         = $this->add_admin_menu( $menu,'menu');
        $this->_pagehooks['site-editor-settings']         = $this->add_admin_menu( $settings_menu, 'submenu' );
        $this->_pagehooks['site-editor-module']         = $this->add_admin_menu( $modules_menu, 'submenu' );
        //$this->_pagehooks['site-editor-extend']          = $this->add_admin_menu( $extend_menu, 'submenu' );
        //$this->_pagehooks['site-editor-skins']          = $this->add_admin_menu( $skins_menu, 'submenu' );
        //$this->_pagehooks['site-editor-edit-files']    = $this->add_admin_menu( $edit_module, 'submenu' );

        // Adds my_help_tab when my_admin_page loads
        add_action('load-'.$this->_pagehooks['site-editor-module'], array( $this , 'modules_page_add_help_tab' ) );

    }

    //admin Controller:: index admin page
    function get_site_editor_admin(){
        require_once SED_ADMIN_DIR . DS . 'index.php';
    }


    function modules_page_add_help_tab () {

        add_screen_option( 'per_page', array('label' => _x( 'Number of items per page:', 'items per page (screen options)' , 'site-editor' ) , 'default' => 30 , 'option' => 'sed_modules_per_page' ) );

        get_current_screen()->add_help_tab( array(
            'id'        => 'modules_help_tab',
            'title'     => __('Overview Modules' , 'site-editor'),
            'content'   =>
                '<p>' . __( 'All the files you&#8217;ve uploaded are listed in the Media Library, with the most recent uploads listed first. You can use the Screen Options tab to customize the display of this screen.' ) . '</p>' .
                '<p>' . __( 'You can narrow the list by file type/status using the text link filters at the top of the screen. You can also filter the list by date using the drop down menu above the media table.' ) . '</p>'
        ) );

        get_current_screen()->add_help_tab( array(
            'id'		=> 'adding-plugins',
            'title'		=> __('Adding Plugins'),
            'content'	=>
                '<p>' . __('If you know what you&#8217;re looking for, Search is your best bet. The Search screen has options to search the WordPress.org Plugin Directory for a particular Term, Author, or Tag. You can also search the directory by selecting popular tags. Tags in larger type mean more plugins have been labeled with that tag.') . '</p>' .
                '<p>' . __('If you just want to get an idea of what&#8217;s available, you can browse Featured and Popular plugins by using the links in the upper left of the screen. These sections rotate regularly.') . '</p>' .
                '<p>' . __('You can also browse a user&#8217;s favorite plugins, by using the Favorites link in the upper left of the screen and entering their WordPress.org username.') . '</p>' .
                '<p>' . __('If you want to install a plugin that you&#8217;ve downloaded elsewhere, click the Upload link in the upper left. You will be prompted to upload the .zip package, and once uploaded, you can activate the new plugin.') . '</p>'
        ) );

        get_current_screen()->set_help_sidebar(
            '<p><strong>' . __('For more information:') . '</strong></p>' .
            '<p>' . __('<a href="https://codex.wordpress.org/Plugins_Add_New_Screen" target="_blank">Documentation on Installing Plugins</a>') . '</p>' .
            '<p>' . __('<a href="https://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
        );

        ob_start();

    }


    /*
   add_menu_page( page_title, menu_title, capability,
   menu_slug, function, icon_url, position );
   ➤ page_title — Text used for the HTML title (between <title> tags).
   ➤ menu_title — Text used for the menu name in the Dashboard.
   ➤ capability — Minimum user capability required to see menu.
   ➤ menu_slug — Unique slug name for your menu.
   ➤ function — Displays page content for the menu settings page.
   ➤ icon_url — Path to custom icon for menu (default: images/generic.png).
   ➤ position — The position in the menu order the menu should appear. By default, the menu

   ➤ add_dashboard_page() — Adds submenu items to the Dashboard menu
   ➤ add_posts_page() — Adds submenu items to the Posts menu
   ➤ add_media_page() — Adds a submenu item to the Media menu
   ➤ add_links_page() — Adds a submenu item to the Links menu
   ➤ add_pages_page() — Adds a submenu item to the Pages menu
   ➤ add_comments_page() — Adds a submenu item to the Comments menu
   ➤ add_plugins_page() — Adds a submenu item to the Plugins menu
   ➤ add_theme_page() — Adds a submenu item to the Appearance menu
   ➤ add_users_page() — Adds a submenu item to the Users page (or Profi le based on role)
   ➤ add_management_page() — Adds a submenu item to the Tools menu
   ➤ add_options_page() — Adds a submenu item to the Settings menu
   */

    //menu is object
    function add_admin_menu( $menu , $type ){
        switch ( $type ) {
            case 'menu':
                return add_menu_page( $menu->page_title, $menu->menu_title, $menu->capability, $menu->menu_slug, $menu->func, $menu->icon_url, $menu->position );
                break;
            case 'submenu':
                return add_submenu_page( $menu->parent, $menu->page_title, $menu->menu_title, $menu->capability,$menu->menu_slug,$menu->func );
                break;
            /*case 'plugins':
                return add_plugins_page($menu->page_title, $menu->menu_title, $menu->capability, $menu->menu_slug, $menu->func);
            break;
            case 'theme':
                return add_theme_page($menu->page_title, $menu->menu_title, $menu->capability, $menu->menu_slug, $menu->func);
            break;*/
            case 'options':
                return add_options_page($menu->page_title, $menu->menu_title, $menu->capability, $menu->menu_slug, $menu->func);
                break;
        }
    }


    /*
     * @function    : add_actions_to_list
     * @description : add editor link for pages , posts and custom post types items in wp admin > posts lists
     */
    function add_actions_to_list($actions, $post) {

        if( !current_user_can( 'edit_by_site_editor' ) || !current_user_can( 'edit_post' , $post->ID ) || $post->post_status == "trash" )
            return $actions;

        $editor_url = get_sed_url( $post->ID , "post" );

        if($editor_url === false)
            return false;

        $actions['sed_site_editor_link'] = "<a class='sed_edit_siteeditor' href='{$editor_url}'>" . __( 'Edit With SiteEditor' , "site-editor") . "</a>";
        return $actions;
    }

    /*
     * @function    : add_button_to_editor
     * @description : add editor button link for pages , posts and custom post types items in wp post editor page
     */
    function add_button_to_editor($type) {
        global $post;

        if( "content" != $type || !current_user_can( 'edit_by_site_editor' ) )
            return false;

        $title = __("Edit With SiteEditor" , "site-editor" );

        $editor_url = get_sed_url( $post->ID , "post" );
        if( $editor_url === false )
            return false;

        $context = "<a href='{$editor_url}' class='button button-primary button-medium'  title='{$title}'>$title</a>";
        echo $context;
    }

    /*
     * @function    : sed_tag_row_actions
     * @description : add editor button link for terms in wp admin terms page
     */
    function sed_tag_row_actions($actions, $tag) {

        if( !current_user_can( 'edit_by_site_editor' ) )
            return $actions;

        $taxonomy = get_taxonomy( $tag->taxonomy );
        if( !$taxonomy->public )
            return $actions;

        $editor_url = get_sed_url( "term_" . $tag->term_id , "tax" , '' , array( 'term' => $tag ) );

        if($editor_url === false)
            return false;

        $actions['sed_site_editor_link'] = "<a class='sed_edit_siteeditor' href='{$editor_url}'>" . __( 'Edit With SiteEditor' , "site-editor") . "</a>";
        return $actions;
    }


    function module_set_screen_option($status, $option, $value) {
        if ( 'sed_modules_per_page' == $option ) return $value;
    }

}

new SiteEditorAdminRender;
