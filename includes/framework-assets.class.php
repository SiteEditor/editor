<?php

/**
 *
 */
if(!class_exists('SEDFrameworkAssetsManager'))
{
    class SEDFrameworkAssetsManager extends SiteEditorAssetsManager
    {

        public $suffix;

        function __construct( ) {

            $this->suffix = ".min";

            add_action( 'wp_default_scripts'  , array( $this , 'default_scripts' ) );

            add_action( 'wp_enqueue_scripts'  , array( $this , 'enqueue_scripts' ) );

            add_action( 'wp_default_styles'   , array( $this , 'default_styles' ) );

            add_action( 'wp_enqueue_styles'   , array( $this , 'enqueue_styles' ) );

        }

        function default_scripts(){

            $this->add( 'images-loaded',        SED_FRAMEWORK_ASSETS_URL . '/js/imagesloaded/imagesloaded.pkgd'.$this->suffix.'.js', array() ,"1.2.4");

            $this->add( 'sed-app-site',         SED_FRAMEWORK_ASSETS_URL . '/js/sed_app_site'.$this->suffix.'.js', array( 'jquery' ) , "1.0.0" );

            $this->add( 'wow-animate',          SED_FRAMEWORK_ASSETS_URL . '/js/animate/wow'.$this->suffix.'.js', array( ) , "1.0.2" , 1 );

            $this->add( 'lightbox',             SED_FRAMEWORK_ASSETS_URL . '/js/lightbox/lightbox'.$this->suffix.'.js', array( 'jquery' ) );

            $this->add( 'carousel',             SED_FRAMEWORK_ASSETS_URL . '/js/slick.carousel/slick'.$this->suffix.'.js', array( ) ,"1.3.7");

            $this->add( 'easing',               SED_FRAMEWORK_ASSETS_URL . '/js/easing/jquery.easing'.$this->suffix.'.js', array('jquery') ,"1.3");

            $this->add( 'sed-masonry',          SED_FRAMEWORK_ASSETS_URL . '/js/masonry/sed-masonry'.$this->suffix.'.js', array('masonry','sed-livequery' , 'images-loaded') ,"1.2.4");

            $this->add( 'isotope',              SED_FRAMEWORK_ASSETS_URL . '/js/isotope/isotope.pkgd'.$this->suffix.'.js', array() ,"2.2.0");

            $this->add( 'waypoints',            SED_FRAMEWORK_ASSETS_URL . '/js/waypoints/waypoints'.$this->suffix.'.js', array('jquery') ,"2.0.5");

            $this->add( 'jquery-parallax',      SED_FRAMEWORK_ASSETS_URL . '/js/parallax/jquery.parallax'.$this->suffix.'.js', array( 'jquery' ) , "1.1.3" , 1 );

            $this->add( 'sed-ajax-load-posts',  SED_FRAMEWORK_ASSETS_URL . '/js/post.ajax/sed-ajax-load-posts'.$this->suffix.'.js', array( ) , "1.0.0" , 1 );

            $this->add( 'render-scripts',       SED_FRAMEWORK_ASSETS_URL . '/js/render'.$this->suffix.'.js', array( 'jquery' , 'wow-animate' , 'jquery-parallax' ) , "1.0.0" , 1 );

            $this->add( 'jplayer-plugin',       SED_FRAMEWORK_ASSETS_URL . '/js/jplayer/jquery.jplayer'.$this->suffix.'.js', array('jquery') ,"2.7.0");

            $this->add( 'jplayer-playlist',     SED_FRAMEWORK_ASSETS_URL . '/js/jplayer/jplayer.playlist'.$this->suffix.'.js', array('jquery','jplayer-plugin') ,"2.4.0");

        }

        function enqueue_scripts(){

            if( !site_editor_app_on() ){
                wp_enqueue_script('sed-app-site');
            }

            wp_enqueue_script('sed-livequery');

            wp_enqueue_script('wow-animate');

            wp_enqueue_script('jquery-parallax');

            wp_enqueue_script('render-scripts');

        }

        function default_styles(){

            $this->add_css( 'css3-animate',     SED_FRAMEWORK_ASSETS_URL . '/css/animate/css/animate'.$this->suffix.'.css' );

            $this->add_css( 'lightbox',         SED_FRAMEWORK_ASSETS_URL . '/css/lightbox/lightbox'.$this->suffix.'.css' );

            $this->add_css( 'carousel',         SED_FRAMEWORK_ASSETS_URL . '/css/slick.carousel/slick'.$this->suffix.'.css' , array() , '1.3.7');

            $this->add_css( 'general',          SED_FRAMEWORK_ASSETS_URL . '/css/general'.$this->suffix.'.css' , array() , SED_APP_VERSION );

            $this->add_css( 'custom-scrollbar', SED_FRAMEWORK_ASSETS_URL . '/css/scrollbar/jquery.mCustomScrollbar'.$this->suffix.'.css', array(), '2.3' );

        }


        function enqueue_styles(){

            //call base styles( base less framework )
            $main_style = array(
                "handle"    => 'main-style' ,
                'src'       => SED_UPLOAD_URL . '/style/siteeditor.css',
                'deps'      => array(),
                'ver'       => SED_APP_VERSION,
                'media'     => 'all',
            );

            extract( $main_style );

            wp_register_style( $handle , $src , $deps , $ver , $media ) ;
            
            wp_enqueue_style( $handle ) ;

            wp_enqueue_style( 'general' );

            wp_enqueue_style( 'css3-animate');
        }

    }

}
