<?php
/*
Module Name: Twitter
Module URI: http://www.siteeditor.org/modules/twitter
Description: Module Twitter For Page Builder Application
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.0
*/

class PBTwitterShortcode extends PBShortcodeClass{
    static $sed_counter_id = 0;
    
    private $settingsFild = array();

	/**
	 * Register module with siteeditor.
	 */
	function __construct() {
		parent::__construct( array(
                "name"        => "sed_twitter",  //*require
                "title"       => __("Twitter","site-editor"),   //*require for toolbar
                "description" => __("Add Twitter To Page","site-editor"),
                "icon"        => "twitter",  //*require for icon toolbar
                "module"      =>  "twitter"  //*require
                //"is_child"    =>  "false"  //for childe shortcodes like sed_tr , sed_td for table module
            )// Args
		);
	}

    function get_atts(){
        $atts = array(
            "consumer_key"          => '' ,
            "consumer_secret"       => '' ,
            "access_token"          => '' ,
            "access_token_secret"   => '' ,
            "twitter_id"            => '' ,
            "count"                 => 3  ,
            "has_cover"             => true
        );

        return $atts;

    }

    function add_shortcode( $atts , $content = null ){
        
        self::$sed_counter_id++;
        $module_html_id = "sed_twitter_module_html_id_" . self::$sed_counter_id;

        $this->set_vars( array(
            "module_html_id"     => $module_html_id ,   
        ));     

    }

    function shortcode_settings(){

        return array(

            "consumer_key"   => array(
                "type"      => "text",
                "label"     => __("Consumer Key:","site-editor"),
                "description"  => __('Consumer Key',"site-editor")
            ),

            "consumer_secret"   => array(
                "type"      => "text",
                "label"     => __("Consumer Secret:","site-editor"),
                "description"  => __('Consumer Secret',"site-editor")
            ),

            "access_token"   => array(
                "type"      => "text",
                "label"     => __("Access Token:","site-editor"),
                "description"  => __('Access Token',"site-editor")
            ),

            "access_token_secret"   => array(
                "type"      => "text",
                "label"     => __("Access Token Secret:","site-editor"),
                "description"  => __('Access Token Secret',"site-editor")
            ),

            "twitter_id"   => array(
                "type"      => "text",
                "label"     => __("Twitter Username:","site-editor"),
                "description"  => __('Twitter Username',"site-editor")
            ),

            'count' => array(
                'type'  => 'number',
                'after_field'  => 'px',
                'label' => __('Number of Tweets:', 'site-editor'),
                "description"  => __('This option allows you to set the number of tweets that is going to show in this module.', 'site-editor'),
            ),
            "align"     =>  array(
                "type"          => "align" ,
                "label"         => __("Align", "site-editor"),
                "default"       => "default"
            ),
            'spacing' => array(
                "type"          => "spacing" ,
                "label"         => __("Spacing", "site-editor"),
                "default"       => "10 0 10 0" ,
            ), 
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
        );

    }

    function contextmenu( $context_menu ){
        $context_menu->create_menu( "twitter" , __("Twitter","site-editor") , 'icon-twitter' , 'class' , 'element' , '' , "sed_twitter" , array(
            "duplicate"    => false ,
            "edit_style"        =>  false,
            "change_skin"  =>  false ,
        ) );
    }

}
new PBTwitterShortcode;

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "socials" ,
    "name"        => "twitter",
    "title"       => __("twitter","site-editor"),
    "description" => __("","site-editor"),
    "icon"        => "icon-twitter",
    //"tpl_type"    => "underscore" ,
    "shortcode"   => "sed_twitter",
    "transport"   => "ajax" ,
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    //"js_module"   => array( 'twitter-api-module', 'twitter/js/twitter-api-module.min.js', array('sed-frontend-editor') )
));
