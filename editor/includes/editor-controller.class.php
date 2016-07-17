<?php

Class SEDEditorController {

    public function __construct( $registry ){

        global $site_editor_app;

        $this->registry = $registry;

        do_action( 'sed_init' );

        $this->site_editor_app = $site_editor_app; 

        //$this->registry->template->functions_template_loader("siteEditor");

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
