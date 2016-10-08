<?php

Class site_editor_indexController Extends baseController {


    public function index() {

        $module_info = sed_get_setting("module_info");

        global $sed_error;

        if( !class_exists( 'SiteEditorSetup' ) )
            require_once( SED_ADMIN_INC_PATH . DS . 'sed-setup.class.php' );

        if( SiteEditorSetup::is_installed() ){
            $this->show_options_page();
        }else{

            $this->registry->template->massage = $sed_error->get_error();
            $this->registry->template->site_editor_install = new SiteEditorSetup;

            /*** load the index template ***/
            $this->registry->template->show('install');

        }

    }

    private function show_options_page(){
        global $sed_error , $sed_general_data , $options_machine;
        $title = __('Site Editor Settings','site-editor');
        $massage = '';

        $options_machine->options = $sed_general_data;

        if( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'compile_framework' ){
            
            if( !class_exists( 'SEDAppLess' ) )
                require_once SED_INC_DIR . DS . 'sed_app_less.class.php';

            $result = SEDAppLess::compile_base_framework();
            if( $result === true )
               $massage =  __("Less Framework is compiled." ,"site-editor");
            else
               $massage = sprintf( __("Less Framework Error : %s" ,"site-editor") , $result );
        }


        if( isset( $_REQUEST['action'] ) && ( $_REQUEST['action'] === 'save' || $_REQUEST['action'] === 'reset' ) ){
            $options_machine->save_options_admin();
        }

        foreach( $options_machine->items AS $tab => $options ){
            foreach( $options AS $id => $value ){
                if( isset( $value['html'] ) && method_exists('site_editor_indexController', $value['html'] ) ){
                    $func = $value['html'];
                    $value = isset( $sed_general_data[$id] ) ? $sed_general_data[$id] : ( isset( $value['std'] ) ? $value['std']  : '' );
                    $options_machine->items[$tab][$id]['html'] = $this->$func( $id , $value );
                }
            }
        }

        /*** set a template variable ***/
        $this->registry->template->title    = $title;
        $this->registry->template->massage  = $massage;
        $this->registry->template->admin_options = $options_machine;


        /*** load the index template ***/
        $this->registry->template->show('index');
    }


    public function module_less_compile(){
        $this->registry->template->show('module-less-compile');
    }

    public function import_data_content(){
        $this->registry->template->show('import-demo-processing');
    }

    private function theme_less_compile(){

        $content = $this->registry->template->get_content("theme_less_compile");

        return $content;

    }

    private function pages_edit_links( $id , $value = '' ){

        $content = $this->registry->template->get_content("pages_edit_links");

        return $content;
    }

    private function get_font_families( $id , $value = '' ){

        $this->registry->template->id       = $id;
        $this->registry->template->value    = $value;
        $content = $this->registry->template->get_content("font-family");

        return $content;

    }

    private function color_palette( $id , $value = '' ){

        $this->registry->template->id    = $id;
        $this->registry->template->value    = $value;
        $content = $this->registry->template->get_content("color-palette");

        return $content;

    }

    private function import_demo_data( $id , $value = '' ){

        $this->registry->template->id    = $id;
        $this->registry->template->value    = $value;
        $content = $this->registry->template->get_content("import-demo-data");

        return $content;
    }


}
