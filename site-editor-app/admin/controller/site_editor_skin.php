<?php

Class site_editor_skinController Extends baseController {


    function __construct( $registry = null ){
        parent::__construct( $registry );

    }

    public function index(){
        global $sed_pb_modules;

        $module = isset( $_REQUEST['module'] ) ? $_REQUEST['module'] : "";
        $modules = $sed_pb_modules->get_modules();
        $module = $sed_pb_modules->module_basename( $module );

        if( empty( $modules ) ){
            $error = new WP_Error( 'empty_modules', __("Does Not Exist Modules" , "site-editor") );
            wp_die( $error );
        }

        if( empty( $module ) || !$sed_pb_modules->is_module( $module ) )
            $module = current( array_keys( $modules ) );

        $module_main_file = WP_CONTENT_DIR . "/" . $module;

        $module_path = dirname( $module_main_file );

        $skins   = glob( $module_path . "/skins/*" );

        $thumb = array();

        $skins_installed = $sed_pb_modules->sed_skin->get_skins_installed( $module );

        foreach ( $skins as $skin ) {
            $files_skins = list_files( $skin );
            foreach ($files_skins as $file) {
                if( preg_match( "/screenshot/", $file )  )
                    $thumb[basename( $skin )] = content_url( dirname( $module ) ) . '/' . "skins". '/' . basename( $skin ) . '/' . basename( $file ) ;
            }

        }

        //parent::compile_less_files( SED_PB_MODULES_PATH . DS . $module . DS . 'skins' );

        $this->registry->template->skins_installed = $skins_installed;
        $this->registry->template->module = $module;
        $this->registry->template->modules = $modules;
        $this->registry->template->skins = $skins;
        $this->registry->template->thumb = $thumb;
        $this->registry->template->show("skin/index");
    }

    public function reinstall(){
        global $sed_pb_modules;

        $module = isset( $_REQUEST['module'] ) ? $_REQUEST['module'] : "";
        $module = $sed_pb_modules->module_basename( $module );
        $module_name = $sed_pb_modules->get_module_name( $module );

        $skin = isset( $_REQUEST['skin'] ) ? $_REQUEST['skin'] : "";

        $this->registry->template->module_name = $module_name;
        $this->registry->template->module = $module;
        $this->registry->template->skin = $skin;
        $this->registry->template->sed_pb_modules = $sed_pb_modules;
        $this->registry->template->show("skin/install-skin");
    }


    public function install(){
        global $sed_pb_modules;
        $module = isset( $_REQUEST['module'] ) ? $_REQUEST['module'] : "";
        $module = $sed_pb_modules->module_basename( $module );
        $module_name = $sed_pb_modules->get_module_name( $module );

        $skin = isset( $_REQUEST['skin'] ) ? $_REQUEST['skin'] : "";

        $this->registry->template->module_name = $module_name;
        $this->registry->template->module = $module;
        $this->registry->template->skin = $skin;
        $this->registry->template->sed_pb_modules = $sed_pb_modules;
        $this->registry->template->show("skin/install-skin");
    }

}