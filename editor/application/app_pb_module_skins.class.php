<?php
/**
 *
 */
if( !class_exists( 'SEDFile' ) )
    require_once dirname( __FILE__ ) . DS . 'app_file.class.php';

class SEDPageBuilderModuleSkins
{
    function is_skin( $module , $skin ){
        return is_dir( $this->get_skin( $module , $skin ) );
    }

    function get_skin( $module , $skin ){
        $pos = strrpos( $module , "/" );
        if( $pos > 0 ){
            $module_base = substr( $module , 0 , $pos  );
        }else{
            $module_base = $module;
        }
        return WP_CONTENT_DIR . "/" . $module_base . '/skins/' . $skin;
    }

    function get_scripts_info( $module , $skin ){
        $skin_path  = $this->get_skin( $module , $skin  ) . DS . 'js';
        $js_files   = SEDFile::list_files( $skin_path , '' , '' , array( 'js' ) );
        $js_info    = array();

        $module_rel_dir = dirname( $module );

        if( !empty( $js_files ) )
            foreach ( $js_files as $file ) {

                $data_file = SEDFile::get_file_data( $file , "js_info" );

                if( $data_file !== false && $data_file['handle'] ){

                    //$this->echo_message( sprintf( __("begin Compile %s less file for skin %s from module %s","site-editor" ) , $data_file['handle'] , $skin  , $module ) );

                    $src        = "$module_rel_dir/skins/$skin/js/" . basename( $file );
                    $handle     = $data_file['handle'];
                    $deps       = isset( $data_file['deps'] )   ? $data_file['deps']    : array();
                    $ver        = isset( $data_file['ver'] )    ? $data_file['ver']     : '1.0.0';
                    $in_footer  = isset( $data_file['in_footer'] )  ? $data_file['in_footer']   : false;
                    
                    $js_info[$handle] = array(
                        "handle"      => $handle,
                        "src"         => $src,
                        "deps"        => $deps,
                        "ver"         => $ver,
                        "in_footer"   => $in_footer
                    );
                }
            }
        return $js_info;
    }
    function get_styles_info( $module , $skin ){
        $skin_path  = $this->get_skin( $module , $skin  ) . DS . 'css';
        $css_files   = SEDFile::list_files( $skin_path , '' , '' , array( 'css' ) );
        $css_info    = array();

        $module_rel_dir = dirname( $module );

        if( !empty( $css_files ) )
            foreach ( $css_files as $file ) {

                $data_file = SEDFile::get_file_data( $file , "css_info" );

                if( $data_file !== false && $data_file['handle'] ){

                    //$this->echo_message( sprintf( __("begin Compile %s less file for skin %s from module %s","site-editor" ) , $data_file['handle'] , $skin  , $module ) );

                    $src        = "$module_rel_dir/skins/$skin/css/" . basename( $file );
                    $handle     = $data_file['handle'];
                    $deps       = isset( $data_file['deps'] )   ? $data_file['deps']    : array();
                    $ver        = isset( $data_file['ver'] )    ? $data_file['ver']     : '1.0.0';
                    $media      = isset( $data_file['media'] )  ? $data_file['media']   : 'all';
                    
                    $css_info[$handle] = array(
                        "handle"      => $handle,
                        "src"         => $src,
                        "deps"        => $deps,
                        "ver"         => $ver,
                        "media"       => $media
                    );
                }
            }
        return $css_info;
    }
    function get_tpls_info( $module , $skin ){
        $skin_path  = $this->get_skin( $module , $skin  ) . DS . 'tpl';
        $tpls       = SEDFile::list_files( $skin_path , '' , '' , array( 'tpl' ) );
        $tpls_url   = array();

        $module_rel_dir = dirname( $module );

        if( !empty( $tpls ) )
            foreach ( $tpls as $tpl )
                $tpls_url[] = "$module_rel_dir/skins/$skin/tpl/" . basename( $tpl );

        return $tpls_url;
    }

    function get_module_skins( $module ){
        global $sed_pb_modules;

        $module_name = $sed_pb_modules->get_module_name( $module );
        $module_main_file = WP_CONTENT_DIR . "/" . $module;
        $skins_path = dirname( $module_main_file ) . "/skins/" ;

        $skins = array();
        $skins_folders = glob($skins_path.'*');

        if(!empty( $skins_folders )){
            foreach ($skins_folders as $folder) {

                if( !is_dir( $folder ) )
                    continue;

                $folder_name = basename($folder);
                $skins[] = $folder_name;
            }
        }

        return $skins;
    }

    function reinstall_skin( $module , $skin ){
        $result = $this->remove_skin_info( $module , $skin );
        if( !$result ){
            return new WP_Error('skin_remove_error', sprintf( __("Skin %s not found or this skin not allready installed","site-editor" ) , $skin ) );
        }

        $result = $this->install_skin( $module , $skin );

        if( !$result || is_wp_error( $result ) ){
            return $result;
        }

        return true;
    }

    function install_skin( $module , $skin ){

        if(  !$this->is_skin( $module , $skin ) )
            return false;

        $skin_info = array(
            'less'      => $this->less_skin_compile( $module , $skin ) ,
            "scripts"   => $this->get_scripts_info( $module , $skin ),
            "styles"    => $this->get_styles_info( $module , $skin ),
            "tpls"      => $this->get_tpls_info( $module , $skin ),
        );

        global $sed_pb_modules;
        $module_name = $sed_pb_modules->get_module_name( $module );

        $result = $this->save_skin_info( $module , $skin , $skin_info );
        if( $result && !is_wp_error( $result ) ){
            $this->echo_message( sprintf( __("skin %s successfully installed for module %s.","site-editor" ) , $skin  , $module_name ) , 'success' );
            return true;
        }else{
            return $result;
        }
    }

    private function less_skin_compile( $module , $skin ){

        if(  !$this->is_skin( $module , $skin ) )
            $this->echo_message( sprintf( __("skin %s not found for module %s.","site-editor" ) , $skin  , $module )  , "error" );

        $skin_path      = $this->get_skin( $module , $skin );

        $less_files     = SEDFile::list_files( $skin_path , '' , '' , array( 'less' ) );
        $less_info      = array();
            
        foreach ( $less_files as $file ) {

            $data_file = SEDFile::get_file_data( $file , "less_info" );

            if( $data_file !== false && $data_file['handle'] ){

                //$this->echo_message( sprintf( __("begin Compile %s less file for skin %s from module %s","site-editor" ) , $data_file['handle'] , $skin  , $module ) );

                if( !class_exists( 'SEDAppLess' ) )
                    require_once SED_APP_PATH . DS . 'sed_app_less.class.php';

                $css_path = SEDAppLess::relative_path( $file , $module , "abs" );

                $uri_css_file = str_replace( DS , '/' , $css_path);

                $abs_css_path = SEDAppLess::upload_path( $file , $module );

                $handle     = $data_file['handle'];
                $deps       = isset( $data_file['deps'] )   ? $data_file['deps']    : array();
                $ver        = isset( $data_file['ver'] )    ? $data_file['ver']     : '1.0.0';
                $media      = isset( $data_file['media'] )  ? $data_file['media']   : 'all';

                $import     = isset( $data_file['import'] )  ? $data_file['import']   : array();

                $less = array(
                    "handle"      => $handle,
                    "src"         => $uri_css_file, //SED_UPLOAD_URL .
                    "deps"        => $deps,
                    "ver"         => $ver,
                    "media"       => $media ,
                    "import"      => $import ,
                    "src_rel"     => str_replace( DS , '/' , SEDAppLess::relative_path( $file , $module) )
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
                            $this->echo_message( __("Error : Css File Move Failed","site-editor" ) , 'error' );
                        else
                            $this->echo_message( sprintf( __("Css File Move successful : %s.","site-editor" ) , $handle ) );
                    }else{
                        $this->echo_message( __("Error : Css File Move Failed","site-editor" ) , 'error' );
                    }

                }else{
                    $result_compile = SEDAppLess::compile_file( $file , $abs_css_path );

                    /*if( $result_compile === true ){
                        $this->echo_message( sprintf( __("less %s is compiled.","site-editor" ) , $handle ) );
                    }*/
                    if( $result_compile !== true )
                        $this->echo_message( sprintf( __("Error LESS : %s","site-editor" ) , $result_compile ) , 'error');

                    $less_info[$less['handle']] = $less;
                }
            }
        }

        return $less_info;
    }

    private function remove_skin_info( $module , $skin ){

        if( !$this->is_skin( $module , $skin ) )
            return false;

        global $sed_pb_modules;
        $module_name = $sed_pb_modules->get_module_name( $module );

        $module_info = (array) sed_get_setting("module_info");

        if( isset( $module_info[$module_name]['skins'] ) && isset( $module_info[$module_name]['skins'][$skin] ) ){
            unset( $module_info[$module_name]['skins'][$skin] );
            return sed_update_setting( "module_info" , $module_info );
        }else{
            return false;
        }

    }

    private function save_skin_info( $module , $skin , $skin_info ){

        if( !$this->is_skin( $module , $skin ) )
            return false;

        global $sed_pb_modules;
        $module_name = $sed_pb_modules->get_module_name( $module );

        $module_info = (array) sed_get_setting("module_info");
        $module_info[$module_name]['skins'][$skin] = $skin_info;
        return sed_update_setting( "module_info" , $module_info );

    }

    public function get_skins_installed( $module , $type = 'names' ){
        global $sed_pb_modules;
        $module_name = $sed_pb_modules->get_module_name( $module );

        $module_info = (array) sed_get_setting("module_info");

        if( isset( $module_info[$module_name]['skins'] ) ){
            if( $type == 'names' ){
                return array_keys( $module_info[$module_name]['skins'] );
            }else if( $type == 'info' ){
                return $module_info[$module_name]['skins'];
            }else{
                return false;
            }

        }else{
            return false;
        }
    }

    private function echo_message( $msg , $type = "success"){
        printf("<li><div class='install-process-module-%s'>%s</div></li>\n", $type , $msg ); 
        ob_flush();
        flush();

    }
}