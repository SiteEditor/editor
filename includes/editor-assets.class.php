<?php

/**
 *
 */
if(!class_exists('SEDEditorAssetsManager'))
{
    class SEDEditorAssetsManager extends SiteEditorAssetsManager
    {

        function __construct( ) {

            add_action( 'wp_default_scripts'  , array( $this , 'default_scripts' ) );

            add_action( 'wp_default_styles'   , array( $this , 'default_styles' ) );

            add_action( 'sed_enqueue_scripts' , array( $this , 'enqueue_editor_scripts' ) );

            add_action( 'wp_enqueue_scripts'  , array( $this , 'enqueue_frontend_scripts' ) );

            add_action( 'sed_enqueue_styles'  , array( $this , 'enqueue_editor_styles' ) );

            add_action( 'wp_enqueue_styles'   , array( $this , 'enqueue_frontend_styles' ) );

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
            $this->add( 'jquery-css',           SED_EDITOR_ASSETS_URL . '/js/jquery/jquery.css'.$this->suffix.'.js', array('jquery'), '2.3' );

            $this->add( 'multi-level-box',      SED_EDITOR_ASSETS_URL . '/js/multilevelbox/multiLevelBox'.$this->suffix.'.js', array('jquery'), '2.3' );

            //site editor drag & drop
            $this->add( 'sed-drag-drop',        SED_EDITOR_ASSETS_URL . '/js/jquery/jquery.drag-drop'.$this->suffix.'.js', array('jquery'), '2.3' );

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
                'seduploader' ,
                'sed-drag-drop' ,
            );

            //register SiteEditor Script
            $this->add("siteeditor", SED_EDITOR_ASSETS_URL . "/js/siteeditor/siteeditor".$this->suffix.".js" , $deps , SED_APP_VERSION , 1 );

            $deps = array(
                'bootstrap-tab' ,
                'bootstrap-tooltip' ,
                'bootstrap-dropdown' ,
                'siteeditor'
            );

            $this->add("sed-render", SED_EDITOR_ASSETS_URL . "/js/render.min".$this->suffix.".js" , $deps , SED_APP_VERSION , 1 );

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

            $this->add( 'sed-handlebars',       SED_EDITOR_ASSETS_URL . '/js/handlebars/sed.handlebars'.$this->suffix.'.js', array('handlebars' , 'jquery' , 'underscore' ) );

            $this->add( 'jquery-contextmenu',   SED_EDITOR_ASSETS_URL . '/js/contextmenu/jquery.contextmenu'.$this->suffix.'.js', array( 'jquery' ) );

            $this->add( 'tinycolor',            SED_EDITOR_ASSETS_URL . '/js/colorpicker/js/tinycolor'.$this->suffix.'.js', array( ),"",1 );

            $this->add( 'sed-tinymce',           SED_EDITOR_FOLDER_URL . '/lib/tinymce/tinymce'.$this->suffix.'.js', array() ,"4.0.5");

            $deps = array(
                'jquery' ,
                //'backbone' ,
                'underscore' ,
                'jquery-ui-sortable' ,
                'jquery-ui-dialog' ,
                'modernizr' ,
                'sed-livequery' ,
                'sed-handlebars' ,
                'jquery-contextmenu' ,
                'tinycolor' ,
                'sed-tinymce'
            );

            //register SiteEditor Script For Editor in the Front End
            $this->add( 'sed-frontend-editor',  SED_EDITOR_ASSETS_URL . "/js/frontend-editor/frontend-editor".$this->suffix.".js" , $deps , SED_APP_VERSION );

        }

        function enqueue_frontend_scripts(){

            wp_enqueue_script( 'sed-frontend-editor' );
            
        }

        function default_styles(){

            //register Editor Core & plugin scripts
            if( is_site_editor() )
                $this->register_editor_styles();
            else
                $this->register_frontend_styles();

        }

        function register_editor_styles(){

            $this->add_css( 'siteeditor',       SED_EDITOR_ASSETS_URL . '/css/siteeditor'.$this->suffix.'.css', array(), SED_APP_VERSION );

        }


        function enqueue_editor_styles(){

            wp_enqueue_style('siteeditor');

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

}
