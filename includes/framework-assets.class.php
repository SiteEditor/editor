<?php

/**
 *
 */
if(!class_exists('SEDFrameworkAssetsManager'))
{
    class SEDFrameworkAssetsManager
    {

        public static $bootstrap_version;
        public $suffix;

        function __construct( ) {
            self::$bootstrap_version = "3.3.5";
            $this->suffix = ".min";

            add_action("wp_default_scripts" , array( $this , "default_scripts" ) );
        }

        function default_scripts(){

            //register bootstrap js plugins
            $this->register_bootstrap_scripts();

            //register chosen
            $this->add("chosen", SED_ASSETS_URL . "/js/chosen/chosen.jquery".$this->suffix.".js" , array('jquery'  ) , "1.1.0" );
            
            //register livequery
            $this->add( 'jquery-livequery'  , SED_ASSETS_URL . '/js/livequery/jquery.livequery'.$this->suffix.'.js', array( 'jquery' ) , '1.0.0' );
            $this->add( 'sed-livequery'     , SED_ASSETS_URL . '/js/livequery/sed.livequery'.$this->suffix.'.js', array( 'jquery-livequery' ) , '1.0.0' );

            //register modernizr
            $this->add( 'yepnope'   , SED_ASSETS_URL . '/js/yepnope/yepnope'.$this->suffix.'.js', array(), '2.5.6' );
            $this->add( 'modernizr' , SED_ASSETS_URL . '/js/modernizr/modernizr.custom'.$this->suffix.'.js', array('yepnope'), '2.8.1' );

        }

        function add( $handle, $src, $deps = array(), $ver = false, $in_footer = null ){
            
            wp_register_script( $handle, $src, $deps, $ver, $in_footer );
            
        }
        
        function default_styles(){

        }

        function default_fonts(){

        }

    }

}
