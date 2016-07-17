<?php
/**
*
*/

class SiteEditorSetup
{
    private $steps;
    private $modules_base;
    private $theme_name;
    private $themeURL;
    private $theme_folder_path;
    private $result_install = false ;

    function __construct()
    {
        if( !defined( 'SED_ADMIN_INC_PATH' ) )
            define('SED_ADMIN_INC_PATH', SED_ADMIN_DIR . DS . 'includes');

        if( !defined( 'SED_ADMIN_TMPL_PATH' ) )
            define('SED_ADMIN_TMPL_PATH', SED_ADMIN_DIR . DS . 'templates' );

        //get method when plugin active
        //register_activation_hook( __FILE__ , array( $this , "plugin_activated" ) );

        //register_activation_hook( __FILE__ , array( &$this , 'install' ) );

        //register_deactivation_hook( __FILE__, array(&$this, 'plugin_deactivate') );

        //register_uninstall_hook( __FILE__, array(&$this, 'uninstall') );

        global $wp_version, $sed_pb_modules ;

        if ( version_compare( $wp_version, '4.0', '<' ) ){
            deactivate_plugins( SED_PLUGIN_BASENAME );
            wp_die( __( 'SiteEditor requires WordPress version 4.0 or higher.' , "site-editor" ) );
        }

        $this->theme_name = "stars-ideas";
        $this->themeURL   = "http://mylocal/wp-tester-en/wp-content/plugins/site-editor/stars-ideas.zip";
        $this->theme_folder_path = get_theme_root();

        $this->steps = array_merge( array(
            "configuration"         => false,
            "less_framework"        => false,
            "install_theme"         => false,
            "install_modules_base"  => false,
            "install_sample_data"   => false
        ) , (array) sed_get_setting("status_install_steps") );

        $this->modules_base = $sed_pb_modules->get_modules_base();

        add_action( 'admin_enqueue_scripts', array(&$this,'sed_load_admin_scripts') );

    }

    public function deactivate_plugin(){

    }

    //activation
    function plugin_deactivate() {
        //deactivate_plugins( SED_PLUGIN_BASENAME );
        //wp_die( __( 'we can not installed SiteEditor plugin.' , "site-editor" ) );
    }

    function plugin_activated(){

    }

    function uninstall() {
        //delete_option( $this->option_settings );
        //remove any additional options and custom tables
    }

    public function install(){
        global $wp_version;
        
        if ( version_compare( $wp_version, '4.0', '<' ) )
            wp_die( 'This plugin requires WordPress version 4.0 or higher.' );

        $steps = $this->steps;

        $counter = 0;
        if ( ob_get_level() == 0 ) ob_start();

        foreach ( $steps as $step => $status ) {
            $counter++;
            if( !$this->check_step( $step ) ){

                if( is_callable( array( $this , $step ) ) ){
                    if( $this->$step() ){
                        $this->save_step( $step );
                    }else
                        sed_print_message( sprintf( __("step %d : %s is not installed" ,"site-editor") , $counter , $step ) , "error" );

                }else{
                    unset( $this->steps[$step] );
                    sed_print_message( sprintf( __("step %d : %s is not defined" ,"site-editor") , $counter , $step ) , "error" );
                }

            }else{

                sed_print_message( sprintf( __("Step %d : %s is compelated already!" ,"site-editor") , $counter , $step ) , "success" );
                $this->save_step( $step );

            }
        }


        if( $this->is_installed() )
            sed_print_message( __("site editor successfully installed" ,"site-editor") , "success" );
        else
            sed_print_message( __("Site editor is not installed" ,"site-editor") , "error" );

        ob_end_flush();
    }

    private function check_step( $step ){
        $method  = 'check_'.$step;

        if( is_callable( array( $this , $method ) ) )
            return $this->$method();
        else
            return $this->steps[$step];
    }

    private function save_step( $step ){
        $status_install_steps = sed_get_setting( "status_install_steps");
        $status_install_steps[$step]   = true;
        sed_update_setting( "status_install_steps", $status_install_steps );
    }

    static function is_installed(){
        $status_install_steps = array_merge( array(
            "configuration"         => false,
            "less_framework"        => false,
            "install_theme"         => false,
            "install_modules_base"  => false
        ), (array) sed_get_setting("status_install_steps") );
        return !in_array( false , $status_install_steps );

    }

    public function sed_load_admin_scripts(){
        wp_enqueue_style( "sed-admin-style" , SED_EDITOR_FOLDER_URL . 'admin/templates/default/css/style.css' , array() , '1.0.0' , 'all');

    }

    public function show_page_install(){
        include SED_ADMIN_TMPL_PATH . DS . "default" . DS . "install.php";
    }

    /*
    * step 1
    * save plugin version
    * save default settings
    */
    private function configuration(){
        global $wpdb;

        //define the custom table name
        $table_name = $wpdb->prefix .'sed_template';
        //build the query to create our new table
        $sql = "CREATE TABLE IF NOT EXISTS " .$table_name ." (
              `template_id` int(11) NOT NULL AUTO_INCREMENT,
              `title` varchar(200) NOT NULL,
              `name` varchar(200) NOT NULL,
              `tags` varchar(200) NOT NULL,
              `group` varchar(40) NOT NULL DEFAULT 'general',
              `description` text NOT NULL,
              `screenshot` varchar(255) NOT NULL,
              `author` varchar(100) NOT NULL,
              `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
              `date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
              PRIMARY KEY (`template_id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

        $wpdb->query( $sql );

        //save the table structure version number
        //add_option( $this->app_name.'_db_version', $this->app_db_version );
        //add_option( $this->app_name.'_ap_version', $this->app_version );

        $default_settings = array(
            'site_editor_page_title' => __('SiteEditor','site-editor')
        );

        $settings = wp_parse_args( (array) get_option('site-editor-settings'), $default_settings );
        update_option( 'site-editor-settings', $settings );
        
        return true;
    }
    
    /* 
    * step 2 
    * install stars-ideas theme
    * 1- get download zip theme 
    * 2- unzip theme in themes folder
    * 3- active stars-ideas theme
    * @return true complate step
    */
    private function install_theme(){
        global $sed_error;
        $result = false;
        # download theme
        $theme = wp_get_theme( 'stars-ideas' );
        $current_theme = wp_get_theme();
    
        if( $current_theme == 'stars-ideas' ){
            sed_print_message( __("step 2 : Install and active theme completly" ,"site-editor") , "success" );
            return true;
        }elseif ( $theme->exists() || $theme->is_allowed() ){
            switch_theme( $theme->get_stylesheet() );
            sed_print_message( __("step 2 : Install and active theme completly" ,"site-editor") , "success" );
            return true;
        }else{
            if( !class_exists( 'SEDFile' ) )
                require_once SED_INC_DIR . DS . 'app_file.class.php';

            $theme_package  = SEDFile::get_download_package( $this->themeURL );
            if( !$sed_error->is_error( $theme_package ) ){
                $delete_package = ( $this->themeURL != $theme_package );
                sed_print_message( __("Theme download is complete." ,"site-editor") , "success" );
                


                # unzip theme in theme folder
                $working_dir = SEDFile::unpack_package( $theme_package , $delete_package , $this->theme_folder_path . DS );
                    if( !$sed_error->is_error( $working_dir ) ){
                    sed_print_message( __("Unzip process is complete." ,"site-editor") , "success" );
                    $theme = wp_get_theme( 'stars-ideas' );
                    if ( ! $theme->exists() || ! $theme->is_allowed() )
                        sed_print_message( __("theme is not installed." ,"site-editor") , "error" );
                    else{
                        switch_theme( $theme->get_stylesheet() );
                        sed_print_message( __("step 2 : Install and active theme completly" ,"site-editor") , "info" );
                        return true;
                    }
                }else
                    sed_print_message( $sed_error->get_message( $working_dir ) , "error");

            }else
                sed_print_message( $sed_error->get_message( $theme_package ) , "error");

        }
        return $result;
    }

    private function check_install_theme(){
        if( wp_get_theme() == $this->theme_name ){
            return true;
        }
        else
            return false;
    }
    private function less_framework(){

        if( !class_exists( 'SEDAppLess' ) )
            require_once SED_INC_DIR . '/sed_app_less.class.php';

        $result = SEDAppLess::compile_base_framework();

        if( $result === true ){
           sed_print_message( __("Less Framework is compiled." ,"site-editor") , "success" );
           return true;
        }
        else{
           sed_print_message( sprintf( __("Less Framework Error : %s" ,"site-editor") , $result ) , "error" );
           return false;
        }
    }
    /*
    * step 3
    * install module base
    * 1- get download zip theme
    * 2- unzip theme in themes folder
    * 3- active stars-ideas theme
    * @return true complate step
    */
   private function install_modules_base(){
        global $sed_pb_modules;

        return true;

        $result_setup_module = array();

        $result_install = array();

        foreach ( $this->modules_base as $name => $path ) {
            if( empty( $path ) )
                $result_install[ $name ] = $sed_pb_modules->install( $name );
            else
                $result_install[ $name ] = $sed_pb_modules->install( $name , $path );
            if( !$result_install[ $name ] )
                sed_print_message( sprintf( __("Module %s is not installed","site-editor" ) , $name ) , "title" );
        }
        if( !in_array( false , $result_install ) )
            return true;
        else
            return false;
   }

    /*
    * step 5
    * install sample demo data
    */
   private function install_sample_data(){
        //do_action( 'sed_install_sample_data_step' );
        //return SiteEditorImporter::$install_sample_data_status;
        return true;
   }

}