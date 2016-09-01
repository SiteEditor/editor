<?php
/*
* Module Name: Page Navigation
* Module URI: http://www.siteeditor.org/modules/page-nav
* Description: Page Navigation Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/

class PBPageNavShortcode extends PBShortcodeClass{
    private $module_settings;
    /**
     * Register module with siteeditor.
     */
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_page_nav",                               //*require
                "title"       => __("Page Navigation","site-editor"),                 //*require for toolbar
                "description" => __("Edit Page Navigation in Front End","site-editor"),
                "icon"        => "icon-page-nav",                               //*require for icon toolbar
                "module"      =>  "page-nav"         //*require
                //"is_child"    =>  "false"       //for childe shortcodes like sed_tr , sed_td for table module
            ) // Args
        );

    }

    function get_atts(){

        $atts = array(
        'align_page_nav'    => 'ta-c' ,
        );
        return $atts;
    }

    static function get_nav_items( $custom_wp_query = '' ){

        if( !empty( $custom_wp_query ) ){
            $wp_query = $custom_wp_query;
        }else{
            global $wp_query;
        }

        global $paged;
        $items = array();

        $sed_page_nav_args = apply_filters( "sed_page_nav_args" , array(
            'format'            => '',
            'paged'             => ( is_front_page() && !get_query_var( 'paged' ) ) ? get_query_var('page') : get_query_var( 'paged', 1 ) ,
            'total_pages'       => $wp_query->max_num_pages,
            'prev_text'         => '&larr;',
            'next_text'         => '&rarr;',
            'type'              => 'list',
            'max_page_show'     => 5,
        ));

        extract( $sed_page_nav_args );
        if ( !$paged )
            $paged = 1;

        /*****************************/
        $older      = false;
        $previous   = false;
        $next       = false;
        $newer      = false;
        /****************************/

        $count_items_cat  = ceil( $total_pages / $max_page_show  );



        $current_cat     = ceil( $paged / $max_page_show );
        if( $current_cat < 1 )
            $current_cat = 1;

        $start_item  = ( ( $current_cat -1 ) * $max_page_show ) + 1;

        $end_item = $current_cat * $max_page_show ;

        if( $end_item > $total_pages )
            $end_item = $total_pages;

        /************/
        if( $current_cat > 1 ){
            $older = ( ( $current_cat - 2 ) * $max_page_show ) + 1;
        }
        /*****************/
        if( $paged > 1 ){
            $previous = $paged - 1;
        }
        /*****************/
        if( ( $paged + 1 ) <= $total_pages ){
            $next   = $paged + 1;
        }
        /****************/
        if( $count_items_cat > 1 && $current_cat < $count_items_cat ){
            $newer = ( $current_cat * $max_page_show ) + 1;
        }

        if ( $total_pages > 1 ){

            if( $older ){
                $items[] = array(
                    "link"          => esc_url( get_pagenum_link( $older ) ),
                    "title"         => __("older","site-editor") ,
                    "icon"          => "fa fa-angle-double-left" ,
                    "class_item"    => "older icon" ,
                );
            }

            if( $previous ){
                $items[] = array(
                    "link"          => esc_url( get_pagenum_link( $previous ) ) ,
                    "title"         => __("previous page","site-editor") ,
                    "icon"          => "fa fa-angle-left" ,
                    "class_item"    => "previous icon" ,
                );
             }
            /*******************************************/
            for( $i = $start_item ; $i <= $end_item ; $i++ ){
                $items[] = array(
                    "link"          => esc_url( get_pagenum_link( $i ) ),
                    "title"         => sprintf( __("Page %s" , "site-editor" ) , $i ),
                    "text"          => $i ,
                    "class_link"    => ( $i == $paged ) ? "current-page" : '' ,
                );
            }
            /***********************************************/
            if( $next ){
                $items[] = array(
                    "link"          => esc_url( get_pagenum_link( $next ) ) ,
                    "title"         => __("next page","site-editor") ,
                    "icon"          => "fa fa-angle-right" ,
                    "class_item"    => "next icon" ,
                );
            }

            if( $newer ){
                $items[] = array(
                    "link"          => esc_url( get_pagenum_link( $newer ) ) ,
                    "title"         => __("newer","site-editor") ,
                    "icon"          => "fa fa-angle-double-right" ,
                    "class_item"    => "newer icon" ,
                );
            }
        }
        return $items;
    }

    function add_shortcode( $atts , $content = null ){
             //var_dump( $atts );
        //$this->add_style("font-awesome");


    }

    function shortcode_settings(){

        return array(
            'align_page_nav' => array(
                "type"      => "select",
                "label"     => __("Align","site-editor"),
                "desc"      => __('You can use this to set modules to be left aligned, right aligned or centered.',"site-editor"),
                "options"           => array(
                    "ta-l"    => __("Left","site-editor"),
                    "ta-r"    => __("Right","site-editor"),
                    "ta-c"    => __("Center","site-editor"),
                ),
            ),
            "skin"           => 'skin_refresh',
            "animation"  =>  array(
                "type"          => "animation" ,
                "label"         => __("Animation Settings", "site-editor"),
            ),
        );

    }

    function custom_style_settings(){
        return array(

            array(
            'navigation-container' , 'ul' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow' ) , __("Navigations Container" , "site-editor") ) ,

            array(
            'navigation-item' , 'ul li a ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Navigation Item" , "site-editor") ) ,

            array(
            'navigation-item-hover' , 'ul li a:hover ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Navigation Item Hover" , "site-editor") ) ,

            array(
            'navigation-item-active' , 'ul li a:active ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Navigation Item Active" , "site-editor") ) ,

            array(
            'navigation-item-current' , 'ul li a.current-page' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Navigation Item Current" , "site-editor") ) ,

            array(
            'navigation-item-current-hover' , 'ul li a.current-page:hover ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Navigation Item Current Hover" , "site-editor") ) ,

            array(
            'navigation-item' , 'ul li.older a ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Navigation Item Older" , "site-editor") ) ,

            array(
            'navigation-item' , 'ul li.previous a ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Navigation Item Previous" , "site-editor") ) ,

            array(
            'navigation-item' , 'ul li.next a ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Navigation Item Next" , "site-editor") ) ,

            array(
            'navigation-item' , 'ul li.newer a ' ,
            array( 'background','gradient','border','border_radius' ,'padding','margin','shadow','text_shadow' , 'font' ,'line_height','text_align' ) , __("Navigation Item Newer" , "site-editor") ) ,


        );
    }

    function contextmenu( $context_menu ){
      $page_nav_menu = $context_menu->create_menu( "page-nav" , __("Page Navigation","site-editor") , 'page-nav' , 'class' , 'element' , '' , "sed_page_nav" , array(
            "seperator"        => array(45 , 75),
            "duplicate"    => false
        ));
    }

}

new PBPageNavShortcode;

global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "basic" ,
    "name"        => "page-nav",
    "title"       => __("Page Navigation","site-editor"),
    "description" => __("Edit Page Navigation in Front End","site-editor"),
    "icon"        => "icon-page-nav",
    "type_icon"   => "font",
    "shortcode"         => "sed_page_nav",
    "show_ui_in_toolbar"    => false ,
    "priority"          => 11 ,
    "transport"   => "refresh" ,
    //"js_plugin"   => 'image/js/image-plugin.min.js',
    "js_module"   => array( 'page_nav_module_script', 'page-nav/js/page-nav-module.min.js', array('site-iframe') )
));

