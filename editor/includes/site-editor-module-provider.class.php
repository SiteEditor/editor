<?php

class SEDPBModuleProvider{

    public $pb_modules_tmpl;
                            

    function __construct(  ) {
        if( site_editor_app_on() ){
            add_action( 'wp_footer', array( $this, 'sed_app_pagebuilder_modules' ), 1000000 );
            add_action( 'wp_footer', array( &$this, 'siteeditor_check_less_compailer' ), 10 );
        }
    }

    function siteeditor_check_less_compailer(){
       global $sed_apps;

        if( ! $live_modules = sed_get_setting("live_module") )
            return ;

       $modules_activate = array_keys( $live_modules );
       $modules = $sed_apps->module_info;
       $not_compiled_files = array();

        foreach( $modules_activate AS $module_name  ){
            $module_info = $modules[$module_name];
            $skins = $module_info['skins'];
            foreach( $skins AS $skin => $skin_info ){
                $lesses = $skin_info['less'];
                if( !empty( $lesses ) )
                    $not_compiled_files = array_merge( $not_compiled_files , $this->check_lesses_compiled( $lesses ) );
            }
            $lesses = $module_info['less'];



            if( !empty( $lesses ) )
                $not_compiled_files = array_merge( $not_compiled_files , $this->check_lesses_compiled( $lesses ) );
        }


        if( !empty( $not_compiled_files ) ){
            include SED_PLUGIN_DIR . DS . 'framework' . DS . 'SEDAjaxLess' . DS  .'view'. DS . 'modal_less_compile.php';
        }

    }

    function check_lesses_compiled( $lesses ){
        $not_compiled_files = array();
        foreach( $lesses AS $handle => $less_info ){
            $less_files = array();
            $less_files[] = WP_CONTENT_DIR . substr( str_replace('/' , DS , $less_info["src"] ) , 0 , -4) . ".less";

            $import_files = $less_info['import'];
            if( !empty( $import_files ) && is_array( $import_files ) ){
                $wp_upload = wp_upload_dir();

        		$constants = array(
        			'@sed-root'				=> SED_PLUGIN_DIR  ,
        			'@sed-framework-root'	=> SED_FRAMEWORK_ASSETS_DIR. DS ."less" . DS . "siteeditor" ,
        			'@sed-modules-root'		=> SED_PB_MODULES_PATH ,
                    '@sed-images-root'		=> SED_PB_IMAGES_PATH ,
        			'@sed-plugins-root'		=> WP_PLUGIN_DIR ,
        			'@sed-themes-root'		=> get_theme_root(),
                    '@sed-uploads-root'		=> $wp_upload['basedir']  ,
        		);

                foreach( $import_files AS $file_path ){
                    foreach( $constants As $var => $val ){
                        $file_path = str_replace( $var , $val , $file_path );
                    }

                    $file_path = str_replace( '/' , DS , $file_path );
                    $wp_base = str_replace( '/' , DS , dirname( dirname( WP_PLUGIN_DIR ) ) );
                    $is_abs_path = strpos( $file_path , $wp_base  );

                    if( $is_abs_path === false ){
                        $file_path = dirname( $less_files[0] ) . DS . $file_path;
                    }

                    $less_files[] = $file_path;
                }
            }

            $css_abs_path = SED_UPLOAD_PATH . str_replace( '/' , DS , '/modules' . $less_info["src_rel"] );

            if( !class_exists( 'SEDAppLess' ) ) 
                require_once SED_INC_DIR . DS . 'sed_app_less.class.php';

            $is_compiled = SEDAppLess::checkedCompile( $less_files , $css_abs_path );
            if( !$is_compiled )
                $not_compiled_files[$handle] = array( $less_info["src"] , $less_info["src_rel"] );
        }

        return $not_compiled_files;
    }

    public function sed_app_pagebuilder_modules(){

        global $sed_apps; var_dump( SED()->editor->attachments_loaded );

        if( is_array( SED()->editor->attachments_loaded ) && !empty( SED()->editor->attachments_loaded ) ) {
            $attachments = array_map('wp_prepare_attachment_for_js', SED()->editor->attachments_loaded );
            $attachments = array_filter($attachments);
        }else
            $attachments = array();

        if( ! $live_modules = sed_get_setting("live_module") )
            $modules_activate = array();
        else
            $modules_activate = array_keys( $live_modules );

        $modules_info = array();
        foreach( $modules_activate AS $module_name ){
            $module_info = $sed_apps->module_info[$module_name];

            $skins = $module_info['skins'];
            foreach( $skins AS $skin => $skin_info ){
                $skin_scripts = $skin_info['scripts'];
                $scripts = array();

                if( !empty( $skin_scripts ) ){
                    foreach( $skin_scripts AS $key => $script ){
                        $scripts[] = array( $script['handle'] , content_url( "/" . $script['src'] ), $script['deps'] , $script['ver'] , $script['in_footer'] );
                    }
                }

                $modules_info[$module_name]['skins'][$skin]['scripts'] = $scripts;

                $styles = array();
                $lesses = $skin_info['less'];
                if( !empty( $lesses ) ){
                    foreach( $lesses AS $handle => $less_info  ){
                        $styles[] = array( $less_info['handle'] , SED_UPLOAD_URL. "/modules" .$less_info['src_rel'] , $less_info['deps'] , $less_info['ver'] , $less_info['media'] );
                    }
                }

                $skin_styles = $skin_info['styles'];
                $css_styles = array();

                if( !empty( $skin_styles ) ){
                    foreach( $skin_styles AS $key => $style ){
                        $css_styles[] = array( $style['handle'] , content_url( "/" . $style['src'] ) , $style['deps'] , $style['ver'] , $style['media'] );
                    }
                }

                $modules_info[$module_name]['skins'][$skin]['styles'] = array_merge( $css_styles , $styles );
                   
            }

 
        }


                /*var _sedAppPageBuilderModulesScripts = <?php echo wp_json_encode( $site_editor_app->pagebuilder->modules_scripts ); ?>;
                //var _sedAppPageBuilderModulesStyles = <?php echo wp_json_encode( $site_editor_app->pagebuilder->modules_styles ); ?>; */
        ?>



		<script type="text/javascript">
                var _sedAppPageBuilderModulesInfo = <?php echo wp_json_encode( $modules_info ); ?>;
                var _sedAppPBAttachmentsSettings = <?php if( !empty( $attachments ) ) echo wp_json_encode( $attachments ); else echo "{}"; ?>;
		</script>

        <?php

    }

}

new SEDPBModuleProvider();

