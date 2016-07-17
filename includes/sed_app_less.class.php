<?php
class SEDAppLess
{

	public static $constants = '';

	function __construct() {

		global $less_vars;

        $wp_upload = wp_upload_dir();

		// Siteeditor Variables
		$constants = array(
			'sed-root'				=> sprintf( "\"%s\"", SED_PLUGIN_URL . "/" ),
			'sed-framework-root'	=> sprintf( "\"%s\"", SED_EDITOR_FOLDER_URL. "libraries/less-framework/" ),
			'sed-modules-root'		=> sprintf( "\"%s\"", SED_PB_MODULES_URL ),
            'sed-images-root'		=> sprintf( "\"%s\"", SED_PB_IMAGES_URL ),
			'sed-plugins-root'		=> sprintf( "\"%s\"", WP_PLUGIN_URL . "/" ),
			'sed-themes-root'		=> sprintf( "\"%s\"", get_theme_root_uri() . "/" ),
            'sed-uploads-root'		=> sprintf( "\"%s\"", $wp_upload['baseurl'] . "/" ),
            'main-color'            => "#19cbe5",
            'perfect-main-color'    => "#FAC300",
            'base-color-a'          => "#FFFFFF",
            'base-color-b'          => "#D5D5D5",
            'base-color-c'          => "#000000"
		);

		if(is_array($less_vars))
			$constants = array_merge($less_vars, $constants);

        add_filter('sed_less_vars' , array( $this , "add_color_font_vars" ) );

		self::$constants = apply_filters('sed_less_vars', $constants);  //var_dump( self::$constants );
	}

    function add_color_font_vars( $constants ){
        global $sed_general_data;

        if( is_array( $sed_general_data ) && !empty( $sed_general_data ) ){
            if( $sed_general_data['sed-color-palette'] == "custom" ){
                $constants['main-color']                    = $sed_general_data['sed-main-color'];
                $constants['perfect-main-color']            = $sed_general_data['sed-perfect-main-color'];
                $constants['base-color-a']                  = $sed_general_data['sed-base-color1'];
                $constants['base-color-b']                  = $sed_general_data['sed-base-color2'];
                $constants['base-color-c']                  = $sed_general_data['sed-base-color3'];
            }else{
                $colors_palette = explode( "," , trim($sed_general_data['sed-color-palette']) );
                $constants['main-color']            = $colors_palette[0];
                $constants['perfect-main-color']    = $colors_palette[1];
                $constants['base-color-a']          = $colors_palette[2];
                $constants['base-color-b']          = $colors_palette[3];
                $constants['base-color-c']          = $colors_palette[4];
            }

            $constants['font-family-base']              = $sed_general_data['font-family-base'];
            $constants['font-size-base']                = $sed_general_data['font-size-base'];
            $constants['line-height-base']              = $sed_general_data['line-height-base'];
            $constants['headings-font-family']          = $sed_general_data['headings-font-family'];
            $constants['headings-font-weight']          = $sed_general_data['headings-font-weight'];
            $constants['headings-line-height']          = $sed_general_data['headings-line-height'];

        }

        return $constants;
    }

    static function compile_file( $file , $compiled_path , $less_base_module = true ){

        if( !is_file( $file ) )
            return false;

        $base_path      = SED_FRAMEWORK_ASSETS_DIR . DS .  'less' . DS . 'siteeditor' . DS . 'module-base.less';
        
        // SET Options
        //============
        $options_compiler = array(
            //'sourceMap'           => true,
            //'sourceMapWriteTo'    => SED_BASE_DIR . '\writable_folder\filename.map',
            //'sourceMapURL'        => SED_BASE_DIR . '\writable_folder\filename.map',
            'compress'              => true
        );

        // SET VARIABLES
        $var_less = self::$constants;

        // SET DIRECTORY FOR IMPORT LESS FILES
        $import_dir = array(
            SED_PB_MODULES_PATH                                                     => '' , // sed-modules-root
            SED_FRAMEWORK_ASSETS_DIR . DS .  'less' . DS . 'siteeditor'             => '' , // sed-framework-root
            SED_EDITOR_DIR                                                          => '' , // sed-root
            SED_PLUGIN_DIR                                                          => '' , // sed-plugin-root
            SED_PLUGIN_DIR                                                          => '' , // sed-theme-root
        );

        require_once SED_INC_DIR . DS . 'less.php' . DS . 'less.php';

        $parser = new Less_Parser( $options_compiler );

        $parser->ModifyVars( $var_less );

        $parser->SetImportDirs( $import_dir );
                              //var_dump( $file , $compiled_path );
        try {

            if( $less_base_module && is_file( $base_path ) )
                $parser->parseFile( $base_path );

            $parser->parseFile( $file );

            global $wp_filesystem;
            if( empty( $wp_filesystem ) ) {
                require_once( ABSPATH .'/wp-admin/includes/file.php' );
                WP_Filesystem();
            }

            // create directory when not exists
            if( !is_dir( dirname( $compiled_path ) ) ){
                wp_mkdir_p( dirname( $compiled_path ) );
                @chmod( dirname( $compiled_path ) ,0777);
            }

            if( $wp_filesystem ) {
                $wp_filesystem->put_contents(
                    $compiled_path,
                    $parser->getCss(),
                    FS_CHMOD_FILE // predefined mode settings for WP files
                );
            }

            return true;
        }catch (exception $e) {
            return $e->getMessage();
        }
    }

    static function compile_base_framework( ){

        $less_framework = SED_FRAMEWORK_ASSETS_DIR . DS .  'less' . DS . 'siteeditor' . DS . 'siteeditor.less';
        $compiled_path  = SED_UPLOAD_PATH . '/style/siteeditor.css';

        return self::compile_file( $less_framework , $compiled_path , false );
    }

	// compile only if changed input has changed or output doesn't exist
	public static function checkedCompile($in, $out) {

        $is_compailed = true;

        if( is_array( $in ) ){

            if( empty( $in ) )
                return true;

            foreach( $in  As $file ){
        		if (file_exists($file) && file_exists($out) && filemtime($file) > filemtime($out)) {
        			$is_compailed = false;
                    break;
        		}
            }
        }else{
      		if (file_exists($in) && filemtime($in) > filemtime($out)) {
      			$is_compailed = false;
      		}
        }

		return $is_compailed;
	}

    static function is_main_less( $file ){

        if( !file_exists( $file ) )
            return false;
        $data_file = SEDFile::get_file_data( $file , 'less_info' );
        return isset( $data_file['handle'] );

    }

    //$type ==== rel || abs
    static function relative_path( $file , $module , $type = "rel" ){
        $base_path   =  str_replace('/', DS , WP_CONTENT_DIR );
        $path_file   =  str_replace('/', DS , $file );

        if( $type == "rel" ){
            $module_dir  =  str_replace('/', DS , $module );
            $module_dir  = DS . dirname( dirname( $module_dir ) );
        }

        $css_path = str_replace( $base_path , "" , $path_file );

        if( $type == "rel" ){
            $css_path = str_replace( $module_dir , "" , $css_path );
        }

        $css_path = substr( $css_path , 0 , -4 ) . 'css';

        return $css_path;
    }

    static function upload_path( $file , $module ){

        $css_path = self::relative_path( $file , $module );
        $abs_css_path = SED_UPLOAD_PATH . DS . "modules" . $css_path ;
        return $abs_css_path;

    }

}

new SEDAppLess();