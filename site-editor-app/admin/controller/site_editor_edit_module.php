<?php

Class site_editor_edit_moduleController Extends baseController {

    function __construct( $registry = null ){
        parent::__construct( $registry );

        if( !class_exists( 'SEDFile' ) )
            require_once SED_APP_PATH . '/sed_file.class.php';

        if( !class_exists( 'SEDAppLess' ) )
            require_once SED_APP_PATH . '/sed_app_less.class.php';

    }

    public function index() {
        $this->get_edit_page();
    }

    public function update() {
        $this->get_edit_page();
    }

    private function get_edit_page() {
        global $sed_pb_modules , $sed_error;

        $module = isset( $_REQUEST['module'] ) ? $_REQUEST['module'] : "";
        $skin = isset( $_REQUEST['skin'] ) ? $_REQUEST['skin'] : "";
        $modules = $sed_pb_modules->get_modules();
        $module = $sed_pb_modules->module_basename( $module );

        if( empty( $modules ) ){
            $error = new WP_Error( 'empty_modules', __("Does Not Exist Modules" , "site-editor") );
            wp_die( $error );
        }

        if( empty( $module ) || !$sed_pb_modules->is_module( $module ) )
            $module = current( array_keys( $modules ) );

        $editable_extensions  = array( 'less' );

        $files_path = ( empty( $skin ) ) ? WP_CONTENT_DIR . "/" . dirname($module) : WP_CONTENT_DIR . "/" . dirname($module) . "/skins/" . $skin;
        $files = SEDFile::list_files( $files_path , '' , WP_CONTENT_DIR . "/" , $editable_extensions ) ;

        if( empty( $files ) ){
            $error = new WP_Error( 'not_less_files_exist', __("Do not exist any less file in this module" , "site-editor") );
            wp_die( $error );
        }

        $num_files_index = count( $files ) - 1;
        $current_file = isset( $_REQUEST['file'] ) ? WP_CONTENT_DIR . "/" . dirname( dirname($module) ) . "/" . $_REQUEST['file'] : WP_CONTENT_DIR . "/" . $files[$num_files_index];

        if ( ! is_file( $current_file ) ) {
            wp_die( sprintf('<p>%s</p>', __('No such file exists! Double check the name and try again.' , 'site-editor' ) ) );
        } else {
            // Get the extension of the file
            if ( preg_match('/\.([^.]+)$/', $current_file, $matches) ) {
                $ext = strtolower( $matches[1] );
                // If extension is not in the acceptable list, skip it
                if ( !in_array( $ext, $editable_extensions) )
                    wp_die(sprintf('<p>%s</p>', __('Files of this type are not editable.')));
            }
        }

        if( isset( $_REQUEST['action'] ) &&  $_REQUEST['action'] == "update" ){
            $this->less_update( $current_file , $module );
        }

        foreach( $files AS $key => $less_file ){
            $less_file = str_replace( dirname( dirname($module) ) . "/" , '' , $less_file );
            $less_file = str_replace( DS , "/" , $less_file );
            $files[$key] = $less_file;
        }

        $files = array_reverse( $files );

        $module_name = $sed_pb_modules->get_module_name( $module );

        $module_skins = $sed_pb_modules->sed_skin->get_module_skins( $module );

        $this->registry->template->title            = sprintf( __('Edit Module : %s' , 'site-editor' ) , $module_name ) ;
        $this->registry->template->skin             = $skin;
        $this->registry->template->module_skins     = $module_skins;
        $this->registry->template->is_active        = $sed_pb_modules->is_module_active( $module ) ;
        $this->registry->template->files            = $files;
        $this->registry->template->current_file     = $current_file ;
        $this->registry->template->module_name      = $module_name ;
        $this->registry->template->module           = $module ;
        $this->registry->template->content          = file_get_contents( $current_file );
        $this->registry->template->modules          = $modules;
        $this->registry->template->massage          = $sed_error->get_error();

        $this->registry->template->show("module/edit");

    }

    private function less_update( $file , $module ) {
        global $sed_error;

        if( !isset( $_POST['newcontent'] ) ){
            $sed_error->set_error( array(
                    "edite_module"      => array(
                            "type"      =>'error',
                            "massage"   => __( 'Send data is invalid' , 'site-editor' )
                        )

                )
            );
        }

        $newcontent = wp_unslash( $_POST['newcontent'] );

        if ( is_file( $file ) ) {

            global $wp_filesystem;
            if( empty( $wp_filesystem ) ) {
                require_once( ABSPATH .'/wp-admin/includes/file.php' );
                WP_Filesystem();
            }

            if( $wp_filesystem ) {
                $wp_filesystem->put_contents(
                    $file,
                    $newcontent,
                    FS_CHMOD_FILE // predefined mode settings for WP files
                );

                if( substr( $file , -5 ) == ".less" ){
                    $module_path = $file;

                    if( SEDAppLess::is_main_less( $file ) ){

                        $abs_css_path = SEDAppLess::upload_path( $file , $module );

                        $result_compile = SEDAppLess::compile_file( $file , $abs_css_path );

                        if( $result_compile === true )
                            $sed_error->set_error( array(
                                "edite_module"      => array(
                                    "type"          => 'updated',
                                    "massage"       => sprintf( __("less %s is compiled.","site-editor" ) , substr( basename( $file ) , 0 , -5 ) )
                                ))
                            );
                        else
                            $sed_error->set_error( array(
                                "edite_module"      => array(
                                        "type"      =>'error',
                                        "massage"   => sprintf( __("Error LESS : %s","site-editor" ) , $result_compile )
                                    )
                                )
                            );
                    }else{
                        $sed_error->set_error( array(
                            "edite_module"      => array(
                                "type"          => 'updated',
                                "massage"       > sprintf( __("less %s is updated.","site-editor" ) , substr( basename( $file ) , 0 , -5 ) )
                            ))
                        );
                    }

                }else{
                    $sed_error->set_error( array(
                        "edite_module"      => array(
                            "type"      =>'updated',
                            "massage"   => __( 'File is successfully updated.' , 'site-editor' )
                        ))
                    );
                }

            }else{
                $sed_error->set_error( array(
                        "edite_module"      => array(
                                "type"      =>'error',
                                "massage"   => __( 'Module update failed.' , 'site-editor' )
                            )
                    )
                );
            }

        } else {
            $sed_error->set_error( array(
                    "edite_module"      => array(
                            "type"      =>'error',
                            "massage"   => __( 'File Not Found Module update failed.' , 'site-editor' )
                        )
                )
            );

        }

    }

}