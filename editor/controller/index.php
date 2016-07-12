<?php

Class indexController Extends baseController {

    public function index() {
        //$this->registry->template->apps_loader("siteeditor");
        global $site_editor_app,$site_editor_script;



        //call scripts for load in siteeditor template                                                                                                            //, 'jquery-append'
        /*$site_editor_script->load_scripts(array(
            'jquery' ,
            'backbone' ,
            'yepnope' ,
            'modernizr',
            'underscore' ,
            'ajax-queue' ,
            'jquery-css' ,
            'jquery-livequery' ,
            'jquery-browser' ,
            'jquery-ui-full',
            'colorpicker',
            'bootstrap' ,
            'jquery-scrollbar',
            'multi-level-box',
            'plupload' ,
            'seduploader' ,
            'sed-drag-drop',
            'siteeditor-base' ,
            'siteeditor-shortcode' ,
            'siteeditor-ajax' ,
            'siteeditor-modules-scripts' ,
            'undomanager' ,
            'sed-undomanager' ,
            'siteeditor-css',

            //'siteeditor' ,
            "siteEditorControls",
            "styleEditorControls",
            "pbModulesControls",
            "mediaClass",
            "appPreviewClass",
            "appTemplateClass",
            "pagebuilder",
            "contextmenu",
            "sed-settings",
            "sed-save",

            'chosen'
        ));*/
        
        do_action( 'sed_init' );

        if(isset($_REQUEST['type_editor']) && !empty($_REQUEST['type_editor'])){
            $site_editor_app->current_type = $_REQUEST['type_editor'];
        }else{
            $site_editor_app->current_type = $site_editor_app->default_type;
        }

        $this->site_editor_app = $site_editor_app;

        $this->registry->template->functions_template_loader("siteEditor");

        $this->registry->template->render_template();

        $this->header();

        $this->content( );

        $this->footer();

    }

    private function content( ){
    	/*** should not have to call this here.... FIX ME ***/

       // $this->registry->template->site_editor = $this->site_editor;
    	$this->registry->template->site_editor_app = $this->site_editor_app;
        $this->registry->template->app_content = $this->app_content();
    	//$this->registry->template->app_footer = $this->app_footer();
        $this->registry->template->app_header = $this->app_header();
    	$this->registry->template->app_panel = $this->app_panel();
    	$this->registry->template->show('content');

    }

    private function header($name = 'site-editor'){

        $this->registry->template->site_editor_app = $this->site_editor_app;
        $this->registry->template->application_name   = $name;
        $this->registry->template->application_desc  = __('SiteEditor The Most Powerfull DMS For Wordpress','site-editor');
        $this->registry->template->site_editor_head = $this->registry->template->site_editor_head();

        $this->registry->template->show("header");
    }

    private function footer($name = ''){

        $this->registry->template->site_editor_footer = $this->registry->template->site_editor_footer();
        $this->registry->template->show("footer");

    }

    private function app_header(){

        $this->registry->template->site_editor_app = $this->site_editor_app;
        $this->registry->template->app_toolbar = $this->app_toolbar();
        $content = $this->registry->template->get_content("app_header");

        return $content;
    }

    /*private function app_footer(){

        //$this->registry->template->site_editor_app = $this->site_editor_app;
        $content = $this->registry->template->get_content("app_footer");

        return $content;
    } */

    private function app_content(){

        $this->registry->template->site_editor_app = $this->site_editor_app;
        $content = $this->registry->template->get_content("app_content");

        return $content;

    }

    private function app_toolbar(){

        $this->registry->template->site_editor_app = $this->site_editor_app;
        $content = $this->registry->template->get_content("app_toolbar");

        return $content;


    }

    private function app_panel(){

        $this->registry->template->site_editor_app = $this->site_editor_app;
        $content = $this->registry->template->get_content("app_panel");

        return $content;
    }

}
