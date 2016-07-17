<?php

/**
 *
 */
if(!class_exists('SEDEditorAssetsManager'))
{
    class SEDEditorAssetsManager extends SiteEditorAssetsManager
    {

        function __construct( ) {

            add_action( 'init'                , array( $this , 'default_scripts' ) , 0 );

            add_action( 'init'                , array( $this , 'default_styles' ) , 0 );

            add_action( 'sed_enqueue_scripts' , array( $this , 'enqueue_editor_scripts' ) );

            add_action( 'wp_enqueue_scripts'  , array( $this , 'enqueue_frontend_scripts' ) );

            add_action( 'sed_enqueue_scripts'  , array( $this , 'enqueue_editor_styles' ) );

            add_action( 'wp_enqueue_scripts'   , array( $this , 'enqueue_frontend_styles' ) );

        }

        function default_scripts(){

            //register Editor Core & plugin scripts
            if( is_site_editor() )
                $this->register_editor_scripts();
            else
                $this->register_frontend_scripts();

        }

        function register_editor_scripts(){

            //jquery css 3 support
            $this->add( 'jquery-css',           SED_EDITOR_ASSETS_URL . '/js/jquery/jquery-css'.$this->suffix.'.js', array('jquery'), '2.3' );

            $this->add( 'multi-level-box',      SED_EDITOR_ASSETS_URL . '/js/multilevelbox/multiLevelBox'.$this->suffix.'.js', array('jquery'), '2.3' );

            //site editor drag & drop
            $this->add( 'sed-drag-drop',        SED_EDITOR_ASSETS_URL . '/js/jquery/jquery-drag-drop'.$this->suffix.'.js', array('jquery'), '2.3' );

            //color picker
            $this->add( 'sed-colorpicker',      SED_EDITOR_ASSETS_URL . '/js/colorpicker/spectrum'.$this->suffix.'.js' , array('jquery') , "1.0.0" );

            $deps = array(
                'jquery' ,
                'backbone' ,
                'underscore' ,
                //jquery ui
                'jquery-ui-sortable' ,
                'jquery-ui-spinner' ,
                'jquery-ui-accordion' ,
                'jquery-ui-dialog' ,
                //other
                'modernizr' ,
                'sed-livequery' ,
                'jquery-css' ,
                'sed-colorpicker' ,
                'multi-level-box' ,
                'jquery-scrollbar' ,
                //'seduploader' ,
                'sed-drag-drop' ,
            );
      
            //register SiteEditor Script
            $this->add("siteeditor", SED_EDITOR_ASSETS_URL . "/js/siteeditor".$this->suffix.".js" , $deps , SED_APP_VERSION , 1 );

            $deps = array(
                'bootstrap-tab' ,
                'bootstrap-tooltip' ,
                'bootstrap-dropdown' ,
                'siteeditor'
            );

            $this->add("sed-render", SED_EDITOR_ASSETS_URL . "/js/render".$this->suffix.".js" , $deps , SED_APP_VERSION , 1 );

        }

        /**
         * 'jquery' ,
         *
         * 'backbone' ,
         * 'underscore' ,
         * 
         * 'yepnope' ,
         * 'modernizr',
         * 
         * 'jquery-css' ,
         * 'jquery-livequery' ,
         * 
         * //'jquery-ui-full',
         * 'jquery-ui-sortable' ,
         * 'jquery-ui-spinner' ,
         * 'jquery-ui-accordion' ,
         * 'jquery-ui-dialog' ,
         * 'jquery-ui-progressbar' ,
         * 
         * 'sed-colorpicker',
         * 
         * //'bootstrap' ,
         * 'bootstrap-tab' ,
         * 'bootstrap-tooltip' ,
         * 'bootstrap-dropdown' ,
         * 
         * 'jquery-scrollbar',
         * 'multi-level-box',
         * 'plupload' ,
         * 'seduploader' ,
         * 'sed-drag-drop',
         * 'siteeditor-base' ,
         * 'siteeditor-shortcode' ,
         * 'siteeditor-ajax' ,
         * 'siteeditor-modules-scripts' ,
         * 'siteeditor-css',
         * 
         * //'siteeditor' ,
         * "siteEditorControls",
         * "styleEditorControls",
         * "pbModulesControls",
         * "mediaClass",
         * "appPreviewClass",
         * "appTemplateClass",
         * "pagebuilder",
         * "contextmenu",
         * "sed-settings",
         * "sed-save",
         * 
         * 'chosen'
         */
        function enqueue_editor_scripts(){
            
            wp_enqueue_script( 'siteeditor' );
            
            wp_enqueue_script( 'sed-render' );
            
        }

        function register_frontend_scripts(){

            $this->add( 'handlebars',           SED_EDITOR_ASSETS_URL . '/js/handlebars/handlebars'.$this->suffix.'.js', array( ) );

            $this->add( 'sed-handlebars',       SED_EDITOR_ASSETS_URL . '/js/handlebars/sed-handlebars'.$this->suffix.'.js', array('handlebars' , 'jquery' , 'underscore' ) );

            $this->add( 'jquery-contextmenu',   SED_EDITOR_ASSETS_URL . '/js/contextmenu/jquery-contextmenu'.$this->suffix.'.js', array( 'jquery' , 'sed-frontend-editor' ) );

            $this->add( 'tinycolor',            SED_EDITOR_ASSETS_URL . '/js/colorpicker/tinycolor'.$this->suffix.'.js', array( ),"",1 );

            $this->add( 'sed-tinymce',          SED_EDITOR_ASSETS_URL . '/libs/tinymce/tinymce'.$this->suffix.'.js', array() ,"4.0.5");

            $deps = array(
                'jquery' ,
                //'backbone' ,
                'underscore' ,
                'jquery-ui-sortable' ,
                'jquery-ui-dialog' ,
                'modernizr' ,
                'sed-livequery' ,
                'sed-handlebars' ,
                'tinycolor' ,
                'sed-tinymce'
            );

            //register SiteEditor Script For Editor in the Front End
            $this->add( 'sed-frontend-editor',  SED_EDITOR_ASSETS_URL . "/js/frontend-editor".$this->suffix.".js" , $deps , SED_APP_VERSION );

        }

        function enqueue_frontend_scripts(){

            wp_enqueue_script( 'sed-frontend-editor' );

            wp_enqueue_script( 'jquery-contextmenu' );
            
        }

        function default_styles(){

            //register Editor Core & plugin scripts
            if( is_site_editor() )
                $this->register_editor_styles();
            else
                $this->register_frontend_styles();

        }

        function register_editor_styles(){

            if( SED()->version_type == "production" ){

                $this->add_css( 'siteeditor',           SED_EDITOR_ASSETS_URL . '/css/siteeditor'.$this->suffix.'.css', array(), SED_APP_VERSION );

            }else{

                $this->add_css( 'sed-main-accordion',            SED_EDITOR_ASSETS_URL . '/css/siteeditor/accordion.css', array(), SED_APP_VERSION  );
                $this->add_css( 'sed-main-alert',                SED_EDITOR_ASSETS_URL . '/css/siteeditor/alert.css', array(), SED_APP_VERSION  );
                $this->add_css( 'sed-main-animation' ,           SED_EDITOR_ASSETS_URL . '/css/siteeditor/animation.css', array(), SED_APP_VERSION );
                $this->add_css( 'sed-main-button',               SED_EDITOR_ASSETS_URL . '/css/siteeditor/button.css', array(), SED_APP_VERSION  );
                $this->add_css( 'sed-main-color-fonts',          SED_EDITOR_ASSETS_URL . '/css/siteeditor/color-fonts.css', array(), SED_APP_VERSION  );
                $this->add_css( 'sed-main-colorselector',        SED_EDITOR_ASSETS_URL . '/css/siteeditor/colorselector.css', array(), SED_APP_VERSION );
                $this->add_css( 'sed-main-dialog',               SED_EDITOR_ASSETS_URL . '/css/siteeditor/dialog.css', array(), SED_APP_VERSION );
                $this->add_css( 'sed-main-dialog-page-box',      SED_EDITOR_ASSETS_URL . '/css/siteeditor/dialog-page-box.css', array(), SED_APP_VERSION );
                $this->add_css( 'sed-main-dropdown',             SED_EDITOR_ASSETS_URL . '/css/siteeditor/dropdown.css', array(), SED_APP_VERSION );
                $this->add_css( 'sed-main-dropdown-styleeditor', SED_EDITOR_ASSETS_URL . '/css/siteeditor/dropdown-styleeditor.css', array(), SED_APP_VERSION );
                $this->add_css( 'sed-main-icon-library',         SED_EDITOR_ASSETS_URL . '/css/siteeditor/icon-library.css', array(), SED_APP_VERSION );
                $this->add_css( 'sed-main-library',              SED_EDITOR_ASSETS_URL . '/css/siteeditor/library.css', array(), SED_APP_VERSION );
                $this->add_css( 'sed-main-loading',              SED_EDITOR_ASSETS_URL . '/css/siteeditor/loading.css', array(), SED_APP_VERSION );
                $this->add_css( 'sed-main-multilevelbox',        SED_EDITOR_ASSETS_URL . '/css/siteeditor/multilevelbox.css', array(), SED_APP_VERSION  );
                $this->add_css( 'sed-main-chosen',               SED_EDITOR_ASSETS_URL . '/css/siteeditor/chosen.css', array(), SED_APP_VERSION  );
                $this->add_css( 'sed-main-sp-dark',              SED_EDITOR_ASSETS_URL . '/css/siteeditor/sp-dark.css', array(), SED_APP_VERSION  );
                $this->add_css( 'sed-main-spectrum',             SED_EDITOR_ASSETS_URL . '/css/siteeditor/spectrum.css', array(), SED_APP_VERSION  );
                $this->add_css( 'sed-main-scrollbar',            SED_EDITOR_ASSETS_URL . '/css/siteeditor/scrollbar.css', array(), SED_APP_VERSION  );
                //$this->add_css( 'sed-main-jquery-ui' ,           SED_EDITOR_ASSETS_URL . '/css/siteeditor/jquery-ui.css', array(), SED_APP_VERSION );
                //$this->add_css( 'sed-main-jquery-ui-ie',         SED_EDITOR_ASSETS_URL . '/css/siteeditor/jquery-ui-ie.css', array(), SED_APP_VERSION  );
                $this->add_css( 'sed-main-organize-posts',       SED_EDITOR_ASSETS_URL . '/css/siteeditor/organize-posts.css', array(), SED_APP_VERSION  );
                $this->add_css( 'sed-main-pages',                SED_EDITOR_ASSETS_URL . '/css/siteeditor/pages.css', array(), SED_APP_VERSION  );
                $this->add_css( 'sed-main-panel',                SED_EDITOR_ASSETS_URL . '/css/siteeditor/panel.css', array(), SED_APP_VERSION  );
                $this->add_css( 'sed-main-preview',              SED_EDITOR_ASSETS_URL . '/css/siteeditor/preview.css', array(), SED_APP_VERSION  );
                $this->add_css( 'sed-main-siteeditor-app',       SED_EDITOR_ASSETS_URL . '/css/siteeditor/siteeditor-app.css', array(), SED_APP_VERSION  );
                $this->add_css( 'sed-main-settings',             SED_EDITOR_ASSETS_URL . '/css/siteeditor/settings.css', array(), SED_APP_VERSION  );  
                $this->add_css( 'sed-main-slider' ,              SED_EDITOR_ASSETS_URL . '/css/siteeditor/slider.css', array(), SED_APP_VERSION );
                $this->add_css( 'sed-main-spinner',              SED_EDITOR_ASSETS_URL . '/css/siteeditor/spinner.css', array(), SED_APP_VERSION  );
                $this->add_css( 'sed-main-style-editor',         SED_EDITOR_ASSETS_URL . '/css/siteeditor/style-editor.css', array(), SED_APP_VERSION  );
                $this->add_css( 'sed-main-tab',                  SED_EDITOR_ASSETS_URL . '/css/siteeditor/tab.css', array(), SED_APP_VERSION  );
                $this->add_css( 'sed-main-template-library',     SED_EDITOR_ASSETS_URL . '/css/siteeditor/template-library.css', array(), SED_APP_VERSION  );
                $this->add_css( 'sed-main-tooltip',              SED_EDITOR_ASSETS_URL . '/css/siteeditor/tooltip.css', array(), SED_APP_VERSION  );
                $this->add_css( 'sed-main-z2',                   SED_EDITOR_ASSETS_URL . '/css/siteeditor/z2/z.css', array(), SED_APP_VERSION  );
                $this->add_css( 'sed-main-font-icon-siteeditor', SED_EDITOR_ASSETS_URL . '/css/siteeditor/font-icon-siteeditor.css', array(), SED_APP_VERSION  );
                $this->add_css( 'sed-main-font-extra',           SED_EDITOR_ASSETS_URL . '/css/siteeditor/font-extra.css', array(), SED_APP_VERSION );

            }
        }


        function enqueue_editor_styles(){

            if( SED()->version_type == "production" ){

                wp_enqueue_style( 'siteeditor'  );

            }else{

                wp_enqueue_style( 'sed-main-accordion'  );
                wp_enqueue_style( 'sed-main-alert'  );
                wp_enqueue_style( 'sed-main-animation'  );
                wp_enqueue_style( 'sed-main-button'  );
                wp_enqueue_style( 'sed-main-color-fonts'  );
                wp_enqueue_style( 'sed-main-colorselector' );
                wp_enqueue_style( 'sed-main-dialog' );
                wp_enqueue_style( 'sed-main-dialog-page-box' );
                wp_enqueue_style( 'sed-main-dropdown' );
                wp_enqueue_style( 'sed-main-dropdown-styleeditor' );
                wp_enqueue_style( 'sed-main-icon-library' );
                wp_enqueue_style( 'sed-main-library' );
                wp_enqueue_style( 'sed-main-loading' );
                wp_enqueue_style( 'sed-main-multilevelbox'  );
                wp_enqueue_style( 'sed-main-chosen'  );
                wp_enqueue_style( 'sed-main-sp-dark'  );
                wp_enqueue_style( 'sed-main-spectrum'  );
                wp_enqueue_style( 'sed-main-scrollbar'  );
                //wp_enqueue_style( 'sed-main-jquery-ui'  );
                //wp_enqueue_style( 'sed-main-jquery-ui-ie'  );
                wp_enqueue_style( 'sed-main-organize-posts'  );
                wp_enqueue_style( 'sed-main-pages'  );
                wp_enqueue_style( 'sed-main-panel'  );
                wp_enqueue_style( 'sed-main-preview'  );
                wp_enqueue_style( 'sed-main-siteeditor-app'  );
                wp_enqueue_style( 'sed-main-settings'  );
                wp_enqueue_style( 'sed-main-slider'  );
                wp_enqueue_style( 'sed-main-spinner'  );
                wp_enqueue_style( 'sed-main-style-editor'  );
                wp_enqueue_style( 'sed-main-tab'  );
                wp_enqueue_style( 'sed-main-template-library'  );
                wp_enqueue_style( 'sed-main-tooltip'  );
                wp_enqueue_style( 'sed-main-z2' );
                wp_enqueue_style( 'sed-main-font-icon-siteeditor' );
                wp_enqueue_style( 'sed-main-font-extra' ); 

            }        

        }


        function register_frontend_styles(){

            $this->add_css( 'site-iframe',      SED_EDITOR_ASSETS_URL . '/css/frontend-editor/site-iframe'.$this->suffix.'.css' , array(), SED_APP_VERSION  );

            $this->add_css( 'contextmenu',      SED_EDITOR_ASSETS_URL . '/css/frontend-editor/contextmenu'.$this->suffix.'.css' , array(), SED_APP_VERSION  );

            $this->add_css( 'fonts-sed-iframe', SED_EDITOR_ASSETS_URL . '/css/frontend-editor/fonts-sed-iframe'.$this->suffix.'.css' , array(), SED_APP_VERSION  );

        }


        function enqueue_frontend_styles(){

            wp_enqueue_style("contextmenu");
            wp_enqueue_style("site-iframe");
            wp_enqueue_style("fonts-sed-iframe");

        }


    }

    new SEDEditorAssetsManager();

}
