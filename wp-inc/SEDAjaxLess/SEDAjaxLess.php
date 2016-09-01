<?php
/**
* 
*/
class SEDAjaxLess
{

    function __construct()
    {
        add_filter( "sed_addon_settings", array($this,'set_settings'));
        add_action( 'wp_enqueue_scripts', array($this, 'script') );

    }

    function script()
    {
    	wp_enqueue_script( "less-compile-ajax" , SED_PLUGIN_URL . '/wp-inc/SEDAjaxLess/js/less-compile-ajax.min.js' , array('jquery') , '1.0.0' , true );
    }

    public static function driver()
    {
        global $sed_ajax , $sed_error;
        $sed_ajax->check_ajax_handler('sed_ajax_compiler' , 'sed_app_compile');

        $out        = array();
        $uploads    = wp_upload_dir();
        $dir        = $uploads['basedir'];

        extract( $_POST['filedata'] );

        $compile_files      = isset( $modules ) ? $modules : ( isset( $compile_files ) ? $compile_files : array() );
        $save_compile       = isset( $save_compile ) ? $save_compile : false ;
        $vars               = isset( $vars ) ? ( is_array( $vars ) ? $vars : array( $vars ) ) : array();
        $siteeditor_compile = isset( $siteeditor_compile ) ? $siteeditor_compile : false ;

        if( !empty( $compile_files ) && is_array( $compile_files )  ){

            $start          = microtime( true );
            $files          = array();
            $src_compiled   = array();

            if( !class_exists( 'SEDAppLess' ) )
                require_once SED_APP_PATH . DS . 'sed_app_less.class.php';

            foreach( $compile_files AS $file_info ){
                $css_abs_path = str_replace('/' , DS , $file_info[1] );
                $css_rel_path = str_replace('/' , DS , $file_info[2] );

                $file = WP_CONTENT_DIR . substr( $css_abs_path , 0 , -4 ) . '.less';

                $abs_css_path = SED_UPLOAD_PATH . DS . "modules" . $css_rel_path ;

                $result = SEDAppLess::compile_file( $file , $abs_css_path );

                if ( $result === true )
                    $src_compiled[] = SED_UPLOAD_URL. "/modules" . $file_info[2] ;
            }

            $out['styles']    = $src_compiled;
            $out['getErrors'] = $sed_error->get_message();
            $out['time'] = microtime( true ) - $start . ' ms';

            wp_send_json_success( array(
                'output'    => $out
            ) );

        }else{

            $output = __("not any valid file for compile" , "site-editor");
            wp_send_json_error( array(
                'output'    => $output
            ) );
        }

    }

    public function set_settings( $sed_addon_settings ){
        global $site_editor_app;
        $sed_addon_settings['SEDLess'] = array(
            'nonce'  => array(
                'compile'    =>  wp_create_nonce( 'sed_app_compile_' . $site_editor_app->get_stylesheet() ) ,
            )
        );
        return $sed_addon_settings;
    }

}
