<?php

/**
 *
 */
if(!class_exists('SiteEditorAssetsManager'))
{
    class SiteEditorAssetsManager
    {

        public static $bootstrap_version;
        public $suffix = ".min";

        function __construct( ) {
            self::$bootstrap_version = "3.3.5";

            add_action("init" , array( $this , "default_scripts" ) , 0 );
        }

        function default_scripts( ){

            //register bootstrap js plugins
            $this->register_bootstrap_scripts();

            //register chosen
            $this->add("chosen",                SED_ASSETS_URL . "/js/chosen/chosen.jquery".$this->suffix.".js" , array('jquery'  ) , "1.1.0" );
            
            //register livequery
            $this->add( 'jquery-livequery',     SED_ASSETS_URL . '/js/livequery/jquery.livequery'.$this->suffix.'.js', array( 'jquery' ) , '1.0.0' );
            $this->add( 'sed-livequery',        SED_ASSETS_URL . '/js/livequery/sed.livequery'.$this->suffix.'.js', array( 'jquery-livequery' ) , '1.0.0' );

            //register modernizr
            $this->add( 'yepnope'   ,           SED_ASSETS_URL . '/js/yepnope/yepnope'.$this->suffix.'.js', array(), '2.5.6' );
            $this->add( 'modernizr' ,           SED_ASSETS_URL . '/js/modernizr/modernizr.custom'.$this->suffix.'.js', array('yepnope'), '2.8.1' );

            //scrollbar
            $this->add( 'jquery-scrollbar',     SED_ASSETS_URL . '/js/scrollbar/jquery.mCustomScrollbar.concat'.$this->suffix.'.js', array('jquery'), '2.3' );
            
        }

        function register_bootstrap_scripts(){

            $this->add("bootstrap-affix",       SED_ASSETS_URL . "/js/bootstrap/affix/affix".$this->suffix.".js" , array('jquery') , self::$bootstrap_version );
            
            $this->add("bootstrap-alert",       SED_ASSETS_URL . "/js/bootstrap/alert/alert".$this->suffix.".js" , array( 'jquery' , 'bootstrap-transition' ) , self::$bootstrap_version );
            
            $this->add("bootstrap-button",      SED_ASSETS_URL . "/js/bootstrap/button/button".$this->suffix.".js" , array('jquery') , self::$bootstrap_version );
            
            $this->add("bootstrap-carousel",    SED_ASSETS_URL . "/js/bootstrap/carousel/carousel".$this->suffix.".js" , array( 'jquery' , 'bootstrap-transition' ) , self::$bootstrap_version );
            
            $this->add("bootstrap-collapse",    SED_ASSETS_URL . "/js/bootstrap/collapse/collapse".$this->suffix.".js" , array( 'jquery' , 'bootstrap-transition' ) , self::$bootstrap_version );
            
            $this->add("bootstrap-dropdown",    SED_ASSETS_URL . "/js/bootstrap/dropdown/dropdown".$this->suffix.".js" , array('jquery') , self::$bootstrap_version );
            
            $this->add("bootstrap-modal",       SED_ASSETS_URL . "/js/bootstrap/modal/modal".$this->suffix.".js" , array( 'jquery' , 'bootstrap-transition'  ) , self::$bootstrap_version );
            
            $this->add("bootstrap-popover",     SED_ASSETS_URL . "/js/bootstrap/popover/popover".$this->suffix.".js" , array('jquery' , 'bootstrap-tooltip' ) , self::$bootstrap_version );
            
            $this->add("bootstrap-scrollspy",   SED_ASSETS_URL . "/js/bootstrap/scrollspy/scrollspy".$this->suffix.".js" , array('jquery') , self::$bootstrap_version );
            
            $this->add("bootstrap-tab",         SED_ASSETS_URL . "/js/bootstrap/tab/tab".$this->suffix.".js" , array( 'jquery' , 'bootstrap-transition' ) , self::$bootstrap_version );
            
            $this->add("bootstrap-tooltip",     SED_ASSETS_URL . "/js/bootstrap/tooltip/tooltip".$this->suffix.".js" , array( 'jquery' , 'bootstrap-transition' ) , self::$bootstrap_version );
            
            $this->add("bootstrap-transition",  SED_ASSETS_URL . "/js/bootstrap/transition/transition".$this->suffix.".js" , array('jquery') , self::$bootstrap_version );
        }

        function add( $handle, $src, $deps = array(), $ver = false, $in_footer = null ){
            
            wp_register_script( $handle, $src, $deps, $ver, $in_footer );
            
        }

        function add_css($handle , $src , $deps = array() , $ver = '' , $media = "all"){

            wp_register_style( $handle, $src, $deps, $ver, $media );

        }

        function default_styles(){

        }

    }

    new SiteEditorAssetsManager();

}
