<?php

if( !class_exists( 'SEDFile' ) )
    require_once dirname( __FILE__ ) . DS . 'app_file.class.php';

class SEDPageBuilderModules extends SiteEditorModules{

    public $sed_skin;
    public static $modules_base_rel;

	function __construct( $args = array() ) {
        $args = wp_parse_args( $args, array(
            'app_name' => 'pagebuilder'
        ) );

		parent::__construct( $args );

        if( !class_exists( 'SEDPageBuilderModuleSkins' ) )
            require_once dirname( __FILE__ ) . DS . 'app_pb_module_skins.class.php';

        $this->sed_skin = new SEDPageBuilderModuleSkins;

        self::$modules_base_rel = 'plugins/' . SED_PLUGIN_NAME . '/site-editor-app/applications/pagebuilder/modules/';

        add_action("admin_init" , array( $this , "check_exist_modules" ) );

	}

    function get_modules_base(){
        return parent::get_modules();
    }

    function is_module_base( $module ){
        $module_name = $this->get_module_name( $module );

        $module_base = ( array ) $this->get_modules_base();

        return isset( $module_base["{$module_name}/{$module_name}.php"] );
    }

    function is_install( $module ){
        $module_name = $this->get_module_name( $module );

        $module_info = (array) sed_get_setting("module_info");
        return isset( $module_info[$module_name] );
    }

    function is_module( $module ){
        $module = $this->module_basename( trim( $module ) );
        $modules = $this->get_modules();

        if( in_array( $module , array_keys( $modules ) ) ){
            return true;
        }else{
            return false;
        }

    }

    function is_module_active( $module ) {
        $site_editor_settings = get_option('site-editor-settings');
        $activate_modules = isset( $site_editor_settings['live_module'] ) ? $site_editor_settings['live_module'] : array() ;
        $activate_modules = array_values( $activate_modules );

        return in_array( $module, $activate_modules );
    }

    function check_exist_modules(){

        $modules = $this->get_modules();
        $modules = array_keys( $modules );

        $modules = array_map( array( $this , 'get_module_name' ) , $modules );

        $modules_info = (array) sed_get_setting("module_info");

        $live_module = sed_get_setting( "live_module" );

        if( !empty( $live_module ) ){
            $active_update_needle = false;

            foreach( $live_module AS $module_name => $module_file ){

                if( !in_array( $module_name , $modules ) && isset( $live_module[ $module_name ] ) ){

                    unset( $live_module[$module_name] );
                    $active_update_needle = true;

                }

            }

            if( $active_update_needle ){
                sed_update_setting( "live_module" , $live_module );
            }
        }

        if( !empty( $modules_info ) ){
            $update_needle = false;

            foreach( $modules_info AS $module_name => $info ){

                if( !in_array( $module_name , $modules ) ){

                    unset( $modules_info[$module_name] );
                    $update_needle = true;
                }

            }

            if( $update_needle ){
                sed_update_setting( "module_info" , $modules_info );
            }

        }

    }

    function check_reapet_module_name( $module ){
        $module_name = $this->get_module_name( $module );

        $modules_info = (array) sed_get_setting("module_info");

        if( !empty( $modules_info ) ){
            $modules_names = array_keys( $modules_info );

            if( in_array( $module_name , $modules_names ) ){
                return true;
            }

        }

        return false;
    }

    function get_modules(){
        $sed_modules = ( array ) $this->get_modules_base();

        $modules = array();

        foreach( (array) $sed_modules AS $module_file => $module_data ){
            $module_file = self::$modules_base_rel . $module_file;
            $modules[ $module_file ] = $module_data;
        }

        return apply_filters( 'sed_modules', $modules );
    }

    function remove_module_info( $module ){

        $module_info = (array) sed_get_setting("module_info");
        $module_name = $this->get_module_name( $module );

        if( isset( $module_info[$module_name] ) ){
            unset( $module_info[$module_name] );
            sed_update_setting( "module_info" , $module_info );
        }

    }

    function validate_module( $module ) {

        $module_main_file = WP_CONTENT_DIR . "/" . $module;

        $module_path = dirname( $module_main_file );

        if( !is_dir( $module_path  ) )
            return new WP_Error('module_not_found', sprintf( __("module %s not found","site-editor" ) , $module ) );

        if ( validate_file($module) )
            return new WP_Error('module_invalid', __('Invalid module path.' , 'site-editor'));

        if ( ! file_exists( $module_main_file ) )
            return new WP_Error('module_file_not_found', __('Module Main file does not exist.' , 'site-editor'));

        $installed_modules = $this->get_modules();

        if ( ! isset($installed_modules[$module]) )
            return new WP_Error('no_module_header', __('The module does not have a valid header.' , 'site-editor'));

        $module_skins = glob( $module_path . DS . "skins" . DS . "default" . "*" );

        if( empty( $module_skins ) )
            return new WP_Error( 'skin_not_found' , sprintf( __("We can not find default skin in module %s","site-editor" ) , $module ) );

        return 0;
    }

    function activate_module( $module, $redirect = '' ) {
        $module = $this->module_basename( trim( $module ) );

        if( !$this->is_install( $module ) ){
            $module_name = $this->get_module_name( $module );
            return new WP_Error( 'module_not_installed' , sprintf( __("The %s module not installed.","site-editor" ) , $module_name ) );
        }

        $site_editor_settings = get_option('site-editor-settings');
        $activate_modules = isset( $site_editor_settings['live_module'] ) ? $site_editor_settings['live_module'] : array() ;

        $current = array_values( $activate_modules );

        $valid = $this->validate_module($module);
        if ( is_wp_error($valid) )
            return $valid;

        if ( !in_array( $module, $current ) ) {
            if ( !empty($redirect) )
                wp_redirect(add_query_arg('_error_nonce', wp_create_nonce('module-activation-error_' . $module), $redirect)); // we'll override this later if the plugin can be included without fatal error

            $module_name = $this->get_module_name( $module );

            $activate_modules[$module_name] = $module;
            sed_update_setting( "live_module" , $activate_modules );


            do_action( 'sed_pb_activated_module', $module_name );

            return true;
        }

        return null;
    }

    function deactivate_module( $module, $redirect = '' ) {
        $module = $this->module_basename( trim( $module ) );

        if( !$this->is_install( $module ) ){
            $module_name = $this->get_module_name( $module );
            return new WP_Error( 'module_not_installed' , sprintf( __("The %s module not installed.","site-editor" ) , $module_name ) );
        }

        $site_editor_settings = get_option('site-editor-settings');
        $activate_modules = isset( $site_editor_settings['live_module'] ) ? $site_editor_settings['live_module'] : array() ;

        $current = array_values( $activate_modules );

        if ( in_array( $module, $current ) ) {
            if ( !empty($redirect) )
                wp_redirect(add_query_arg('_error_nonce', wp_create_nonce('module-deactivation-error_' . $module), $redirect)); // we'll override this later if the plugin can be included without fatal error

            //$live_module = sed_get_setting( "live_module" );
            $module_name = $this->get_module_name( $module );

            unset( $activate_modules[$module_name] );

            sed_update_setting( "live_module" , $activate_modules );


            do_action( 'sed_pb_deactivated_module', $module_name );

            return true;
        }

        return null;
    }

    function install( $module , $active = true , $path = null ){

        $module = $this->module_basename( trim( $module ) );

        $module_info = array();

        $module_name = $this->get_module_name( $module );

        # check module installed and activad
        if( $this->is_install( $module ) ){
            if( !$active ){
                $this->print_message( sprintf( __("%s Module already installed","site-editor" ) , $module_name ) , 'error');
                return false;
            }else{
                if( $this->activate_module( $module ) ){
                    $this->print_message( sprintf( __("Module %s success to installed and activited.","site-editor" ) , $module_name ) , 'success');
                    return true;
                }else{
                    $this->print_message( sprintf( __("%s Module already installed but not activited","site-editor" ) , $module_name ) , 'error');
                    return false;
                }
            }
        }

        if( $this->check_reapet_module_name( $module ) ){
            $this->print_message( sprintf( __("Exist another module with the same name","site-editor" ) , $module_name ) , "error" );
            $this->print_message( sprintf( __("Module %s not success to installed.","site-editor" ) , $module_name ) , 'error' );
            return false;
        }

        $valid = $this->validate_module( $module );

        if( !is_wp_error($valid) ){

            $this->print_message( sprintf( __("Start install module %s","site-editor" ) , $module_name ) , "title" );

            if( !$this->install_skins( $module ) ){
                $this->print_message( sprintf( __("Module %s not installed","site-editor" ) , $module_name ) , "error" );
                return false;
            }

            $module_info['less'] = $this->less_module_compile( $module );

            $this->save_module_info( $module , $module_info );

            if( $active ){

                if( $this->activate_module( $module ) ){
                    $this->print_message( sprintf( __("Module %s success to installed and activited.","site-editor" ) , $module_name ) , 'success');
                    return true;
                }else{
                    $this->print_message( sprintf( __("Module %s success to installed but not activited","site-editor" ) , $module_name ) , 'error');
                    return false;
                }

            }else{

                //var_dump( $_REQUEST );

                $active_link = '<a href="' . wp_nonce_url('admin.php?page=site_editor_module&amp;action=activate&amp;module=' . $module . '&amp;show_modules=' . $_REQUEST['show_modules'] . '&amp;paged=' . $_REQUEST['paged'] . '&amp;s=' . $_REQUEST['s'], 'sed-activate-module_' . $module) . '" title="' . sprintf( __("Active %s" , "site-editor" ) , $module_name ) . '" class="button">' . sprintf( __("Active %s" , "site-editor" ) , $module_name ) . '</a>';

                $this->print_message( sprintf( __("Module %s success to installed.","site-editor" ) , $module_name ) , 'success' );

                $this->print_message( $active_link , "button" );

                return true;
            }
        }else{
            $this->print_message( $valid->get_error_message() , "error" );
            $this->print_message( sprintf( __("Module %s not success to installed.","site-editor" ) , $module_name ) , 'error' );
            return false;
        }
    }

    private function save_module_info( $module , $curr_module_info = array() ){

        $module_name = $this->get_module_name( $module );

        $module_info = (array) sed_get_setting("module_info");

        $module_info[$module_name] = array_merge(  (array) $module_info[$module_name] , $curr_module_info );

        sed_update_setting( "module_info" , $module_info );

    }

    private function install_skins( $module ){

        $module_main_file = WP_CONTENT_DIR . "/" . $module;

        $module_path = dirname( $module_main_file );

        $skins_path     = array_filter( glob( $module_path . DS . 'skins' . DS . '*' ) , 'is_dir' );
        $result         = array();

        $module_name = $this->get_module_name( $module );

        if( empty( $skins_path ) ){
            $this->print_message( sprintf( __("skin not found for module %s.","site-editor" ) , $module_name )  , "error" );
            return false;
        }

        $this->print_message( sprintf( __("begin install skins for module %s.","site-editor" ) , $module_name )  , "info" );

        $result_skins_install = true;

        foreach ( $skins_path as $skin_path ){
            $result = $this->sed_skin->install_skin( $module , basename( $skin_path ) );

            if( !$result || is_wp_error( $result ) ){
                $this->print_message( sprintf( __( "An error occurred in the installation skin %s" ,"site-editor" ) , $skin_path )  , "error" );
                $result_skins_install = false;
            }
        }

        return $result_skins_install;
    }

    private function less_module_compile( $module , $check_compile = true ){

        $module = $this->module_basename( trim( $module ) );

        $less_info      = array();

        $module_main_file = WP_CONTENT_DIR . "/" . $module;

        $module_path = dirname( $module_main_file );

        $less_files     = SEDFile::list_files( $module_path , 'skins' , '' , array( 'less' ) );

        foreach ( $less_files as $file ) {

            $data_file = SEDFile::get_file_data( $file , "less_info" );

            if( $data_file !== false && $data_file['handle'] ){

                $module_name = $this->get_module_name( $module );

                $this->print_message( sprintf( __("Start less compilation for %s","site-editor" ) , $module_name ) );


                if( !class_exists( 'SEDAppLess' ) )
                    require_once dirname( __FILE__ ) . DS . 'sed_app_less.class.php';

                $css_path = SEDAppLess::relative_path( $file , $module , "abs" );

                $uri_css_file = str_replace( DS , '/' , $css_path);

                $abs_css_path = SEDAppLess::upload_path( $file , $module );

                $handle     = $data_file['handle'];
                $deps       = isset( $data_file['deps'] )   ? $data_file['deps']    : array();
                $ver        = isset( $data_file['ver'] )    ? $data_file['ver']     : '1.0.0';
                $media      = isset( $data_file['media'] )  ? $data_file['media']   : 'all';

                $import     = isset( $data_file['import'] )  ? $data_file['import']   : array();

                $less = array(
                    "handle"        => $handle,
                    "src"           => $uri_css_file, //SED_UPLOAD_URL .
                    "deps"          => $deps,
                    "ver"           => $ver,
                    "media"         => $media ,
                    "import"        => $import ,
                    "src_rel"       => str_replace( DS , '/' , SEDAppLess::relative_path( $file , $module) )
                );

                $filename = basename($file);
                $css_filename = substr( $filename , 0 , -4 ) . 'css';
                $css_file = dirname( $file ) . DS . $css_filename;

                if( file_exists( $css_file ) ){
                    global $wp_filesystem;
                    if( empty( $wp_filesystem ) ) {
                        require_once( ABSPATH .'/wp-admin/includes/file.php' );
                        WP_Filesystem();
                    }
                    // create directory when not exists
                    if( !is_dir( dirname( $abs_css_path ) ) ){
                        wp_mkdir_p( dirname( $abs_css_path ) );
                        @chmod( dirname( $abs_css_path ) ,0777);
                    }

                    if( $wp_filesystem ) {
                        if( !$wp_filesystem->move( $css_file , $abs_css_path , true ) )
                            $this->print_message( __("Error : Css File Move Failed","site-editor" ) , 'error' );
                        else
                            $this->print_message( sprintf( __("Css File Move successful : %s.","site-editor" ) , $handle ) );
                    }else{
                        $this->print_message( __("Error : Css File Move Failed","site-editor" ) , 'error' );
                    }

                }else{
                    $result_compile = SEDAppLess::compile_file( $file , $abs_css_path );

                    if( $result_compile === true ){
                        $this->print_message( sprintf( __("less %s is compiled.","site-editor" ) , $handle ) );
                        $less_info[$less['handle']] = $less;

                    }
                    else
                        $this->print_message( sprintf( __("Error LESS : %s","site-editor" ) , $result_compile ) );
                }
            }

        }
        return $less_info;
    }

    function get_module_name( $module ){
        $module = $this->module_basename( $module );
        $pos = strrpos( $module , "/" );
        if( $pos > 0 ){
            $module = substr( $module , $pos + 1 );
        }

        $strleng = strlen( $module ) - 4;

        $module_name = substr( $module , 0 ,  $strleng );

        return $module_name;
    }

}