<?php

Class Applications extends SiteEditorModules{

    var $header;
    var $toolbar;
    var $panel;
    var $content;
    var $footer;
    var $app_components;
    var $app_name;
    var $app_modules_dir;
    var $contextmenu;


    function __construct( $args = array() , $components = array()  ) {

      $args = wp_parse_args( $args, array(
          'app_name' => ''
      ) );

      $this->app_name = $args['app_name'];

      $this->app_modules_dir = SED_APPS_PATH . DS . $this->app_name . DS . "modules";

      /*** maybe set the db name here later ***/
      //$app_components = array("header","toolbar","panel","content","footer");
      $this->app_components = $components;
      //apply_filters( $tag, $value, $param, $otherparam );
      //$app_components = apply_filters("application_components",$app_components);

      $this->load_components();

    }


    function load_components(){

       foreach( $this->app_components AS $component ){
           //load application components
           $filename = strtolower("app_{$component}.class.php");
           include (SED_APP_PATH . DS . $filename);
           $class = "App".ucfirst( strtolower( $component ) );
           $this->$component = new $class();
       }

    }

}

