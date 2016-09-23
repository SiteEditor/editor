<?php
/**
 * @package      
 *
 *siteeditor.Platform
 *
 * @subpackage  AppToolbar
 *
 * @copyright   Copyright (C) 2013 - 2014 , Inc. All rights reserved.
 * @license     see LICENSE
 */

defined('_SEDEXEC') or die;

/**
 * Application Toolbar Class For Mind Map Application and Other
 *
 * @package     
 *
 *siteeditor.Platform
 *
 * @subpackage  AppToolbar
 * @since       1.0.0
 */
Class AppPanel {

    /**
    * @var    icon base url for toolbar element
    * @since  1.0.0
    */
    public $icon_url;

    /**
    * @var    array contain all tabs and elements
    * @since  1.0.0
    */
    public $panels = array();

    public $template;

    public $current_app;

    /**
    * Class constructor.
    *
    * @param   $args
    *
    *
    * @desc    zmind_parse_args do not orginal zmind or php fuction
    *
    *
    * @since   1.0.0
    */
    function __construct(  $args = array() ) {

        $args = wp_parse_args( $args, array(
            'app' => 'siteeditor',
            'template' => 'default',
            'icon_path' => SED_BASE_DIR . DS . "images" . DS . "app_icon" . DS ,
            'icon_url' => SED_BASE_URL . "images/app_icon/"
        ) );
        /**
         * OPTIONAL: Declare each item in $args as its own variable i.e. $type, $before.
         */
         extract( $args, EXTR_SKIP );
         $this->template = $template;
         $this->current_app = $app;
         $this->icon_url = $icon_url;

    }

    /**
    *
    * @Add New Tab To ToolBar
    *
    * @param $name Unique :: tab name
    *
    * @param $title :: tab title
    *
    * @param $type  :: tab type --- element | element  :: element panel or element panel
    *
    * @param $icon :: tab icon
    *
    * @param $attr :: tab Attributes for html element :: array("class" => "file tab-file" ,"onclick" => "run_open_file();",... );
    *
    * @return void
    *
    */

    public function add_new_panel( $name , $title , $icon = "" , $def_content ,  $content_title = '' , $capability  , $attr = array(), $extra = array(),  $view = array('module' => 'default' , 'file' => 'default.php') )
    {
        $panel = new stdClass;
        $panel->name           = $name;
        $panel->title          = $title;
        $panel->icon           = $this->icon_url.$icon;
        $panel->capability     = $capability;
        $panel->content_title  = $content_title;
        $panel->def_content    = $def_content;
        $panel->attr           = $attr;
        $panel->extra          = $extra;

        if(is_array($view) && isset($view['module'] ) && $view['file']){
            ob_start();

            if(file_exists(SED_TMPL_PATH . DS . $this->template . DS . "modules" . DS . $view['module'] . DS .  $view['file'])){
                require_once SED_TMPL_PATH . DS . $this->template . DS . "modules" . DS . $view['module'] . DS .  $view['file'];
            }elseif(file_exists(SED_APPS_PATH . DS . $this->current_app . DS . "modules" . DS . $view['module'] . DS . "view" . DS . $view['file'])){
                require_once SED_APPS_PATH . DS . $this->current_app . DS . "modules" . DS . $view['module'] . DS . "view" . DS . $view['file'];
            }elseif( file_exists( $view['file'] ) ){
                require_once $view['file'];
            }

            $content = ob_get_contents();
            ob_end_clean();
        }else{
            $content = "";
        }

        if(!empty($content)){
            $panel->content = $content;
        }else{
            $panel->content = (!empty($def_content)) ? $def_content: "";
        }

        $this->panels[$panel->name] = $panel;
    }



}